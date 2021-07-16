<?php
/**
 * DZCP - deV!L`z ClanPortal - Server ( api.dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * DZCP - deV!L`z ClanPortal - Server
 * Homepage: https://www.dzcp.de
 * E-Mail: lbrucksch@hammermaps.de
 * Author Lucas Brucksch
 * Copyright 2021 © Codedesigns
 */

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\BrowserConsoleHandler;

use Symfony\Component\Filesystem as FS;
use Nette\Database as NETTE;

use Phpfastcache\CacheManager;
use Phpfastcache\Exceptions\PhpfastcacheDriverCheckException;
use Phpfastcache\Exceptions\PhpfastcacheDriverException;
use Phpfastcache\Exceptions\PhpfastcacheDriverNotFoundException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use phpFastCache\Exceptions\phpFastCacheInvalidConfigurationException;

/**
 * Class BaseSystemAbstract
 */
abstract class BaseSystemAbstract {

    /**
     * @var bool
     */
    public bool $cronjob;

    /**
     * @var int
     */
    private int $cronjob_time;

    /**
     * @var string
     */
    private string $cronjob_log_file;

    /**
     * @var array
     */
    private array $logger;

    /**
     * @var bool
     */
    private bool $dzcp_client;

    /**
     * @var array
     */
    private array $input;

    /**
     * @var GUMP
     */
    private GUMP $gump;

    /**
     * @var FS\Filesystem
     */
    private FS\Filesystem $filesystem;

    /**
     * @var \Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface
     */
    private $cache_instance;

    /**
     * @var NETTE\Connection
     */
    private NETTE\Connection $database;

    /**
     * @var BaseSession
     */
    private BaseSession $session;

    /**
     * @var bool
     */
    private bool $enable_session;

    /**
     * BaseSystemAbstract constructor.
     * @param bool $cronjob
     * @throws PhpfastcacheDriverCheckException
     * @throws PhpfastcacheDriverException
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheInvalidConfigurationException
     * @throws ReflectionException
     * @throws PhpfastcacheLogicException|\Phpfastcache\Exceptions\PhpfastcacheLogicException
     */
    public function __construct(bool $cronjob = false) {
        $this->cronjob = $cronjob;
        $this->cronjob_time = 0;
        $this->cronjob_log_file = '';
        $this->input = [];

        //Fix for cronjob
        if(!array_key_exists('HTTP_USER_AGENT',$_SERVER)) {
            $_SERVER['HTTP_USER_AGENT'] = '';
        }

        // Is DZCP
        $this->dzcp_client = (strtolower($_SERVER['HTTP_USER_AGENT']) === 'dzcp');

        // no debug & error reporting
        if($this->isDZCPClient()) {
            error_reporting(0);
            ini_set('display_errors', 0);
        }

        // Init Logger System
        $this->logger['system'] = new Logger('System');
        $this->logger['database'] = new Logger('Database');
        $this->logger['cronjob'] = new Logger('Cronjob');

        if(!(DEBUG && $this->isDZCPClient()) || DEBUG_TO_FILE) {
            $this->getSystemLogger()->pushHandler(new StreamHandler(LOG_PATH.'/BaseSystem'.
                ($this->isCronjob() ? '_'.date("Y-m-d_H:i:s",$this->getCronjobTime()).'.cronjob' : '').'.log',
                DEBUG &&  DEBUG_SYSTEM ? Logger::DEBUG : Logger::CRITICAL));

            $this->getDatabaseLogger()->pushHandler(new StreamHandler(LOG_PATH.'/PDO'.
                ($this->isCronjob() ? '_'.date("Y-m-d_H:i:s",$this->getCronjobTime()).'.cronjob' : '').'.log',
                DEBUG && DEBUG_DATABASE ? Logger::DEBUG : Logger::WARNING));
        }

        if($this->isCronjob()) {
            $this->cronjob_log_file = '/Cronjob.log';
            $this->getCronjobLogger()->pushHandler(new StreamHandler(LOG_PATH . $this->cronjob_log_file, Logger::INFO));
        }

        // Now add browser console
        if(DEBUG && !$this->isDZCPClient()) {
            if(DEBUG_SYSTEM) {
                $this->getSystemLogger()->pushHandler(new BrowserConsoleHandler(Logger::DEBUG));
                $this->getSystemLogger()->info('Logger is enabled');
            }

            if(DEBUG_DATABASE) {
                $this->getDatabaseLogger()->pushHandler(new BrowserConsoleHandler(Logger::DEBUG));
                $this->getDatabaseLogger()->info('Logger is enabled');
            }

            if($this->isCronjob()) {
                $this->getCronjobLogger()->info('Logger is enabled');
            }
        }

        //Init Fileystem
        $this->filesystem = new FS\Filesystem();

        // GUMP
        $this->gump = new \GUMP();
        $input = $this->getGump()->sanitize(array_merge($_GET,$_POST));
        $this->getSystemLogger()->debug('GUMP: Inputs:',$input);

        //Filter Input
        $this->getGump()->validation_rules(['serialize' => 'required']);
        $this->getGump()->filter_rules(['serialize' => 'trim|sanitize_string']);

        try {
            $serialize_input = $this->getGump()->run($input);
        } catch (Exception $e) {
            $serialize_input = false;
            $this->getSystemLogger()->warning('GUMP: Input: "serialize" is required!');
        }

        $unserialize = [];
        if($serialize_input !== false && is_array($serialize_input)) {
            if(!empty($serialize_input['serialize']))
                $unserialize = unserialize(gzuncompress(hex2bin($serialize_input['serialize'])));
        }

        //Decode
        foreach($input as $key => $var) {
            if($key == 'serialize') continue;
            if(array_key_exists($key,$unserialize)) {
                if($unserialize[$key]) {
                    $this->input[$key] = unserialize(gzuncompress(hex2bin($var)));
                    continue;
                }
            }

            $this->input[$key] = utf8_decode($var);
        }

        if($this->isDZCPClient()) {
            //Filter Input
            $this->getGump()->validation_rules(['language' => 'required|alpha_numeric|min_len,2']);
            $this->getGump()->filter_rules(['language' => 'trim|sanitize_string']);

            try {
                $language_input = $this->getGump()->run($this->input);
                if ($language_input !== false) {
                    $language = $this->getInput()['language'];
                } else {
                    $language = 'en';
                }
            } catch (Exception $e) {
                $language = 'en';
            }

            if ($this->getFilesystem()->exists(VENDOR_PATH . '/wixel/gump/lang/' . $language . '.php')) {
                $this->getSystemLogger()->debug('GUMP: Language "' . $language . '" is loaded');
                try {
                    $this->gump = new \GUMP($language);
                } catch (Exception $e) {
                    $this->getSystemLogger()->critical('GUMP: Language "' . $language . '" is not loaded!',
                        [$e->getMessage(),$e->getLine(),$e->getFile()]);
                    $this->gump = new \GUMP();
                } //Re Init with language
            } else {
                $this->getSystemLogger()->warning('GUMP: Language "' . $language . '" is not exists!');
            } unset($language,$language_input);
        }

        // Check is a Instance of GUMP
        if(!$this->getGump() instanceof GUMP) {
            $this->getSystemLogger()->critical('GUMP-Error: GUMP not loaded!');
        }

        // Init Cache
        $cache_config = new Phpfastcache\Config\ConfigurationOption([
            "path" => SCRIPT_PATH.'/cache',
            "itemDetailedDate" => false
        ]);

        try {
            $this->cache_instance = CacheManager::getInstance('files',$cache_config);
            $this->getSystemLogger()->debug('CacheManager: Use APC as Cache');
        } catch (PhpfastcacheDriverCheckException |
                PhpfastcacheDriverException |
                PhpfastcacheDriverNotFoundException |
                PhpfastcacheInvalidArgumentException |
                phpFastCacheInvalidConfigurationException
        $e) {
            $this->getSystemLogger()->critical('CacheManager-Error: '.$e->getMessage(),$e);
            $this->getSystemLogger()->debug('CacheManager: Use Files as Cache');
            $this->cache_instance = CacheManager::getInstance('files',$cache_config);
        }

        // Init Database
        $sql_options = ['lazy' => true, PDO::ATTR_PERSISTENT => SQL_PERSISTENT];
        $this->database = new NETTE\Connection(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $sql_options);

        if($sql_options[PDO::ATTR_PERSISTENT])
            $this->getDatabaseLogger()->debug('Persistent connect to: "'.$this->database->getDsn().'"');
        else
            $this->getDatabaseLogger()->debug('Connect to: "'.$this->database->getDsn().'"');

        $this->database->connect();
        unset($sql_options);

        //Sessions
        $this->enable_session = false;
        $this->session = new BaseSession($this);
        $this->session->setGcPeriod(1800);

        $this->getSystemLogger()->debug('Common: initialized!');
    }

    /**
     * BaseSystemAbstract shutdown.
     */
    public function __shutdown(): void {
        $this->getSystemLogger()->debug('Common: shutdown!');

        //Save Session
        $this->getSession()->save();

        // Disconnect from database server
        if(!SQL_PERSISTENT) {
            $this->getDatabaseLogger()->debug('Disconnect: "' . $this->database->getDsn() . '"');
            $this->getDatabase()->disconnect();
        }
        unset($this->database);

        // Unregister Logger
        foreach ($this->logger as $name => $logger) {
            $this->logger[$name]->close();
            unset($this->logger[$name],$logger);
        }
    }

    /**
     * System Start.
     */
    public function __run(): void {
        //Only Cronjob
        if($this->isCronjob()) {
            $this->setCronjobTime(time());

            //Session data garbage collection
            $this->getSession()->gc();
        }

        //Sessions
        if(array_key_exists('session',$this->getInput()) || $this->enable_session) {
            if (!$this->isCronjob() || $this->enable_session) {
                $this->enable_session = true;
                try {
                    $this->session->start();
                } catch (Exception $e) {
                    $this->getSystemLogger()->critical('BaseSession-Error: ' . $e->getMessage(), $e);
                }
            }
        }
    }

    /**
     * @param $data
     */
    public function __debug($data) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        exit();
    }

    /**
     * Getter / Setter
     */

    /**
     * @param int $cronjob_time
     */
    public function setCronjobTime(int $cronjob_time): void
    {
        $this->cronjob_time = $cronjob_time;
    }

    /**
     * @return bool
     */
    public function isCronjob(): bool
    {
        return $this->cronjob;
    }

    /**
     * @return int
     */
    public function getCronjobTime(): int
    {
        return $this->cronjob_time;
    }

    /**
     * @return Logger
     */
    public function getSystemLogger(): Logger
    {
        return $this->logger['system'];
    }

    /**
     * @return Logger
     */
    public function getDatabaseLogger(): Logger
    {
        return $this->logger['database'];
    }

    /**
     * @return Logger
     */
    public function getCronjobLogger(): Logger
    {
        return $this->logger['cronjob'];
    }

    /**
     * @return bool
     */
    public function isDZCPClient(): bool
    {
        return $this->dzcp_client;
    }

    /**
     * @return array
     */
    public function getInput(): array
    {
        return is_null($this->input) ? [] : $this->input;
    }

    /**
     * @param array $input
     */
    public function setInput(array $input): void {
        $this->input = $input;
    }

    /**
     * @return FS\Filesystem
     */
    public function getFilesystem(): FS\Filesystem
    {
        return $this->filesystem;
    }

    /**
     * @return GUMP
     */
    public function getGump(): GUMP
    {
        return $this->gump;
    }

    /**
     * @return \Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface
     */
    public function getCacheInstance(): \Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface
    {
        return $this->cache_instance;
    }

    /**
     * @return NETTE\Connection
     */
    public function getDatabase(): NETTE\Connection
    {
        return $this->database;
    }

    /**
     * @return BaseSession
     */
    public function getSession(): BaseSession
    {
        return $this->session;
    }

    /**
     * @return string
     */
    public function getCronjobLogFile(): string
    {
        return $this->cronjob_log_file;
    }

    /**
     * @return bool
     */
    public function isEnableSession(): bool
    {
        return $this->enable_session;
    }

    /**
     */
    public function enableSession(): void
    {
        //Sessions
        $this->enable_session = true;
        try {
            $this->session->start();
        } catch (Exception $e) {
            $this->getSystemLogger()->critical('BaseSession-Error: ' . $e->getMessage(), $e);
        }
    }
}