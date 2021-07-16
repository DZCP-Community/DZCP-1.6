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

if(defined('_Konto')) {


    /*
    $qry = common::$sql['default']->select("SELECT `id`,`kat`,`titel`,`datum`,`autor` "
            . "FROM `{prefix_artikel}` "
            . "WHERE `public` = 1 ".common::orderby_sql(["artikel","titel","datum","kat"], 'ORDER BY `datum` DESC')." "
            . "LIMIT ".(common::$page - 1)*settings::get('m_artikel').",".settings::get('m_artikel').";");

    if(common::$sql['default']->rowCount()) {
        $show = ''; $color = 0;
        foreach($qry as $get) {
            $getk = common::$sql['default']->fetch("SELECT `kategorie` FROM `{prefix_news_kats}` WHERE `id` = ?;", [$get['kat']]);
            $titel = '<a style="display:block" href="?action=show&amp;id='.$get['id'].'">'.stringParser::decode($get['titel']).'</a>';

            //-> Gen List
            $smarty->caching = true;
            $smarty->assign('autor',common::autor($get['autor']));
            $smarty->assign('date',date("d.m.y", $get['datum']));
            $smarty->assign('titel',$titel);
            $smarty->assign('color',$color);
            $smarty->assign('kat',stringParser::decode($getk['kategorie']));
            $smarty->assign('comments',common::cnt('{prefix_artikel_comments}'," WHERE `artikel` = ?","id",[$get['id']]));
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/konto_show.tpl',common::getSmartyCacheHash('artikel_show_'.$get['id']));
            $smarty->clearAllAssign(); $color++;
        }
    } else {
        $smarty->caching = false;
        $smarty->assign('colspan',4);
        $show = $smarty->fetch('string:'._no_entrys_yet);
        $smarty->clearAllAssign();
    }
    */



    $orderby = array_key_exists('orderby',$_GET) ? strtolower($_GET['orderby']) : '';
    $order = array_key_exists('order',$_GET) ? $_GET['order'] == 'ASC' : false;
    $balance = common::getServer()->AccountShow(common::userid(),$orderby,$order,2);

    if(count($balance->getItems())) {
        $show = ''; $color = 0;
        foreach($balance->getItems() as $get) {
            $smarty->caching = true;
            $smarty->assign('datum',date("d.m.Y - H:i:s",$get['created']));
            $smarty->assign('action',$get['action']);
            $smarty->assign('transid',$get['transid']);
            $smarty->assign('to',$get['to'] ? $get['to'] : '-');
            $smarty->assign('from',$get['from'] ? $get['from'] : '-');
            $smarty->assign('balance',number_format($get['balance']), 0, '', '.');
            $smarty->assign('color',$color);
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/konto_show.tpl',common::getSmartyCacheHash('konto_show_'.$get['transid']));
            $smarty->clearAllAssign(); $color++;
        }
    }

    $seiten = common::nav($balance->getTotal(),2,common::orderby_nav());

    $smarty->caching = false;
    /** @var TYPE_NAME $show */
    $smarty->assign('show',$show);
    $smarty->assign('seiten',$seiten);
    $smarty->assign('notification_page','');
    $smarty->assign('order_action',common::orderby('action'));
    $smarty->assign('order_transid',common::orderby('transid'));
    $smarty->assign('order_to',common::orderby('to'));
    $smarty->assign('order_from',common::orderby('from'));
    $smarty->assign('order_balance',common::orderby('balance'));
    $smarty->assign('order_datum',common::orderby('datum'));
    $smarty->assign('summe',number_format($balance->getBalance(), 0, '', '.'));
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/konto.tpl');
    $smarty->clearAllAssign();
}