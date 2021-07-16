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

## OUTPUT BUFFER START ##
if(!ob_start("ob_gzhandler")) ob_start();
define('basePath', dirname(__FILE__));

## INCLUDES ##
include(basePath . "/inc/common.php");

/**
 * Startseite fur einen User abrufen
 * @return string
 */
function startpage()
{
    $startpageID = (common::$userid >= 1 ? common::data('startpage') : 0);
    if (!$startpageID) {
        return 'user/?action=userlobby';
    }
    $get = common::$sql['default']->fetch("SELECT `url`,`level` FROM `{prefix_startpage}` WHERE `id` = ? LIMIT 1", [$startpageID]);
    if (!common::$sql['default']->rowCount()) {
        common::$sql['default']->update("UPDATE `{prefix_users}` SET `startpage` = 0 WHERE `id` = ?;", [common::$userid]);
        return 'user/?action=userlobby';
    }

    $page = $get['level'] <= common::$chkMe ? stringParser::decode($get['url']) : 'user/?action=userlobby';
    return (!empty($page) ? $page : 'news/');
}

header('Location: ' . (common::$chkMe ? startpage() : 'news/'));