<?php

namespace Webmasters\Doctrine;

use Doctrine\Common\Cache\{ChainCache, ApcuCache, ArrayCache, ZendDataCache};
use Doctrine\Common\Annotations\{AnnotationException, CachedReader, AnnotationReader};
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\DriverChain;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM as ORM;

use Gedmo\DoctrineExtensions;

class Bootstrap implements IBootstrap {
    protected static $singletonInstance = null;

    protected $annotationReader;
    protected $cache;
    protected $cachedAnnotationReader;
    protected $driverChain;
    protected $listeners;
    protected $ormConfiguration;
    protected $configuration;

    protected $eventManager;
    protected $entityManager;

    /**
     * Bootstrap constructor.
     * @param Configuration $configuration
     */
    protected function __construct(Configuration $configuration) {
        $this->configuration = $configuration;
        $vendorDir = realpath(__DIR__ . '/../../../../..');
        $baseDir = dirname($vendorDir);
        $host = php_uname('n');
        $this->errorMode();

        if (empty($configuration->getConnectionOptions())) {
            $path = $baseDir . '/Config/';
            if (file_exists($path . $host . '-config.php')) {
                require_once $path . $host . '-config.php';
            } elseif (file_exists($path . 'default-config.php')) {
                require_once $path . 'default-config.php';
                if(file_exists($path . 'app-config.php')) {
                    require_once $path . 'app-config.php';
                }
            } else {
                die(sprintf('"config/default-config.php" or "config/%s-config.php" missing!', $host));
            }

            if(isset($connectionOptions)) {
                $configuration->setConnectionOptions($connectionOptions);
            } else if(isset($applicationConfig) && array_key_exists('connectionOptions',$applicationConfig)) {
                $configuration->setConnectionOptions($applicationConfig['connectionOptions']);
            } else {
                die(sprintf('Connection options missing!', $host));
            }
        }

        if(empty($configuration->getBaseDir())) {
            $configuration->setBaseDir($baseDir);
        }

        if(empty($configuration->getEntityDir())) {
            $configuration->setEntityDir($baseDir . '/src/Entities');
        }

        if(empty($configuration->getVendorDir())) {
            $configuration->setVendorDir($vendorDir);
        }

        if(empty($this->configuration->getMetadataCacheImpl())) {
            $configuration->setMetadataCacheImpl($this->getDefaultCache());
        }

        if(empty($this->configuration->getQueryCacheImpl())) {
            $configuration->setQueryCacheImpl($this->getDefaultCache());
        }

        if(empty($this->configuration->getResultCacheImpl())) {
            $configuration->setResultCacheImpl($this->getDefaultCache());
        }

        if (empty($this->configuration->getEntityNamespace())) {
            $this->configuration->setEntityNamespace(basename($configuration->getEntityDir()));
        }
    }

    /**
     * @return ChainCache
     */
    private function getDefaultCache(): ChainCache {
        $driver = [];
        if(function_exists('zend_shm_cache_fetch')) {
            $driver[] = new ZendDataCache();
        }

        if(function_exists('apcu_fetch')) {
            $driver[] = new ApcuCache();
        }

        $driver[] = new ArrayCache();
        return new ChainCache($driver);
    }

    protected function __clone() {}

    /**
     * @param Configuration $configuration
     * @return Bootstrap
     */
    public static function getInstance(Configuration $configuration): Bootstrap {
        if (self::$singletonInstance == null) {
            self::$singletonInstance = new Bootstrap($configuration);
        }

        return self::$singletonInstance;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration(): Configuration {
        return $this->configuration;
    }

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration): void {
        $this->configuration = $configuration;
        if(self::$singletonInstance instanceof Bootstrap) {
            self::$singletonInstance = new Bootstrap($configuration);
        }
    }

    /**
     * @param string $key
     * @param $var
     */
    public function setCustomOption(string $key,$var): void {
        $config = $this->configuration->getCustomConfig();
        $config->set($key,$var);
        $this->configuration->setCustomConfig($config);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    public function getCustomOption(string $key) {
        $config = $this->configuration->getCustomConfig();
        if($config->has($key)) {
            return $config->get($key);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool {
        return $this->configuration->getDebugMode();
    }

    /**
     * @return AnnotationReader
     * @throws AnnotationException
     */
    public function getAnnotationReader(): AnnotationReader {
        if ($this->annotationReader === null) {
            $this->annotationReader = new AnnotationReader();
        }

        return $this->annotationReader;
    }

    /**
     * @return CachedReader
     * @throws AnnotationException
     */
    public function getCachedAnnotationReader(): CachedReader {
        if ($this->cachedAnnotationReader === null) {
            $this->cachedAnnotationReader = new CachedReader(
                $this->getAnnotationReader(),
                $this->configuration->getMetadataCacheImpl(),
                $this->isDebug());
        }

        return $this->cachedAnnotationReader;
    }

    /**
     * @return DriverChain
     * @throws AnnotationException
     */
    public function getDriverChain(): DriverChain {
        if ($this->driverChain === null) {
            $this->driverChain = new DriverChain();

            $ormDir = realpath($this->configuration->getVendorDir().'/doctrine/orm/lib/Doctrine/ORM');
            // Sicherheitshalber die Datei fuer die Standard Doctrine Annotationen registrieren
            AnnotationRegistry::registerFile($ormDir . '/Mapping/Driver/DoctrineAnnotations.php');

            // Gedmo Annotationen aktivieren sofern Paket installiert
            if ($this->configuration->getGedmoExt()) {
                if (class_exists('\\Gedmo\\DoctrineExtensions')) {
                    DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
                        $this->driverChain,
                        $this->getCachedAnnotationReader()
                    );
                } else {
                    die('"gedmo/doctrine-extensions" missing!');
                }
            }

            // Wir verwenden die neue Annotations-Syntax für die Entities
            $annotationDriver = new AnnotationDriver($this->getCachedAnnotationReader(), [$this->configuration->getEntityDir()]);

            // AnnotationDriver fuer den Entity-Namespace aktivieren
            $this->driverChain->addDriver($annotationDriver, $this->configuration->getEntityNamespace());
        }

        return $this->driverChain;
    }

    /**
     * @return ORM\Configuration
     * @throws AnnotationException
     */
    public function getOrmConfiguration(): ORM\Configuration {
        if ($this->ormConfiguration === null) {
            $this->ormConfiguration = new ORM\Configuration();

            // Teile Doctrine mit, wie es mit Proxy-Klassen umgehen soll
            $this->ormConfiguration->setProxyNamespace('Proxies');
            $this->ormConfiguration->setProxyDir($this->configuration->getProxyDir());
            $this->ormConfiguration->setAutoGenerateProxyClasses($this->configuration->getAutogenerateProxyClasses());

            // Ergaenze die DriverChain in der Konfiguration
            $this->ormConfiguration->setMetadataDriverImpl($this->getDriverChain());

            // Cache fuer Metadaten, Queries und Results benutzen
            $this->ormConfiguration->setMetadataCacheImpl($this->configuration->getMetadataCacheImpl());
            $this->ormConfiguration->setQueryCacheImpl($this->configuration->getQueryCacheImpl());
            $this->ormConfiguration->setResultCacheImpl($this->configuration->getResultCacheImpl());
        }

        return $this->ormConfiguration;
    }

    /**
     * @param $ext
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function getListener($ext, $name) {
        if (!isset($this->listeners[$ext][$name])) {
            throw new \Exception(
                sprintf('Listener "%s\%s" missing', $ext, $name)
            );
        }

        return $this->listeners[$ext][$name];
    }

    /**
     * @return EventManager
     * @throws AnnotationException
     */
    public function getEventManager(): EventManager {
        if ($this->eventManager === null) {
            $this->eventManager = new EventManager();

            // Erweiterungen aktivieren
            $this->initGedmoListeners();
        }

        return $this->eventManager;
    }

    /**
     * @return mixed
     * @throws AnnotationException
     */
    public function getEntityManager(): EntityManager {
        if ($this->entityManager === null) {
            $className = $this->configuration->getEntityManagerClass();
            $this->entityManager = $className::create(
                $this->configuration->getConnectionOptions(),
                $this->getOrmConfiguration(),
                $this->getEventManager()
            );
        }

        return $this->entityManager;
    }

    /**
     * Debug Mode
     */
    protected function errorMode(): void {
        if (!$this->isDebug()) {
            error_reporting(null);
            ini_set('display_errors', 0); // nur bei nicht fatalen Fehlern
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            if (!ini_get('display_errors')) {
                die('Enable display_errors in php.ini!');
            }
        }
    }

    /**
     * @throws AnnotationException
     */
    protected function initGedmoListeners(): void {
        if (count($this->configuration->getGedmoExt()) >= 1) {
            $this->listeners['Gedmo'] = []; $listener = null;
            foreach ($this->configuration->getGedmoExt() as $name) {
                if (is_string($name)) {
                    $listenerClass = '\\Gedmo\\' . $name . '\\' . $name . 'Listener';
                    $listener = new $listenerClass();
                    $this->listeners['Gedmo'][$name] = $listener;
                }

                if(is_object($listener)) {
                    $listener->setAnnotationReader($this->getCachedAnnotationReader());
                    $this->eventManager->addEventSubscriber($listener);
                }
            }
        }
    }
}
