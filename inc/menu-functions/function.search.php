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

function smarty_function_search($params,Smarty_Internal_Template &$smarty) {
    return '';

    /*
    global $dir;
    if($dir == 'forum' || common::$search_forum) {
        $smarty->caching = true;
        return $smarty->fetch('file:['.common::$tmpdir.']menu/search/search_forum.tpl',common::getSmartyCacheHash('menu_search_forum'));
    }

    $smarty->caching = true;
    $smarty->assign('searchword',(empty($_GET['searchword']) ? _search_word : $_GET['searchword']));
    $search = $smarty->fetch('file:['.common::$tmpdir.']menu/search/search.tpl',common::getSmartyCacheHash('menu_search'));
    return $search;
    */
}