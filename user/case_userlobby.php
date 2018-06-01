<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_user_lobby;
    if($chkMe) {
        $can_erase = false;

        //Get Userinfos
        $lastvisit = userstats('lastvisit');
        $lastvisit = empty($lastvisit) ? "0" : $lastvisit;

        /** Neue Foreneintraege anzeigen */
        $qrykat = db("SELECT s1.`id`,s2.`kattopic`,s1.`intern`,s2.`id` FROM `".
            $db['f_kats']."` AS `s1` LEFT JOIN `".$db['f_skats'].
            "` AS `s2` ON s1.`id` = s2.`sid` ORDER BY s1.`kid`,s2.`kattopic`;");

        $forumposts = '';
        if(_rows($qrykat) >= 1) {
            while($getkat = _fetch($qrykat)) {
                unset($nthread);
                unset($post);
                unset($forumposts_show);

                if(fintern($getkat['id'])) {
                    $qrytopic = db("SELECT `lp`,`id`,`topic`,`first`,`sticky` FROM `".$db['f_threads']."` WHERE `kid` = ".$getkat['id']." AND `lp` > ".$lastvisit." ORDER BY `lp` DESC LIMIT 150;");
                    if(_rows($qrytopic) >= 1) {
                        $forumposts_show = '';
                        while($gettopic = _fetch($qrytopic)) {
                            $lp = 0; $cnt = "";
                            $count = cnt($db['f_posts'], " WHERE `date` > ".$lastvisit." AND `sid` = ".$gettopic['id']);
                            $lp = cnt($db['f_posts'], " WHERE `sid` = ".$gettopic['id']);

                            if($count == 0) {
                                $cnt = 1;
                                $pagenr = 1;
                                $post = "";
                            } elseif($count == 1) {
                                $cnt = 1;
                                $pagenr = ceil($lp/config('m_fposts'));
                                $post = _new_post_1;
                            } else {
                                $cnt = $count;
                                $pagenr = ceil($lp/config('m_fposts'));
                                $post = _new_post_2;
                            }

                            $nthread = $gettopic['first'] == 1 ? _no_new_thread : _new_thread;

                            if(check_new($gettopic['lp'],1)) {
                                $intern = ($getkat['intern'] != 1 ? '' : '<span class="fontWichtig">'._internal.':</span>&nbsp;&nbsp;&nbsp;');
                                $wichtig = ($gettopic['sticky'] != 1 ? '' : '<span class="fontWichtig">'._sticky.':</span> ');

                                $date = (date("d.m.")==date("d.m.",$gettopic['lp']))
                                  ? '['.date("H:i",$gettopic['lp']).']'
                                  : date("d.m.",$gettopic['lp']).' ['.date("H:i",$gettopic['lp']).']';

                                $can_erase = true;
                                $forumposts_show .= '&nbsp;&nbsp;'.$date. show(_user_new_forum, array("cnt" => $cnt,
                                                                                                          "tid" => $gettopic['id'],
                                                                                                          "thread" => re($gettopic['topic']),
                                                                                                          "intern" => $intern,
                                                                                                          "wichtig" => $wichtig,
                                                                                                          "post" => $post,
                                                                                                          "page" => $pagenr,
                                                                                                          "nthread" => $nthread,
                                                                                                          "lp" => $lp +1));
                            }
                        }
                    }

                    if(!empty($forumposts_show))
                        $forumposts .= '<div style="padding: 4px 4px 4px 0;"><span class="fontBold">' .$getkat['kattopic'].'</span></div>'.$forumposts_show;
                }
            }
        }

        /** Neue Private Nachrichten anzeigen */
        $getmsg = db("SELECT `id`,`an`,`datum` FROM `".$db['msg']."` WHERE `an` = ".$userid." AND `readed` = 0 AND `see_u` = 0 ORDER BY `datum` DESC;",false,true);

        $check = cnt($db['msg'], " WHERE `an` = ".$userid." AND `readed` = 0 AND `see_u` = 0");
        if($check == 1)
            $mymsg = show(_lobby_mymessage, array("cnt" => 1));
        else if($check >= 1) {
            $mymsg = show(_lobby_mymessages, array("cnt" => $check));
        } else
            $mymsg = show(_lobby_no_mymessages, array());

        /** Neue News anzeigen */
        if($chkMe >= 2) {
            $qrynews = db("SELECT `id`,`datum` FROM `".$db['news']."` WHERE `public` = 1 AND `datum` <= ".time()." ORDER BY `id` DESC;");
        } else {
            $qrynews = db("SELECT `id`,`datum` FROM `".$db['news']."` WHERE `public` = 1 AND `intern` = 0 AND `datum` <= ".time()." ORDER BY `id` DESC;");
        }

        $news = '';
        if(_rows($qrynews) >= 1) {
            while($getnews  = _fetch($qrynews)) {
                if(check_new($getnews['datum'],1)) {
                    $check = cnt($db['news'], " WHERE `datum` > ".$lastvisit." AND `public` = 1");
                    $cnt = $check == "1" ? "1" : $check;
                    $can_erase = true;
                    $news = show(_user_new_news, array("cnt" => $cnt, "eintrag" => _lobby_new_news));
                }
            }
        }

        /** Neue News comments anzeigen */
        $qrycheckn = db("SELECT `id`,`titel` FROM `".$db['news']."` WHERE `public` = 1 AND `datum` <= ".time().";"); $newsc = '';
        if(_rows($qrycheckn) >= 1) {
            while($getcheckn = _fetch($qrycheckn)) {
                $getnewsc = db("SELECT `id`,`news`,`datum` FROM `" . $db['newscomments'] . "` WHERE `news` = " . $getcheckn['id'] . " ORDER BY `datum` DESC;");
                if (_rows($getnewsc)) {
                    $getnewsc = _fetch($getnewsc);
                    if (check_new($getnewsc['datum'], 1)) {
                        $check = cnt($db['newscomments'], " WHERE `datum` > " . $lastvisit . " AND `news` = " . $getnewsc['news']);
                        if ($check == "1") {
                            $cnt = "1";
                            $eintrag = _lobby_new_newsc_1;
                        } else {
                            $cnt = $check;
                            $eintrag = _lobby_new_newsc_2;
                        }

                        $can_erase = true;
                        $newsc .= show(_user_new_newsc, array("cnt" => $cnt,
                            "id" => $getnewsc['news'],
                            "news" => re($getcheckn['titel']),
                            "eintrag" => $eintrag));
                    }
                }
            }
        }

        /** Neue Forum Topics anzeigen */
        $qryft = db("SELECT s1.`t_text`,s1.`id`,s1.`topic`,s1.`kid`,s2.`kattopic`,s3.`intern`,s1.`sticky` FROM `".
            $db['f_threads']."` AS `s1`, `".$db['f_skats']."` AS `s2`, `".$db['f_kats'].
            "` AS `s3` WHERE s1.`kid` = s2.`id` AND s2.`sid` = s3.`id` ORDER BY s1.`lp` DESC LIMIT 10;");
        $ftopics = '';
        if(_rows($qryft) >= 1) {
            while($getft = _fetch($qryft)) {
                if(fintern($getft['kid'])) {
                    $lp = cnt($db['f_posts'], " WHERE sid = '".$getft['id']."'"); $lp++;
                    $pagenr = ceil($lp/config('m_fposts'));
                    $page = ($pagenr == 0 ? 1 : $pagenr);
                    $getp = db("SELECT `text` FROM `".$db['f_posts']."` WHERE `kid` = ".$getft['kid'].
                        " AND `sid` = ".$getft['id']." ORDER BY `date` DESC LIMIT 1;",false,true);

                    $text = strip_tags(!empty($getp) ? $getp['text'] : $getft['t_text']);
                    $intern = $getft['intern'] != 1 ? "" : '<span class="fontWichtig">'._internal.':</span>';
                    $wichtig = $getft['sticky'] != 1 ? '' : '<span class="fontWichtig">'._sticky.':</span> ';
                    $ftopics .= show($dir."/userlobby_forum", array("id" => $getft['id'],
                                                                    "pagenr" => $page,
                                                                    "p" => $lp +1,
                                                                    "intern" => $intern,
                                                                    "wichtig" => $wichtig,
                                                                    "lpost" => cut(strip_tags(re($text)), 100,true,false),
                                                                    "kat" => re($getft['kattopic']),
                                                                    "titel" => re($getft['topic']),
                                                                    "kid" => $getft['kid']));
                }
            }
        }

        // Userlevel
        $mylevel = '';
        switch (data("level")) {
            case 1: $mylevel = _status_user; break;
            case 2: $mylevel = _status_trial; break;
            case 3: $mylevel = _status_member; break;
            case 4: $mylevel = _status_admin; break;
        }

        $erase = $can_erase ? _user_new_erase : '';
        $index = show($dir."/userlobby", array("userlobbyhead" => _userlobby,
                                               "erase" => $erase,
                                               "pic" => useravatar(),
                                               "mynick" => autor($userid),
                                               "myrank" => getrank($userid),
                                               "myposts" => userstats("forumposts"),
                                               "mylogins" => userstats("logins"),
                                               "myhits" => userstats("hits"),
                                               "mymsg" => $mymsg,
                                               "mylevel" => $mylevel,
                                               "puser" => _user,
                                               "plevel" => _admin_user_level,
                                               "plogins" => _profil_logins,
                                               "phits" => _profil_pagehits,
                                               "prank" => _profil_position,
                                               "pposts" => _profil_forenposts,
                                               "board" => _forum,
                                               "threads" => _forum_thread,
                                               "nforum" => _lobby_forum,
                                               "ftopics" => $ftopics,
                                               "lastforum" => _last_forum,
                                               "forum" => $forumposts,
                                               "nnewsc" => _lobby_newsc,
                                               "newsc" => $newsc,
                                               "nmsg" => _msg,
                                               "nnews" => _lobby_news,
                                               "news" => $news,
                                               "neuerungen" => _lobby_new));
    }
    else
        $index = error(_error_have_to_be_logged, 1);
}