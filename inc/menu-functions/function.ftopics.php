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

function smarty_function_ftopics($params,Smarty_Internal_Template &$smarty) {
    $qry = common::$sql['default']->select("SELECT s1.*,s2.`kattopic`,s2.`id` as `subid` "
        . "FROM `{prefix_forum_threads}` as `s1`, `{prefix_forum_sub_kats}` as `s2`, {prefix_forum_kats} as `s3` "
        . "WHERE s1.`kid` = s2.`id` AND s2.`sid` = s3.`id` ORDER BY s1.`lp` DESC LIMIT 100;");

    $f = 0; $ftopics = '';
    if(common::$sql['default']->rowCount()) {
        foreach($qry as $get) {
            if($f == settings::get('m_ftopics')) { break; }
            if(common::forum_intern($get['kid'])) {
                $lp = common::cnt("{prefix_forum_posts}", " WHERE `sid` = ?","id", [$get['id']]);
                $pagenr = ceil($lp/settings::get('m_fposts'));
                $page = !$pagenr ? 1 : $pagenr;
                $info = '';
                if(!common::$mobile->isMobile() || common::$mobile->isTablet()) {
                    $info = 'onmouseover="DZCP.showInfo(\'' . common::jsconvert(stringParser::decode($get['topic'])) . '\', \'' .
                        _forum_kat . ';' . _forum_posts . ';' . _forum_lpost . '\', \'' . stringParser::decode($get['kattopic']) . ';' . ++$lp . ';' .
                        date("d.m.Y H:i", $get['lp']) . _uhr . '\')" onmouseout="DZCP.hideInfo()"';
                }

                $smarty->caching = false;
                $smarty->assign('id',$get['id']);
                $smarty->assign('pagenr',$page);
                $smarty->assign('p',$lp);
                $smarty->assign('titel',common::cut(stringParser::decode($get['topic']),settings::get('l_ftopics')));
                $smarty->assign('info',$info);
                $smarty->assign('kid',$get['kid']);
                $ftopics .= $smarty->fetch('file:['.common::$tmpdir.']menu/forum/forum_topics.tpl');
                $f++;
            }
        }
    }

    return empty($ftopics) ? '<div style="margin:2px 0;text-align:center;">'._no_entrys.'</div>' : '<table class="navContent" cellspacing="0">'.$ftopics.'</table>';
}