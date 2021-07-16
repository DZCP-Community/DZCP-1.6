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

/* Nicht DSGVO konform! */
/*
class sfs extends common {
    private static $endpoint = 'https://api.stopforumspam.org/api';
    private static $url = '';
    private static $json = '';
    private static $confidence = 70;
    private static $frequency = 50;
    private static $autoblock = true;
    private static $blockuser = false;
    public static function check() {
        ## http://de.wikipedia.org/wiki/Private_IP-Adresse ##
        if(!self::validateIpV4Range(self::$userip['v4'], '[192].[168].[0-255].[0-255]') && !self::validateIpV4Range(self::$userip['v4'], '[127].[0].[0-255].[0-255]') &&
            !self::validateIpV4Range(self::$userip['v4'], '[10].[0-255].[0-255].[0-255]') && !self::validateIpV4Range(self::$userip['v4'], '[172].[16-31].[0-255].[0-255]')) {
            $get = self::$sql['default']->fetch("SELECT * FROM `{prefix_ipban}` WHERE `ipv4` = ? LIMIT 1;", [self::$userip['v4']]);
            if(self::$sql['default']->rowCount()) {
                if((time()-$get['time']) > (2*86400) && $get['enable']) {
                    self::get(['ip' => self::$userip['v4']]); //Array ( [success] => 1 [ip] => Array ( [lastseen] => 2013-04-26 19:57:51 [frequency] => 1327 [appears] => 1 [confidence] => 99.89 ) )
                    $stopforumspam = self::$json;
                    if(is_array($stopforumspam) && array_key_exists('success', $stopforumspam) && $stopforumspam['success']) {
                        $stopforumspam = $stopforumspam['ip']; // Array ( [lastseen] => 2013-04-26 19:57:51 [frequency] => 1327 [appears] => 1 [confidence] => 99.89 )
                        $stopforumspam_data_db = unserialize($get['data']);
                        if($stopforumspam['appears'] == '1' && ($stopforumspam['confidence'] >= self::$confidence && $stopforumspam['frequency'] >= self::$frequency) && self::$autoblock) {
                            $stopforumspam_data_db['confidence'] = $stopforumspam['confidence'];
                            $stopforumspam_data_db['frequency'] = $stopforumspam['frequency'];
                            $stopforumspam_data_db['lastseen'] = $stopforumspam['lastseen'];
                            $stopforumspam_data_db['banned_msg'] = 'Autoblock by stopforumspam.com';
                            self::$sql['default']->update("UPDATE `{prefix_ipban}` SET `time` = ?, `typ` = 1, `data` = ? WHERE `id` = ?;",
                                    [time(),serialize($stopforumspam_data_db),$get['id']]);
                            self::$sql['default']->delete("DELETE FROM `{prefix_counter_ips}` WHERE `ipv4` = ?;", [self::$userip['v4']]);
                            self::$sql['default']->delete("DELETE FROM `{prefix_counter_whoison}` WHERE `ipv4` = ?;", [self::$userip['v4']]);
                            self::$sql['default']->delete("DELETE FROM `{prefix_iptodns}` WHERE `ipv4` = ?;", [self::$userip['v4']]);
                            self::$blockuser = true;
                        } else {
                            $stopforumspam_data_db['appears'] = $stopforumspam['appears'];
                            self::$sql['default']->update("UPDATE `{prefix_ipban}` SET `time` = ?, `typ` = 0, `data` = ? WHERE `id` = ?;",
                                    [time(),serialize($stopforumspam_data_db),$get['id']]);
                            self::$blockuser = false;
                        }
                    }
                }
                else if($get['typ'] == 1)
                    self::$blockuser = true;
                else
                    self::$blockuser = false;
            } else {
                //typ: 0 = Off, 1 = GSL, 2 = SysBan, 3 = Ipban
                self::get(['ip' => self::$userip['v4']]); //Array ( [success] => 1 [ip] => Array ( [lastseen] => 2013-04-26 19:57:51 [frequency] => 1327 [appears] => 1 [confidence] => 99.89 ) )
                $stopforumspam = self::$json;
                if(is_array($stopforumspam) && array_key_exists('success', $stopforumspam) && $stopforumspam['success']) {
                    $stopforumspam = $stopforumspam['ip']; // Array ( [lastseen] => 2013-04-26 19:57:51 [frequency] => 1327 [appears] => 1 [confidence] => 99.89 )
                    if($stopforumspam['appears'] == '1' && $stopforumspam['confidence'] >= self::$confidence && $stopforumspam['frequency'] >= self::$frequency && self::$autoblock) {
                        $stopforumspam['banned_msg'] = 'Autoblock by stopforumspam.com';
                        self::$sql['default']->delete("DELETE FROM `{prefix_counter_ips}` WHERE `ipv4` = ?;", [self::$userip['v4']]);
                        self::$sql['default']->delete("DELETE FROM `{prefix_counter_whoison}` WHERE `ipv4` = ?;", [self::$userip['v4']]);
                        self::$sql['default']->delete("DELETE FROM `{prefix_iptodns}` WHERE `ipv4` = ?;", [self::$userip['v4']]);
                        self::$sql['default']->insert("INSERT INTO `{prefix_ipban}` SET `ipv4` = ?, `time` = ?, `typ` = 1, `data` = ?;",
                                [self::$userip['v4'],time(),serialize($stopforumspam)]); //Banned
                        self::$blockuser = true;
                    } else {
                        $stopforumspam['banned_msg'] = '';
                        self::$sql['default']->insert("INSERT INTO `{prefix_ipban}` SET `ipv4` = ?, `time` = ?,`typ` = 0, `data` = ?;",
                                [self::$userip['v4'],time(),serialize($stopforumspam)]); //Add to DB
                        self::$blockuser = false;
                    }
                }
            }
        }
    }

    public static function is_spammer()
    { return self::$blockuser; }

    public static function get( $args = []) {
        self::$url = self::$endpoint.'api?f=json&'.http_build_query($args, '', '&');
        if(!self::call_json($args)) return ['data' => ['success' => '0']];
        return ['data' => ['success' => '0']];
    }

    protected static function call_json($args = []) {
        if(config::$view_error_reporting && config::$debug_save_to_file) {
            $fp = fopen(basePath."/inc/_logs_/fsf_ips.log", "a+");
            fwrite($fp, self::$url); 
            fclose($fp);
        }

        if(!(self::$json = common::get_external_contents(self::$endpoint,$args))) return false;
        if(empty(self::$json)) return false;

        self::$json = json_decode(self::$json,true);
        if(!self::$json) false;
        return true;
    }
}
*/