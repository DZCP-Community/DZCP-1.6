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

if(defined('_Votes')) {
    $get = common::$sql['default']->fetch("SELECT `id`,`intern`,`closed` FROM `{prefix_votes}` WHERE `id` = ?;", [(int)($_GET['id'])]);
    if(!$get['intern'] || ($get['intern'] && common::$chkMe)) {
        $qryv = common::$sql['default']->select("SELECT `user_id`,`time`,`created` FROM `{prefix_ip_action}` WHERE `what` = 'vid_".$get['id']."' ORDER BY `time` DESC;");
        if(common::$chkMe == 4 || $get['closed'] || common::permission('votesadmin') || common::$sql['default']->rows("SELECT `id` FROM `{prefix_ip_action}` "
                . "WHERE `user_id` = ? AND `what` = ?;", [common::$userid,'vid_'.$get['id']])) {
            foreach ($qryv as $getv) {
                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                $smarty->caching = false;
                $smarty->assign('user',$getv['user_id'] ? common::autor($getv['user_id']) : _gast);
                $smarty->assign('date', date("d.m.y H:i",$getv['created'])._uhr);
                $smarty->assign('class',$class);
                $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/voted_show.tpl');
                $smarty->clearAllAssign();
            }
        }

        if(empty($show)) {
            $smarty->caching = false;
            $smarty->assign('colspan',2);
            $show = $smarty->fetch('string:'._no_entrys_yet);
            $smarty->clearAllAssign();
        }

        $smarty->caching = false;
        $smarty->assign('show',$show);
        $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/voted.tpl');
        $smarty->clearAllAssign();
    } else
        $index = common::error(_error_vote_show,1);
}