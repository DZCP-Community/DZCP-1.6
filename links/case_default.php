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

if (!defined('_Links')) exit();

$qry = common::$sql['default']->select("SELECT * FROM `{prefix_links}` ORDER BY banner DESC;");
if(common::$sql['default']->rowCount()) {
    foreach($qry as $get) {
        if($get['banner']) {
            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $smarty->assign('banner',stringParser::decode($get['text']));
            $banner = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/links_bannerlink.tpl');
            $smarty->clearAllAssign();
        } else {
            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $smarty->assign('text',str_replace('http://','',stringParser::decode($get['url'])));
            $banner = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/links_textlink.tpl');
            $smarty->clearAllAssign();
        }

        $smarty->caching = false;
        $smarty->assign('beschreibung',BBCode::parse_html((string)$get['beschreibung']));
        $smarty->assign('hits',$get['hits']);
        $smarty->assign('banner',$banner);
        $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/links_show.tpl');
        $smarty->clearAllAssign();
    }
}

if(empty($show)) {
    $smarty->caching = false;
    $smarty->assign('colspan',4);
    $show = $smarty->fetch('string:'._no_entrys_yet);
    $smarty->clearAllAssign();
}

$smarty->caching = false;
$smarty->assign('show',$show);
$index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/links.tpl');
$smarty->clearAllAssign();