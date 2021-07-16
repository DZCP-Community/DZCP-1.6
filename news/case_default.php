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

if(defined('_News')) {
    //-> Kategorie Filter
    if(!($kat = isset($_GET['kat']) ? (int)($_GET['kat']) : 0)) {
        $navKat = 'lazy';
        $n_kat = '';
        $navWhere = "WHERE `public` = 1 ".(!common::permission("intnews") ? "AND `intern` = 0" : '')."";
    } else {
        $n_kat = "AND `kat` = ".$kat;
        $navKat = $kat;
        $navWhere = "WHERE `kat` = '".$kat."' AND public = 1 ".(!common::permission("intnews") ? "AND `intern` = 0" : '')."";
    }

    //Sticky News
    $qry = common::$sql['default']->select("SELECT * FROM `{prefix_news}` WHERE `sticky` >= ? AND `datum` <= ? AND "
            . "`public` = 1 ".(common::permission("intnews") ? "" : "AND `intern` = 0")." ".$n_kat." "
            . "ORDER BY `datum` DESC LIMIT ".((common::$page - 1)*settings::get('m_news')).",".settings::get('m_news').";",
            [($time=time()),$time]);

    $show_sticky = '';
    if(common::$sql['default']->rowCount()) {
        foreach($qry as $get) {
            //-> Viewed
            $smarty->caching = false;
            $smarty->assign('viewed',$get['viewed']);
            $viewed = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/news_viewed.tpl');
            $smarty->clearAllAssign();

            //Bild
            $newsimage_get = common::$sql['default']->fetch("SELECT `katimg`,`kategorie` FROM `{prefix_news_kats}` WHERE `id` = ?;", [$get['kat']]);
            $newsimage = 'https://static.dzcp.de/images/newskat/'.stringParser::decode($newsimage_get['katimg']);
            foreach(common::SUPPORTED_PICTURE as $tmpendung) {
                if(file_exists(rootPath."/static/images/news/".$get['id'].".".$tmpendung)) {
                    $newsimage = 'https://static.dzcp.de/images/news/'.$get['id'].'.'.$tmpendung;
                    break;
                }
            }

            //-> News [Caching]
            $smarty->caching = true;
            $smarty->assign('titel',stringParser::decode($get['titel']));
            $smarty->assign('kat',$newsimage);
            $smarty->assign('kat_name',stringParser::decode($newsimage_get['kategorie']));
            $smarty->assign('id',$get['id']);
            $smarty->assign('is_mobile',common::$mobile->isMobile(),true);
            $smarty->assign('comments',common::cnt('{prefix_news_comments}', " WHERE `news` = ?","id",[(int)($get['id'])]));
            $smarty->assign('showmore','');
            $smarty->assign('dp','none');
            $smarty->assign('dir',common::$designpath);
            $smarty->assign('intern',boolval($get['intern']));
            $smarty->assign('sticky',_news_sticky);
            $smarty->assign('more',BBCode::parse_html((string)$get['klapptext']));
            $smarty->assign('viewed',$viewed);
            $smarty->assign('text',BBCode::parse_html((string)$get['text']));
            $smarty->assign('datum',date("d.m.y H:i", $get['datum']));
            $smarty->assign('autor',common::autor($get['autor']));
            $show_sticky .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/news_show.tpl',common::getSmartyCacheHash('news_'.$get['id']));
            $smarty->clearAllAssign();
        }

        unset($get,$newsimage,$viewed,$links);
    }

    //News
    $qry = common::$sql['default']->select("SELECT * FROM `{prefix_news}` WHERE `sticky` < ? AND `datum` <= ? "
            . "AND `public` = 1 ".(common::permission("intnews") ? "" : "AND `intern` = 0")." ".$n_kat." "
            . "ORDER BY `datum` DESC LIMIT ".(common::$page - 1)*settings::get('m_news').",".settings::get('m_news').";",
            [($time=time()),$time]); $show = '';

    if(common::$sql['default']->rowCount()) {
        foreach($qry as $get) {
            //-> Viewed
            $smarty->caching = false;
            $smarty->assign('viewed',$get['viewed']);
            $viewed = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/news_viewed.tpl');
            $smarty->clearAllAssign();

            //-> News-Kategorie Bild
            foreach(common::SUPPORTED_PICTURE as $end) {
                if (file_exists(basePath . "/inc/images/nopic." . $end)) {
                    $newsimage = '../inc/images/nopic.' . $end;
                    break;
                }
            }

            //Bild
            $newsimage_get = common::$sql['default']->fetch("SELECT `katimg`,`kategorie`,`color` FROM `{prefix_news_kats}` WHERE `id` = ?;", [$get['kat']]);
            $newsimage = 'https://static.dzcp.de/thumbgen.php?img=images/newskat/'.stringParser::decode($newsimage_get['katimg']).'&width=238';

            //-> News Bild by ID
            foreach(common::SUPPORTED_PICTURE as $tmpendung) {
                //-> News Bild by ID
                if(file_exists(rootPath."/static/images/news/".$get['id'].".".$tmpendung)) {
                    $newsimage = 'https://static.dzcp.de/thumbgen.php?img=images/news/'.$get['id'].'.'.$tmpendung.'&width=238';
                    break;
                }
            }

            //-> News [Caching]
            $smarty->caching = true;
            $smarty->assign('titel',stringParser::decode($get['titel']));
            $smarty->assign('kat',$newsimage);
            $smarty->assign('kat_name',stringParser::decode($newsimage_get['kategorie']));
            $smarty->assign('id',$get['id']);
            $smarty->assign('is_mobile',common::$mobile->isMobile(),true);
            $smarty->assign('comments',common::cnt('{prefix_news_comments}', " WHERE `news` = ?","id",[(int)($get['id'])]));
            $smarty->assign('showmore','');
            $smarty->assign('dir',common::$designpath);
            $smarty->assign('intern',boolval($get['intern']));
            $smarty->assign('color',stringParser::decode($newsimage_get['color']));
            $smarty->assign('sticky','');
            $smarty->assign('more',BBCode::parse_html((string)$get['more']));
            $smarty->assign('viewed',$viewed);
            $smarty->assign('text',BBCode::parse_html((string)$get['text']));
            $smarty->assign('datum',date("d.m.y H:i", $get['datum']));
            $smarty->assign('autor',common::autor($get['autor']));
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/news_show.tpl',common::getSmartyCacheHash('news_'.$get['id']));
            $smarty->clearAllAssign();
        }

        unset($get,$newsimage,$viewed,$links);
    }

    //-> Kategorie Filter Menu
    $qrykat = common::$sql['default']->select("SELECT `id`,`kategorie` FROM `{prefix_news_kats}`;");
    $kategorien = '';
    if(common::$sql['default']->rowCount()) {
        foreach($qrykat as $getkat) {
            $kategorien .= common::select_field($getkat['id'],(isset($_GET['kat']) && (int)($_GET['kat']) == $getkat['id']),stringParser::decode($getkat['kategorie']));
        }
    }

    //-> Index Output
    $smarty->caching = false;
    $smarty->assign('show',$show);
    $smarty->assign('show_sticky',$show_sticky);
    $smarty->assign('nav',common::nav(common::cnt('{prefix_news}',$navWhere),settings::get('m_news'),"?kat=".$navKat));
    $smarty->assign('kategorien',$kategorien);
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/news.tpl');
    unset($smarty,$show,$show_sticky,$kategorien);
}