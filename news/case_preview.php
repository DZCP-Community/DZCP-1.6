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

if(defined('_News')) {
    //-> Klapptext
    $klapp = '';
    if ($_POST['klapptitel']) {
        $smarty->caching = false;
        $smarty->assign('klapplink', $_POST['klapptitel']);
        $smarty->assign('which', 'collapse');
        $smarty->assign('id', '_prev');
        $klapp = $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/news_klapplink.tpl');
        $smarty->clearAllAssign();
    }

    $links1 = '';
    if(!empty($_POST['url1'])) {
        $smarty->caching = false;
        $smarty->assign('link',$_POST['link1']);
        $smarty->assign('url',common::links($_POST['url1']));
        $smarty->assign('target',"_blank");
        $links1 = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/news_link.tpl');
        $smarty->clearAllAssign();
    }

    $links2 = '';
    if(!empty($_POST['url2'])) {
        $smarty->caching = false;
        $smarty->assign('link',$_POST['link2']);
        $smarty->assign('url',common::links($_POST['url2']));
        $smarty->assign('target',"_blank");
        $links2 = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/news_link.tpl');
        $smarty->clearAllAssign();
    }

    $links3 = '';
    if(!empty($_POST['url3'])) {
        $smarty->caching = false;
        $smarty->assign('link',$_POST['link3']);
        $smarty->assign('url',common::links($_POST['url3']));
        $smarty->assign('target',"_blank");
        $links3 = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/news_link.tpl');
        $smarty->clearAllAssign();
    }

    $links = '';
    if (!empty($links1) || !empty($links2) || !empty($links3)) {
        $smarty->caching = false;
        $smarty->assign('link1',$links1);
        $smarty->assign('link2',$links2);
        $smarty->assign('link3',$links3);
        $links = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/news_links.tpl');
        $smarty->clearAllAssign();
    }

    $intern = ''; $sticky = '';
    if(isset($_POST['intern']) && $_POST['intern'] == 1) {
        $intern = _votes_intern;
    }
    
    if (isset($_POST['sticky']) && $_POST['sticky'] == 1) {
        $sticky = _news_sticky;
    }

    //-> News-Kategorie Bild
    foreach(common::SUPPORTED_PICTURE as $end) {
        if (file_exists(basePath . "/inc/images/nopic." . $end)) {
            $newsimage = '../inc/images/nopic.' . $end;
            break;
        }
    }

    $intern = $_POST['intern'] ? _votes_intern : "";

    //Bild
    $newsimage_get = common::$sql['default']->fetch("SELECT `katimg`,`kategorie`,`color` FROM `{prefix_news_kats}` WHERE `id` = ?;", [$_POST['kat']]);
    $newsimage = 'https://static.dzcp.de/thumbgen.php?img=images/newskat/'.stringParser::decode($newsimage_get['katimg']).'&width=238';

    //-> News Preview
    $smarty->caching = false;
    $smarty->assign('titel',stringParser::decode($_POST['titel']));
    $smarty->assign('kat',$newsimage);
    $smarty->assign('id',1);
    $smarty->assign('kat_name',stringParser::decode($newsimage_get['kategorie']));
    $smarty->assign('color',stringParser::decode($newsimage_get['color']));
    $smarty->assign('comments',_news_comments_prev);
    $smarty->assign('showmore','');
    $smarty->assign('dp','compact');
    $smarty->assign('notification_page','');
    $smarty->assign('sticky',$sticky);
    $smarty->assign('intern',$intern);
    $smarty->assign('more',BBCode::parse_html((string)$_POST['morenews']));
    $smarty->assign('text',BBCode::parse_html((string)$_POST['newstext']));
    $smarty->assign('datum',date("j.m.y H:i", time()));
    $smarty->assign('links',$links);
    $smarty->assign('autor',common::autor());
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/news_show_full.tpl');
    $smarty->clearAllAssign();

    common::update_user_status_preview();
    header('Content-Type: text/html; charset=utf-8');
    exit(utf8_encode('<table class="mainContent" cellspacing="1">'.$index.'</table>'));
}