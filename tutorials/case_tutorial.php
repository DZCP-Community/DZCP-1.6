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

if (!defined('_Tutorials')) exit();

$index = common::error(_error_wrong_permissions);

$tutorial_id = (int)($_GET['id']);
$get = common::$sql['default']->fetch("SELECT s1.*,s2.`level` FROM `{prefix_tutorials}` AS s1 LEFT JOIN `{prefix_tutorials_kats}` AS s2 ".
    "ON s1.`kat` = s2.`id` WHERE s2.`level` <= ? AND s1.`id` = ?;", [common::$chkMe,$tutorial_id]);
if(common::$sql['default']->rowCount()) {
    /*
    switch (common::$do) {
        case 'add':
            if (common::$sql['default']->rows("SELECT `id` FROM `{prefix_tutorials}` WHERE `id` = ?;", [$tutorial_id]) != 0) {
                if (settings::get("reg_tutorial") && !common::$chkMe) {
                    $index = common::error(_error_have_to_be_logged, 1);
                } else {
                    if (!common::ipcheck("tutid(" . $tutorial_id . ")", settings::get('f_tutorialscom'))) {
                        if (empty($_POST['comment'])) {
                            if(notification::has()) {
                                javascript::set('AnchorMove', 'tutorial');
                            }
                            if (empty($_POST['eintrag'])) {
                                notification::add_error(_empty_eintrag,'tutorial');
                            }
                        } else {
                            common::$sql['default']->insert("INSERT INTO `{prefix_tutorials_comments}` SET `tutorial` = ?,`datum` = ?,`nick` = ?,`email` = ?,`hp` = ?,`reg` = ?,`comment` = ?, `ipv4` = ?, `ipv6` = ?;",
                                [$tutorial_id,time(),(isset($_POST['nick']) && !common::$userid ? stringParser::encode($_POST['nick']) : common::data('nick')),(isset($_POST['email']) && !common::$userid ? stringParser::encode($_POST['email']) : common::data('email')),
                                    (isset($_POST['hp']) && !common::$userid ? stringParser::encode(common::links($_POST['hp'])) : stringParser::encode(common::links(stringParser::decode(common::data('hp'))))),
                                    (int)(common::$userid),stringParser::encode($_POST['comment']),common::$userip['v4'],common::$userip['v6']]);
                            common::setIpcheck("tutid(" . $tutorial_id . ")");
                            if(notification::has()) {
                                javascript::set('AnchorMove', 'notification-box');
                            }
                            $_POST = []; //Clear Post
                            notification::add_success(_comment_added);
                        }
                    } else {
                        $smarty->caching = false;
                        $smarty->assign('sek',settings::get('f_newscom'));
                        $error = $smarty->fetch('string:'._error_flood_post);
                        $smarty->clearAllAssign();
                        notification::add_error($error);
                        unset($error);
                    }
                }
            } else {
                notification::add_error(_id_dont_exist);
            }
            break;
        case 'delete':
            if(notification::has()) {
                javascript::set('AnchorMove', 'notification-box');
            }
            $reg = common::$sql['default']->fetch("SELECT `reg` FROM `{prefix_tutorials_comments}` WHERE `id` = ?;", [($cid = (int)($_GET['cid']))],'reg');
            if ($reg == common::$userid || common::permission('tutorials')) {
                common::$sql['default']->delete("DELETE FROM `{prefix_tutorials_comments}` WHERE `id` = ?;", [$cid]);
                notification::add_success(_comment_deleted);
            } else {
                notification::add_error(_error_wrong_permissions);
            }
            break;
        case 'editcom':
            if(notification::has()) {
                javascript::set('AnchorMove', 'notification-box');
            }
            $reg = common::$sql['default']->fetch("SELECT `reg` FROM `{prefix_tutorials_comments}` WHERE `id` = ?;", [($cid = (int)($_GET['cid']))],'reg');
            if (common::$sql['default']->rowCount() && !empty($_POST['comment'])) {
                if ($reg == common::$userid || common::permission('tutorials')) {
                    //-> Editby Text
                    $smarty->caching = false;
                    $smarty->assign('autor',common::autor(common::$userid));
                    $smarty->assign('time',date("d.m.Y H:i", time()));
                    $editedby = $smarty->fetch('string:'._edited_by);
                    $smarty->clearAllAssign();

                    common::$sql['default']->update("UPDATE `{prefix_tutorials_comments}` SET `nick` = ?, `email` = ?, `hp` = ?, `comment` = ?, `editby` = ? WHERE `id` = ?;", [(isset($_POST['nick']) ? stringParser::encode($_POST['nick']) : ''),
                        (isset($_POST['email']) ? stringParser::encode($_POST['email']) : ''),
                        (isset($_POST['hp']) ? stringParser::encode(common::links($_POST['hp'])) : ''),
                        (isset($_POST['comment']) ? stringParser::encode($_POST['comment']) : ''),
                        stringParser::encode($editedby),$cid]);

                    $_POST = []; //Clear Post
                    notification::add_success(_comment_edited);
                } else {
                    notification::add_error(_error_edit_post,'tutorial');
                }
            } else {
                notification::add_error(_empty_eintrag,'tutorial');
            }
            break;
        case 'edit':
            $get = common::$sql['default']->fetch("SELECT `id`,`reg`,`comment` FROM `{prefix_tutorials_comments}` WHERE `id` = ?;", [(int)($_GET['cid'])]);
            if (common::$userid >= 1 && ($get['reg'] == common::$userid || common::permission('tutorials'))) {
                if(notification::has()) {
                    javascript::set('AnchorMove', 'comForm');
                }

                $smarty->caching = false;
                $smarty->assign('nick',common::autor($get['reg']));
                $smarty->assign('action','?action=tutorial&amp;do=editcom&amp;id=' . $tutorial_id .'&amp;cid=' . (int)($_GET['cid']));
                $smarty->assign('prevurl','../tutorials/?action=compreview&do=edit&id=' . $tutorial_id .'&cid=' . (int)($_GET['cid']));
                $smarty->assign('id',$get['id']);
                $smarty->assign('posteintrag',stringParser::decode($get['comment']));
                $smarty->assign('notification',notification::get('global',true));
                $add = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/comments/comments_edit.tpl');
                $smarty->clearAllAssign();
            } else {
                if(notification::has()) {
                    javascript::set('AnchorMove', 'notification-box');
                }
                notification::add_error(_error_edit_post,'tutorial');
            }
            break;
    }
    */

    /************************
     * View Tutorials
     ************************/

    //Update viewed
    if (common::count_clicks('tutorials', $get['id'])) {
        common::$sql['default']->update("UPDATE `{prefix_news}` SET `viewed` = (viewed+1) WHERE `id` = ?;", [$get['id']]);
    }

    //-> Tutorials-Kategorie Bild
    foreach(common::SUPPORTED_PICTURE as $end) {
        if (file_exists(basePath . "/inc/images/nopic." . $end)) {
            $pic = '../inc/images/nopic.' . $end;
            break;
        }
    }

    //-> Tutorials Bild by ID
    foreach(common::SUPPORTED_PICTURE as $tmpendung) {
        //-> News Bild by ID
        if(file_exists(rootPath."/static/images/tutorials/".$get['id'].".".$tmpendung)) {
            $pic = 'https://static.dzcp.de/thumbgen.php?img=images/tutorials/'.$get['id'].'.'.$tmpendung.'&width=238';
            break;
        }
    }

    $qryc = common::$sql['default']->select("SELECT * FROM `{prefix_tutorials_comments}` WHERE `tutorial` = ? ORDER BY `datum` DESC LIMIT ".
        (common::$page - 1)*settings::get('m_tutorial_comments').",".settings::get('m_tutorial_comments').";",[$get['id']]);
    $comments = ''; $entrys = 0;
    if(common::$sql['default']->rowCount()) {
        $entrys = common::cnt("{prefix_tutorials_comments}", " WHERE `tutorial` = ?","id",[$get['id']]);
        $i = ($entrys-(common::$page - 1)*settings::get('max_comments'));
        foreach($qryc as $getc) {
            $edit = ""; $delete = ""; $onoff = ""; $avatar = "";
            if ((common::$chkMe != 'unlogged' && $getc['reg'] == common::$userid) || common::permission("tutorials")) {
                $edit = common::getButtonEditSingle($get['id'],"action=tutorial&amp;do=edit&amp;cid=".$getc['id']);
                $delete = common::button_delete_single($get['id'],"action=tutorial&amp;do=delete&amp;cid=".$getc['id'],_button_title_del,_confirm_del_entry);
            }

            $email = ""; $hp = "";
            if (!$getc['reg']) {
                //-> Homepage Link
                if (!empty($getc['hp'])) {
                    $smarty->caching = false;
                    $smarty->assign('hp', common::links(stringParser::decode($getc['hp'])));
                    $hp = $smarty->fetch('string:' . _hpicon_forum);
                    $smarty->clearAllAssign();
                }

                //-> E-Mail
                if (empty($getc['email'])) {
                    $email = common::CryptMailto(stringParser::decode($getc['email']), _emailicon_forum);
                }

                $smarty->caching = true;
                $smarty->assign('nick',stringParser::decode($getc['nick']));
                $smarty->assign('email',$email);
                $nick = $smarty->fetch('string:'._link_mailto,common::getSmartyCacheHash('_link_mailto_'.$email.'_'.stringParser::decode($getc['nick'])));
                $smarty->clearAllAssign();
            } else {
                $onoff = common::onlinecheck($getc['reg']);
                $nick = common::autor($getc['reg']);
            }

            $smarty->caching = true;
            $smarty->assign('nick',stringParser::decode($getc['nick']));
            $smarty->assign('email',$email);
            $nick = $smarty->fetch('string:'._link_mailto,common::getSmartyCacheHash('_link_mailto_'.$email.'_'.stringParser::decode($getc['nick'])));
            $smarty->clearAllAssign();

            $smarty->caching = false;
            $smarty->assign('postid',$i);
            $smarty->assign('datum',date("d.m.Y", $getc['datum']));
            $smarty->assign('zeit',date("H:i", $getc['datum']));
            $smarty->assign('edit',$edit);
            $smarty->assign('delete',$delete);
            $titel = $smarty->fetch('string:'._eintrag_titel);
            $smarty->clearAllAssign();

            $smarty->caching = true;
            $smarty->assign('titel',$titel);
            $smarty->assign('comment',BBCode::parse_html((string)$getc['comment']));
            $smarty->assign('nick',$nick);
            $smarty->assign('hp',$hp);
            $smarty->assign('editby',BBCode::parse_html((string)$getc['editby']));
            $smarty->assign('email',$email);
            $smarty->assign('avatar',common::useravatar($getc['reg']));
            $smarty->assign('onoff',$onoff);
            $smarty->assign('rank',common::getrank($getc['reg']));
            $smarty->assign('ip',common::getPostedIP($getc));
            $comments .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/comments/comments_show.tpl',common::getSmartyCacheHash('artikel_comments_'.$getc['id']));
            $smarty->clearAllAssign();
            $i--;
        }
    }

    $add = false;
    if(settings::get("reg_tutcomments") && common::$chkMe) {
        if (!common::ipcheck("tcid(".$tutorial_id.")", settings::get('f_tutorialscom')) && empty($add)) {
            $smarty->caching = false;
            $smarty->assign('nick',common::autor(common::$userid));
            $smarty->assign('action','../tutorials/?action=tutorial&amp;do=add&amp;id=' . (isset($tutorial_id) ? $tutorial_id : '1'));
            $smarty->assign('prevurl','../tutorials/?action=compreview&id=' . (isset($tutorial_id) ? $tutorial_id : '1'));
            $smarty->assign('id',(isset($tutorial_id) ? $tutorial_id : '1'));
            $smarty->assign('posteintrag',(isset($_POST['comment']) ? $_POST['comment'] : ''));
            $smarty->assign('notification',notification::get('tutorial',true));
            $add = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/comments/comments_add.tpl');
            $smarty->clearAllAssign();
        } else {
            $smarty->caching = false;
            $smarty->assign('sek',settings::get('f_tutorialscom'));
            $notification = $smarty->fetch('string:'._error_flood_post);
            $smarty->clearAllAssign();
            notification::add_error($notification);
            unset($notification);
        }
    }

    $seiten = common::nav($entrys, settings::get('m_comments'), "?action=tutorial&amp;id=" . $tutorial_id);

   $smarty->caching = false;
   $smarty->assign('show',$comments);
   $smarty->assign('seiten',$seiten);
   $smarty->assign('add',$add,true);
   $comments = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/comments/comments.tpl');
   $smarty->clearAllAssign();

    $level = 0;
    switch ($get['difficulty']) {
        default:
        case 1: $level = 1; break;
        case 2: $level = 2; break;
        case 3: $level = 3; break;
        case 4: $level = 4; break;
    }

    $smarty->caching = false;
    $smarty->assign('head',_tutorial." - ".$get['name']);
    $smarty->assign('pic',$pic);
    $smarty->assign('autor',common::autor($get['autor']));
    $smarty->assign('level',$level);
    $smarty->assign('comments',$comments,true); //Comments
    $smarty->assign('notification_page',notification::get());
    $smarty->assign('beschreibung',BBCode::parse_html((string)$get['beschreibung']));
    $smarty->assign('datum',date("j.m.y H:i", (empty($get_news['datum']) ? time() : $get_news['datum'])));
    $smarty->assign('tutorial',BBCode::parse_html((string)$get['tutorial']));
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/show_tutorial.tpl');
    $smarty->clearAllAssign();
}