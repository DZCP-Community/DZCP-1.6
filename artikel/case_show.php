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

if(defined('_Artikel') && isset($_GET['id']) && !empty($_GET['id'])) {
    $artikel_id = (int)($_GET['id']); $add = '';
    if (!common::$sql['default']->fetch("SELECT `public` FROM `{prefix_artikel}` WHERE `id` = ?;", [$artikel_id],'public') && !common::permission("artikel")) {
        $index = common::error(_error_wrong_permissions, 1);
    } else {
        $add = false;
        $get_artikel = common::$sql['default']->fetch("SELECT * FROM `{prefix_artikel}` WHERE `id` = ?".(common::permission("artikel") ? ";" : " AND public = 1;"), [$artikel_id]);
        if (!common::$sql['default']->rowCount()) {
            $index = common::error(_id_dont_exist, 1);
        } else {
            switch (common::$do) {
                case 'add':
                    if (common::$sql['default']->rows("SELECT `id` FROM `{prefix_artikel}` WHERE `id` = ?;", [$artikel_id]) != 0) {
                        if (settings::get("reg_artikel") && !common::$chkMe) {
                            $index = common::error(_error_have_to_be_logged, 1);
                        } else {
                            if (!common::ipcheck("artid(" . $_GET['id'] . ")", settings::get('f_artikelcom'))) {
                                if (empty($_POST['comment'])) {
                                    if(notification::has()) {
                                        javascript::set('AnchorMove', 'startpage');
                                    }
                                    if (empty($_POST['eintrag'])) {
                                        notification::add_error(_empty_eintrag,'artikel');
                                    }
                                } else {
                                    common::$sql['default']->insert("INSERT INTO `{prefix_artikel_comments}` SET `artikel` = ?,`datum` = ?,`nick` = ?,`email` = ?,`hp` = ?,`reg` = ?,`comment` = ?, `ipv4` = ?, `ipv6` = ?;",
                                    [$artikel_id,time(),(isset($_POST['nick']) && !common::$userid ? stringParser::encode($_POST['nick']) : common::data('nick')),(isset($_POST['email']) && !common::$userid ? stringParser::encode($_POST['email']) : common::data('email')),
                                    (isset($_POST['hp']) && !common::$userid ? stringParser::encode(common::links($_POST['hp'])) : stringParser::encode(common::links(stringParser::decode(common::data('hp'))))),
                                        (int)(common::$userid),stringParser::encode($_POST['comment']),common::$userip['v4'],common::$userip['v6']]);
                                    common::setIpcheck("artid(" . $artikel_id . ")");
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
                    $reg = common::$sql['default']->fetch("SELECT `reg` FROM `{prefix_artikel_comments}` WHERE `id` = ?;", [($cid = (int)($_GET['cid']))],'reg');
                    if ($reg == common::$userid || common::permission('artikel')) {
                        common::$sql['default']->delete("DELETE FROM `{prefix_artikel_comments}` WHERE `id` = ?;", [$cid]);
                        notification::add_success(_comment_deleted);
                    } else {
                        notification::add_error(_error_wrong_permissions);
                    }
                    break;
                case 'editcom':
                    if(notification::has()) {
                        javascript::set('AnchorMove', 'notification-box');
                    }
                    $reg = common::$sql['default']->fetch("SELECT `reg` FROM `{prefix_artikel_comments}` WHERE `id` = ?;", [($cid = (int)($_GET['cid']))],'reg');
                    if (common::$sql['default']->rowCount() && !empty($_POST['comment'])) {
                        if ($reg == common::$userid || common::permission('artikel')) {
                            /**
                            //-> Editby Text
                            $smarty->caching = false;
                            $smarty->assign('autor',common::autor(common::$userid));
                            $smarty->assign('time',date("d.m.Y H:i", time()));
                            $editedby = $smarty->fetch('string:'._edited_by);
                            $smarty->clearAllAssign();
                            **/

                            common::$sql['default']->update("UPDATE `{prefix_artikel_comments}` SET `nick` = ?, `email` = ?, `hp` = ?, `comment` = ?, `editby` = ?
                                          WHERE `id` = ?;", [(isset($_POST['nick']) ? stringParser::encode($_POST['nick']) : ''),
                                          (isset($_POST['email']) ? stringParser::encode($_POST['email']) : ''),
                                          (isset($_POST['hp']) ? stringParser::encode(common::links($_POST['hp'])) : ''),
                                          (isset($_POST['comment']) ? stringParser::encode($_POST['comment']) : ''),
                                          stringParser::encode(json_encode(['autor' => common::$userid, 'time' => time()])),$cid]);

                            $_POST = []; //Clear Post
                            notification::add_success(_comment_edited);
                        } else {
                            notification::add_error(_error_edit_post,'artikel');
                        }
                    } else {
                        notification::add_error(_empty_eintrag,'artikel');
                    }
                    break;
                case 'edit':
                    $get = common::$sql['default']->fetch("SELECT `id`,`reg`,`comment` FROM `{prefix_artikel_comments}` WHERE `id` = ?;", [(int)($_GET['cid'])]);
                    if (common::$userid >= 1 && ($get['reg'] == common::$userid || common::permission('artikel'))) {
                        if(notification::has()) {
                            javascript::set('AnchorMove', 'comForm');
                        }

                        $smarty->caching = false;
                        $smarty->assign('nick',common::autor($get['reg']));
                        $smarty->assign('action','?action=show&amp;do=editcom&amp;id=' . $artikel_id .'&amp;cid=' . (int)($_GET['cid']));
                        $smarty->assign('prevurl','../artikel/?action=compreview&do=edit&id=' . $artikel_id .'&cid=' . (int)($_GET['cid']));
                        $smarty->assign('id',$get['id']);
                        $smarty->assign('posteintrag',stringParser::decode($get['comment']));
                        $smarty->assign('notification',notification::get('global',true));
                        $add = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/comments_edit.tpl');
                        $smarty->clearAllAssign();
                    } else {
                        if(notification::has()) {
                            javascript::set('AnchorMove', 'notification-box');
                        }
                        notification::add_error(_error_edit_post,'artikel');
                    }
                break;
            }

            /************************
             * View Artikel
             ************************/
            //Update viewed
            if (common::count_clicks('artikel', $artikel_id)) {
                common::$sql['default']->update("UPDATE `{prefix_artikel}` SET `viewed` = (viewed+1) WHERE `id` = ?;", [$artikel_id]);
            }

            $links1 = '';
            if(!empty($get_news['url1'])) {
                $smarty->caching = false;
                $smarty->assign('link',stringParser::decode($get_artikel['link1']));
                $smarty->assign('url',utf8_decode($get_artikel['url1']));
                $smarty->assign('target',"_blank");
                $links1 = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel_link.tpl');
                $smarty->clearAllAssign();
            }

            $links2 = '';
            if(!empty($get_news['url2'])) {
                $smarty->caching = false;
                $smarty->assign('link',stringParser::decode($get_artikel['link2']));
                $smarty->assign('url',utf8_decode($get_artikel['url2']));
                $smarty->assign('target',"_blank");
                $links2 = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel_link.tpl');
                $smarty->clearAllAssign();
            }

            $links3 = '';
            if(!empty($get_news['url3'])) {
                $smarty->caching = false;
                $smarty->assign('link',stringParser::decode($get_artikel['link3']));
                $smarty->assign('url',utf8_decode($get_artikel['url3']));
                $smarty->assign('target',"_blank");
                $links3 = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel_link.tpl');
                $smarty->clearAllAssign();
            }

            $links = '';
            if (!empty($links1) || !empty($links2) || !empty($links3)) {
                $smarty->caching = true;
                $smarty->assign('link1',$links1);
                $smarty->assign('link2',$links2);
                $smarty->assign('link3',$links3);
                $links = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel_links.tpl',md5('artikel_links_'.$get_artikel['id']));
                $smarty->clearAllAssign();
            }

            //Artikel Comments
            $qryc = common::$sql['default']->select("SELECT * FROM `{prefix_artikel_comments}` WHERE `artikel` = ? "
                ."ORDER BY `datum` DESC LIMIT ".(common::$page - 1)*settings::get('m_comments').",".settings::get('m_comments').";",
                [$artikel_id]);

            $entrys = common::cnt('{prefix_artikel_comments}', " WHERE `artikel` = ?","id",[$artikel_id]);
            $i = ($entrys - (common::$page - 1) * settings::get('m_comments')); $comments = '';
            foreach($qryc as $getc) {
                $edit = ""; $delete = "";
                if ((common::$chkMe >= 1 && $getc['reg'] == common::$userid) || common::permission("news")) {
                    $smarty->caching = true;
                    $smarty->assign('action',"?action=show&amp;do=edit&amp;cid=" . $getc['id']."&amp;id=".$get_artikel['id']);
                    $smarty->assign('title',_button_title_edit);
                    $edit = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/button_edit.tpl',
                        common::getSmartyCacheHash('_button_edit_'.$get_artikel['id'].'_cid_'.$getc['id']));
                    $smarty->clearAllAssign();

                    $smarty->caching = true;
                    $smarty->assign('id',$get_artikel['id']);
                    $smarty->assign('action',"?action=show&amp;do=delete&amp;cid=".$getc['id']."&amp;id=".$get_artikel['id']);
                    $smarty->assign('title',_button_title_del);
                    $smarty->assign('del',_confirm_del_entry);
                    $delete = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/button_delete.tpl',
                        common::getSmartyCacheHash('_button_delete_'.$get_artikel['id'].'_cid_'.$getc['id']));
                    $smarty->clearAllAssign();
                }

                $email = ""; $hp = ""; $avatar = ""; $onoff = "";
                if (!$getc['reg']) {
                    //-> Homepage Link
                    $hp = "";
                    if (!empty($getc['hp'])) {
                        $smarty->caching = false;
                        $smarty->assign('hp',common::links(stringParser::decode($getc['hp'])));
                        $hp = $smarty->fetch('string:'._hpicon_forum);
                        $smarty->clearAllAssign();
                    }

                    if ($getc['email']) {
                        $email = '<br />' . common::CryptMailto(stringParser::decode($getc['email']), _emailicon_forum);
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
                $comments .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/comments_show.tpl',common::getSmartyCacheHash('artikel_comments_'.$getc['id']));
                $smarty->clearAllAssign();
                $i--;
            }

            if (settings::get("reg_artikel") && common::$chkMe) {
                if (!common::ipcheck("artid(".$_GET['id'].")", settings::get('f_artikelcom')) && empty($add)) {
                    $smarty->caching = false;
                    $smarty->assign('nick',common::autor(common::$userid));
                    $smarty->assign('action','../artikel/?action=show&amp;do=add&amp;id=' . (isset($_GET['id']) ? $_GET['id'] : '1'));
                    $smarty->assign('prevurl','../artikel/?action=compreview&id=' . (isset($_GET['id']) ? $_GET['id'] : '1'));
                    $smarty->assign('id',(isset($_GET['id']) ? $_GET['id'] : '1'));
                    $smarty->assign('posteintrag',(isset($_POST['comment']) ? $_POST['comment'] : ''));
                    $smarty->assign('notification',notification::get('artikel',true));
                    $add = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/comments_add.tpl');
                    $smarty->clearAllAssign();
                } else if (empty($add) && !notification::has()) {
                    $smarty->caching = false;
                    $smarty->assign('sek',settings::get('f_newscom'));
                    $notification = $smarty->fetch('string:'._error_flood_post);
                    $smarty->clearAllAssign();
                    notification::add_error($notification);
                    unset($notification);
                }
            }

            if(empty($comments)) {
                $smarty->caching = false;
                $smarty->assign('colspan',1);
                $comments = $smarty->fetch('string:'._no_entrys_yet);
                $smarty->clearAllAssign();
            }

            $seiten = common::nav($entrys, settings::get('m_comments'), "?action=show&amp;id=" . $_GET['id'] . "");
            $smarty->caching = false;
            $smarty->assign('show',$comments);
            $smarty->assign('seiten',$seiten);
            $smarty->assign('add',$add,true);
            $showmore = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/comments.tpl');
            $smarty->clearAllAssign();

            $artikelimage = '../inc/images/uploads/newskat/'.common::$sql['default']->fetch("SELECT `katimg` FROM `{prefix_news_kats}` WHERE `id` = ?;", [$get_artikel['kat']],'katimg');
            foreach (common::SUPPORTED_PICTURE as $tmpendung) {
                if (file_exists(basePath . "/inc/images/uploads/artikel/".$get_artikel['id'].".".$tmpendung)) {
                    $artikelimage = '../inc/images/uploads/artikel/'.$get_artikel['id'].'.'.$tmpendung;
                    break;
                }
            }

            //-> Artikel [Caching]
            $where = $where." - ".stringParser::decode($get_artikel['titel']);
            $smarty->caching = false;
            $smarty->assign('titel',stringParser::decode($get_artikel['titel']));
            $smarty->assign('kat',$artikelimage);
            $smarty->assign('id',$get_artikel['id']);
            $smarty->assign('display','inline');
            $smarty->assign('notification_page',notification::get(),true);
            $smarty->assign('comments',$entrys,true); //Comments
            $smarty->assign('showmore',$showmore,true); //Comments
            $smarty->assign('viewed',$get_artikel['viewed']); //Comments
            $smarty->assign('text',BBCode::parse_html((string)$get_artikel['text']));
            $smarty->assign('datum',date("j.m.y H:i", (empty($get_artikel['datum']) ? time() : $get_artikel['datum'])));
            $smarty->assign('links',$links);
            $smarty->assign('autor',common::autor($get_artikel['autor']));
            $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/show_more.tpl',common::getSmartyCacheHash('artikel_full_'.$get_artikel['id']));
            $smarty->clearAllAssign();
        }
    }
}