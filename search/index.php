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

## OUTPUT BUFFER START ##
if(!ob_start("ob_gzhandler")) ob_start();
define('basePath', dirname(dirname(__FILE__).'../'));

## INCLUDES ##
include(basePath."/inc/common.php");
include(basePath."/forum/helper.php");

## SETTINGS ##
$dir = "search";
$where = _forum_search_head;
$smarty = common::getSmarty(); //Use Smarty

## SECTIONS ##
$showartikel = ''; $showsites = '';
switch (common::$action):
    default:
        common::$search_forum = true;

        //check $_GET var
        $acheck2 = ''; $acheck1 = '';
        if(isset($_GET['area']) && $_GET['area'] == 'topic')
            $acheck2 = 'checked="checked"';
        else
            $acheck1 = 'checked="checked"';

        $tcheck2 = ''; $tcheck1 = '';
        if(isset($_GET['type']) && $_GET['type'] == 'autor')
            $tcheck2 = 'checked="checked"';
        else
            $tcheck1 = 'checked="checked"';

        $i=0; $strkat = ''; $getstr = '';
        foreach ($_GET as $key => $value) {
            $key = trim($key);
            $sep = (!$i ? '?' : '&');
            $getstr .= $sep.$key.'='.$value;
            if(preg_match("#k_#",$key))
                $strkat .= $key.'|';
            $i++;
        }
        if(common::permission("intforum")) {
            $qry = common::$sql['default']->select("SELECT `id`,`name`,`intern` FROM `{prefix_forum_kats}` ".
                (common::$is_addons ? " WHERE (`addons` = 1 OR `addons` = -1)" : " WHERE (`addons` != 1 OR `addons` = -1)")." ORDER BY `kid`;");
        } else {
            $qry = common::$sql['default']->select("SELECT `id`,`name`,`intern` FROM `{prefix_forum_kats}` WHERE ".
                (common::$is_addons ? "(`addons` = 1 OR `addons` = -1) AND" : " (`addons` != 1 OR `addons` = -1) AND")." `intern` = 0 ORDER BY `kid`;");
        }

        $fkats = '';
        foreach($qry as $get) {
            $fkats .= '<li><label class="searchKat" style="text-align:center">'.stringParser::decode($get['name']).'</label></li>'; $showt = "";
            $qrys = common::$sql['default']->select("SELECT `id`,`kattopic` FROM `{prefix_forum_sub_kats}` WHERE `sid` = ? ORDER BY `kattopic`;",[$get['id']]);
            foreach($qrys as $gets) {
                $intF = common::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_access}` WHERE `user` = ? AND `forum` = ?;",[common::$userid,$gets['id']]);
                if(!$get['intern'] || (($get['intern'] && $intF) || common::$chkMe == 4)) {
                    if(preg_match("#k_".$gets['id']."\|#",$strkat))
                        $kcheck = 'checked="checked"';
                    else
                        $kcheck = '';

                    $fkats .= '<li><label class="search" for="k_'.$gets['id'].'">'
                        .'<input type="checkbox" class="chksearch" name="k_'.$gets['id'].'" id="k_'.$gets['id'].'" '.$kcheck.' onclick="DZCP.hideForumFirst()" value="true" />'
                        .'&nbsp;&nbsp;'.stringParser::decode($gets['kattopic']).'</label></li>';
                }
            }
        } unset($get,$gets,$qry,$qrys,$intF,$kcheck);

        //Auswertung
        if(common::$do == 'search' && !empty($_GET['search']) && $_GET['search'] != _search_word) {
            $maxfsearch = 20;
            $_SESSION['search_con'] = $_GET['con'];
            $dosearch = '';
            if($_GET['type'] == 'autor') {
                $_SESSION['search_type'] = 'autor';
                if($_SESSION['search_con'] == 'or') {
                    $suche = explode(" ",$_GET['search']); $z=0;
                    for($x=0;$x<count($suche);$x++) {
                        $qryu = common::$sql['default']->select("SELECT `id` FROM `{prefix_users}` WHERE `nick` LIKE '%".stringParser::encode(trim($suche[$x]))."%';");
                        if(common::$sql['default']->rowCount()) {
                            foreach($qryu as $getu) {
                                $c = (!$z ? 'WHERE (' : 'OR ');
                                $dosearch .= $c."s1.`t_reg` = ".$getu['id']." OR s2.`reg` = ".$getu['id']." ";
                            }
                            $z++;
                        } //foreach
                    } //for

                    $suche = explode(" ",$_GET['search']);
                    for($x=0;$x<count($suche);$x++) {
                        $b = (!$z ? 'WHERE (' : 'OR ');
                        $dosearch .= $b."s1.`t_nick` LIKE '%".stringParser::encode(trim($suche[$x]))."%' OR s2.`nick` LIKE '%".stringParser::encode(trim($suche[$x]))."%' ";
                        $z++;
                    }
                } else {
                    $qryu = common::$sql['default']->select("SELECT `id` FROM `{prefix_users}` WHERE `nick` LIKE '%".stringParser::encode(trim($_GET['search']))."%';"); $x=0;
                    if(common::$sql['default']->rowCount()) {
                        foreach($qryu as $getu) {
                            $c = (!$x ? 'WHERE (' : 'OR ');
                            $dosearch .= $c."s1.`t_reg` = ".$getu['id']." OR s2.`reg` = ".$getu['id']." ";
                            $x++;
                        }
                    }

                    $c = (!$x ? 'WHERE (' : 'OR ');
                    $dosearch .= $c."s1.`t_nick` LIKE '%".stringParser::encode(trim($_GET['search']))."%' OR s2.`nick` LIKE '%".stringParser::encode(trim($_GET['search']))."%'";
                }

                $dosearch .= ')';
            } else {
                $_SESSION['search_type'] = 'text';
                if($_SESSION['search_con'] == 'or') {
                    $suche = explode(" ",$_GET['search']);
                    for($x=0;$x<count($suche);$x++) {
                        $c = (!$x ? 'WHERE (' : 'OR ');
                        if($_GET['area'] != 'topic') {
                            $dosearch .= $c." s1.`t_text` LIKE '%".stringParser::encode(trim($suche[$x]))."%' OR s2.`text` LIKE '%".stringParser::encode(trim($suche[$x])).
                                "%' OR s1.`topic` LIKE '%".stringParser::encode(trim($suche[$x]))."%' ";
                        } else {
                            $dosearch .= $c." s1.`topic` LIKE '%".stringParser::encode(trim($suche[$x]))."%' ";
                        }
                      }
                } else {
                    if($_GET['area'] != 'topic') {
                        $dosearch .= "WHERE (s1.`t_text` LIKE '%".stringParser::encode(trim($_GET['search']))."%' OR s2.`text` LIKE '%".stringParser::encode(trim($_GET['search'])).
                            "%' OR s1.`topic` LIKE '%" .stringParser::encode(trim($_GET['search']))."%' ";
                    } else {
                        $dosearch .= "WHERE (s1.`topic` LIKE '%".stringParser::encode(trim($_GET['search']))."%'";
                    }
                }

                $dosearch .= ')';
            } unset($c,$x,$suche,$z,$qryu,$getu);

            if(!empty($strkat)) {
                $dosearch .= ' AND (';
                $kat = explode("|",$strkat);
                for($y=0;$y<count($kat)-1;$y++) {
                    $d = (!$y ? '' : 'OR ');
                    $k = $kat[$y];
                    $k = str_replace("k_","",$k);
                    $dosearch .= $d."s3.id = ".(int)($k)." ";
                }
                $dosearch .= ')';
            } unset($strkat,$k,$y,$kat);

            //Intern
            $dosearch .= (!common::permission("intforum")) ? 'AND s4.`intern` = 0' : '';

            //Addons
            $dosearch .= (common::$is_addons ? " AND (s4.`addons` = 1 OR s4.`addons` = -1)" : " AND (s4.`addons` != 1 OR s4.`addons` = -1)");

            //SQL
            $qry = common::$sql['default']->select("SELECT s1.`id`,s1.`topic`,s1.`kid`,s1.`t_reg`,s1.`t_email`,"
                ."s1.`t_nick`,s1.`hits`,s4.`intern`,s1.`sticky`,s1.`global`,s1.`closed`,s1.`lp`,s1.`subtopic` "
                ."FROM `{prefix_forum_threads}` AS s1 "
                ."LEFT JOIN `{prefix_forum_posts}` AS s2 "
                ."ON s1.`id` = s2.`sid` "
                ."LEFT JOIN `{prefix_forum_sub_kats}` AS s3 "
                ."ON s1.`kid` = s3.`id` "
                ."LEFT JOIN `{prefix_forum_kats}` AS s4 "
                ."ON s3.`sid` = s4.`id` "
                .$dosearch." "
                ."GROUP by s1.`id` "
                ."ORDER BY s1.`lp` DESC "
                ."LIMIT ".(common::$page - 1)*$maxfsearch.",".$maxfsearch.";");

            $entrys = common::$sql['default']->rows("SELECT s1.`id` "
                ."FROM `{prefix_forum_threads}` AS s1 "
                ."LEFT JOIN `{prefix_forum_posts}` AS s2 "
                ."ON s1.`id` = s2.`sid` "
                ."LEFT JOIN `{prefix_forum_sub_kats}` AS s3 "
                ."ON s2.`kid` = s3.`id` "
                ."AND s1.`kid` = s3.`id` "
                ."LEFT JOIN `{prefix_forum_kats}` AS s4 "
                ."ON s3.`sid` = s4.`id` "
                .$dosearch." "
                ."GROUP by s1.`id`;");

            $results = '';
            foreach($qry as $get) {
                $intF = common::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_access}` WHERE `user` = ? AND `forum` = ?;",[common::$userid,$get['id']]);
                if(($get['intern'] == 1 && !$intF && common::$chkMe != 4)) $entrys--;
                if(!$get['intern'] || (($get['intern'] && $intF) || common::$chkMe == 4)) {
                    $cntpage = common::cnt('{prefix_forum_posts}', " WHERE `sid` = ?",'id',[$get['id']]);
                    $pagenr = $cntpage >= 1 ? ceil($cntpage/settings::get('m_ftopics')) : 1;
                    $getlp = common::$sql['default']->fetch("SELECT `date`,`nick`,`reg`,`email`,`sid` FROM `{prefix_forum_posts}` WHERE `sid` = ? ORDER BY `date` DESC;",[$get['id']]);
                    $lpost = "-"; $lpdate = "";
                    if(common::$sql['default']->rowCount()) {
                        //Check Unreaded
                        $iconpic = "icon_topic_latest.gif";
                        if(common::$userid >= 1 && $_SESSION['lastvisit']) {
                            //Check in Threads
                            if(common::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_threads}` "
                                . "WHERE (`t_date` >= ? || `lp` >= ?) AND `t_reg` != ? AND `id` = ?;",
                                [$_SESSION['lastvisit'],$_SESSION['lastvisit'],common::$userid,$getlp['sid']])) {
                                $iconpic = "icon_topic_newest.gif";
                            }
                        }

                        $smarty->caching = false;
                        $smarty->assign('nick',_forum_from.' '.common::autor($getlp['reg'], '',
                                stringParser::decode($getlp['nick']),
                                stringParser::decode($getlp['email']),10).' ');
                        $smarty->assign('post_link','../forum/?action=showthread&id='.$get['id']);
                        $smarty->assign('page',($pagenr >= 2 ? '&page='.$pagenr : ''));
                        $smarty->assign('post',($cntpage >= 1 ? '#p'.($cntpage+1) : ''));
                        $smarty->assign('img',$iconpic);
                        $smarty->assign('date',forum_date_tranclate($getlp['date']));
                        $lpost = $smarty->fetch('file:['.common::$tmpdir.']forum/forum_thread_lpost.tpl');
                        $smarty->clearAllAssign();
                    } unset($getlp);

                    $smarty->caching = false;
                    $smarty->assign('topic',chunk_split(stringParser::decode($get['topic']),38,"<br>"));
                    $smarty->assign('id',$get['id']);
                    $smarty->assign('sticky',$get['sticky']);
                    $smarty->assign('global',$get['global']);
                    $smarty->assign('intern',$get['intern']);
                    $smarty->assign('closed',$get['closed']);
                    $smarty->assign('lpid',$cntpage+1);
                    $smarty->assign('page',$pagenr);
                    $threadlink = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/forum_thread_search_link.tpl');
                    $smarty->clearAllAssign();

                    //save hl
                    $_SESSION['search_hl']['forumtopic'][$get['id']] = stringParser::encode($_GET['search']);

                    $smarty->caching = false;
                    $smarty->assign('new',common::check_new((int)$get['lp']));
                    $smarty->assign('topic',$threadlink);
                    $smarty->assign('subtopic',common::cut(stringParser::decode($get['subtopic']),settings::get('l_forumsubtopic'),true,false));
                    $smarty->assign('hits',$get['hits']);
                    $smarty->assign('replys',common::cnt("{prefix_forum_posts}", " WHERE `sid` = ?","id", [$get['id']]));
                    $smarty->assign('color',$color);
                    $smarty->assign('lpost',$lpost);
                    $smarty->assign('autor',common::autor($get['t_reg'], '', stringParser::decode($get['t_nick']), stringParser::decode($get['t_email']), 50));
                    $results .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/forum_search_results.tpl');
                    $smarty->clearAllAssign();
                    $color++;
                }
            } //foreach

            if(empty($results)) {
                $results = '<tr><td colspan="5" class="contentMainSecond">'._no_entrys.'</td></tr>';
            }

            $nav = common::nav($entrys,$maxfsearch,$getstr);
            $smarty->caching = false;
            $smarty->assign('nav',$nav);
            $smarty->assign('results',$results);
            $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/forum_search_show.tpl');
            $smarty->clearAllAssign();
        }

        //Diverse Abfragen
        $chk_con = ''; $all_board = '';
        if(isset($_GET['searchplugin'])) {
            $onclick = 'onclick="more(1)" style="cursor:pointer"';
            $img = '<img id="img1" src="../inc/images/expand.gif" alt="" />';
            $style = 'style="display:none"';

            if(empty($strkat))
                $all_board = 'checked="checked"';

            if($_SESSION['search_con'] == 'or')
                $chk_con = 'selected="selected"';
        } else {
            $all_board = 'checked="checked"';
            $style = '';
            $onclick = '';
            $img = '';
        }

        $smarty->caching = false;
        $smarty->assign('fkats',$fkats);
        $smarty->assign('show',$show);
        $smarty->assign('search',$_GET['search']);
        $smarty->assign('onclick',$onclick);
        $smarty->assign('img',$img);
        $smarty->assign('chkcon',$chk_con);
        $smarty->assign('style',$style);
        $smarty->assign('all_board',$all_board);
        $smarty->assign('acheck1',$acheck1);
        $smarty->assign('acheck2',$acheck2);
        $smarty->assign('tcheck1',$tcheck1);
        $smarty->assign('tcheck2',$tcheck2);
        $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/search.tpl');
        $smarty->clearAllAssign();
break;
case 'site';
    if(!empty($_GET['searchword']) && $_GET['searchword'] != _search_word) {
        //Suche in News
        $qry = common::$sql['default']->select("SELECT `id`,`titel` FROM `{prefix_news}` WHERE (`titel` LIKE '%" .
            stringParser::encode($_GET['searchword']) . "%' AND `titel` != '') OR (`text` LIKE '%" .
            stringParser::encode($_GET['searchword']) . "%' AND `text` != '') ORDER BY `titel` ASC;");
        foreach ($qry as $get) {
            $class = ($color % 2) ? "contentMainFirst" : "contentMainSecond";
            $color++;
            $smarty->caching = false;
            $smarty->assign('class', $class);
            $smarty->assign('type', 'news');
            $smarty->assign('href', '../news/index.php?action=show&amp;id=' . $get['id']);
            $smarty->assign('titel', stringParser::decode($get['titel']));
            $shownews .= $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/search_show.tpl');
            $smarty->clearAllAssign();
        }

        //Suche in Artikel
        $qry = common::$sql['default']->select("SELECT `id`,`titel` FROM `{prefix_artikel}` WHERE (`titel` LIKE '%" .
            stringParser::encode($_GET['searchword']) . "%' AND `titel` != '') OR (`text` LIKE '%" .
            stringParser::encode($_GET['searchword']) . "%' AND `text` != '') ORDER BY `titel` ASC;");
        $color = 0; $showartikel = '';
        foreach ($qry as $get) {
            $class = ($color % 2) ? "contentMainFirst" : "contentMainSecond";
            $color++;
            $smarty->caching = false;
            $smarty->assign('class', $class);
            $smarty->assign('type', 'artikel');
            $smarty->assign('href', '../news/index.php?action=show&amp;id=' . $get['id']);
            $smarty->assign('titel', stringParser::decode($get['titel']));
            $showartikel .= $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/search_show.tpl');
            $smarty->clearAllAssign();
        } unset($get, $qry);

        //Suche in Seiten
        $qry = common::$sql['default']->select("SELECT `id`,`titel` FROM `{prefix_sites}` WHERE (`titel` LIKE '%"
            .stringParser::encode($_GET['searchword']) . "%' AND `titel` != '') OR (`text` LIKE '%"
            .stringParser::encode($_GET['searchword']) . "%' AND `text` != '') ORDER BY `titel` ASC;");
        $color = 0; $showsites = '';
        foreach ($qry as $get) {
            $class = ($color % 2) ? "contentMainFirst" : "contentMainSecond";
            $color++;
            $smarty->caching = false;
            $smarty->assign('class', $class);
            $smarty->assign('type', 'site');
            $smarty->assign('href', '../sites/?show=' . $get['id']);
            $smarty->assign('titel', stringParser::decode($get['titel']));
            $showsites .= $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/search_show.tpl');
            $smarty->clearAllAssign();
        } unset($get, $qry, $color, $class);

        if (!empty($shownews))
            $shownews = '<tr><td class="contentMainTop"><b>' . _news . '</b></td></tr>' . $shownews;

        if (!empty($showartikel))
            $showartikel = '<tr><td class="contentMainTop"><b>' . _artikel . '</b></td></tr>' . $showartikel;

        if (!empty($showsites))
            $showsites = '<tr><td class="contentMainTop"><b>' . _search_sites . '</b></td></tr>' . $showsites;
    }

    if(empty($shownews) && empty($showartikel) && empty($showsites)) {
        $smarty->caching = false;
        $smarty->assign('colspan',1);
        $shownews = $smarty->fetch('string:'._no_entrys_yet);
        $smarty->clearAllAssign();
    }

    $smarty->caching = false;
    $smarty->assign('shownews',$shownews);
    $smarty->assign('showartikel',$showartikel);
    $smarty->assign('showsites',$showsites);
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/search_global.tpl');
    $smarty->clearAllAssign();
break;
endswitch;

## INDEX OUTPUT ##
$title = common::$pagetitle." - ".$where;
common::page($index, $title, $where);