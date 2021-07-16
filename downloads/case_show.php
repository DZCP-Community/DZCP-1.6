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

$qry = common::$server->getDlCategorys(1200); $kats = '';
if(count($qry->getCategorys()) && !$qry->isError()) {
    foreach($qry->getCategorys() as $get) {
        /*
         * SubKats
         */
        $subkats = '';
        foreach($get->getSubCategorys() as $get_subkats) {
            $smarty->cache_lifetime = 1200;
            $smarty->assign('subkat',stringParser::decode($get_subkats['name']));
            $smarty->assign('kid',stringParser::decode($get->getId()));
            $smarty->assign('skid',stringParser::decode($get_subkats['id']));
            $subkats .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/download_subkats.tpl');
            $smarty->clearAllAssign();
        }

        /*
         * Kats
         */
        if(!empty($subkats)) {
            $smarty->cache_lifetime = 1200;
            $smarty->assign('kat', stringParser::decode($get->getName()));
            $smarty->assign('subkats', $subkats);
            $kats .= $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/download_kats.tpl');
            $smarty->clearAllAssign();
        }
    }
}

// Top Downloads
$top_dl = ''; $i = 1;
$where_sql = ['public'=>true];
if(!common::permission('dlintern'))
    $where_sql['intern'] = false;

$where_sql['catID'] = (int)$_GET['id'];
$where_sql['subCatID'] = (int)$_GET['sub'];

$qry = common::$server->getDownloads(['desc'=>true,'where'=>$where_sql],610, true);
if(!$qry->isError()) {
    foreach ($qry->getDownloads() as $get) {
        $smarty->caching = false;
        $smarty->assign('titel', common::cut(stringParser::decode($get->getName()), 18));
        $smarty->assign('date', date("d.m.Y - H:i:s", intval($get->getTime())));
        $smarty->assign('fulltitel', BBCode::parse_html($get->getName()));
        $smarty->assign('size', common::parser_filesize($get->getStats()->getSize()));
        $smarty->assign('hits', $get->getStats()->getDownloads());
        $smarty->assign('id', $get->getId());
        $smarty->assign('i', $i);
        $top_dl .= $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/top_new_dl.tpl');
        $smarty->clearAllAssign();
        $i++;
    }
}

$smarty->assign('head', common::$server->getDlCategory($where_sql['catID'], 6000, true)->getName());
$smarty->assign('sub', common::$server->getDlSubCategory($where_sql['subCatID'],6000)->getName());
$smarty->assign('kats', $kats);
$smarty->assign('show', $top_dl);
$index = $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/downloads_kats.tpl');