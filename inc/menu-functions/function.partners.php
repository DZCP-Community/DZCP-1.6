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

function smarty_function_partners($params,Smarty_Internal_Template &$smarty) {
    $params['only_id'] = !array_key_exists('only_id',$params) ? 0 : (int)$params['only_id'];
    $params['begin_id'] = !array_key_exists('begin_id',$params) ? 1 : (int)$params['begin_id'];
    $params['end_id'] = !array_key_exists('end_id',$params) ? 0 : (int)$params['end_id'];
    $params['limit'] = !array_key_exists('limit',$params) ? 0 : (int)$params['limit'];
    if($params['end_id'] >= 2 && $params['begin_id'] >= 1) {
        $qry = common::$sql['default']->select("SELECT `id`,`textlink`,`link`,`banner` FROM `{prefix_partners}` ORDER BY `textlink` ASC".
            ($params['limit'] ? ' LIMIT '.$params['begin_id'].','.$params['end_id'] : '').";");
    } else {
        $qry = common::$sql['default']->select("SELECT `id`,`textlink`,`link`,`banner` FROM `{prefix_partners}` ORDER BY `textlink` ASC".
            ($params['limit'] ? ' LIMIT '.$params['limit'] : '').";");
    }

    $partners = ''; $table = '';
    if(common::$sql['default']->rowCount()) {
        foreach($qry as $get) {
            if($params['only_id'] >= 1 && $params['only_id'] != $get['id'])
                continue;

            if($get['textlink']) {
                $smarty->caching = false;
                $smarty->assign('link',stringParser::decode($get['link']));
                $smarty->assign('name',stringParser::decode($get['banner']));
                $partners .= $smarty->fetch('file:['.common::$tmpdir.']menu/partners/partners_textlink.tpl');
            } else {
                $smarty->caching = false;
                $smarty->assign('link',stringParser::decode($get['link']));
                $smarty->assign('title',htmlspecialchars(str_replace('http://', '', stringParser::decode($get['link']))));
                $smarty->assign('banner',stringParser::decode($get['banner']));
                $partners .= $smarty->fetch('file:['.common::$tmpdir.']menu/partners/partners.tpl');
            }

            $table = strstr($partners, '<tr>') ? true : false;
        }
    }

    return empty($partners) ? '<div style="margin:2px 0;text-align:center;">'._no_entrys.'</div>' : ($table ? '<table class="navContent" cellspacing="0">'.$partners.'</table>' : $partners);
}