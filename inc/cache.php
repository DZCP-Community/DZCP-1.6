<?php
/**
 * DZCP - deV!L`z ClanPortal - Mainpage ( dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * Diese Datei ist ein Bestandteil von dzcp.de
 * Diese Version wurde speziell von Lucas Brucksch (Codedesigns) für dzcp.de entworfen bzw. verändert.
 * Eine Weitergabe dieser Datei außerhalb von dzcp.de ist nicht gestattet.
 * Sie darf nur für die Private Nutzung (nicht kommerzielle Nutzung) verwendet werden.
 *
 * Homepage: http://www.dzcp.de
 * E-Mail: info@web-customs.com
 * E-Mail: lbrucksch@codedesigns.de
 * Copyright 2017 © CodeKing, my-STARMEDIA, Codedesigns
 */

use Phpfastcache\CacheManager;
use phpFastCache\Exceptions\phpFastCacheCoreException;

class Cache extends CacheManager {
    //Time config for system-cache ttl in seconds
    const TIME_USERSTATS = 300; //function userstats() & function userstats_increase()
    const TIME_USERINDEX = 300; //function getUserIndex()
    const TIME_USERPERM = 300; //function permission()
    const TIME_VOTE_ANSWER = 10; //function voteanswer()
    const TIME_ONLINE_CHECK = 15; //function onlinecheck()
    const TIME_TEMPLATE_XML = 300; //function smarty_function_templateswitch() & function sysTemplateswitch()
    const TIME_IPS_BLOCKING = 30; //function check_ip()
    const TIME_FILEMAN_IMG_STATS = 30; //fileman:function fileslist()
    const TIME_LESS = 1800; //function less()
    const TIME_LANGUAGE = 1800; //function lang()

    //Class Stuff
    private $cache_index = null;
    public $cache_config = [];

    function __construct() {
        $this->setEncoding("UTF-8");

        $this->cache_index['file'] = null;
        $this->cache_index['memory'] = null;
        $this->cache_index['net'] = null;

        // Setup File Path on your config files
        self::setDefaultConfig(new Phpfastcache\Config\Config(["path" => basePath.'/inc/_cache_']));

        $this->cache_config['file'] = new Phpfastcache\Config\Config([
            "itemDetailedDate" => false,
            "autoTmpFallback" => true,
            "defaultTtl" => 30,
        ]);

        $this->cache_config['memory'] = new Phpfastcache\Config\Config([
            "itemDetailedDate" => false,
            "autoTmpFallback" => true,
            "defaultTtl" => 30,
        ]);

        //File Cache
        if(extension_loaded('Zend Data Cache') && function_exists('zend_disk_cache_store')) { //Zend Server
            $this->cache_index['file'] = self::getInstance('zenddisk',$this->cache_config['file']);
        } else {
            $this->cache_index['file'] = self::getInstance('files', $this->cache_config['file']);
        }

        //Memory Cache
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            if(extension_loaded('Zend Data Cache') && function_exists('zend_shm_cache_store')) { //Zend Server
                $this->cache_index['memory'] = self::getInstance('zendshm',$this->cache_config['memory']);
            } else if(extension_loaded('wincache') && function_exists('wincache_ucache_set')) {
                $this->cache_index['memory'] = self::getInstance('wincache',$this->cache_config['memory']);
            } else if(extension_loaded('apcu') && ini_get('apc.enabled')) {
                $this->cache_index['memory'] = self::getInstance('apcu',$this->cache_config['memory']);
            } else if(extension_loaded('apc') && ini_get('apc.enabled') && strpos(PHP_SAPI, 'CGI') === false) {
                $this->cache_index['memory'] = self::getInstance('apc',$this->cache_config['memory']);
            } else if(extension_loaded('xcache') && function_exists('xcache_get')) {
                $this->cache_index['memory'] = self::getInstance('xcache',$this->cache_config['memory']);
            } else {
                $this->cache_index['memory'] = null;
            }
        } else {
            if(extension_loaded('Zend Data Cache') && function_exists('zend_shm_cache_store')) { //Zend Server
                $this->cache_index['memory'] = self::getInstance('zendshm',$this->cache_config['memory']);
            } else if(extension_loaded('apcu') && ini_get('apc.enabled')) {
                $this->cache_index['memory'] = self::getInstance('apcu',$this->cache_config['memory']);
            } else if(extension_loaded('apc') && ini_get('apc.enabled') && strpos(PHP_SAPI, 'CGI') === false) {
                $this->cache_index['memory'] = self::getInstance('apc',$this->cache_config['memory']);
            } else if(extension_loaded('xcache') && function_exists('xcache_get')) {
                $this->cache_index['memory'] = self::getInstance('xcache',$this->cache_config['memory']);
            } else {
                $this->cache_index['memory'] = null;
            }
        }

        //Network Memory Cache (NetCache)
        if(config::$use_network_cache) {
            if(config::$is_memcache && function_exists('memcache_connect')) {
                $this->cache_index['net'] = self::getInstance('memcache', ['memcache' => [config::$memcache_host, config::$memcache_port, 1],
                    'compress_data' => true,
                    'defaultKeyHashFunction' => 'sha1']);
            } else if(config::$is_memcache && class_exists('Memcached')) {
                $this->cache_index['net'] = self::getInstance('memcached', ['memcache' => [config::$memcache_host, config::$memcache_port, 1],
                    'compress_data' => true]);
            } else if(config::$is_redis && class_exists('Redis')) {
                $this->cache_index['net'] = self::getInstance('redis', ['host' => config::$redis_host,
                    'port' => config::$redis_port,
                    'password' => config::$redis_password,
                    'database' => config::$redis_database,
                    'timeout' => config::$redis_timeout,
                    'compress_data' => true,
                    'defaultKeyHashFunction' => 'sha1']);
            } else if(config::$is_redis && class_exists("\\Predis\\Client")) {
                $this->cache_index['net'] = self::getInstance('predis', ['host' => config::$redis_host,
                    'port' => config::$redis_port,
                    'password' => config::$redis_password,
                    'database' => config::$redis_database,
                    'timeout' => config::$redis_timeout,
                    'compress_data' => true,
                    'defaultKeyHashFunction' => 'sha1']);
            }
        }
    }

    private function Get($type,$key) {
        if($this->cache_index[$type] != null) {
            $CachedItem = $this->cache_index[$type]->getItem($key);
            return $CachedItem->get($key);
        }

        return false;
    }

    private function Exists($type,$key) {
        if($this->cache_index[$type] != null) {
            $CachedItem = $this->cache_index[$type]->getItem($key);
            return !is_null($CachedItem->get($key));
        }

        return false;
    }

    private function Set($type,$key,$var,$ttl=600) {
        if($this->cache_index[$type] != null) {
            $CachedItem = $this->cache_index[$type]->getItem($key);
            $CachedItem->set($var)->expiresAfter($ttl);
            return $this->cache_index[$type]->save($CachedItem);
        }

        return false;
    }

    private function Delete($type,$key) {
        if($this->cache_index[$type] != null) {
            return $this->cache_index[$type]->delete($key);
        }

        return false;
    }

    //Public
    public function IsMemory() {
        return $this->cache_index['memory'] != null;
    }

    public function IsNet() {
        return $this->cache_index['net'] != null;
    }

    public function AutoGet(string $key) {
        if($this->IsNet())
            return $this->Get('net',$key);
        else if($this->IsMemory())
            return $this->Get('memory',$key);
        else
            return $this->Get('file',$key);
    }

    public function AutoExists(string $key) {
        if($this->IsNet())
            return $this->Exists('net',$key);
        else if($this->IsMemory())
            return $this->Exists('memory',$key);
        else
            return $this->Exists('file',$key);
    }

    public function AutoSet(string $key,$var,int $ttl=600) {
        if($this->IsNet())
            return $this->Set('net',$key,$var,$ttl);
        else if($this->IsMemory())
            return $this->Set('memory',$key,$var,$ttl);
        else
            return $this->Set('file',$key,$var,$ttl);
    }

    public function AutoDelete(string $key) {
        if($this->IsNet())
            return $this->Delete('net',$key);
        else if($this->IsMemory())
            return $this->Delete('memory',$key);
        else
            return $this->Delete('file',$key);
    }

    public function FileGet(string $key) {
        return $this->Get('file',$key);
    }

    public function FileExists(string $key) {
        return $this->Exists('file',$key);
    }

    public function FileSet(string $key,$var,int $ttl=600) {
        return $this->Set('file',$key,$var,$ttl);
    }

    public function FileDelete(string $key) {
        return $this->Delete('file',$key);
    }

    public function MemGet(string $key) {
        return $this->Get('memory',$key);
    }

    public function MemExists(string $key) {
        return $this->Exists('memory',$key);
    }

    public function MemSet(string $key,$var,int $ttl=600) {
        return $this->Set('memory',$key,$var,$ttl);
    }

    public function MemDelete(string $key) {
        return $this->Delete('memory',$key);
    }

    public function NetGet(string $key) {
        return $this->Get('net',$key);
    }

    public function NetExists(string $key) {
        return $this->Exists('net',$key);
    }

    public function NetSet(string $key,$var,int $ttl=600) {
        return $this->Set('net',$key,$var,$ttl);
    }

    public function NetDelete(string $key) {
        return $this->Delete('net',$key);
    }

    public function AutoMemGet(string $key) {
        if($this->cache_index['net'] != null) {
            return $this->Get('net',$key);
        }

        return $this->Get('memory',$key);
    }

    public function AutoMemExists(string $key) {
        if($this->cache_index['net'] != null) {
            return $this->Exists('net', $key);
        }

        return $this->Exists('memory', $key);
    }

    public function AutoMemSet(string $key,$var,int $ttl=600) {
        if($this->cache_index['net'] != null) {
            return $this->Set('net',$key,$var,$ttl);
        }

        return $this->Set('memory',$key,$var,$ttl);
    }

    public function AutoMemDelete(string $key) {
        if($this->cache_index['net'] != null) {
            return $this->Delete('net', $key);
        }

        return $this->Delete('memory', $key);
    }

    private function setEncoding($encoding = 'UTF-8', $language = null)
    {
        if ($language === null || !in_array($language, ['uni', 'Japanese', 'ja', 'English', 'en'], true)) {
            $language = 'uni';
        }
        switch (strtoupper($encoding)) {
            case 'UTF-8':
                if (extension_loaded("mbstring")) {
                    mb_internal_encoding($encoding);
                    mb_http_output($encoding);
                    mb_http_input($encoding);
                    mb_language($language);
                    mb_regex_encoding($encoding);
                } else {
                    throw new phpFastCacheCoreException("MB String need to be installed for Unicode Encoding");
                }
                break;
        }
    }
}