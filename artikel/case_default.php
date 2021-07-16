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
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel_show.tpl',common::getSmartyCacheHash('artikel_show_'.$get['id']));
            $smarty->clearAllAssign(); $color++;
        }
    } else {
        $smarty->caching = false;
        $smarty->assign('colspan',4);
        $show = $smarty->fetch('string:'._no_entrys_yet);
        $smarty->clearAllAssign();
    }

    $seiten = common::nav(common::cnt("{prefix_artikel}"),settings::get('m_artikel'),"?page".(isset($_GET['show']) ? $_GET['show'] : 0).common::orderby_nav());
    $smarty->caching = false;
    /** @var TYPE_NAME $show */
    $smarty->assign('show',$show);
    $smarty->assign('nav',$seiten);
    $smarty->assign('order_autor',common::orderby('autor'));
    $smarty->assign('order_datum',common::orderby('datum'));
    $smarty->assign('order_titel',common::orderby('titel'));
    $smarty->assign('order_kat',common::orderby('kat'));
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel.tpl');
    $smarty->clearAllAssign();
    unset($seiten,$show,$qry,$get,$getk,$titel,$class);
}