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

/**
 * Usage {notification index="global"} || {notification index="xxxyyyy"}
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_notification($params,Smarty_Internal_Template &$smarty) {
    $params['tr'] = !array_key_exists('tr',$params) ? 0 : $params['tr'];
    if(array_key_exists($params['index'],notification::$notification_index))
        return notification::get($params['index'],(bool)$params['tr']);

    return "";
}