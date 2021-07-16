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

//-> Generiert die Infobox bei Fehlern oder Erfolg etc. / neuer Ersatz fur function info() & error()
class notification {
    static public $notification_index = ['global' => []];
    static private $notification_success = false;
    static private $smarty = NULL;

    public static function add_error($msg = '', $index='global', $link = false, $time = 3) {
        self::$notification_success = false;
        return self::import('error', $msg, $link, $time, $index);
    }

    public static function add_success($msg = '', $index='global', $link = false, $time = 3) {
        self::$notification_success = true;
        return self::import('success', $msg, $link, $time, $index);
    }

    public static function add_notice($msg = '', $index='global', $link = false, $time = 3) {
        return self::import('notice', $msg, $link, $time, $index);
    }

    public static function add_warning($msg = '', $index='global', $link = false, $time = 3) {
        return self::import('warning', $msg, $link, $time, $index);
    }

    public static function add_custom($status = 'custom', $msg = '', $index='global', $link = false, $time = 3) {
        return self::import($status, $msg, $link, $time, $index);
    }

    public static function get($index='global',$tr=false): string {
        $notification = '';
        self::$smarty = common::getSmarty(true);
        self::$smarty->caching = false;
        if(array_key_exists(strval($index),self::$notification_index) &&
            count(self::$notification_index[$index]) >= 1) {
            foreach (self::$notification_index[$index] as $id => $data) {
                if($data['link']) {
                    $data['link'] = '<script language="javascript" type="text/javascript">window.setTimeout("DZCP.goTo(\''.$data['link'].'\');", '.($data['time']*1000).');</script>'
                        . '<noscript><meta http-equiv="refresh" content="'.$data['time'].';url='.$data['link'].'"></noscript>';
                } else { $data['link'] = ''; } unset($data['time']);
                $data['status_msg'] = (defined('_notification_'.$data['status']) ? constant('_notification_'.$data['status']) : $data['status']);
                foreach ($data as $key => $var) {
                    self::$smarty->assign($key,$var);
                }
                $notification .= self::$smarty->fetch('file:['.common::$tmpdir.']page/notification_box.tpl');
                unset(self::$notification_index[$index][$id]);
            }
        }

        self::$smarty->clearAllAssign();
        return ($tr ? '<tr><td class="contentMainFirst" colspan="2" align="center">'.$notification.'</td></tr>' : $notification);
    }

    public static function is_success() {
        return self::$notification_success;
    }

    public static function has($index='global') {
        if(!array_key_exists($index,self::$notification_index)) return false;
        return (count(self::$notification_index[$index]) >= 1);
    }

    //Private
    private static function import($status, $msg, $link, $time, $index) {
        $data = ['status' => strtolower($status), 'msg' => $msg, 'link' => $link, 'time' => $time];
        self::$notification_index[$index][] = $data;
    }
}