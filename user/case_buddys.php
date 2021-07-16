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

//TODO: Zu langsam!!! Optimierung!
if(defined('_UserMenu')) {
    $where = _site_user_buddys;
    if(!common::$chkMe) {
        $index = common::error(_error_have_to_be_logged, 1);
    } else {
        switch (common::$do) {
            case 'add':
                if($_POST['users'] == "-") {
                    $index = common::error(_error_select_buddy, 1);
                } elseif($_POST['users'] == common::$userid) {
                    $index = common::error(_error_buddy_self, 1);
                } elseif(!check_buddy($_POST['users'])) {
                    $index = common::error(_error_buddy_already_in, 1);
                } else {
                    common::$sql['default']->insert("INSERT INTO `{prefix_user_buddys}` SET `user` = ?, `buddy` = ?;",
                    [(int)(common::$userid),(int)($_POST['users'])]);

                    $smarty->caching = false;
                    $smarty->assign('user',common::autor(common::$userid));
                    $msg = $smarty->fetch('string:'._buddy_added_msg);
                    $smarty->clearAllAssign();

                    $title = _buddy_title;
                    common::$sql['default']->insert("INSERT INTO `{prefix_messages}` SET "
                               . "`datum` = ".time().", "
                               . "`von` = 0, "
                               . "`an` = ?, "
                               . "`titel` = ?, "
                               . "`nachricht` = ?;", [(int)($_POST['users']),stringParser::encode($title),stringParser::encode($msg)]);

                    $index = common::info(_add_buddy_successful, "?action=buddys");
                }
            break;
            case 'addbuddy':
                $user = isset($_GET['id']) ? $_GET['id'] : $_POST['users'];
                if($user == "-") {
                    $index = common::error(_error_select_buddy, 1);
                } elseif($user == common::$userid) {
                    $index = common::error(_error_buddy_self, 1);
                } elseif(!check_buddy($user)) {
                    $index = common::error(_error_buddy_already_in, 1);
                } else {
                    common::$sql['default']->insert("INSERT INTO `{prefix_user_buddys}` SET `user` = ?, `buddy` = ?;", [(int)(common::$userid),(int)($user)]);

                    $smarty->caching = false;
                    $smarty->assign('user',common::autor(common::$userid));
                    $msg = $smarty->fetch('string:'._buddy_added_msg);
                    $smarty->clearAllAssign();

                    $title = _buddy_title;
                    common::$sql['default']->insert("INSERT INTO `{prefix_messages}` SET "
                               . "`datum` = ".time().", "
                               . "`von` = 0, "
                               . "`an` = ?, "
                               . "`titel` = ?, "
                               . "`nachricht` = ?;", [(int)($user),stringParser::encode($title),stringParser::encode($msg)]);

                    $index = common::info(_add_buddy_successful, "?action=buddys");
                }
            break;
            case 'delete':
                if(isset($_GET['id']) && (int)($_GET['id']) >= 1) {
                    common::$sql['default']->delete("DELETE FROM `{prefix_user_buddys}` "
                               . "WHERE `buddy` = ? AND `user` = ?;", [(int)($_GET['id']),common::$userid]);

                    $smarty->caching = false;
                    $smarty->assign('user',addslashes(common::autor(common::$userid)));
                    $msg = $smarty->fetch('string:'._buddy_del_msg);
                    $smarty->clearAllAssign();

                    $title = _buddy_title;
                    common::$sql['default']->insert("INSERT INTO `{prefix_messages}` SET "
                               . "`datum` = ".time().", "
                               . "`von` = 0, "
                               . "`an` = ?, "
                               . "`titel` = ?, "
                               . "`nachricht` = ?;", [(int)($_GET['id']),stringParser::encode($title),stringParser::encode($msg)]);

                    $index = common::info(_buddys_delete_successful, "../user/?action=buddys");
                }
            break;
            default:
                $qry = common::$sql['default']->select("SELECT `buddy` FROM `{prefix_user_buddys}` WHERE `user` = ?;", [common::$userid]);
                $too = ""; $buddys = ""; $usersNL= [];
                foreach($qry as $get) {
                    //Private Massage
                    $smarty->caching = false;
                    $smarty->assign('id',$get['buddy']);
                    $smarty->assign('nick',stringParser::decode(common::data("nick",$get['buddy'])));
                    $pn = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_pn_write.tpl');
                    $smarty->clearAllAssign();

                    $smarty->caching = false;
                    $smarty->assign('id',$get['buddy']);
                    $delete = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/buddys/buddys_delete.tpl');
                    $smarty->clearAllAssign();

                    $too = common::$sql['default']->rows("SELECT `id` FROM `{prefix_user_buddys}` where `user` = ? AND `buddy` = ?;",
                        [$get['buddy'],common::$userid]) ? _buddys_yesicon : _buddys_noicon;
                    $usersNL[$get['buddy']] = true;

                    $smarty->caching = false;
                    $smarty->assign('nick',common::autor($get['buddy']));
                    $smarty->assign('onoff',common::onlinecheck($get['buddy']));
                    $smarty->assign('pn',$pn);
                    $smarty->assign('color',$color);
                    $smarty->assign('too',$too);
                    $smarty->assign('delete',$delete);
                    $buddys .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/buddys/buddys_show.tpl');
                    $smarty->clearAllAssign(); $color++;
                }

                if (empty($buddys)) {
                    $smarty->caching = false;
                    $smarty->assign('colspan',5);
                    $buddys = $smarty->fetch('string:'._no_entrys_yet);
                    $smarty->clearAllAssign();
                }

                $qry = common::$sql['default']->select("SELECT `id`,`nick` FROM `{prefix_users}` WHERE `level` != 0 ORDER BY `nick`;");
                $users = '';
                foreach($qry as $get) {
                    if(!array_key_exists($get['id'], $usersNL) && $get['id'] != common::$userid) {
                        $smarty->caching = false;
                        $smarty->assign('id',$get['id']);
                        $smarty->assign('selected','');
                        $smarty->assign('nick',stringParser::decode(common::data("nick",$get['id'])));
                        $users .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_option_users.tpl');
                        $smarty->clearAllAssign();
                    }
                }

                $smarty->caching = false;
                $smarty->assign('users',$users);
                $add = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/buddys/buddys_add.tpl');
                $smarty->clearAllAssign();

                $smarty->caching = false;
                $smarty->assign('show',$buddys);
                $smarty->assign('add',$add);
                $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/buddys/buddys.tpl');
                $smarty->clearAllAssign();
            break;
        }
    }
}