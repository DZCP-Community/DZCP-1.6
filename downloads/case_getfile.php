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

if (!defined('_Downloads')) exit();

if(settings::get("reg_dl") && !common::$chkMe)
    $index = common::error(_error_unregistered,1);
else if($_SESSION['dl_id'] >= 1) {
    $dl_key = common::$server->getDownloadKey($_SESSION['dl_id']);
    if(!$dl_key->isError() && !empty($dl_key->getKey()))
        header("Location: ".$dl_key->getServer()."?key=".$dl_key->getKey());
    else
        $index = common::error(_error_api,1);
}