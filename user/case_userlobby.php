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

if(defined('_UserMenu')) {
    $where = _site_user_lobby;
    if (common::$chkMe) {
        $can_erase = false;
        if(isset($_POST['erase']) && (int)($_POST['erase']) == 1) {
            $_SESSION['lastvisit'] = time();
            common::$sql['default']->update("UPDATE `{prefix_user_stats}` "
                       . "SET `lastvisit` = ? "
                       . "WHERE `user` = ?;",
            [(int)($_SESSION['lastvisit']),common::$userid]);
        }

        //Get Userinfos
        $lastvisit = $_SESSION['lastvisit'];

        /** Neue Foreneintraege anzeigen */
        $qrykat = common::$sql['default']->select("SELECT s1.`id`,s2.`kattopic`,s1.`intern`,s2.`id` FROM `{prefix_forum_kats}` AS `s1` "
                             . "LEFT JOIN `{prefix_forum_sub_kats}` AS `s2` "
                             . "ON s1.`id` = s2.`sid` "
                             . "ORDER BY s1.`kid`,s2.`kattopic`;");

        $forumposts = '';
        if (common::$sql['default']->rowCount()) {
            foreach($qrykat as $getkat) {

                if(is_bool($getkat))
                    continue;

                unset($nthread,$post,$forumposts_show);
                if (common::forum_intern((int)($getkat['id']))) {
                    $qrytopic = common::$sql['default']->select("SELECT `lp`,`id`,`topic`,`first`,`sticky` "
                                           . "FROM `{prefix_forum_threads}` "
                                           . "WHERE `kid` = ? AND `lp` > ? "
                                           . "ORDER BY `lp` DESC LIMIT 150;",
                                [$getkat['id'],$lastvisit]);
                    if (common::$sql['default']->rowCount()) {
                        $forumposts_show = '';
                        foreach($qrytopic as $gettopic) {
                            $count = common::cnt('{prefix_forum_posts}', " WHERE `date` > ? AND `sid` = ?",'id', [$lastvisit,$gettopic['id']]);
                            $lp = common::cnt('{prefix_forum_posts}', " WHERE `sid` = ?",'id', [$gettopic['id']]);
                            
                            if ($count == 0) {
                                $cnt = 1;
                                $pagenr = 1;
                                $post = "";
                            } elseif ($count == 1) {
                                $cnt = 1;
                                $pagenr = ceil($lp / settings::get('m_fposts'));
                                $post = _new_post_1;
                            } else {
                                $cnt = $count;
                                $pagenr = ceil($lp / settings::get('m_fposts'));
                                $post = _new_post_2;
                            }

                            $nthread = $gettopic['first'] == 1 ? _no_new_thread : _new_thread;
                            if (common::check_new($gettopic['lp'])) {
                                $intern = ($getkat['intern'] != 1 ? '' : '<span class="fontWichtig">' . _internal . ':</span>&nbsp;&nbsp;&nbsp;');
                                $wichtig = ($gettopic['sticky'] != 1 ? '' : '<span class="fontWichtig">' . _sticky . ':</span> ');
                                $date = (date("d.m.") == date("d.m.", $gettopic['lp'])) ? '[' . date("H:i", $gettopic['lp']) . ']' : date("d.m.", $gettopic['lp']) . ' [' . date("H:i", $gettopic['lp']) . ']';
                                $can_erase = true;

                                $smarty->caching = false;
                                $smarty->assign('cnt',$cnt);
                                $smarty->assign('tid',$gettopic['id']);
                                $smarty->assign('thread',stringParser::decode($gettopic['topic']));
                                $smarty->assign('intern',$intern);
                                $smarty->assign('wichtig',$wichtig);
                                $smarty->assign('post',$post);
                                $smarty->assign('page',$pagenr);
                                $smarty->assign('nthread',$nthread);
                                $smarty->assign('lp',($lp + 1));
                                $forumposts_show .= "&nbsp;&nbsp;" . $date . $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/userlobby/userlobby_new_forum.tpl');
                                $smarty->clearAllAssign();
                            }
                        }
                    }

                    if (!empty($forumposts_show)) {
                        $forumposts .= '<div style="padding:4px;padding-left:0"><span class="fontBold">' . $getkat['kattopic'] . '</span></div>' . $forumposts_show;
                    }
                }
            }
        }

        /** Neue Registrierte User anzeigen */
        $getu = common::$sql['default']->fetch("SELECT `id`,`regdatum` "
                                 . "FROM `{prefix_users}` "
                                 . "ORDER BY `id` DESC;");
        $user = '';
        if (!empty($getu) && common::check_new($getu['regdatum'])) {
            $check = common::cnt('{prefix_users}', " WHERE `regdatum` > ?",'id', [$lastvisit]);
            if ($check == 1) {
                $cnt = 1;
                $eintrag = _new_users_1;
            } else {
                $cnt = $check;
                $eintrag = _new_users_2;
            }

            $can_erase = true;
            $smarty->caching = false;
            $smarty->assign('cnt',$cnt);
            $smarty->assign('eintrag',$eintrag);
            $user = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/userlobby/userlobby_new_users.tpl');
            $smarty->clearAllAssign();
        }

        /** Neue Private Nachrichten anzeigen */
        $getmsg = common::$sql['default']->fetch("SELECT `id`,`an`,`datum` "
                                   . "FROM `{prefix_messages}` "
                                   . "WHERE `an` = ? AND `readed` = 0 AND `see_u` = 0 "
                                   . "ORDER BY `datum` DESC;",
                  [common::$userid]);
        $check = common::cnt("{prefix_messages}", " WHERE `an` = ? AND `readed` = 0 AND `see_u` = 0",'id', [common::$userid]);
        if ($check >= 1) {
            $smarty->caching = false;
            $smarty->assign('cnt',$check);
            $mymsg = $smarty->fetch('string:'.($check == 1 ? _lobby_mymessage : _lobby_mymessages));
            $smarty->clearAllAssign();

        } else {
            $smarty->caching = false;
            $mymsg = $smarty->fetch('string:'._lobby_no_mymessages);
            $smarty->clearAllAssign();
        }

        /** Neue News anzeigen */
        $qrynews = ($qrycheckn = common::$sql['default']->select("SELECT `id`,`datum`,`titel` "
                                            . "FROM `{prefix_news}` "
                                            . "WHERE `public` = 1".(common::$chkMe >= 2 ? '' : ' AND `intern` = 0')." AND `datum` <= ".time()." "
                                            . "ORDER BY `id` DESC;"));

        $news = '';
        if (common::$sql['default']->rowCount()) {
            foreach($qrynews as $getnews) {
                if (common::check_new($getnews['datum'])) {
                    $check = common::cnt("{prefix_news}", " WHERE `datum` > ?".(common::$chkMe >= 2 ? '' : ' AND `intern` = 0')." AND `public` = 1",'id', [$lastvisit]);
                    $cnt = $check == "1" ? "1" : $check;

                    $can_erase = true;
                    $smarty->caching = false;
                    $smarty->assign('cnt',$cnt);
                    $news = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/userlobby/userlobby_new_news.tpl');
                    $smarty->clearAllAssign();
                }
            } //end foreach
        }

         /** Neue News comments anzeigen */
        $newsc = '';
        if (common::$sql['default']->rowCount()) {
            foreach($qrycheckn as $getcheckn) {
                $getnewsc = common::$sql['default']->fetch("SELECT `id`,`news`,`datum` "
                                             . "FROM `{prefix_news_comments}` "
                                             . "WHERE `news` = ? "
                                             . "ORDER BY `datum` DESC;",
                            [$getcheckn['id']]);

                if(is_bool($getnewsc))
                    continue;

                if (common::check_new(intval($getnewsc['datum']))) {
                    $check = common::cnt("{prefix_news_comments}", " WHERE `datum` > ? AND `news` = ?",'id', [$lastvisit,$getnewsc['news']]);
                    if ($check == "1") {
                        $cnt = "1";
                        $eintrag = _lobby_new_newsc_1;
                    } else if ($check >= 2) {
                        $cnt = $check;
                        $eintrag = _lobby_new_newsc_2;
                    }

                    if ($check) {
                        $can_erase = true;
                        $smarty->caching = false;
                        $smarty->assign('cnt',$cnt);
                        $smarty->assign('id',$getnewsc['news']);
                        $smarty->assign('news',stringParser::decode($getcheckn['titel']));
                        $smarty->assign('eintrag',$eintrag);
                        $newsc .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/userlobby/userlobby_new_newsc.tpl');
                        $smarty->clearAllAssign();
                    }
                }
            } //end foreach
        }

        /** Neue Votes anzeigen */
        $getnewv = common::$sql['default']->fetch("SELECT `datum` FROM `{prefix_votes}` "
                                    . "WHERE `forum` = 0 ".(common::permission("votes") ? '' : 'AND `intern` = 0 ')
                                    . "ORDER BY `datum` DESC;");
        $newv = '';
        if (!empty($getnewv) && common::check_new($getnewv['datum'])) {
            $check = common::cnt('{prefix_votes}', " WHERE `datum` > ? AND `forum` = 0",'id', [$lastvisit]);
            if ($check == "1") {
                $cnt = "1";
                $eintrag = _new_vote_1;
            } else {
                $cnt = $check;
                $eintrag = _new_vote_2;
            }

            $can_erase = true;
            $smarty->caching = false;
            $smarty->assign('cnt',$cnt);
            $smarty->assign('eintrag',$eintrag);
            $newv = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/userlobby/userlobby_new_votes.tpl');
            $smarty->clearAllAssign();
        }

        /** Kalender Events anzeigen */
        $getkal = common::$sql['default']->fetch("SELECT `id`,`datum`,`title` "
                                   . "FROM `{prefix_events}` "
                                   . "WHERE `datum` > ".time()." "
                                   . "ORDER BY `datum`;");
        $nextkal = '';
        if (!empty($getkal) && common::check_new($getkal['datum'])) {
            if (date("d.m.Y", $getkal['datum']) == date("d.m.Y", time())) {
                $smarty->caching = false;
                $smarty->assign('time',mktime(0, 0, 0, date("m", $getkal['datum']), date("d", $getkal['datum']), date("Y", $getkal['datum'])));
                $smarty->assign('event',stringParser::decode($getkal['title']));
                $nextkal = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/userlobby/userlobby_kal_today.tpl');
                $smarty->clearAllAssign();
            } else {
                $smarty->caching = false;
                $smarty->assign('time',mktime(0, 0, 0, date("m", $getkal['datum']), date("d", $getkal['datum']), date("Y", $getkal['datum'])));
                $smarty->assign('date',date("d.m.Y", $getkal['datum']));
                $smarty->assign('event',stringParser::decode($getkal['title']));
                $nextkal = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/userlobby/userlobby_kal_not_today.tpl');
                $smarty->clearAllAssign();
            }
        }

        /** Neue Artikel anzeigen */
        $qryart = common::$sql['default']->select("SELECT `id`,`datum` "
                             . "FROM `{prefix_artikel}` "
                             . "WHERE `public` = 1 "
                             . "ORDER BY `id` DESC;");
        $artikel = '';
        if (common::$sql['default']->rowCount()) {
            foreach($qryart as $getart) {
                if (common::check_new($getart['datum'])) {
                    $check = common::cnt('{prefix_artikel}', " WHERE `datum` > ? AND `public` = 1",'id', [$lastvisit]);
                    if ($check == "1") {
                        $cnt = "1";
                        $eintrag = _lobby_new_art_1;
                    } else {
                        $cnt = $check;
                        $eintrag = _lobby_new_art_2;
                    }

                    $can_erase = true;
                    $smarty->caching = false;
                    $smarty->assign('cnt',$cnt);
                    $smarty->assign('eintrag',$eintrag);
                    $artikel .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/userlobby/userlobby_new_art.tpl');
                    $smarty->clearAllAssign();
                }
            } //end foreach
        }

        /** Neue Artikel Comments anzeigen */
        $qrychecka = common::$sql['default']->select("SELECT `id` "
                                . "FROM `{prefix_artikel}` "
                                . "WHERE `public` = 1;");
        $artc = '';
        if (common::$sql['default']->rowCount()) {
            foreach($qrychecka as $getchecka) {
                $getartc = common::$sql['default']->fetch("SELECT `id`,`artikel`,`datum` "
                                            . "FROM `{prefix_artikel_comments}` "
                                            . "WHERE `artikel` = ? "
                                            . "ORDER BY `datum` DESC;",
                            [$getchecka['id']]);
                if (!empty($getartc) && common::check_new($getartc['datum'])) {
                    $check = common::cnt('{prefix_artikel_comments}', " WHERE `datum` > ? AND `artikel` = ?",'id', [$lastvisit,$getartc['artikel']]);
                    if ($check == "1") {
                        $cnt = "1";
                        $eintrag = _lobby_new_artc_1;
                    } else {
                        $cnt = $check;
                        $eintrag = _lobby_new_artc_2;
                    }

                    $smarty->caching = false;
                    $smarty->assign('cnt',$cnt);
                    $smarty->assign('id',$getartc['artikel']);
                    $smarty->assign('eintrag',$eintrag);
                    $artc .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/userlobby/userlobby_new_artc.tpl');
                    $smarty->clearAllAssign();
                }
            } //end foreach
        }

        /** Neue Forum Topics anzeigen */
        $qryft = common::$sql['default']->select("SELECT s1.`t_text`,s1.`id`,s1.`topic`,s1.`kid`,s2.`kattopic`,s3.`intern`,s1.`sticky` "
                            . "FROM `{prefix_forum_threads}` as `s1`, `{prefix_forum_sub_kats}` as `s2`, `{prefix_forum_kats}` as `s3` "
                            . "WHERE s1.`kid` = s2.`id` AND s2.`sid` = s3.`id` "
                            . "ORDER BY s1.`lp` DESC LIMIT 10;");
        $ftopics = '';
        if (common::$sql['default']->rowCount()) {
            foreach($qryft as $getft) {
                if (common::forum_intern($getft['kid'])) {
                    $lp = common::cnt('{prefix_forum_posts}', " WHERE `sid` = ?",'id', [$getft['id']]);
                    $pagenr = ceil($lp / settings::get('m_fposts'));
                    $page = (!$pagenr ? 1 : $pagenr);
                    $getp = common::$sql['default']->fetch("SELECT `text` "
                                             . "FROM `{prefix_forum_posts}` "
                                             . "WHERE `kid` = ? AND `sid` = ? "
                                             . "ORDER BY `date` DESC LIMIT 1;",
                            [$getft['kid'],$getft['id']]);

                    $text = strip_tags(!empty($getp) ? stringParser::decode($getp['text']) : stringParser::decode($getft['t_text']));
                    $intern = $getft['intern'] != 1 ? "" : '<span class="fontWichtig">' . _internal . ':</span>';
                    $wichtig = $getft['sticky'] != 1 ? '' : '<span class="fontWichtig">' . _sticky . ':</span> ';

                    $smarty->caching = false;
                    $smarty->assign('id',$getft['id']);
                    $smarty->assign('pagenr',$page);
                    $smarty->assign('p',($lp + 1));
                    $smarty->assign('intern',$intern);
                    $smarty->assign('wichtig',$wichtig);
                    $smarty->assign('lpost',common::cut($text, 100));
                    $smarty->assign('kat',stringParser::decode($getft['kattopic']));
                    $smarty->assign('titel',stringParser::decode($getft['topic']));
                    $smarty->assign('kid',$getft['kid']);
                    $ftopics .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/userlobby/userlobby_forum.tpl');
                    $smarty->clearAllAssign();
                }
            } //end foreach
        }

        // Userlevel
        switch (common::data("level")) {
            case 2:
                $mylevel = _status_user;
                break;
            case 2:
                $mylevel = _status_trial;
                break;
            case 3:
                $mylevel = _status_member;
                break;
            case 4:
                $mylevel = _status_admin;
                break;
            default:
                $mylevel = _status_gast;
                break;
        }

        if (empty($ftopics)) {
            $ftopics = '<tr><td colspan="2" class="contentMainSecond">' . _no_entrys . '</td></tr>';
        }

        $erase = ($can_erase ? _user_new_erase : '');
        
        $smarty->caching = false;
        $smarty->assign('erase',$erase);
        $smarty->assign('myposts',common::userstats("forumposts"));
        $smarty->assign('mylogins',common::userstats("logins"));
        $smarty->assign('myhits',common::userstats("hits"));
        $smarty->assign('mymsg',$mymsg);
        $smarty->assign('mylevel',$mylevel);
        $smarty->assign('kal',$nextkal);
        $smarty->assign('art',$artikel);
        $smarty->assign('artc',$artc);
        $smarty->assign('ftopics',$ftopics);
        $smarty->assign('forum',$forumposts);
        $smarty->assign('votes',$newv);
        $smarty->assign('newsc',$newsc);
        $smarty->assign('user',$user);
        $smarty->assign('news',$news);
        $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/userlobby/userlobby.tpl');
        $smarty->clearAllAssign();
    } else {
        $index = common::error(_error_have_to_be_logged, 1);
    }
}