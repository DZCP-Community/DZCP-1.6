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

if(defined('_Artikel')) {
    $links1 = '';
    if(!empty($_POST['url1'])) {
        $smarty->caching = false;
        $smarty->assign('link',$_POST['link1']);
        $smarty->assign('url',common::links($_POST['url1']));
        $smarty->assign('target',"_blank");
        $links1 = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel_link.tpl');
        $smarty->clearAllAssign();
    }

    $links2 = '';
    if(!empty($_POST['url2'])) {
        $smarty->caching = false;
        $smarty->assign('link',$_POST['link2']);
        $smarty->assign('url',common::links($_POST['url2']));
        $smarty->assign('target',"_blank");
        $links2 = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel_link.tpl');
        $smarty->clearAllAssign();
    }

    $links3 = '';
    if(!empty($_POST['url3'])) {
        $smarty->caching = false;
        $smarty->assign('link',$_POST['link3']);
        $smarty->assign('url',common::links($_POST['url3']));
        $smarty->assign('target',"_blank");
        $links3 = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel_link.tpl');
        $smarty->clearAllAssign();
    }

    $links = '';
    if (!empty($links1) || !empty($links2) || !empty($links3)) {
        $smarty->caching = false;
        $smarty->assign('link1',$links1);
        $smarty->assign('link2',$links2);
        $smarty->assign('link3',$links3);
        $links = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel_links.tpl');
        $smarty->clearAllAssign();
    }

    //empty artikel kat image
    foreach(common::SUPPORTED_PICTURE as $end) {
        if (file_exists(basePath . "/inc/images/nopic." . $end)) {
            $artikelimage = '../inc/images/nopic.' . $end;
            break;
        }
    }

    $katimg = common::$sql['default']->fetch("SELECT `katimg` FROM `{prefix_news_kats}` WHERE `id` = ?;", [(int)($_POST['kat'])],'katimg');
    if(!empty($katimg) && common::$sql['default']->rowCount() && file_exists(basePath.'/inc/images/uploads/newskat/'.stringParser::decode($katimg))) {
        $artikelimage = '../inc/images/uploads/newskat/'.stringParser::decode($katimg);
    }

    //-> Artikel Preview
    $smarty->caching = false;
    $smarty->assign('titel',stringParser::decode($_POST['titel']));
    $smarty->assign('kat',$artikelimage);
    $smarty->assign('id',1);
    $smarty->assign('comments',_news_comments_prev);
    $smarty->assign('notification_page','');
    $smarty->assign('intern',$intern);
    $smarty->assign('text',BBCode::parse_html((string)$_POST['artikel']));
    $smarty->assign('datum',date("j.m.y H:i", time()));
    $smarty->assign('links',$links);
    $smarty->assign('autor',common::autor());
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/show_more.tpl');
    $smarty->clearAllAssign();

    common::update_user_status_preview();
    header('Content-Type: text/html; charset=utf-8');
    exit(utf8_encode('<table class="mainContent" cellspacing="1">'.$index.'</table>'));
}