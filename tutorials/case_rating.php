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

if (!defined('_Tutorials')) exit();

if($_GET['vote'] > 6) {
    $index = error(_tutorials_error_vote,1);
} else {
    $qry = db("SELECT votes,rating,ips FROM ".$sql_prefix."tutorials
               WHERE `id` = '".intval($_GET['id'])."'");
    $get = _fetch($qry);

    $value = $_GET['vote']+$get['rating'];
    $ip = $get['ips'].",".$_GET['ip'];

    $qry_rows = db("SELECT ips FROM ".$sql_prefix."tutorials
                    WHERE `ips` LIKE '%".$userip."%' AND `id` = '".intval($_GET['id'])."'");

    if(_rows($qry_rows) == 0) {
        $update = db("UPDATE ".$sql_prefix."tutorials
                    SET `votes` = votes+1, 
                        `rating` = '".((int)$value)."', 
                        `ips` = '".$ip."' 
                    WHERE `id` = '".intval($_GET['id'])."'");
    }
}