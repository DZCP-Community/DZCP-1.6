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
    $checks = common::$sql['default']->fetch("SELECT s3.`name`,s3.`intern`,s2.`sid`,s1.`kid`,s2.`id` FROM ".
        "`{prefix_forum_kats}` AS s3, `{prefix_forum_sub_kats}` AS s2, `{prefix_forum_threads}` AS s1 WHERE ".
        "s1.`kid` = s2.`id` AND s2.`sid` = s3.`id` AND s1.`id` = ?;",[(int)($_GET['id'])]);

    //Fix correct kat_ID
    if (!array_key_exists('kid', $_SESSION) || !$_SESSION['kid'] || $_SESSION['kid'] != $checks['kid']) {
        $_SESSION['kid'] = $checks['kid'] ? $checks['kid'] : 0;
    }

    if(common::$sql['default']->rows("SELECT * FROM `{prefix_forum_threads}` WHERE `id` = ? AND `kid` = ?;",[(int)($_GET['id']),$checks['kid']])) {
        if($checks['intern'] == 1 && !common::permission("intforum") && !common::forum_intern($checks['id'])) {
            $index = common::error(_error_wrong_permissions, 1);
        } else {
            //Update Hits
            if(!common::$CrawlerDetect->isCrawler()) {
                common::$sql['default']->update("UPDATE `{prefix_forum_threads}` SET `hits` = (hits+1) WHERE `id` = ?;", [(int)($_GET['id'])]);
            }

            //Fix last post time update
            $qryp = common::$sql['default']->select("SELECT `date` FROM `{prefix_forum_posts}` WHERE sid = ?;",[(int)($_GET['id'])]);
            $update_lp_time = 0; //Last Post Time
            foreach($qryp as $getp) {
                if(!$update_lp_time || $update_lp_time < $getp['date']) {
                    $update_lp_time = $getp['date'];
                }
            } unset($qryp,$getp);

            $qryp = common::$sql['default']->select("SELECT * FROM `{prefix_forum_posts}` WHERE sid = ? ".
                "ORDER BY id LIMIT ".(common::$page - 1)*($m_fposts=settings::get('m_fposts')).",".$m_fposts.";",
                [(int)($_GET['id'])]);

            //Button "Zum letzten Eintrag"
            $entrys = common::cnt("{prefix_forum_posts}", " WHERE `sid` = ?",'id',[(int)($_GET['id'])]);
            $pagenr = (!$entrys ? "1" : ceil($entrys/settings::get('m_fposts')));
            $hL = (!empty($_GET['hl']) ? '&amp;hl='.$_GET['hl'] : '');

            $smarty->caching = false;
            $smarty->assign('id',$entrys+1);
            $smarty->assign('tid',$_GET['id']);
            $smarty->assign('page',$pagenr.$hL);
            $lpost = $smarty->fetch('string:'._forum_lastpost);
            $smarty->clearAllAssign();

            /*
             * ###################################
             * Posts
             * ###################################
             */
            $i = 2; //Begin Post ID 2 (1 is thread)
            foreach($qryp as $getp) {
                //User Signatur ausgeben
                $signatur = common::data("signatur",$getp['reg']); $sig = '';
                if(!empty($signatur)) {
                    $sig = sprintf('%s%s',_sig,BBCode::parse_html((string)$signatur));
                } unset($signatur);

                //User Posts ( Uber Avatar )
                $userposts = '';
                if($getp['reg']) {
                    $smarty->caching = false;
                    $smarty->assign('posts',common::userstats("forumposts",$getp['reg']));
                    $userposts = $smarty->fetch('string:'._forum_user_posts);
                    $smarty->clearAllAssign();
                }

                //User Online check
                $onoff = ($getp['reg'] ? common::onlinecheck($getp['reg']) : '');

                //Button Zitat
                $zitat = common::a_img_link("?action=post&amp;do=add&amp;kid=".$_SESSION['kid']."&amp;zitat_post=".$getp['id'].
                    "&amp;id=".$_GET['id'],'quote', _button_title_zitat,'_self');

                //Delete & Edit Button
                $delete = ""; $edit = "";
                if($getp['reg'] == common::$userid || common::permission("forum")) {
                    $edit = common::getButtonEditSingle($getp['id'],"action=post&amp;do=edit");
                    $delete = common::button_delete_single($getp['id'],"action=post&amp;do=delete",_button_title_del,_confirm_del_entry);
                }

                $ftxt = hl($getp['text'], (isset($_GET['hl']) ? $_GET['hl'] : ''));
                $text = isset($_GET['hl']) ? BBCode::parse_html((string)$ftxt['text']) : BBCode::parse_html((string)$getp['text']);

                //Titel
                $smarty->caching = false;
                $smarty->assign('postid', $i+(common::$page-1)*settings::get('m_fposts'));
                $smarty->assign('datum',date("d.m.Y", $getp['date']));
                $smarty->assign('zeit',date("H:i", $getp['date']));
                $smarty->assign('url','?action=showthread&amp;id='.(int)($_GET['id']).'&amp;page='.common::$page.'#p'.($i+(common::$page-1)*settings::get('m_fposts')));
                $smarty->assign('edit',$edit);
                $smarty->assign('delete',$delete);
                $titel = $smarty->fetch('string:'._eintrag_titel_forum);
                $smarty->clearAllAssign();

                $pn = ""; $hp = "";
                if($getp['reg'] != 0) {
                    $getu = common::$sql['default']->fetch("SELECT `nick`,`hp`,`email` FROM `{prefix_users}` WHERE `id` = '".$getp['reg']."';");

                    //PM
                    $smarty->caching = false;
                    $smarty->assign('nick',stringParser::decode($getu['nick']));
                    $pn_name = $smarty->fetch('string:'._pn_write_forum);
                    $smarty->clearAllAssign();
                    $pn = common::a_img_link('../user/?action=msg&amp;do=pn&amp;id='.$getp['reg'],'pn', $pn_name);
                    unset($pn_name);

                    //-> Homepage Link
                    if (!empty($getu['hp'])) {
                        $hp = common::a_img_link(common::links(stringParser::decode($getu['hp'])),'hp', common::links(stringParser::decode($getu['hp'])));
                    }

                } else {
                    //-> Homepage Link
                    if (!empty($getp['hp'])) {
                        $hp = common::a_img_link(common::links(stringParser::decode($getp['hp'])),'hp', common::links(stringParser::decode($getp['hp'])));
                    }
                }

                $nick = common::autor($getp['reg'], '', $getp['nick'], stringParser::decode($getp['email']));
                if(!empty($_GET['hl']) && $_SESSION['search_type'] == 'autor') {
                    if(preg_match("#".$_GET['hl']."#i",$nick))
                        $ftxt['class'] = 'class="highlightSearchTarget"';
                }

                $smarty->caching = false;
                $smarty->assign('nick',$nick);
                $smarty->assign('chkme',common::$chkMe);
                $smarty->assign('postnr',"#".($i+(common::$page-1)*settings::get('m_fposts')));
                $smarty->assign('p',($i+(common::$page-1)*settings::get('m_fposts')));
                $smarty->assign('text',$text);
                $smarty->assign('class',stringParser::decode($ftxt['class']));
                $smarty->assign('pn',$pn);
                $smarty->assign('hp',$hp);
                $smarty->assign('status',common::getrank($getp['reg']));
                $smarty->assign('avatar',common::useravatar($getp['reg']));
                $smarty->assign('ip',common::getPostedIP($getp));
                $smarty->assign('edited',stringParser::decode($getp['edited']));
                $smarty->assign('posts',$userposts);
                $smarty->assign('titel',$titel);
                $smarty->assign('signatur',$sig);
                $smarty->assign('zitat',$zitat);
                $smarty->assign('permission',common::permission("forum"));
                $smarty->assign('closed',common::$sql['default']->fetch("SELECT `closed` FROM `{prefix_forum_threads}` WHERE `id` = ?;",[$getp['sid']],'closed'));
                $smarty->assign('onoff',$onoff);
                $smarty->assign('lp',common::cnt("{prefix_forum_posts}", " WHERE `sid` = ?;","id", [(int)($_GET['id'])])+1);
                $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/forum_posts_show.tpl');
                $smarty->clearAllAssign();
                $i++;
            }

            /*
             * ###################################
             * Thread
             * ###################################
             */
            $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_forum_threads}` WHERE `id` = ?;",[(int)($_GET['id'])]);
            $getw = common::$sql['default']->fetch("SELECT s1.`kid`,s1.`topic`,s2.`kattopic`,s2.`sid` FROM ".
                "`{prefix_forum_threads}` AS s1 LEFT JOIN `{prefix_forum_sub_kats}` AS s2 ON s1.`kid` = s2.`id` WHERE s1.`id` = ?;",
                [(int)($_GET['id'])]);

            $kat = common::$sql['default']->fetch("SELECT `name` FROM `{prefix_forum_kats}` WHERE `id` = ?;",[$getw['sid']]);

            //Fix LastPost Time Bug
            if(!$update_lp_time) {
                $update_lp_time = $get['t_date'];
            }

            if($get['lp'] >= $update_lp_time) {
                common::$sql['default']->update("UPDATE `{prefix_forum_threads}` SET `lp` = ? WHERE `id` = ?;",
                    [$update_lp_time,$get['id']]);
                $get['lp'] = $update_lp_time;
            } unset($update_lp_time);

            //Breadcrumbs
            $smarty->caching = false;
            $smarty->assign('wherepost',stringParser::decode($getw['topic']));
            $smarty->assign('wherekat',stringParser::decode($getw['kattopic']));
            $smarty->assign('mainkat',stringParser::decode($kat['name']));
            $smarty->assign('tid',$_GET['id']);
            $smarty->assign('kid',$getw['kid']);
            $wheres = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/forum_subkat_where.tpl');
            $smarty->clearAllAssign();

            $userposts = ''; $onoff = '';
            if($get['t_reg']) {
                $onoff = common::onlinecheck($get['t_reg']);
                $smarty->caching = false;
                $smarty->assign('posts',common::userstats("forumposts",$get['t_reg']));
                $userposts = $smarty->fetch('string:'._forum_user_posts);
                $smarty->clearAllAssign();
            }

            //Button Zitat
            $zitat = common::a_img_link("?action=post&amp;do=add&amp;kid=".$_SESSION['kid']."&amp;zitat_thread=".$get['id'].
                "&amp;id=".$_GET['id'],'quote', _button_title_zitat,'_self');

            $nav = common::nav($entrys,settings::get('m_fposts'),"?action=showthread&amp;id=".$_GET['id'].$hL);
            $sig = ($signatur=common::data("signatur",$get['t_reg'])) ? _sig.BBCode::parse_html((string)$signatur) : '';
            $edit = $get['t_reg'] == common::$userid || common::permission("forum") ? common::getButtonEditSingle($get['id'],"action=thread&amp;do=edit") : '';

            //Admin
            $admin = '';
            if(common::permission("forum")) {
                $qryok = common::$sql['default']->select("SELECT * FROM `{prefix_forum_kats}` ORDER BY `kid`;"); $move = '';
                foreach($qryok as $getok) {
                    $skat = ""; $c = 0;
                    $qryo = common::$sql['default']->select("SELECT * FROM `{prefix_forum_sub_kats}` WHERE `sid` = ? ORDER BY `kattopic`;",[$getok['id']]);
                    foreach($qryo as $geto) {
                        $skat .= common::select_field($geto['id'],false,'-> '.stringParser::decode($geto['kattopic']));
                        $c++;
                    }

                    if($c) {
                        $move .= common::select_field("lazy", false, stringParser::decode($getok['name']), 'dropdownKat') . $skat;
                    }
                }

                $smarty->caching = false;
                $smarty->assign('id',$get['id']);
                $smarty->assign('move',$move);
                $smarty->assign('closed',$get['closed'] ? 'checked="checked"' : '');
                $smarty->assign('opened',!$get['closed'] ? 'checked="checked"' : '');
                $smarty->assign('global',$get['global'] ? 'checked="checked"' : "");
                $smarty->assign('sticky',$get['sticky'] ? 'checked="checked"' : "");
                $admin = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/admin/showthread_admin.tpl');
                $smarty->clearAllAssign();
                unset($move,$skat,$c,$qryo,$geto,$qryok,$getok);
            }

            //Zitat
            $hl = isset($_GET['hl']) ? $_GET['hl'] : '';
            $ftxt = hl($get['t_text'], $hl);
            if(isset($_GET['hl']))
                $text = stringParser::decode($ftxt['text']);
            else
                $text = BBCode::parse_html((string)$get['t_text']);

            //Titel
            $smarty->caching = false;
            $smarty->assign('postid',1);
            $smarty->assign('datum',date("d.m.Y", $get['t_date']));
            $smarty->assign('zeit',date("H:i", $get['t_date']));
            $smarty->assign('url','?action=showthread&amp;id='.(int)($_GET['id']).'&amp;page=1#p1');
            $smarty->assign('edit',$edit);
            $smarty->assign('delete','');
            $titel = $smarty->fetch('string:'._eintrag_titel_forum);
            $smarty->clearAllAssign();

            $pn = ""; $hp = "";
            if($get['t_reg']) {
                $getu = common::$sql['default']->fetch("SELECT `nick`,`hp`,`email` FROM `{prefix_users}` WHERE `id` = ?;",[$get['t_reg']]);

                //PM
                $smarty->caching = false;
                $smarty->assign('nick',stringParser::decode($getu['nick']));
                $pn_name = $smarty->fetch('string:'._pn_write_forum);
                $smarty->clearAllAssign();
                $pn = common::a_img_link('../user/?action=msg&amp;do=pn&amp;id='.$get['t_reg'],'pn', $pn_name);
                unset($pn_name);

                //-> Homepage Link
                if (!empty($getu['hp'])) {
                    $hp = common::a_img_link(common::links(stringParser::decode($getu['hp'])),'hp', common::links(stringParser::decode($getu['hp'])));
                }
            } else {
                //-> Homepage Link
                if (!empty($get['t_hp'])) {
                    $hp = common::a_img_link(common::links(stringParser::decode($get['t_hp'])),'hp', common::links(stringParser::decode($get['t_hp'])));
                }
            }

            $nick = common::autor($get['t_reg'], '', $get['t_nick'], $get['t_email']);
            if(!empty($_GET['hl']) && $_SESSION['search_type'] == 'autor') {
                if(preg_match("#".$_GET['hl']."#i",$nick))
                    $ftxt['class'] = 'class="highlightSearchTarget"';
            }

            //Forum ABO
            $f_abo = '';
            if(common::$chkMe) {
                $abo = common::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_abo}` WHERE `user` = ? AND `fid` = ?;",
                    [common::$userid,(int)($_GET['id'])]) ? 'checked="checked"' : '';
                $smarty->caching = false;
                $smarty->assign('id',(int)($_GET['id']));
                $smarty->assign('abo',$abo);
                $f_abo = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/forum_abo.tpl');
                $smarty->clearAllAssign();
            }

            $where = $where.' - '.stringParser::decode($getw['topic']);
            $smarty->caching = false;
            $smarty->assign('where',$wheres);
            $smarty->assign('chkme',common::$chkMe);
            $smarty->assign('admin',$admin);
            $smarty->assign('nick',$nick);
            $smarty->assign('threadhead',stringParser::decode($getw['topic']));
            $smarty->assign('titel',$titel);
            $smarty->assign('postnr',1);
            $smarty->assign('class',stringParser::decode($ftxt['class']));
            $smarty->assign('pn',$pn);
            $smarty->assign('hp',$hp);
            $smarty->assign('posts',$userposts);
            $smarty->assign('text',$text);
            $smarty->assign('status',common::getrank($get['t_reg']));
            $smarty->assign('avatar',common::useravatar($get['t_reg']));
            $smarty->assign('edited',stringParser::decode($get['edited']));
            $smarty->assign('editedby','');
            $smarty->assign('signatur',$sig);
            $smarty->assign('zitat',$zitat);
            $smarty->assign('onoff',$onoff);
            $smarty->assign('ip',common::getPostedIP($get));
            $smarty->assign('id',$_GET['id']);
            $smarty->assign('lpost',$lpost);
            $smarty->assign('lp',common::cnt("{prefix_forum_posts}", " WHERE `sid` = ?","id",[(int)($_GET['id'])])+1);
            $smarty->assign('closed',$get['closed']);
            $smarty->assign('permission',common::permission("forum"));
            $smarty->assign('nav',$nav);
            $smarty->assign('is_vote',!empty($get['vote']));
            $smarty->assign('vote',fvote($get['vote']));
            $smarty->assign('f_abo',$f_abo);
            $smarty->assign('show',$show);
            $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/forum_posts.tpl');
            $smarty->clearAllAssign();
        }
    } else {
        $index = common::error(_error_wrong_permissions, 1);
    }
}