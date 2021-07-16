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

function smarty_function_cleanautor($params,Smarty_Internal_Template &$smarty) {
    $params['userid'] = !array_key_exists('userid',$params) ? (common::$userid >= 1 ? common::$userid : 0) : $params['userid'];
    $params['class'] = !array_key_exists('class',$params) ? '' : $params['class'];
    $params['nick'] = !array_key_exists('nick',$params) ? '' : $params['nick'];
    $params['email'] = !array_key_exists('email',$params) ? '' : $params['email'];
    return common::cleanautor(intval($params['userid']));
}