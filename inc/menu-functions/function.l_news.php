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

/**
 * Usage {idir}
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_l_news($params,Smarty_Internal_Template &$smarty) {
    $qry = common::$sql['default']->select("SELECT `id`,`titel`,`autor`,`datum`,`kat`,`public`,`timeshift` "
        . "FROM `{prefix_news}` "
        . "WHERE `public` = 1 AND `datum` <= ? ".(common::permission("intnews") ? "" : "AND `intern` = 0")." "
        . "ORDER BY `id` DESC LIMIT ".settings::get('m_lnews').";", [time()]);

    $l_news = '';
    if(common::$sql['default']->rowCount()) {
        foreach($qry as $get) {
            $getkat = common::$sql['default']->fetch("SELECT `kategorie` FROM `{prefix_news_kats}` WHERE `id` = ?;", [$get['kat']]);
            $info = '';
            if(!common::$mobile->isMobile() || common::$mobile->isTablet()) {
                $info = 'onmouseover="DZCP.showInfo(\'' . common::jsconvert(stringParser::decode($get['titel'])) . '\', \'' .
                    _datum . ';' . _autor . ';' . _news_admin_kat . ';' . _comments_head . '\', \'' . date("d.m.Y H:i", $get['datum']) . _uhr . ';' .
                    common::fabo_autor($get['autor']) . ';' . common::jsconvert(stringParser::decode($getkat['kategorie'])) . ';' .
                    common::cnt('{prefix_news_comments}', "WHERE `news` = ?", "id", [$get['id']]) . '\')" onmouseout="DZCP.hideInfo()"';
            }

            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $smarty->assign('titel',common::cut(stringParser::decode($get['titel']),settings::get('l_lnews')));
            $smarty->assign('datum',date("d.m.Y", $get['datum']));
            $smarty->assign('info',$info);
            $l_news .= $smarty->fetch('file:['.common::$tmpdir.']menu/l_news/last_news.tpl');
        }
    }

    return empty($l_news) ? '<div style="margin:2px 0;text-align:center;">'._no_entrys.'</div>' : '<table class="navContent" cellspacing="0">'.$l_news.'</table>';
}