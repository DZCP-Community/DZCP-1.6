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

if (!defined('_Sponsors')) exit();

$qry = common::$sql['default']->select("SELECT `id`,`link`,`slink`,`beschreibung`,`hits` FROM `{prefix_sponsoren}` WHERE `site` = 1 ORDER BY `pos`;");
foreach($qry as $get) {
    if(empty($get['slink'])) {
        foreach(common::SUPPORTED_PICTURE as $end) {
            if(file_exists(basePath.'/banner/sponsors/site_'.$get['id'].'.'.$end))
                break;
        }

        $smarty->caching = false;
        $smarty->assign('id',$get['id']);
        $smarty->assign('title',str_replace('http://', '', stringParser::decode($get['link'])));
        /** @var TYPE_NAME $end */
        $smarty->assign('banner',"../banner/sponsors/site_".$get['id'].".".$end);
        $banner = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/sponsors_bannerlink.tpl');
        $smarty->clearAllAssign();
    } else {
        $smarty->caching = false;
        $smarty->assign('id',$get['id']);
        $smarty->assign('title',str_replace('http://', '', stringParser::decode($get['link'])));
        $smarty->assign('banner',$get['slink']);
        $banner = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/sponsors_bannerlink.tpl');
        $smarty->clearAllAssign();
    }

    $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
    $smarty->caching = true;
    $smarty->assign('class',$class);
    $smarty->assign('beschreibung',BBCode::parse_html((string)$get['beschreibung']));
    $smarty->assign('hits',$get['hits'],true);
    $smarty->assign('banner',$banner);
    $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/sponsors_show.tpl');
    $smarty->clearAllAssign();
}

if(empty($show))
    $show = '<tr><td colspan="2" class="contentMainSecond">'._no_entrys.'</td></tr>';

$smarty->caching = false;
$smarty->assign('show',$show);
$index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/sponsors.tpl');
$smarty->clearAllAssign();
unset($show);