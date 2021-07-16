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

final class session {
    protected $db = null;
    protected $memcached;
    protected $_prefix;
    protected $_ttl = 3600;
    protected $_lockTimeout = 10;
    protected $_skcrypt;
    protected $_use_sqlS_local;
    public static $securityKey_mcrypt = 'xxxxxxxxxxxxxxx';

    function __construct() {
        $this->_prefix = '_';
        $this->_skcrypt = '';
        $this->_use_sqlS_local = false;

        switch(sessions_backend) {
            case 'memcache':
                if(show_sessions_debug)
                    DebugConsole::insert_info("session::__construct()", "Use Memcache for Sessions");

                session_set_save_handler([$this, 'mem_open'], [$this, 'mem_close'], [$this, 'mem_read'], [$this, 'mem_write'], [$this, 'mem_destroy'], [$this, 'mem_gc']);
                register_shutdown_function('session_write_close');
            break;
            case 'apc':
                if(show_sessions_debug)
                    DebugConsole::insert_info("session::__construct()", "Use APC for Sessions");

                $this->_ttl = sessions_ttl_maxtime;
                session_set_save_handler([$this, 'apc_open'], [$this, 'apc_close'], [$this, 'apc_read'], [$this, 'apc_write'], [$this, 'apc_destroy'], [$this, 'apc_gc']);
                register_shutdown_function('session_write_close');
            break;
            case 'mysql':
                if(show_sessions_debug)
                    DebugConsole::insert_info("session::__construct()", "Use MySQL for Sessions");

                session_set_save_handler([$this, 'sql_open'], [$this, 'sql_close'], [$this, 'sql_read'], [$this, 'sql_write'], [$this, 'sql_destroy'], [$this, 'sql_gc']);
                register_shutdown_function('session_write_close');
            break;
            default:
                if(show_sessions_debug)
                    DebugConsole::insert_info("session::__construct()", "Use PHP-Default for Sessions");
        }
    }

    public final function init($destroy=false) {
        if(!headers_sent() && !self::is_session_started()) {
            if(show_sessions_debug)
                DebugConsole::insert_info("session::init()", "Call session_start()");

            self::$securityKey_mcrypt = (!config::$cryptkey ? self::$securityKey_mcrypt : config::$cryptkey);
            $this->_prefix = self::$securityKey_mcrypt.'_';
            $this->_skcrypt = (!config::$cryptkey ? self::$securityKey_mcrypt : config::$cryptkey);


            $currentCookieParams = session_get_cookie_params();
            $currentCookieParams["secure"] = true;
            $currentCookieParams["httponly"] = true;
            session_set_cookie_params(
                $currentCookieParams["lifetime"],
                $currentCookieParams["path"],
                '.dzcp.de',
                $currentCookieParams["secure"],
                $currentCookieParams["httponly"]
            );

            session_name(sessions_name);
            if(!self::is_session_started() && session_start())
                if(show_sessions_debug)
                    DebugConsole::insert_successful("session::init()", "Sessions started, ready to use");
        }

        if($destroy) {
            DebugConsole::insert_error("session::init()", "Sessions destroy & Regenerate");
            session_unset();
            session_destroy();
            session_regenerate_id(true);
        }

        if (in_array(sessions_encode_type, hash_algos()))
            ini_set('session.hash_function', sessions_encode_type);

        ini_set('session.hash_bits_per_character', 5);
        return true;
    }

    ###################################################
    ################ Memcache Backend #################
    ###################################################
    public final function mem_open() {
        if(show_sessions_debug)
            DebugConsole::insert_info("session::mem_open()", "Connect to Memcache Server");

        if($this->memcached instanceOf Memcache) return false;
        $this->memcached = new Memcache();
        $this->memcached->addServer(sessions_memcache_host,sessions_memcache_port);

        if(show_sessions_debug) {
            if(!$this->memcached->getServerStatus(sessions_memcache_host, sessions_memcache_port)) {
                DebugConsole::insert_error("session::mem_open()", "Connect to Memcache Server failed!");
                DebugConsole::insert_error("session::mem_open()", "Host: ".sessions_memcache_host.':'.sessions_memcache_port);
            }
            else
                DebugConsole::insert_successful("session::mem_open()", "Connected to Memcache Server");
        }

        return !$this->memcached->getServerStatus(sessions_memcache_host, sessions_memcache_port) ? false : true;
    }

    public final function mem_close() {
        if(show_sessions_debug)
            DebugConsole::insert_info("session::mem_close()", "Disconnect Memcache Server");

        return $this->memcached->close();
    }

    public final function mem_read($id) {
        if(show_sessions_debug) {
            DebugConsole::insert_info("session::mem_read()", "Read Session-Data from Memcache");
            DebugConsole::insert_info("session::mem_read()", "Select ID: '".$id."'");
        }

        $data = $this->memcached->get($this->_prefix.$id);
        if(empty($data)) return '';

        if(sessions_encode)
            $data = self::decode($data,$this->_skcrypt,true);

        if(show_sessions_debug)
            DebugConsole::insert_successful("session::mem_read()", $data);

        return $data;
    }

    public final function mem_write($id, $data) {
        if(show_sessions_debug) {
            DebugConsole::insert_info("session::mem_write()", "Write Session-Data to Memcache");
            DebugConsole::insert_info("session::mem_write()", "Select ID: '".$id."'");
        }

        if(show_sessions_debug)
            DebugConsole::insert_successful("session::mem_write()", $data);

        if(sessions_encode)
            $data = self::encode($data,$this->_skcrypt,true);

        $result = $this->memcached->replace($this->_prefix.$id, $data, MEMCACHE_COMPRESSED, sessions_ttl_maxtime);
        if( $result == false )
            $result = $this->memcached->set($this->_prefix.$id, $data, MEMCACHE_COMPRESSED, sessions_ttl_maxtime);

        return $result;
    }

    public final function mem_destroy($id) {
        return $this->memcached->delete($this->_prefix.$id);
    }

    public final function mem_gc($max)
    { return true; }

    ###################################################
    ################### APC Backend ###################
    ###################################################
    public final function apc_open($savePath, $sessionName) {
        $this->_prefix = 'BSession/'.$sessionName;
        if (apc_fetch($this->_prefix.'/TS') === false) {
            apc_store($this->_prefix.'/TS', ['']);
            apc_store($this->_prefix.'/LOCK', ['']);
        }

        if(show_sessions_debug)
            DebugConsole::insert_info("session::apc_open()", "Set default store for APC");

        return true;
    }

    public final function apc_close() { return true; }

    public final function apc_read($id) {
        if(show_sessions_debug) {
            DebugConsole::insert_info("session::apc_read()", "Read Session-Data from APC");
            DebugConsole::insert_info("session::apc_read()", "Select ID: '".$id."'");
        }

        $key = $this->_prefix.'/'.$id;
        if (!apc_fetch($key)) return '';

        if ($this->_ttl) {
            $ts = apc_fetch($this->_prefix.'/TS');
            if (empty($ts[$id])) return '';
            else if (!empty($ts[$id]) && $ts[$id] + $this->_ttl < time()) {
                unset($ts[$id]);
                apc_delete($key);
                apc_store($this->_prefix.'/TS', $ts);
                return '';
            }
        }

        if (!$this->_lockTimeout) {
            $locks = apc_fetch($this->_prefix.'/LOCK');
            if (!empty($locks[$id])) {
                while (!empty($locks[$id]) && $locks[$id] + $this->_lockTimeout >= time()) {
                    usleep(10000);
                    $locks = apc_fetch($this->_prefix.'/LOCK');
                }
            }

            $locks[$id] = time();
            apc_store($this->_prefix.'/LOCK', $locks);
        }

        $data = apc_fetch($key);

        if(sessions_encode)
            $data = self::decode($data,$this->_skcrypt,true);

        if(show_sessions_debug)
            DebugConsole::insert_successful("session::apc_read()", $data);

        return $data;
    }

    public final function apc_write($id, $data) {
        if(show_sessions_debug) {
            DebugConsole::insert_info("session::apc_write()", "Write Session-Data to APC");
            DebugConsole::insert_info("session::apc_write()", "Select ID: '".$id."'");
        }

        $ts = apc_fetch($this->_prefix.'/TS');
        $ts[$id] = time();
        apc_store($this->_prefix.'/TS', $ts);

        $locks = apc_fetch($this->_prefix.'/LOCK');
        unset($locks[$id]);
        apc_store($this->_prefix.'/LOCK', $locks);

        if(show_sessions_debug)
            DebugConsole::insert_successful("session::apc_write()", $data);

        if(sessions_encode)
            $data = self::encode($data,$this->_skcrypt,true);

        return apc_store($this->_prefix.'/'.$id, $data, $this->_ttl);
    }

    public final function apc_destroy($id) {
        if(show_sessions_debug)
            DebugConsole::insert_info("session::apc_destroy()", "Call Session destroy");

        $ts = apc_fetch($this->_prefix.'/TS');
        unset($ts[$id]);
        apc_store($this->_prefix.'/TS', $ts);

        $locks = apc_fetch($this->_prefix.'/LOCK');
        unset($locks[$id]);
        apc_store($this->_prefix.'/LOCK', $locks);

        return apc_delete($this->_prefix.'/'.$id);
    }

    public final function apc_gc($lifetime) {
        if (show_sessions_debug) {
            DebugConsole::insert_info("session::apc_gc()", "Call Garbage-Collection");
        }

        if ($this->_ttl) {
            $lifetime = min($lifetime, $this->_ttl);
        }

        $ts = apc_fetch($this->_prefix.'/TS');
        foreach ($ts as $id=>$time) {
            if ($time + $lifetime < time()) {
                apc_delete($this->_prefix.'/'.$id);
                unset($ts[$id]);
            }
        }

        return apc_store($this->_prefix.'/TS', $ts);
    }

    ###################################################
    ################## MySQL Backend ##################
    ###################################################
    public final function sql_open() {
        global $db,$database;
        if (sessions_sql_sethost) {
            $this->_use_sqlS_local = false;
            $database->setConfig('sessions', ["driver" => sessions_sql_driver, "db" => sessions_sql_db,
            "db_host" => sessions_sql_host, "db_user" => sessions_sql_user, "db_pw" => sessions_sql_pass]);
            $this->db = $database->getInstance('sessions');
        } else {
            $database->cloneConfig('default','sessions');
            $this->_use_sqlS_local = true;
            $this->db = $database->getInstance('sessions');
        }
        
        if(show_sessions_debug) {
            if ($this->db instanceOf database === false) {
                DebugConsole::insert_error("session::sql_open()", "Connect to MySQL Server failed!");
                DebugConsole::insert_error("session::sql_open()", "Host: " . (sessions_sql_sethost ? sessions_sql_host : $db['host']));
                DebugConsole::insert_error("session::sql_open()", "User: " . (sessions_sql_sethost ? sessions_sql_user : $db['user']));
                DebugConsole::insert_error("session::sql_open()", "DB: " . (sessions_sql_sethost ? sessions_sql_db : $db['db']));
                echo DebugConsole::show_logs();
                exit('DZCP-Sessions: Connect to SQL Server failed!');
            }
        }

        return ($this->db instanceOf database === false);
    }

    public final function sql_close() {
        //Not Used
    }

    public final function sql_read($id) {
        if(show_sessions_debug) {
            DebugConsole::insert_info("session::sql_read()", "Read Session-Data from Database");
            DebugConsole::insert_info("session::sql_read()", "Select ID: '".$id."'");
        }

        if ($this->db instanceOf database) {
            $data = $this->db->selectSingle("SELECT `data` FROM `{prefix_sessions}` WHERE `ssid` = ? LIMIT 1;", [$id],'data');
            if(!$this->db->rowCount()) { return ''; }
            if (empty($data)) { return ''; }
            if(sessions_encode) {
                $data = self::decode($data, $this->_skcrypt, true);
            }

            if (show_sessions_debug) {
                DebugConsole::insert_successful("session::sql_read()", $data);
            }
            
            return $data;
        }

        return '';
    }

    public final function sql_write($id, $data) {
        if(show_sessions_debug) {
            DebugConsole::insert_info("session::sql_write()", "Write Session-Data to Database");
            DebugConsole::insert_info("session::sql_write()", "Select ID: '".$id."'");
        }

        if (show_sessions_debug) {
            DebugConsole::insert_successful("session::sql_write()", $data);
        }

        if (sessions_encode) {
            $data = self::encode($data, $this->_skcrypt, true);
        }

        if ($this->db instanceOf database) {
            $time = time();
            $this->db->select("SELECT `id` FROM `{prefix_sessions}` WHERE `ssid` = ? LIMIT 1;", [$id]);
            if(!$this->db->rowCount()) {
                return $this->db->insert("INSERT INTO `{prefix_sessions}` (id, ssid, time, data) VALUES (NULL, ?, ?, ?);", [$id, $time, $data]);
            } else {
                return $this->db->update("UPDATE `{prefix_sessions}` SET `time` = ?, `data` = ? WHERE `ssid` = ?;", [$time, $data, $id]);
            }
        }

        return false;
    }

    public final function sql_destroy($id) {
        if (show_sessions_debug) {
            DebugConsole::insert_info("session::sql_destroy()", "Call Session destroy");
        }

        $this->db->select("SELECT `id` FROM `{prefix_sessions}` WHERE `ssid` = ? LIMIT 1;", [$id]);
        if($this->db->rowCount()) {
            return $this->db->delete("DELETE FROM `{prefix_sessions}` WHERE `ssid` = ?;", [$id]);
        }

        return false;
    }

    public final function sql_gc($max) {
        if (show_sessions_debug) {
            DebugConsole::insert_info("session::sql_gc()", "Call Garbage-Collection");
        }

        $new_time = time() - $max;
        $this->db->select("SELECT `id` FROM `{prefix_sessions}` WHERE `time` < ".$new_time.";");
        if($this->db->rowCount()) {
            return $this->db->delete("DELETE FROM `{prefix_sessions}` WHERE `time` < ".$new_time.";");
        }

        return false;
    }

    public static function encode($data,$mcryptkey='',$binary=false,$hex=false) {
        $crypt = new Crypt(Crypt::MODE_B64,$mcryptkey);
        if (empty($mcryptkey)) { $crypt->__set('Key', self::$securityKey_mcrypt); } 
        else { $crypt->__set('Key', $mcryptkey); }
        if($binary && !$hex) { $crypt->__set('Hash',CRYPT_MODE_BINARY); }
        if(!$binary && $hex) { $crypt->__set('Hash',CRYPT_MODE_HEXADECIMAL); }
        $is_array = is_array($data);
        $data = serialize(['data' => $data, 'array' => $is_array]);
        return $crypt->Encrypt($data);
    }

    public static function decode($data,$mcryptkey='',$binary=false,$hex=false) {
        $crypt = new Crypt(Crypt::MODE_B64,$mcryptkey);
        if (empty($mcryptkey)) { $crypt->__set('Key', self::$securityKey_mcrypt); } 
        else { $crypt->__set('Key', $mcryptkey); }
        if($binary && !$hex) { $crypt->__set('Hash',CRYPT_MODE_BINARY); }
        if(!$binary && $hex) { $crypt->__set('Hash',CRYPT_MODE_HEXADECIMAL); }
        $data = unserialize($crypt->Decrypt($data));
        if (!is_array($data)) {
            return null;
        }
        return $data['data'];
    }
    
    ###################################################
    ##################### Private #####################
    ###################################################

    public static final function is_session_started() {
        if ( php_sapi_name() !== 'cli' ) {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? true : false;
            } else {
                return session_id() === '' ? false : true;
            }
        }

        return false;
    }
}