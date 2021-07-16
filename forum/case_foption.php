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

if(defined('_Forum')) {
    if(common::$do == "fabo") {
        if(isset($_POST['f_abo'])) {
            common::$sql['default']->insert("INSERT INTO `{prefix_forum_abo}` SET `user` = ?, `fid` = ?, `datum` = ?", [(int)(common::$userid),(int)($_GET['id']),time()]);
        } else {
            common::$sql['default']->delete("DELETE FROM `{prefix_forum_abo}` WHERE `user` = ? AND `fid` = ?", [(int)(common::$userid),(int)($_GET['id'])]);
        }
        
        $index = common::info(_forum_fabo_do, "?action=showthread&amp;id=".$_GET['id']."");
    }
}