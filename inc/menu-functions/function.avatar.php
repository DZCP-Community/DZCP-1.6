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
 * Usage {avatar} || {avatar id="1"} || {avatar id="1" height="100" width="70"} || {avatar height="100" width="70"}
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_avatar($params,Smarty_Internal_Template &$smarty) {
    $avatar = '';
    if(common::$chkMe >= 1) {
        $params['id'] = (array_key_exists('id',$params) ? (int)$params['id'] : 0);
        $params['height'] = (array_key_exists('height',$params) ? (int)$params['height'] : 100);
        $params['width'] = (array_key_exists('width',$params) ? (int)$params['width'] : 70);
        $smarty->caching = false;
        $smarty->assign('avatar_show', common::useravatar($params['id'], $params['width'], $params['height']));
        $avatar = $smarty->fetch('file:[' . common::$tmpdir . ']menu/avatar/avatars.tpl');
    }

    return $avatar;
}