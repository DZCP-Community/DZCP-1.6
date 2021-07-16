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

function smarty_function_sponsors($params,Smarty_Internal_Template &$smarty) {
    $params['only_id'] = !array_key_exists('only_id',$params) ? 0 : (int)$params['only_id'];
    $params['begin_id'] = !array_key_exists('begin_id',$params) ? 1 : (int)$params['begin_id'];
    $params['end_id'] = !array_key_exists('end_id',$params) ? 0 : (int)$params['end_id'];
    $params['limit'] = !array_key_exists('limit',$params) ? 0 : (int)$params['limit'];

    if($params['end_id'] >= 2 && $params['begin_id'] >= 1) {
        $qry = common::$sql['default']->select("SELECT `id`,`xlink`,`xend`,`link` FROM `{prefix_sponsoren}` WHERE `box` = 1 ORDER BY `pos`".
            ($params['limit'] ? ' LIMIT '.$params['begin_id'].','.$params['end_id'] : '').";");
    } else {
        $qry = common::$sql['default']->select("SELECT `id`,`xlink`,`xend`,`link` FROM `{prefix_sponsoren}` WHERE `box` = 1 ORDER BY `pos`".
            ($params['limit'] ? ' LIMIT '.$params['limit'] : '').";");
    }

    $sponsors = '';
    if(common::$sql['default']->rowCount()) {
        foreach($qry as $get) {
            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $smarty->assign('title',htmlspecialchars(str_replace('http://', '', stringParser::decode($get['link']))));
            $smarty->assign('banner',(empty($get['xlink']) ? "../banner/sponsors/box_".$get['id'].".".$get['xend'] : stringParser::decode($get['xlink'])));
            $banner = $smarty->fetch('file:['.common::$tmpdir.']menu/sponsors/sponsors_bannerlink.tpl');

            $smarty->caching = false;
            $smarty->assign('banner',$banner);
            $sponsors .= $smarty->fetch('file:['.common::$tmpdir.']menu/sponsors/sponsors.tpl');
        }
    }

    return empty($sponsors) ? '<div style="margin:2px 0;text-align:center;">'._no_entrys.'</div>' : '<table class="navContent" cellspacing="0">'.$sponsors.'</table>';
}