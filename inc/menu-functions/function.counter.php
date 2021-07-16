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

function smarty_function_counter($params,Smarty_Internal_Template &$smarty) {
    global $where;
    $params['js'] = !array_key_exists('js',$params) ? true : $params['js'];
    if($params['js']) {
        $counter = '<div style="width:100%;padding:10px 0;text-align:center"><img src="../inc/images/ajax_loading.gif" alt="" /></div>'.
            "<script language=\"javascript\" type=\"text/javascript\">DZCP.initDynLoader('navCounter','counter','',true);</script>";
    } else {
        if(!common::$CrawlerDetect->isCrawler()) {
            $get2day = common::$sql['default']->fetch("SELECT `visitors` FROM `{prefix_counter}` WHERE `today` = ?;", [date("j.n.Y")]);
            if(common::$sql['default']->rowCount()) {
                $v_today = $get2day['visitors'];
            }

            $gestern = time() - 86400;
            $tag   = date("j", $gestern);
            $monat = date("n", $gestern);
            $jahr  = date("Y", $gestern);
            $yesterday = $tag.".".$monat.".".$jahr;

            $yDay = 0;
            $getyday = common::$sql['default']->fetch("SELECT `visitors` FROM `{prefix_counter}` WHERE `today` = ?;", [$yesterday]);
            if(common::$sql['default']->rowCount()) {
                $yDay = $getyday['visitors'];
            }

            $getstats = common::$sql['default']->fetch("SELECT SUM(visitors) AS `allvisitors`, "
                . "MAX(visitors) AS `maxvisitors`, "
                . "MAX(maxonline) AS `maxonline`, "
                . "AVG(visitors) AS `avgvisitors`, "
                . "SUM(visitors) AS `allvisitors` "
                . "FROM `{prefix_counter}`;");

            $info = '';
            if(($online_reg = common::online_reg()) != 0) {
                $qryo = common::$sql['default']->select("SELECT `id` FROM `{prefix_users}` WHERE (time+1800) > ? AND `online` = 1 ORDER BY `nick`;", [time()]);
                $kats = ''; $text = '';
                if(common::$sql['default']->rowCount()) {
                    foreach($qryo as $geto) {
                        $kats .= common::fabo_autor($geto['id']).';';
                        $text .= common::jsconvert(common::getrank($geto['id'])).';';
                    }
                }

                $info = '';
                if(!common::$mobile->isMobile() || common::$mobile->isTablet()) {
                    $info = 'onmouseover="DZCP.showInfo(\'' . _online_head . '\', \'' . $kats . '\', \'' . $text . '\')" onmouseout="DZCP.hideInfo()"';
                }
            }

            if(empty($where)) {
                $where = '';
            }
            $smarty->caching = false;
            $smarty->assign('v_today',$v_today);
            $smarty->assign('v_yesterday',$yDay);
            $smarty->assign('v_all',$getstats['allvisitors']);
            $smarty->assign('v_perday',round($getstats['avgvisitors'], 2));
            $smarty->assign('v_max',$getstats['maxvisitors']);
            $smarty->assign('g_online',common::online_guests($where));
            $smarty->assign('u_online',common::online_guests($where));
            $smarty->assign('v_online',$getstats['maxonline']);
            $smarty->assign('info',$info);
            $counter = $smarty->fetch('file:['.common::$tmpdir.']menu/counter/counter.tpl');
        }
    }

    return '<div id="navCounter">'.$counter.'</div>';
}