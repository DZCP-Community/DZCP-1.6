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

function smarty_function_vote($params,Smarty_Internal_Template &$smarty) {
    $params['js'] = !array_key_exists('js',$params) ? false : true;
    $get = common::$sql['default']->fetch("SELECT `id`,`closed`,`titel`,`intern` FROM `{prefix_votes}` WHERE `menu` = 1 AND `forum` = 0;"); $vote = '';
    if(common::$sql['default']->rowCount()) {
        if(!$get['intern'] || common::$chkMe >= 1) {
            $qryv = common::$sql['default']->select("SELECT `id`,`stimmen`,`sel` FROM `{prefix_vote_results}` WHERE `vid` = ? ORDER BY `what`;", [$get['id']]);
            $results = '';
            foreach($qryv as $getv) {
                $ipcheck = !common::count_clicks('vote',$get['id'],false);
                $stimmen = common::sum("{prefix_vote_results}", " WHERE `vid` = '".$get['id']."'", "stimmen", [$get['id']]);
                if($stimmen != 0) {
                    if($ipcheck || cookie::get('vid_'.$get['id']) != false || $get['closed']) {
                        $percent = round($getv['stimmen']/$stimmen*100,1);
                        $rawpercent = round($getv['stimmen']/$stimmen*100,0);
                        $votebutton = "";

                        $smarty->caching = false;
                        $smarty->assign('width',$rawpercent);
                        $balken = $smarty->fetch('string:'._votes_balken);

                        $smarty->caching = false;
                        $smarty->assign('answer',stringParser::decode($getv['sel']));
                        $smarty->assign('percent',$percent);
                        $smarty->assign('stimmen',$getv['stimmen']);
                        $smarty->assign('balken',$balken);
                        $results .= $smarty->fetch('file:['.common::$tmpdir.']menu/vote/vote_results.tpl');
                    } else {
                        $votebutton = '<input id="contentSubmitVote" type="submit" value="'._button_value_vote.'" class="voteSubmit" />';

                        $smarty->caching = false;
                        $smarty->assign('id',$getv['id']);
                        $smarty->assign('answer',stringParser::decode($getv['sel']));
                        $results .= $smarty->fetch('file:['.common::$tmpdir.']menu/vote/vote_vote.tpl');
                    }
                } else {
                    $votebutton = '<input id="contentSubmitVote" type="submit" value="'._button_value_vote.'" class="voteSubmit" />';

                    $smarty->caching = false;
                    $smarty->assign('id',$getv['id']);
                    $smarty->assign('answer',stringParser::decode($getv['sel']));
                    $results .= $smarty->fetch('file:['.common::$tmpdir.']menu/vote/vote_vote.tpl');
                }
            }

            $smarty->caching = false;
            $smarty->assign('titel',stringParser::decode($get['titel']));
            $smarty->assign('vid',$get['id']);
            $smarty->assign('results',$results);
            $smarty->assign('votebutton',$votebutton);
            $smarty->assign('stimmen',$stimmen);
            $vote = $smarty->fetch('file:['.common::$tmpdir.']menu/vote/vote.tpl');
        }
    }

    return empty($vote) ? '<div style="margin:2px 0;text-align:center;">'._vote_menu_no_vote.'</div>' : ($params['js'] ? $vote : '<div id="navVote">'.$vote.'</div>');
}