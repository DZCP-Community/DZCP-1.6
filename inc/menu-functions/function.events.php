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

function smarty_function_events($params,Smarty_Internal_Template &$smarty) {
    $qry = common::$sql['default']->select("SELECT `id`,`datum`,`title`,`event` "
        . "FROM `{prefix_events}` "
        . "WHERE `datum` > ? "
        . "ORDER BY `datum` LIMIT ".settings::get('m_events').";", [time()]);
    $eventbox = '';
    if(common::$sql['default']->rowCount()) {
        foreach($qry as $get) {
            $info = '';
            if(!common::$mobile->isMobile() || common::$mobile->isTablet()) {
                $info = 'onmouseover="DZCP.showInfo(\'' . common::jsconvert(stringParser::decode($get['title'])) . '\', \'' . _kalender_uhrzeit . ';' .
                    _datum . '\', \'' . date("H:i", $get['datum']) . _uhr . ';' .
                    date("d.m.Y", $get['datum']) . '\')" onmouseout="DZCP.hideInfo()"';
            }

            $smarty->caching = false;
            $smarty->assign('datum',date("d.m.",$get['datum']));
            $smarty->assign('timestamp',$get['datum']);
            $smarty->assign('event',stringParser::decode($get['title']));
            $events = $smarty->fetch('file:['.common::$tmpdir.']menu/event/next_event_link.tpl');

            $smarty->caching = false;
            $smarty->assign('events',$events);
            $smarty->assign('info',$info);
            $eventbox .= $smarty->fetch('file:['.common::$tmpdir.']menu/event/event.tpl');
        }
    }

    return empty($eventbox) ? '<div style="margin:2px 0;text-align:center;">'._no_events.'</div>' : '<table class="navContent" cellspacing="0">'.$eventbox.'</table>';
}