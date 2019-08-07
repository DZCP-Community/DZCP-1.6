<?php

namespace Webmasters\Doctrine;

use Doctrine\Common\Cache\Cache;
use \Webmasters\Doctrine\ORM\Util\OptionsCollection;

/**
 * Class Configuration
 * @package Webmasters\Doctrine
 */
class Configuration implements IConfiguration {
    private $autogenerate_proxy_classes;
    private $base_dir;
    private $debug_mode;
    private $em_class;
    private $entity_dir;
    private $gedmo_ext;
    private $proxy_dir;
    private $vendor_dir;
    private $connectionOptions;
    private $cache;
    private $custom;
    private $entity_namespace;

    /**
     * Configuration constructor.
     */
    public function __construct() {
        $this->autogenerate_proxy_classes = true;
        $this->base_dir = "";
        $this->debug_mode = false;
        $this->em_class = '\\Webmasters\\Doctrine\\ORM\\EntityManager';
        $this->entity_dir = "";
        $this->gedmo_ext = ['Timestampable'];
        $this->proxy_dir = realpath(ini_get('session.save_path'));
        $this->vendor_dir = "";
        $this->connectionOptions = [];
        $this->cache['query'] = null;
        $this->cache['metadata'] = null;
        $this->cache['result'] = null;
        $this->custom = new OptionsCollection([]);
        $this->entity_namespace = "";
    }

    /**
     * @return array
     */
    public function getConnectionOptions(): array {
        return $this->connectionOptions;
    }

    /**
     * @param array $connectionOptions
     */
    public function setConnectionOptions(array $connectionOptions = []): void {
        $this->connectionOptions = $connectionOptions;
    }

    /**
     * @return bool
     */
    public function getAutogenerateProxyClasses(): bool {
        return $this->autogenerate_proxy_classes;
    }

    /**
     * @param bool $var
     */
    public function setAutogenerateProxyClasses(bool $var = true): void {
        $this->autogenerate_proxy_classes = $var;
    }

    /**
     * @return string
     */
    public function getBaseDir(): string {
        return $this->base_dir;
    }

    /**
     * @param string $dir
     */
    public function setBaseDir(string $dir = ""): void {
        $this->base_dir = $dir;
    }

    /**
     * @return bool
     */
    public function getDebugMode(): bool {
        return $this->debug_mode;
    }

    /**
     * @param bool $var
     */
    public function setDebugMode(bool $var = true): void {
        $this->debug_mode = $var;
    }

    /**
     * @return string
     */
    public function getEntityManagerClass(): string {
        return $this->em_class;
    }

    /**
     * @param string $class
     */
    public function setEntityManagerClass(string $class = ""): void {
        $this->em_class = $class;
    }

    /**
     * @return string
     */
    public function getEntityDir(): string {
        return $this->entity_dir;
    }

    /**
     * @param string $dir
     */
    public function setEntityDir(string $dir = ""): void {
        $this->entity_dir = $dir;
    }

    /**
     * @return string
     */
    public function getEntityNamespace(): string {
        return $this->entity_namespace;
    }

    /**
     * @param string $namespace
     */
    public function setEntityNamespace(string $namespace = ""): void {
        $this->entity_namespace = $namespace;
    }

    /**
     * @return array
     */
    public function getGedmoExt(): array {
        return $this->gedmo_ext;
    }

    /**
     * @param array $ext
     */
    public function setGedmoExt(array $ext = []): void {
        $this->gedmo_ext = $ext;
    }

    /**
     * @return string
     */
    public function getProxyDir(): string {
        return $this->proxy_dir;
    }

    /**
     * @param string $dir
     */
    public function setProxyDir(string $dir = ""): void {
        $this->proxy_dir = $dir;
    }

    /**
     * @return string
     */
    public function getVendorDir(): string {
        return $this->vendor_dir;
    }

    /**
     * @param string $dir
     */
    public function setVendorDir(string $dir = ""): void {
        $this->vendor_dir = $dir;
    }

    /**
     * @return Cache|null
     */
    public function getMetadataCacheImpl(): ?Cache {
        return $this->cache['metadata'];
    }

    /**
     * @param Cache|null $cache
     */
    public function setMetadataCacheImpl(Cache $cache = null): void {
        $this->cache['metadata'] = $cache;
    }

    /**
     * @return Cache|null
     */
    public function getQueryCacheImpl(): ?Cache {
        return $this->cache['query'];
    }

    /**
     * @param Cache|null $cache
     */
    public function setQueryCacheImpl(Cache $cache = null): void {
        $this->cache['query'] = $cache;
    }

    /**
     * @return Cache|null
     */
    public function getResultCacheImpl(): ?Cache {
        return $this->cache['result'];
    }

    /**
     * @param Cache|null $cache
     */
    public function setResultCacheImpl(Cache $cache = null): void {
        $this->cache['result'] = $cache;
    }

    /**
     * @return OptionsCollection
     */
    public function getCustomConfig(): OptionsCollection {
        return $this->custom;
    }

    /**
     * @param OptionsCollection $config
     */
    public function setCustomConfig(OptionsCollection $config): void {
        $this->custom = $config;
    }

    /**
     * @param array $config
     */
    public function setCustomConfigArray(array $config): void {
        $this->custom = new WORM\Util\OptionsCollection($config);
    }
}