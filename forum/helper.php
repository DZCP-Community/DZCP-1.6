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

function fvote_question(array $answers = []) {
    $smarty = common::getSmarty(true);
    $answers_tpl = '';
    if(count($answers) >= 1) {
        $i=1;
        foreach ($answers as $answer) {
            $smarty->caching = false;
            $smarty->assign('answer_num',$i);
            $smarty->assign('answer_key',$answer['key']);
            $smarty->assign('answer',$answer['answer']);
            $answers_tpl .= $smarty->fetch('file:['.common::$tmpdir.']forum/vote/question_vote.tpl');
            $i++;
        }
    }

    $smarty->clearAllAssign();
    return $answers_tpl;
}

function fvote($id, $ajax=false) {
    $smarty = common::getSmarty(true);
    $get = common::$sql['default']->fetch("SELECT `id`,`closed`,`titel` FROM `{prefix_votes}` WHERE `id` = ? ".(common::permission("votes") ? ";" : " AND `intern` = 0;"), [(int)($id)]);
    if(common::$sql['default']->rowCount()) {
        $results = ''; $votebutton = '';
        $qryv = common::$sql['default']->select("SELECT `id`,`stimmen`,`sel` FROM `{prefix_vote_results}` WHERE `vid` = ? ORDER BY `id` ASC;", [$get['id']]);
        if(common::$sql['default']->rowCount()) {
            foreach($qryv as $getv) {
                $stimmen = common::sum('{prefix_vote_results}', " WHERE `vid` = ?", "stimmen", [$get['id']]);
                if($stimmen != 0) {
                    if(common::ipcheck("vid_".$get['id']) || cookie::get('vid_'.$get['id']) != false || $get['closed']) {
                        $percent = round($getv['stimmen']/$stimmen*100,1);
                        $rawpercent = round($getv['stimmen']/$stimmen*100,0);
                        $votebutton = "";

                        $smarty->caching = false;
                        $smarty->assign('width',$rawpercent);
                        $balken = $smarty->fetch('string:'._votes_balken);
                        $smarty->clearAllAssign();

                        $smarty->caching = false;
                        $smarty->assign('answer',stringParser::decode($getv['sel']));
                        $smarty->assign('percent',$percent);
                        $smarty->assign('stimmen',$getv['stimmen']);
                        $smarty->assign('balken',$balken);
                        $results .= $smarty->fetch('file:['.common::$tmpdir.']forum/vote_results.tpl');
                        $smarty->clearAllAssign();
                    } else {
                        $votebutton = '<input id="contentSubmitFVote" type="submit" value="'._button_value_vote.'" class="voteSubmit" />';
                        $smarty->caching = false;
                        $smarty->assign('id',$getv['id']);
                        $smarty->assign('answer',stringParser::decode($getv['sel']));
                        $results .= $smarty->fetch('file:['.common::$tmpdir.']forum/vote_vote.tpl');
                        $smarty->clearAllAssign();
                    }
                } else {
                    $votebutton = '<input id="contentSubmitFVote" type="submit" value="'._button_value_vote.'" class="voteSubmit" />';
                    $smarty->caching = false;
                    $smarty->assign('id',$getv['id']);
                    $smarty->assign('answer',stringParser::decode($getv['sel']));
                    $results .= $smarty->fetch('file:['.common::$tmpdir.']forum/vote_vote.tpl');
                    $smarty->clearAllAssign();
                }
            }
        }

        $getf = common::$sql['default']->fetch("SELECT `id`,`kid` FROM `{prefix_forum_threads}` WHERE `vote` = ?;", [$get['id']]);

        $smarty->caching = false;
        $smarty->assign('titel',stringParser::decode($get['titel']));
        $smarty->assign('vid',$get['id']);
        $smarty->assign('fid',$getf['id']);
        $smarty->assign('kid',$getf['kid']);
        $smarty->assign('results',$results);
        $smarty->assign('votebutton',$votebutton);
        $smarty->assign('stimmen',$stimmen);
        $vote = $smarty->fetch('file:['.common::$tmpdir.']forum/vote_vote.tpl');
        $smarty->clearAllAssign();
    }

    return empty($vote) ? '<div style="margin:2px 0;text-align:center;">'._no_entrys.'</div>' : ($ajax ? $vote : '<div id="navFVote">'.$vote.'</div>');
}

function send_forum_abo(bool $is_thread = false, int $id,string $eintrag,bool $edit = false) {
    global $title;
    $smarty = common::getSmarty(true);
    $checkabo = common::$sql['default']->select("SELECT s1.`user`,s1.`fid`,s2.`nick`,s2.`id`,s2.`email` FROM `{prefix_forum_abo}` AS s1 ".
        "LEFT JOIN `{prefix_users}` AS s2 ON s2.`id` = s1.`user` WHERE s1.`fid` = ?;",[$id]);

    foreach ($checkabo as $getabo) {
        if (common::$userid != $getabo['user']) {
            $gettopic = common::$sql['default']->fetch("SELECT `topic` FROM `{prefix_forum_threads}` WHERE `id` = ;",[$id]);
            $entrys = common::cnt("{prefix_forum_posts}", " WHERE `sid` = ?;",'id',[$id]);

            $smarty->caching = false;
            $smarty->assign('titel',$title);
            if($is_thread && !$edit) {
                $subj = $smarty->fetch('string:'.stringParser::decode(settings::get('eml_fabo_tedit_subj')));
            } else if(!$is_thread &&!$edit) {
                $subj = $smarty->fetch('string:'.stringParser::decode(settings::get('eml_fabo_npost_subj')));
            } else {
                $subj = $smarty->fetch('string:'.stringParser::decode(settings::get('eml_fabo_pedit_subj')));
            }
            $smarty->clearAllAssign();

            $smarty->caching = false;
            $smarty->assign('nick',stringParser::decode($getabo['nick']));
            $smarty->assign('postuser',common::fabo_autor(common::$userid));
            $smarty->assign('topic',$gettopic['topic']);
            $smarty->assign('titel',$title);
            $smarty->assign('domain',common::$httphost);
            $smarty->assign('id',$id);
            $smarty->assign('entrys',!$entrys ? 1 : $entrys);
            $smarty->assign('page',!$entrys ? 1 : ceil($entrys/settings::get('m_fposts')));
            $smarty->assign('text',BBCode::parse_html((string)$eintrag));
            $smarty->assign('clan',settings::get('clanname'));
            if($is_thread && !$edit) {
                $message = $smarty->fetch('string:'.BBCode::bbcode_email(settings::get('eml_fabo_tedit')));
            } else if(!$is_thread &&!$edit) {
                $message = $smarty->fetch('string:'.BBCode::bbcode_email(settings::get('eml_fabo_npost')));
            } else {
                $message = $smarty->fetch('string:'.BBCode::bbcode_email(settings::get('eml_fabo_pedit')));
            }
            $smarty->clearAllAssign();

            common::sendMail(stringParser::decode($getabo['email']), $subj, $message);
        }
    }
}

/**
 * Funktion um Bestimmte Textstellen zu markieren
 * @param string $text
 * @param string $word
 * @return mixed
 */
function hl(string $text,string $word) {
    if(!empty($_GET['hl']) && $_SESSION['search_type'] == 'text') {
        if($_SESSION['search_con'] == 'or') {
            $words = explode(" ",$word);
            for($x=0;$x<count($words);$x++)
                $ret['text'] = preg_replace("#".$words[$x]."#i",'<span class="fontRed" title="'.$words[$x].'">'.$words[$x].'</span>',$text);
        } else
            $ret['text'] = preg_replace("#".$word."#i",'<span class="fontRed" title="'.$word.'">'.$word.'</span>',$text);

        if(!preg_match("#<span class=\"fontRed\" title=\"(.*?)\">#", $ret['text']))
            $ret['class'] = 'class="commentsRight"';
        else
            $ret['class'] = 'class="highlightSearchTarget"';
    } else {
        $ret['text'] = $text;
        $ret['class'] = 'class="commentsRight"';
    }

    return $ret;
}

function forum_date_tranclate(int $date) {
    switch ($_SESSION['language']) {
        case 'de':

            return date("F j, Y, g:i a", $date);
        default:
            return date("F j, Y, g:i a", $date);
    }
}
