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
define('basePath', dirname(dirname(__FILE__).'../'));

## INCLUDES ##
include(basePath."/inc/common.php");

## SETTINGS ##
$dir = "impressum";
$where = _site_impressum;
$smarty = common::getSmarty(); //Use Smarty

## SECTIONS ##
$smarty->caching = false;
$smarty->assign('show_domain',stringParser::decode(settings::get('i_domain')));
$smarty->assign('show_autor',BBCode::parse_html(settings::get('i_autor')));
$index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/impressum.tpl');
$smarty->clearAllAssign();

## INDEX OUTPUT ##
$title = common::$pagetitle." - ".$where;
common::page($index, $title, $where);