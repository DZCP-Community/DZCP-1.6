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
 * @throws SmartyException
 */
function smarty_function_l_artikel($params,Smarty_Internal_Template &$smarty) {
    $qry = common::$sql['default']->select("SELECT `id`,`titel`,`text`,`autor`,`datum`,`kat`,`public` "
        . "FROM `{prefix_artikel}` "
        . "WHERE `public` = 1 "
        . "ORDER BY `id` DESC LIMIT ".settings::get('m_lartikel').";");

    $l_articles = '';
    if(common::$sql['default']->rowCount()) {
        foreach($qry as $get) {
            $getkat = common::$sql['default']->fetch("SELECT `kategorie` FROM `{prefix_news_kats}` WHERE `id` = ?;", [$get['kat']]);
            $text = strip_tags(stringParser::decode($get['text']));
            $info = '';
            if(!common::$mobile->isMobile() || common::$mobile->isTablet()) {
                $info = 'onmouseover="DZCP.showInfo(\'' . common::jsconvert(stringParser::decode($get['titel'])) . '\', \'' . _datum . ';' .
                    _autor . ';' . _news_admin_kat . ';' . _comments_head . '\', \'' . date("d.m.Y H:i", $get['datum']) . _uhr . ';' .
                    common::fabo_autor($get['autor']) . ';' . common::jsconvert(stringParser::decode($getkat['kategorie'])) . ';' .
                    common::cnt('{prefix_artikel_comments}', "WHERE `artikel` = ?", "id", [$get['id']]) . '\')" onmouseout="DZCP.hideInfo()"';
            }

            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $smarty->assign('titel',common::cut(stringParser::decode($get['titel']),settings::get('l_lartikel')));
            $smarty->assign('text',common::cut(BBCode::parse_html($text),260));
            $smarty->assign('datum',date("d.m.Y", $get['datum']));
            $smarty->assign('info',$info);
            $l_articles .= $smarty->fetch('file:['.common::$tmpdir.']menu/l_artikel/last_artikel.tpl');
        }
    }

    return empty($l_articles) ? '<div style="margin:2px 0;text-align:center;">'._no_entrys.'</div>' : '<table class="navContent" cellspacing="0">'.$l_articles.'</table>';
}