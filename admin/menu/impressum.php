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

if(_adminMenu != 'true') exit;

$where = $where.': '._config_impressum_head;

if(common::$do == "update") {
    if(settings::changed(($key='i_autor'),($var=stringParser::encode($_POST['seitenautor'])))) settings::set($key,$var);
    if(settings::changed(($key='i_domain'),($var=stringParser::encode($_POST['domain'])))) settings::set($key,$var);
    settings::load();
    $show = common::info(_config_set, "?admin=impressum");
} else {
    $smarty->caching = false;
    $smarty->assign('domain',stringParser::decode(settings::get('i_domain')));
    $smarty->assign('bbcode',BBCode::parse_html(settings::get('seitenautor')));
    $smarty->assign('postautor',stringParser::decode(settings::get('i_autor')));
    $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_impressum.tpl');
    $smarty->clearAllAssign();

    $smarty->caching = false;
    $smarty->assign('what',"impressum");
    $smarty->assign('value',_button_value_edit);
    $smarty->assign('show',$show);
    $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/imp.tpl');
    $smarty->clearAllAssign();
}