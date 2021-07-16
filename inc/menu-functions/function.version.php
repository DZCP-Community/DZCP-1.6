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

function smarty_function_version($params,Smarty_Internal_Template &$smarty) {
    $params['type'] = !array_key_exists('type',$params) ? 'live' : $params['type'];
    switch ($params['type']) {
        case 'beta':
            $version = common::$server->getVersion(APIClientMethods::DZCP_VERSION_DEV,300);
            break;
        default:
            $version = common::$server->getVersion(APIClientMethods::DZCP_VERSION_STABLE,300);
    }

    if(common::$mobile->isMobile() || common::$mobile->isTablet())
        return !empty($version->getVersion()) ? $version->getVersion() : $version->getOldVersion();

    return (!empty($version->getVersion()) ? $version->getVersion() : $version->getOldVersion()).' / '.
        (!empty($version->getRelease()) ? $version->getRelease() : $version->getOldRelease());
}