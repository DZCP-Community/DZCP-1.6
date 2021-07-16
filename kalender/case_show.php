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

if (!defined('_Kalender')) exit();

$qry = common::$sql['default']->select("SELECT * FROM `{prefix_events}` WHERE DATE_FORMAT(FROM_UNIXTIME(datum), '%d.%m.%Y') = ? ORDER BY `datum`;", [date("d.m.Y",(int)($_GET['time']))]);
$events = '';
foreach($qry as $get) {
    $edit = '';
    if(common::permission("editkalender")) {
        $smarty->caching = false;
        $smarty->assign('action', "../admin/?admin=kalender&do=edit&id=" . $get['id']);
        $smarty->assign('title', _button_title_edit);
        $edit = $smarty->fetch('file:[' . common::$tmpdir . ']page/buttons/button_edit_url.tpl');
        $smarty->clearAllAssign();
    }

    $smarty->caching = false;
    $smarty->assign('edit',$edit);
    $smarty->assign('show_time',date("H:i", $get['datum'])._uhr);
    $smarty->assign('show_event',BBCode::parse_html((string)$get['event']));
    $smarty->assign('show_title',stringParser::decode($get['title']));
    $events .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/event_show.tpl');
    $smarty->clearAllAssign();
}

$smarty->caching = false;
$smarty->assign('datum',date("d.m.Y",$_GET['time']));
$head = $smarty->fetch('string:'._kalender_events_head);
$smarty->clearAllAssign();

$smarty->caching = false;
$smarty->assign('head',$head);
$smarty->assign('events',$events);
$index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/event.tpl');
$smarty->clearAllAssign();
unset($head,$events);