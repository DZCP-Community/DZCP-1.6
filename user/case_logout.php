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

if(defined('_UserMenu')) {
    $where = _site_user_logout;
    if(common::$chkMe && common::$userid) {
        common::$sql['default']->update("UPDATE `{prefix_users}` SET `online` = 0, `sessid` = '' WHERE `id` = ?;", [common::$userid]);
        common::$sql['default']->delete("DELETE FROM `{prefix_autologin}` WHERE `ssid` = ?;", [session_id()]);
        common::setIpcheck("logout(".common::$userid.")");
        common::dzcp_session_destroy();
    }

    header("Location: ../news/");
}