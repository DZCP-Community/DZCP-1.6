<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(defined('_Forum')) {
    $checks = db("SELECT s3.name,s3.intern,s2.sid,s1.kid,s2.id FROM ".
        $db['f_kats']." s3, ".$db['f_skats']." s2, ".$db['f_threads'].
        " s1 WHERE s1.kid = s2.id AND s2.sid = s3.id AND s1.id = '".
        (int)($_GET['id'])."'",false,true);

    $f_check = db("SELECT * FROM `".$db['f_threads']."`  WHERE `id` = ".(int)($_GET['id'])." AND `kid` = ".$checks['kid'].";");
    if(_rows($f_check)) {
        if($checks['intern'] == 1 && !permission("intforum") && !fintern($checks['id'])) {
            $index = error(_error_wrong_permissions, 1);
        } else {
            db("UPDATE `".$db['f_threads']."` SET `hits` = (hits+1) WHERE `id` = ".(int)($_GET['id']).";");

            $entrys = cnt($db['f_posts'], " WHERE `sid` = ".(int)($_GET['id']));
            $pagenr = "1";
            if($entrys >= 1)
                $pagenr = ceil($entrys/config('m_fposts'));

            $hL = '';
            if(!empty($_GET['hl']))
                $hL = '&amp;hl='.$_GET['hl'];

            $lpost = show(_forum_lastpost, array("id" => $entrys+1, "tid" => $_GET['id'], "page" => $pagenr.$hL));

            //Posts
            $qryp = db("SELECT * FROM `".$db['f_posts']."` WHERE `sid` = ".(int)($_GET['id'])." ORDER BY `id` LIMIT ".($page - 1)*config('m_fposts').",".config('m_fposts').";");
            $i = 2;
            while($getp = _fetch($qryp)) {
                $sig = "";
                if(data("signatur",$getp['reg']))
                    $sig = _sig.bbcode(data("signatur",$getp['reg']));

                $userposts = "";
                if($getp['reg'])
                    $userposts = show(_forum_user_posts, array("posts" => userstats("forumposts",$getp['reg'])));

                $onoff = "";
                if($getp['reg'])
                    $onoff = onlinecheck($getp['reg']);

                $zitat = show("page/button_zitat", array("id" => $_GET['id'],
                    "action" => "action=post&amp;do=add&amp;kid=".$getp['kid']."&amp;zitat=".$getp['id'],
                    "title" => _button_title_zitat));

                $delete = ""; $edit = "";
                if($chkMe >= 1 && $getp['reg'] == $userid || permission("forum")) {
                    $edit = show("page/button_edit_single", array("id" => $getp['id'],
                        "action" => "action=post&amp;do=edit",
                        "title" => _button_title_edit));

                    $delete = show("page/button_delete_single", array("id" => $getp['id'],
                        "action" => "action=post&amp;do=delete",
                        "title" => _button_title_del,
                        "del" => convSpace(_confirm_del_entry)));
                }

                $ftxt = hl($getp['text'], isset($_GET['hl']) ? $_GET['hl'] : '');
                if(isset($_GET['hl']))
                    $text = bbcode($ftxt['text']);
                else
                    $text = bbcode($getp['text']);

                if($chkMe == 4)
                    $posted_ip = $getp['ip'];
                else
                    $posted_ip = _logged;

                $titel = show(_eintrag_titel_forum, array("postid" => $i+($page-1)*config('m_fposts'),
                    "datum" => date("d.m.Y", $getp['date']),
                    "zeit" => date("H:i", $getp['date'])._uhr,
                    "url" => '?action=showthread&amp;id='.(int)($_GET['id']).'&amp;page='.$page.'#p'.($i+($page-1)*config('m_fposts')),
                    "edit" => $edit,
                    "delete" => $delete));

                $hp = ""; $icq = ""; $pn = ""; $email = "";
                if($getp['reg']) {
                    $getu = db("SELECT `nick`,`icq`,`hp`,`email` FROM `".$db['users']."` WHERE `id` = ".$getp['reg'].";",false,true);

                    $email = show(_emailicon_forum, array("email" => eMailAddr(re($getu['email']))));
                    $pn = show(_pn_write_forum, array("id" => $getp['reg'], "nick" => $getu['nick']));

                    if(!empty($getu['icq']) || !$getu['icq']) {
                        $uin = show(_icqstatus_forum, array("uin" => $getu['icq']));
                        $icq = '<a href="http://www.icq.com/whitepages/about_me.php?uin='.$getu['icq'].'" target="_blank">'.$uin.'</a>';
                    }

                    if(!empty($getu['hp']))
                        $hp = show(_hpicon_forum, array("hp" => links(re($getu['hp']))));
                } else {
                    $email = show(_emailicon_forum, array("email" => eMailAddr(re($getp['email']))));

                    if(!empty($getp['hp']))
                        $hp = show(_hpicon_forum, array("hp" => links(re($getp['hp']))));
                }

                $nick = autor($getp['reg'], '', re($getp['nick']), re($getp['email']));
                if(!empty($_GET['hl']) && $_SESSION['search_type'] == 'autor') {
                    if(preg_match("#".$_GET['hl']."#i",$nick))
                        $ftxt['class'] = 'class="highlightSearchTarget"';
                }

                $email = ($chkMe >= 1 ? $email : '');
                if($getp['reg'] && data('dsgvo_lock',$getp['reg'])) {
                    $text = _dsgvo_locked_text;
                    $getp['edited'] = '';
                    $zitat = '';
                }

                $show .= show($dir."/forum_posts_show", array("nick" => $nick,
                    "postnr" => "#".($i+($page-1)*config('m_fposts')),
                    "p" => ($i+($page-1)*config('m_fposts')),
                    "text" => $text,
                    "pn" => $pn,
                    "class" => $ftxt['class'],
                    "icq" => $icq,
                    "hp" => $hp,
                    "email" => $email,
                    "status" => getrank($getp['reg']),
                    "avatar" => useravatar($getp['reg']),
                    "ip" => $posted_ip,
                    "edited" => $getp['edited'],
                    "posts" => $userposts,
                    "titel" => $titel,
                    "signatur" => $sig,
                    "zitat" => $zitat,
                    "onoff" => $onoff,
                    "top" => _topicon,
                    "lp" => cnt($db['f_posts'], " WHERE `sid` = ".(int)($_GET['id']))+1));
                $i++;
            }

            $get = db("SELECT * FROM `".$db['f_threads']."` WHERE `id` = ".(int)($_GET['id']).";",false,true);

            $getw = db("SELECT s1.`kid`,s1.`topic`,s2.`kattopic`,s2.`sid` ".
                "FROM `".$db['f_threads']."` AS `s1` ".
                "LEFT JOIN `".$db['f_skats']."` AS `s2` ON s1.`kid` = s2.`id`  ".
                "WHERE s1.`id` = ".(int)($_GET['id']).";",false,true);

            $kat = db("SELECT `name` FROM `".$db['f_kats']."` WHERE `id` = ".$getw['sid'].";",false,true);

            $wheres = show(_forum_post_where, array("wherepost" => re($getw['topic']),
                "wherekat" => re($getw['kattopic']),
                "mainkat" => re($kat['name']),
                "tid" => $_GET['id'],
                "kid" => $getw['kid']));

            $userposts = ""; $onoff = "";
            if($get['t_reg']) {
                $onoff = onlinecheck($get['t_reg']);
                $userposts = show(_forum_user_posts, array("posts" => userstats("forumposts",$get['t_reg'])));
            }

            $zitat = show("page/button_zitat", array("id" => $_GET['id'],
                "action" => "action=post&amp;do=add&amp;kid=".$getw['kid']."&amp;zitatt=".$get['id'],
                "title" => _button_title_zitat));

            $add = '';
            if($get['closed']) {
                $add = show("page/button_closed", array());
            } else if(HasDSGVO()) {
                $add = show(_forum_addpost, array("id" => $_GET['id'], "kid" => $getw['kid']));
            }

            $nav = nav($entrys,config('m_fposts'),"?action=showthread&amp;id=".$_GET['id'].$hL);

            $sig = "";
            if(data("signatur",$get['t_reg']))
                $sig = _sig.bbcode(data("signatur",$get['t_reg']));

            $admin = ''; $editt = ''; $deletet = '';
            if(permission("forum")) {
                $editt = show("page/button_edit_single", array("id" => $get['id'],
                    "action" => "action=thread&amp;do=edit",
                    "title" => _button_title_edit));

                $sticky = $get['sticky'] ? 'checked="checked"' : "";
                $global = $get['global'] ? 'checked="checked"' : "";

                if($get['closed'])
                {
                    $closed = 'checked="checked"';
                    $opened = "";
                } else {
                    $opened = 'checked="checked"';
                    $closed = "";
                }

                $qryok = db("SELECT * FROM `".$db['f_kats']."` ORDER BY `kid`;"); $move = '';
                while($getok = _fetch($qryok)) {
                    $skat = "";
                    $qryo = db("SELECT * FROM ".$db['f_skats']." WHERE sid = '".$getok['id']."' ORDER BY kattopic;");
                    while($geto = _fetch($qryo))
                    {
                        $skat .= show(_forum_select_field_skat, array("value" => $geto['id'],
                            "what" => re($geto['kattopic'])));
                    }

                    $move .= show(_forum_select_field_kat, array("value" => "lazy",
                        "what" => re($getok['name']),
                        "skat" => $skat));
                }

                $admin = show($dir."/admin", array("admin" => _admin,
                    "id" => $get['id'],
                    "open" => _forum_admin_open,
                    "close" => _forum_admin_close,
                    "asticky" => _forum_admin_addsticky,
                    "delete" => _forum_admin_delete,
                    "moveto" => _forum_admin_moveto,
                    "aglobal" => _forum_admin_global,
                    "move" => $move,
                    "closed" => $closed,
                    "opened" => $opened,
                    "global" => $global,
                    "sticky" => $sticky));
            } else {
                //User Admin
                if($get['t_reg'] == $userid) {
                    $editt = show("page/button_edit_single", array("id" => $get['id'], "action" => "action=thread&amp;do=edit", "title" => _button_title_edit));
                    $deletet = show("page/button_delete_single", array("id" => $get['id'], "action" => "action=thread&amp;do=anonym", "title" => _button_title_del, "del" => convSpace(_confirm_del_entry)));
                }
            }

            $hl = isset($_GET['hl']) ? $_GET['hl'] : '';
            $ftxt = hl($get['t_text'], $hl);

            if(isset($_GET['hl']))
                $text = bbcode($ftxt['text']);
            else
                $text = bbcode($get['t_text']);

            if($chkMe == "4")
                $posted_ip = $get['ip'];
            else
                $posted_ip = _logged;

            $titel = show(_eintrag_titel_forum, array("postid" => "1",
                "datum" => date("d.m.Y", $get['t_date']),
                "zeit" => date("H:i", $get['t_date'])._uhr,
                "url" => '?action=showthread&amp;id='.(int)($_GET['id']).'&amp;page=1#p1',
                "edit" => $editt,
                "delete" => $deletet));

            $icq = ""; $hp = ""; $pn = "";
            if($get['t_reg'] != 0) {
                $getu = db("SELECT `nick`,`icq`,`hp`,`email` FROM `".$db['users']."` WHERE `id` = ".$get['t_reg'].";",false,true);
                $email = show(_emailicon_forum, array("email" => eMailAddr(re($getu['email']))));
                $pn = show(_pn_write_forum, array("id" => $get['t_reg'], "nick" => $getu['nick']));
                if(!empty($getu['icq']) || $getu['icq'] >= 1) {
                    $uin = show(_icqstatus_forum, array("uin" => $getu['icq']));
                    $icq = '<a href="http://www.icq.com/whitepages/about_me.php?uin='.$getu['icq'].'" target="_blank">'.$uin.'</a>';
                }

                if(!empty($getu['hp']))
                    $hp = show(_hpicon_forum, array("hp" => links(re($getu['hp']))));
            } else {
                if(!$get['dsgvo']) {
                    $email = show(_emailicon_forum, array("email" => eMailAddr(re($get['t_email']))));
                    if(!empty($get['t_hp']))
                        $hp = show(_hpicon_forum, array("hp" => links(re($get['t_hp']))));
                } else {
                    $text = _dsgvo_deleted_text;
                    $email = ''; $zitat = '';
                }
            }

            $nick = autor($get['t_reg'], '', re($get['t_nick']), re($get['t_email']));
            if(!empty($_GET['hl']) && $_SESSION['search_type'] == 'autor') {
                if(preg_match("#".$_GET['hl']."#i",$nick))
                    $ftxt['class'] = 'class="highlightSearchTarget"';
            }

            $abo = db("SELECT `id` FROM `".$db['f_abo']."` WHERE `user` = '".$userid."' AND fid = '".(int)($_GET['id'])."'",true) ? 'checked="checked"' : '';
            if(!$chkMe) {
                $f_abo = '';
            } else {
                $f_abo = show($dir."/forum_abo", array("id" => (int)($_GET['id']),
                    "abo" => $abo,
                    "abo_info" => _foum_fabo_checkbox,
                    "abo_title" => _forum_abo_title,
                    "submit" => _button_value_save));
            }

            $vote = "";
            if(!empty($get['vote'])) {
                include_once(basePath.'/inc/menu-functions/fvote.php');
                $vote = '<tr><td>'.fvote($get['vote']).'</td></tr>';
            }

            if($get['t_reg'] && data('dsgvo_lock',$get['t_reg'])) {
                $text = _dsgvo_locked_text;
                $get['edited'] = '';
                $email = '';
                $add = '';
            }

            $title = re($getw['topic']).' - '.$title;
            $email = ($chkMe >= 1 ? $email : '');
            $index = show($dir."/forum_posts", array(
                "head" => _forum_head,
                "where" => $wheres,
                "admin" => $admin,
                "nick" => $nick,
                "threadhead" => re($getw['topic']),
                "titel" => $titel,
                "postnr" => "1",
                "class" => $ftxt['class'],
                "pn" => $pn,
                "icq" => $icq,
                "hp" => $hp,
                "email" => $email,
                "posts" => $userposts,
                "text" => $text,
                "status" => getrank($get['t_reg']),
                "avatar" => useravatar($get['t_reg']),
                "edited" => $get['edited'],
                "signatur" => $sig,
                "date" => _posted_by.date("d.m.y H:i", $get['t_date'])._uhr,
                "zitat" => $zitat,
                "onoff" => $onoff,
                "ip" => $posted_ip,
                "top" => _topicon,
                "lpost" => $lpost,
                "lp" => cnt($db['f_posts'], " WHERE sid = '".(int)($_GET['id'])."'")+1,
                "add" => $add,
                "nav" => $nav,
                "vote" => $vote,
                "f_abo" => $f_abo,
                "show" => $show));
        }
    } else {
        $index = error(_error_wrong_permissions, 1);
    }
}