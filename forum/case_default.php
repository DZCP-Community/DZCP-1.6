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

if(defined('_Forum')) {
    //Addons
    $addons_query = isset($_GET['mid']) && (int)$_GET['mid'] >= 1 ? " WHERE `mid` = ".(int)($_GET['mid'])." AND " : (!common::permission('forum') ? " WHERE" : "");

    if(!common::permission('forum')) {
        $addons_query .= (common::$is_addons ? " (`addons` = 1 OR `addons` = -1)" : " (`addons` != 1 OR `addons` = -1)");
    }

    $qry = common::$sql['default']->select("SELECT * FROM `{prefix_forum_kats}`".$addons_query." ORDER BY `kid` ASC;");
    $_SESSION['kid'] = 0;
    foreach($qry as $get) {
        $showt = "";
        $qrys = common::$sql['default']->select("SELECT * FROM `{prefix_forum_sub_kats}` WHERE `sid` = ? ORDER BY pos;", [$get['id']]);
        foreach($qrys as $gets) {
            if($get['intern'] == 0 || ($get['intern'] == 1 && common::forum_intern($gets['id']))) {
                $lpost = "";
                $getlp = common::$sql['default']->fetch("SELECT s1.`kid`,s1.`id`,s1.`date`,s1.`nick`,s1.`reg`,s1.`email`,s2.`kid`,s2.`id`,s2.`t_date`,s2.`lp`,s2.`first` "
                                . "FROM `{prefix_forum_posts}` AS `s1` "
                                . "LEFT JOIN `{prefix_forum_threads}` AS `s2` "
                                . "ON s2.`lp` = s1.`date` "
                                . "WHERE s2.`kid` = ? "
                                . "ORDER BY s1.`date` DESC;", [$gets['id']]);

                $lpost = "-"; $lpdate = 0;
                if(common::cnt('{prefix_forum_threads}', " WHERE `kid` = ?","id", [$gets['id']])) {
                    $getlt = common::$sql['default']->fetch("SELECT `id`,`kid`,`t_date`,`t_nick`,`t_email`,`t_reg`,`lp`,`first`,`topic` "
                        . "FROM `{prefix_forum_threads}` "
                        . "WHERE `kid` = ? "
                        . "ORDER BY `lp` DESC;",
                        [$gets['id']]);

                   $lpost = "";
                   if($getlt['first'] == 1) { //Only Thread
                        //Check Unreaded
                        $iconpic = "icon_topic_latest.gif";
                        if(common::$userid >= 1 && $_SESSION['lastvisit']) {
                            //Check in Threads
                            if(common::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_threads}` "
                                    . "WHERE (`t_date` >= ? || `lp` >= ?) AND `t_reg` != ? AND `id` = ?;",
                                    [$_SESSION['lastvisit'],$_SESSION['lastvisit'],common::$userid,$getlt['id']])) {
                                $iconpic = "icon_topic_newest.gif";
                            }
                        }

                        $smarty->caching = false;
                        $smarty->assign('nick',_forum_from.' '.common::autor($getlt['t_reg'], '',
                                stringParser::decode($getlt['t_nick']),
                                stringParser::decode($getlt['t_email']),10).' ');
                        $smarty->assign('post_link','?action=showthread&kid='.$getlt['kid'].'&id='.$getlt['id']);
                        $smarty->assign('page','');
                        $smarty->assign('post','');
                        $smarty->assign('img',$iconpic);
                        $smarty->assign('date',forum_date_tranclate($getlt['t_date']));
                        $lpost .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/forum_thread_lpost.tpl');
                        $smarty->clearAllAssign();

                        $lpdate = (int)($getlt['t_date']);
                   } elseif(!$getlt['first']) { //With Posts
                        //Check Unreaded
                        $iconpic = "icon_topic_latest.gif";
                        if(common::$userid >= 1 && $_SESSION['lastvisit']) {
                            //Check in Posts
                            if(common::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_posts}` "
                                    . "WHERE `date` >= ? AND `reg` != ? AND `id` = ?;",
                                    [$_SESSION['lastvisit'],common::$userid,$getlp['id']])) {
                                $iconpic = "icon_topic_newest.gif";
                            }
                        }

                       $cntpage = common::cnt("{prefix_forum_posts}", " WHERE `sid` = ?","id",[$getlt['id']]);
                       $pagenr = !$cntpage ? '1' : ceil($cntpage/settings::get('m_fposts'));

                       $smarty->caching = false;
                       $smarty->assign('nick',_forum_from.' '.common::autor((int)$getlp['reg'], '',
                               stringParser::decode($getlp['nick']),
                               stringParser::decode($getlp['email']),10).' ');
                       $smarty->assign('post_link','?action=showthread&kid='.$getlt['kid'].'&id='.$getlt['id']);
                       $smarty->assign('img',$iconpic);
                       $smarty->assign('page',($pagenr >= 2 ? '&page='.$pagenr : ''));
                       $smarty->assign('post',($cntpage >= 1 ? '#p'.($cntpage+1) : ''));
                       $smarty->assign('date',forum_date_tranclate($getlp['date']));
                       $lpost .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/forum_thread_lpost.tpl');
                       $smarty->clearAllAssign();

                       $lpdate = (int)($getlp['date']);
                   }
                }
                
                //Unreaded
                $frompic = "read";
                if(common::$userid >= 1 && $_SESSION['lastvisit']) {
                    //Check new Threads
                    if(common::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_threads}` "
                            . "WHERE (`t_date` >= ? || `lp` >= ?) AND `t_reg` != ? AND `kid` = ?;",
                            [$_SESSION['lastvisit'],$_SESSION['lastvisit'],common::$userid,$gets['id']])) {
                        $frompic = "unread";
                    }
                    
                    //Check new Posts
                    if(common::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_posts}` "
                            . "WHERE `date` >= ? AND `reg` != ? AND `kid` = ?;",
                            [$_SESSION['lastvisit'],common::$userid,$gets['id']])) {
                        $frompic = "unread";
                    }
                }

                $threads = common::cnt('{prefix_forum_threads}', " WHERE `kid` = ?","id", [$gets['id']]);
                $posts = common::cnt('{prefix_forum_posts}', " WHERE `kid` = ?","id", [$gets['id']]);

                //Show
                $smarty->caching = false;
                $smarty->assign('topic',chunk_split(stringParser::decode($gets['kattopic']),50,"<br>"));
                $smarty->assign('subtopic',stringParser::decode($gets['subtopic']));
                $smarty->assign('lpost',$lpost);
                $smarty->assign('frompic',$frompic);
                $smarty->assign('lang',common::rawflag($gets['flag']));
                $smarty->assign('subforum',"");
                $smarty->assign('threads',$threads);
                $smarty->assign('posts',$posts+$threads);
                $smarty->assign('kid',$gets['id']);
                $showt .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/kats_show.tpl');
                $smarty->clearAllAssign();
            }
        } //end while

        $katname = stringParser::decode($get['name']);
        if($get['intern']) {
            $smarty->caching = false;
            $smarty->assign('katname',stringParser::decode($get['name']));
            $katname = $smarty->fetch('string:'._forum_katname_intern);
            $smarty->clearAllAssign();
        }

        if(!empty($showt)) {
            $smarty->caching = false;
            $smarty->assign('katname',$katname);
            $smarty->assign('showt',$showt);
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/kats.tpl');
            $smarty->clearAllAssign();
        }
    }
    
    /* Stats */
    $qrytp = common::$sql['default']->select("SELECT `id`,`user`,`forumposts` FROM `{prefix_user_stats}` ORDER BY `forumposts` DESC LIMIT 5;");
    $show_top = '';
    foreach($qrytp as $gettp) {
        if($gettp['forumposts'] >= 1) {
            $smarty->caching = false;
            $smarty->assign('nick',common::autor($gettp['user']));
            $smarty->assign('posts',$gettp['forumposts']);
            $smarty->assign('color',$color);
            $show_top .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/top_posts_show.tpl');
            $smarty->clearAllAssign(); $color++;
        }
    } //end while
    $color = 0;

    //Top Posters
    $smarty->caching = false;
    $smarty->assign('show',$show_top);
    $top_posts = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/top_posts.tpl');
    $smarty->clearAllAssign(); unset($show_top);

    //Stats
    $smarty->caching = false;
    $smarty->assign('total_posts',common::cnt("{prefix_forum_posts}"));
    $smarty->assign('total_topics',common::cnt("{prefix_forum_threads}"));
    $smarty->assign('total_members',common::cnt("{prefix_users}","WHERE `banned` = 0 AND `level` >= 1"));
    $smarty->assign('top_posts',$top_posts);
    $smarty->assign('newest_member',common::autor(common::$sql['default']->fetch("SELECT `id` FROM `{prefix_users}` WHERE `level` >= 1 AND `banned` = 0 ORDER BY `regdatum` DESC;",[],"id")));
    $stats = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/forum_stats.tpl');
    $smarty->clearAllAssign();

    $smarty->caching = false;
    $smarty->assign('threads',common::cnt("{prefix_forum_threads}"));
    $threads = $smarty->fetch('string:'._forum_cnt_threads);
    $smarty->clearAllAssign();

    $smarty->caching = false;
    $smarty->assign('posts',(common::cnt("{prefix_forum_posts}")+common::cnt("{prefix_forum_threads}")));
    $posts = $smarty->fetch('string:'._forum_cnt_posts);
    $smarty->clearAllAssign();

    /* Wer ist online */
    $qry = common::$sql['default']->select('SELECT `position`,`color` FROM `{prefix_positions}`;'); $team_groups = '';
    foreach($qry as $get) {
        $smarty->caching = false;
        $smarty->assign('color',stringParser::decode($get['color']));
        $smarty->assign('group',stringParser::decode($get['position']));
        $team_groups .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/forum_team_groups.tpl');
        $smarty->clearAllAssign();
    }

    common::update_online($where); //Update Where
    $qryo = common::$sql['default']->select("SELECT `id` FROM `{prefix_users}` WHERE `whereami` LIKE ? AND (time+1800) > ".time().";", ["%".$where."%"]);
       if(common::$sql['default']->rowCount()) {
            $i=0; $check = 1; $nick = '';
            $cnto = common::cnt('{prefix_users}', " WHERE (time+1800) > ".time()." AND `whereami` LIKE ?;",'id', ["%".$where."%"]);
            foreach($qryo as $geto) {
                if($i == 5) {
                    $end = "<br />";
                    $i=0;
                }  else  {
                    $end = ($cnto == $check ? "" : ", ");
                }

                $nick .= common::autorcolerd($geto['id']).$end;
                $i++; $check++;
        } //end while
    } else {
        $nick = _forum_nobody_is_online;
    }
        
    $counter_users = common::online_reg($where,true);
    $counter_gast = common::online_guests($where,true);
    
    $total_users=($counter_users+$counter_gast);
    $smarty->caching = false;
    $smarty->assign('users',$total_users);
    $smarty->assign('counter_gast',$counter_gast);
    $smarty->assign('regs',$counter_users);
    $smarty->assign('counter_users',$counter_users);
    $smarty->assign('gast',$counter_gast);
    $smarty->assign('total_users',$total_users);
    $smarty->assign('timer',(1800/60/60));
    $forum_user_stats = $smarty->fetch('string:'._forum_online_info0);
    $smarty->clearAllAssign();

    $smarty->caching = false;
    $smarty->assign('nick',$nick);
    $smarty->assign('forum_online_info0',$forum_user_stats);
    $smarty->assign('groups',$team_groups);
    $online = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/online.tpl');
    $smarty->clearAllAssign();

    /* Index */
    $smarty->caching = false;
    $smarty->assign('threads',$threads);
    $smarty->assign('stats',$stats);
    $smarty->assign('posts',$posts);
    $smarty->assign('show',$show);
    $smarty->assign('online',$online);
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/forum.tpl');
    $smarty->clearAllAssign();
}