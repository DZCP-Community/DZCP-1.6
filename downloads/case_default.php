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

/*
 * Downloads Top Menu
 */
$dl_top = '';
$where_sql = ['public'=>true,'top'=>true,'addons'=>common::$is_addons];
if(!common::permission('dlintern'))
    $where_sql['intern'] = false;

$qry = common::$server->getDownloads(['limit' => 2,'orderby' => 'time','where'=>$where_sql],600);
if(!$qry->isError()) {
    foreach($qry->getDownloads() as $get) {
        $pic = '../'.common::getTplImgDir().'/downloads/nodl.jpg';
        foreach(common::SUPPORTED_PICTURE as $tmpendung) {
            if(file_exists(rootPath."/static/images/downloads/dl_".$get->getId().".".$tmpendung)) {
                $pic = "https://static.dzcp.de/images/downloads/dl_".$get->getId().".".$tmpendung;
                break;
            }
        }

        $smarty->caching = false;
        $smarty->assign('titel',stringParser::decode($get->getName()));
        $smarty->assign('pic',$pic);
        $smarty->assign('desc',common::cut(BBCode::parse_html($get->getDescription()),190));
        $smarty->assign('kat',common::$server->getDlCategory($get->getCatID(),common::$is_addons)->getName());
        $smarty->assign('date',date("d.m.Y - H:i:s",$get->getTime()));
        $smarty->assign('hits',$get->getStats()->getDownloads());
        $smarty->assign('id',$get->getId());

        $dl_top .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/dl_top_show.tpl');
        $smarty->clearAllAssign();
    }

    $smarty->assign('show',$dl_top);
    $dl_top = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/dl_top.tpl');
    $smarty->clearAllAssign();
}

// Top Downloads
$top_dl = ''; $i = 1;
$where_sql = ['public'=>true,'addons'=>common::$is_addons];
if(!common::permission('dlintern'))
    $where_sql['intern'] = false;

$qry = common::$server->getDownloads(['limit' => 6,'desc'=>true,'where'=>$where_sql],610);
if(!$qry->isError()) {
    foreach ($qry->getDownloads() as $get) {
        $smarty->caching = false;
        $smarty->assign('titel', common::cut(stringParser::decode($get->getName()), 18));
        $smarty->assign('date', date("d.m.Y - H:i:s", $get->getTime()));
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

// New Downloads
$new_dl = ''; $i = 1;
$qry = common::$server->getDownloads(['limit' => 6,'orderby' => 'time','desc'=>true,'where'=>$where_sql], 620);
if(!$qry->isError()) {
    foreach ($qry->getDownloads() as $get) {
        $smarty->caching = false;
        $smarty->assign('titel', common::cut(stringParser::decode($get->getName()), 18));
        $smarty->assign('date', date("d.m.Y - H:i:s", $get->getTime()));
        $smarty->assign('fulltitel', BBCode::parse_html($get->getName()));
        $smarty->assign('size', common::parser_filesize($get->getStats()->getSize()));
        $smarty->assign('hits', $get->getStats()->getDownloads());
        $smarty->assign('id', $get->getId());
        $smarty->assign('i', $i);
        $new_dl .= $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/top_new_dl.tpl');
        $smarty->clearAllAssign();
        $i++;
    }
}

$smarty->caching = false;
$smarty->assign('kats',$kats);
$smarty->assign('new_dl',$new_dl);
$smarty->assign('top_dl',$top_dl);
$smarty->assign('dl_top',$dl_top);
$index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/downloads.tpl');
$smarty->clearAllAssign();