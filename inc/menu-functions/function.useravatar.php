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

function smarty_function_useravatar($params,Smarty_Internal_Template &$smarty) {
    $params['userid'] = !array_key_exists('userid',$params) ? 0 : $params['userid'];
    $params['width'] = !array_key_exists('width',$params) ? 100 : $params['width'];
    $params['height'] = !array_key_exists('height',$params) ? 100 : $params['height'];
    return common::useravatar((int)$params['userid'],(int)$params['width'],(int)$params['height']);
}