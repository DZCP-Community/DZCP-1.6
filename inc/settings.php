<?php
/**
 * DZCP - deV!L`z ClanPortal - Mainpage ( dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * ge�ndert d�rch my-STARMEDIA und Codedesigns.
 *
 * Diese Datei ist ein Bestandteil von dzcp.de
 * Diese Version wurde speziell von Lucas Brucksch (Codedesigns) f�r dzcp.de entworfen bzw. ver�ndert.
 * Eine Weitergabe dieser Datei au�erhalb von dzcp.de ist nicht gestattet.
 * Sie darf nur f�r die Private Nutzung (nicht kommerzielle Nutzung) verwendet werden.
 *
 * Homepage: http://www.dzcp.de
 * E-Mail: info@web-customs.com
 * E-Mail: lbrucksch@codedesigns.de
 * Copyright 2017 � CodeKing, my-STARMEDIA, Codedesigns
 */

class settings extends common {
    private static $index = [];

    /**
     * Gibt eine Einstellung aus der Settings Tabelle zur�ck
     * @param string $what
     * @param bool $decode
     * @return string|int|boolean
     */
    public final static function get(string $what='',bool $decode=false) {
        $what = utf8_encode(strtolower($what));
        $get = self::$sql['default']->fetch("SELECT `value` FROM `{prefix_settings}` WHERE `key` = ? LIMIT 1;", [$what]);
        if(!self::$sql['default']->rowCount()) {
            if (show_settings_debug) {
                DebugConsole::insert_error('settings::get()', 'Setting "' . $what . '" not found in ' . self::$sql['default']->rep_prefix('{prefix_settings}'));
            }
        } else {
            return ($decode ? stringParser::decode(utf8_decode($get['value'])) : utf8_decode($get['value']));
        }

        return false;
    }

    /**
     * Gibt mehrere Einstellungen aus der Settings Tabelle zur�ck
     * @param array $what
     * @return array|boolean
     */
    public final static function get_array($what= []) {
        if (!is_array($what) || !count($what) || empty($what)) {
            return false;
        }

        $return = [];
        foreach ($what as $key) {
            $key = strtolower($key);
            if(array_key_exists($key, self::$index)) {
                $data = self::$index[$key];
                $return[$key] = utf8_decode($data['value']);
            }
        }

        if(count($return) >= 1) return $return;
        return false;
    }

    /**
     * Gibt die Standard Einstellung einer Einstellung zur�ck
     * @param string $what
     * @return mixed|boolean
     */
    public final static function get_default($what='') {
        $what = utf8_encode(strtolower($what));
        if (self::is_exists($what)) {
            $data = self::$index[$what];
            return utf8_decode($data['default']);
        } else {
            $get = self::$sql['default']->fetch("SELECT `default` FROM `{prefix_settings}` WHERE `key` = ? LIMIT 1;", [$what]);
            if(!self::$sql['default']->rowCount()) {
                if (show_settings_debug) {
                    DebugConsole::insert_error('settings::get_default()', 'Setting "' . $what . '" not found in '.common::$sql['default']->rep_prefix('{prefix_settings}'));
                }
            } else {
                return utf8_decode($get['default']);
            }
        }

        return false;
    }

    /**
     * Aktualisiert die Werte innerhalb der Settings Tabelle
     * @param string $what
     * @param string $var
     * @param bool $default
     * @return boolean
     */
    public final static function set($what='',$var='',bool $default=true) {
        $what = utf8_encode(strtolower($what));
        if(self::is_exists($what)) {
            if(self::changed($what,$var)) {
                $var = !is_integer($var) && empty($var) && $default ? self::get_default($what) : $var;
                $data = self::$index[$what];
                $data['value'] = ($data['length'] >= 1 ? self::cut($var,((int)$data['length']),false, false) : $var);
                self::$index[$what] = $data;
                if (show_settings_debug) {
                    DebugConsole::insert_successful('settings::set()', 'Set "'.$what.'" to "'.$var.'"');
                }
                return self::$sql['default']->update("UPDATE `{prefix_settings}` SET `value` = ? WHERE `key` = ?;",
                    [utf8_encode($data['length'] >= 1 ? self::cut($var,((int)$data['length']),false, false) : $var),$what]) ? true : false;
            }
        }

        return false;
    }

    /**
     * Vergleicht den Aktuellen Wert mit dem neuen Wert ob ein Update erforderlich ist
     * @param string $what
     * @param string $var
     * @return boolean
     */
    public final static function changed($what='',$var='') {
        $what = utf8_encode(strtolower($what));
        if(self::is_exists($what)) {
            $data = self::$index[$what];
            return ($data['value'] == (is_integer($var) ? $var : utf8_encode($var)) ? false : true);
        }

        return false;
    }

    /**
     * Pr�ft ob ein Key existiert
     * @param string $what
     * @return boolean
     */
    public final static function is_exists($what='') { 
        return (array_key_exists(strtolower($what), self::$index)); 
    }

    /**
     * Laden der Einstellungen aus der Datenbank
     */
    public final static function load() {
        $qry = self::$sql['default']->select("SELECT `key`,`value`,`default`,`length`,`type` FROM `{prefix_settings}`;");
        foreach($qry as $get) {
            $setting = [];
            $setting['value'] = !((int)$get['length']) ? $get['type'] == 'int' ? ((int)$get['value']) : ((string)$get['value'])
            : self::cut($get['type'] == 'int' ? ((int)$get['value']) : ((string)$get['value']),((int)$get['length']),false,false);
            $setting['default'] = $get['type'] == 'int' ? ((int)$get['default']) : ((string)$get['default']);
            $setting['length'] = ((int)$get['length']);
            self::$index[$get['key']] = $setting; unset($setting);
        }
    }

    /**
     * Eine neue Einstellung in die Datenbank schreiben
     * @param string $what
     * @param string $var
     * @param string $default
     * @param string $length
     * @param boolean $int
     * @return boolean
     */
    public final static function add($what='',$var='',$default='',$length='',$int=false) {
        $what = strtolower($what);
        if(!self::is_exists($what)) {
            $setting = [];
            $setting['value'] = !((int)$length) ? $int ? ((int)$var) : ((string)$var)
            : self::cut($int ? ((int)$var) : ((string)$var),((int)$length),false,false);
            $setting['default'] = $int ? ((int)$default) : ((string)$default);
            $setting['length'] = ((int)$length);
            self::$index[$what] = $setting;
            unset($setting);
            if (show_settings_debug) {
                DebugConsole::insert_successful('settings::add()', 'Add "'.$what.'" set to "'.$var.'"');
            }
            return self::$sql['default']->insert("INSERT INTO `{prefix_settings}` SET `key` = ?, `value` = ?,"
                . "`default` = ?,`length` = ?,`type` = '".($int ? 'int' : 'string')."';", [utf8_encode($what),utf8_encode($var),utf8_encode($default),$length]);
        }

        return false;
    }

    /**
     * L�scht eine Einstellung aus der Datenbank
     * @param string $what
     * @return boolean
     */
    public final static function remove($what='') {
        $what = strtolower($what);
        if(self::is_exists($what)) {
            if (show_settings_debug) {
                DebugConsole::insert_info('settings::remove()', 'Remove "'.$what.'"');
            }
            unset(self::$index[$what]);
            return self::$sql['default']->delete("DELETE FROM `{prefix_settings}` WHERE `key` = ?;", [$what]) ? true : false;
        }

        return false;
    }
}