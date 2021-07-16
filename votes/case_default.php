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
    $whereIntern = ' AND `intern` = 0';
    if(common::permission('votes')) {
        $whereIntern = '';
    }

    $fvote = '';
    if(!settings::get('forum_vote'))
        $fvote = empty($whereIntern) ? ' AND `forum` = 0' : ' AND `forum` = 0';

    $qry = common::$sql['default']->select('SELECT votes.*,sum(votes_result.`stimmen`) as `ges_stimmen` FROM `{prefix_votes}` as votes, `{prefix_vote_results}` as `votes_result`'
            . ' WHERE votes.`id` = votes_result.`vid` '.$whereIntern.$fvote.''
            . ' GROUP by votes.`id` '.common::orderby_sql(['titel','datum','von','ges_stimmen'], 'ORDER BY `datum`;'));
    foreach($qry as $get) {
        $qryv = common::$sql['default']->select('SELECT * FROM `{prefix_vote_results}` '
                           . 'WHERE `vid` = '.$get['id'].' ORDER BY `id`;');

        $check = ''; $ipcheck = false; $intern = '';
        $stimmen = $get['ges_stimmen'];
        $vid = 'vid_'.$get['id'];
        if($get['intern']) {
            $showVoted = '';
            $intern = _votes_intern;
        }

        $results = ''; $color2 = 0;
        $ipcheck = !common::count_clicks('vote',$get['id'],false);
        foreach($qryv as $getv) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            if($ipcheck || cookie::get('vid_'.$get['id']) != false || $get['closed']) {
                $percent = $getv['stimmen'] >= 1 ? round($getv['stimmen']/$stimmen*100,2) : 0;
                $rawpercent = $getv['stimmen'] >= 1 ?round($getv['stimmen']/$stimmen*100,0) : 0;
                $smarty->caching = false;
                $smarty->assign('width',$rawpercent);
                $balken = $smarty->fetch('string:'._votes_balken);
                $smarty->clearAllAssign();
                $result_head = _votes_results_head;
                $votebutton = "";
                $smarty->caching = false;
                $smarty->assign('answer',stringParser::decode($getv['sel']));
                $smarty->assign('percent',$percent);
                $smarty->assign('class',$class);
                $smarty->assign('stimmen',$getv['stimmen']);
                $smarty->assign('balken',$balken);
                $results .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/votes_results.tpl');
                $smarty->clearAllAssign();
            } else {
                $result_head = _votes_results_head_vote;
                $votebutton = '<input id="voteSubmit_'.$get['id'].'" type="submit" value="'._button_value_vote.'" class="submit" />';
                $smarty->caching = false;
                $smarty->assign('id',$getv['id']);
                $smarty->assign('answer',stringParser::decode($getv['sel']));
                $smarty->assign('class',$class);
                $results .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/votes_vote.tpl');
                $smarty->clearAllAssign();
            }
        }

        $showVoted = '';
        if($get['intern'] && $stimmen != 0 && ($get['von'] == common::$userid || common::permission('votes'))) {
            $showVoted = ' <a href="?action=showvote&amp;id='.$get['id'].'"><img src="../inc/images/lupe.gif" alt="" title="'.
            _show_who_voted.'" class="icon" /></a>';
        }

        if(isset($_GET['show']) && $_GET['show'] == $get['id']) {
            $moreicon = "collapse";
            $display = "";
        } else {
            $moreicon = "expand";
            $display = "none";
        }

        $ftitel = $get['forum'] ? stringParser::decode($get['titel']).' (Forum)' : stringParser::decode($get['titel']);
        $smarty->caching = false;
        $smarty->assign('titel',$ftitel);
        $smarty->assign('vid',$get['id']);
        $smarty->assign('icon',$moreicon);
        $smarty->assign('intern',$intern);
        $titel = $smarty->fetch('string:'._votes_titel);
        $smarty->clearAllAssign();

        $closed = $get['closed'] ? _closedicon_votes : '';
        $class = ($color2 % 2) ? "contentMainSecond" : "contentMainFirst"; $color2++;
        $smarty->caching = false;
        $smarty->assign('datum',date("d.m.Y", $get['datum']));
        $smarty->assign('titel',$titel);
        $smarty->assign('vid',$get['id']);
        $smarty->assign('display',$display);
        /** @var TYPE_NAME $result_head */
        $smarty->assign('result_head',$result_head);
        $smarty->assign('results',$results);
        $smarty->assign('show',$showVoted);
        $smarty->assign('closed',$closed);
        $smarty->assign('autor', common::autor($get['von']));
        $smarty->assign('class',$class);
        /** @var TYPE_NAME $votebutton */
        $smarty->assign('votebutton',$votebutton);
        $smarty->assign('stimmen',$stimmen);
        $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/votes_show.tpl');
        $smarty->clearAllAssign();
    }

    if(empty($show)) {
        $smarty->caching = false;
        $smarty->assign('colspan',4);
        $show = $smarty->fetch('string:'._no_entrys_yet);
        $smarty->clearAllAssign();
    }

    $smarty->caching = false;
    $smarty->assign('show',$show);
    $smarty->assign('order_titel',common::orderby('titel'));
    $smarty->assign('order_autor',common::orderby('von'));
    $smarty->assign('order_datum', common::orderby('datum'));
    $smarty->assign('order_stimmen',common::orderby('ges_stimmen'));
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/votes.tpl');
    $smarty->clearAllAssign();
}