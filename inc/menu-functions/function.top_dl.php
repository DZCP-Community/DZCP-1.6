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

require_once(common::$server->getModelsDir()."/DZCPDlCategory.php");

function smarty_function_top_dl($params,Smarty_Internal_Template &$smarty) {
    $where_sql = ['public'=>true,'addons'=>common::$is_addons,'top'=>true];
    if(!common::permission('dlintern'))
        $where_sql['intern'] = false;

    $top_dl = '';
    $options = [];
    if(!settings::get('m_topdl')) {
        $options['limit'] = settings::get('m_topdl');
        $options['desc'] = true;
    }

    $options['where'] = $where_sql;
    $qry = common::$server->getDownloads($options,600);
    $qryCat = common::$server->getDlCategorys(1200);

    $category = new DZCPDlCategory(['name' => '-']);
    if(count($qryCat->getCategorys()) && !$qryCat->isError()) {
        foreach ($qry->getDownloads() as $get) {
            if(!$qryCat->isError()) {
                foreach ($qryCat->getCategorys() as $category) {
                    if($category->getId() == $get->getCatID()) {
                        break;
                    }
                }
            }

            $info = '';
            if(!common::$mobile->isMobile() || common::$mobile->isTablet()) {
                $info = 'onmouseover="DZCP.showInfo(\'' . common::jsconvert(stringParser::decode($get->getName())) .
                    '\', \'' . _datum . ';' . _dl_dlkat . ';' . _hits . '\', \'' . date("d.m.Y H:i", $get->getTime()) . _uhr . ';' .
                    common::jsconvert(stringParser::decode($category->getName())) . ';' . $get->getStats()->getDownloads() . '\')" onmouseout="DZCP.hideInfo()"';
            }

            $smarty->assign('id',$get->getId());
            $smarty->assign('titel',common::cut(stringParser::decode($get->getName()),settings::get('l_topdl')));
            $smarty->assign('info',$info);
            $smarty->assign('hits',$get->getStats()->getDownloads());
            $top_dl .= $smarty->fetch('file:['.common::$tmpdir.']menu/top_dl/top_dl.tpl');
        }
    }

    return empty($top_dl) ? '<div style="margin:2px 0;text-align:center;">'._no_entrys.'</div>' : '<table class="navContent" cellspacing="0">'.$top_dl.'</table>';
}