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
    if (!common::permission("editusers") && common::$do != 'reidenty') {
        $index = common::error(_error_wrong_permissions);
    } elseif (isset($_GET['edit']) && $_GET['edit'] == common::$userid && common::$do != 'reidenty') {
        $qrySquads = common::$sql['default']->select("SELECT `id`,`name` FROM `{prefix_groups}` ORDER BY `id`;");
        $esquads = '';
        foreach($qrySquads as $getsq) {
            $qrypos = common::$sql['default']->select("SELECT `id`,`position` FROM `{prefix_positions}` ORDER BY `pid`;");
            $posi = "";
            foreach($qrypos as $getpos) {
                $check = common::$sql['default']->rows("SELECT `id` FROM `{prefix_user_posis}` WHERE `posi` = ? AND `group` = ? AND `user` = ?;",
                    [$getpos['id'],$getsq['id'],(int)($_GET['edit'])]);
                $posi .= common::select_field($getpos['id'],$check,stringParser::decode($getpos['position']));
            }

            $check = common::$sql['default']->rows("SELECT `id` FROM `{prefix_group_user}` WHERE `user` = ? AND `group` = ?;",
                [(int)($_GET['edit']),$getsq['id']]) ? 'checked="checked"' : '';

            $smarty->caching = false;
            $smarty->assign('id',$getsq['id']);
            $smarty->assign('check',$check);
            $smarty->assign('eposi',$posi);
            $smarty->assign('squad',stringParser::decode($getsq['name']));
            $esquads .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/admin/admin_checkfield_squads.tpl');
            $smarty->clearAllAssign();
        } unset($posi,$check,$getsq,$qrySquads);

        $smarty->caching = false;
        $smarty->assign('esquad',$esquads);
        $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/admin/admin_self.tpl');
        $smarty->clearAllAssign();
    } elseif (isset($_GET['edit']) &&
        (common::data("level", (int)($_GET['edit'])) == 4 || common::rootAdmin((int)($_GET['edit']))) &&
        !common::rootAdmin(common::$userid) && common::$do != 'reidenty') {
        $index = common::error(_error_edit_admin, 1);
    } else {
        switch (common::$do) {
            case 'identy': // Get a new identy
                if((common::data("level", (int)($_GET['id'])) == 4 && !common::rootAdmin((int)($_GET['id'])) && !common::rootAdmin(common::$userid))) {
                    $index = common::error(_identy_admin, 1);
                } else {
                    $smarty->caching = false;
                    $smarty->assign('nick',common::autor($_GET['id']));
                    $msg = $smarty->fetch('string:'._admin_user_get_identy);
                    $smarty->clearAllAssign();

                    $old_id = common::$userid;
                    common::$sql['default']->update("UPDATE `{prefix_users}` SET `online` = 0, `sessid` = '' WHERE id = ?;", [$old_id]); //Logout
                    session_regenerate_id();

                    //Admin Callback
                    $_SESSION['admin_id'] = $old_id;
                    $_SESSION['admin_pwd'] = stringParser::decode(common::data("pwd", $old_id));
                    $_SESSION['admin_ip'] = common::$userip['v4'];
                    unset($old_id);

                    //Set User Data
                    $_SESSION['id'] = (int)($_GET['id']);
                    $_SESSION['pwd'] = stringParser::decode(common::data("pwd", (int)($_GET['id'])));
                    $_SESSION['ip'] = common::$userip['v4'];

                    common::$sql['default']->update("UPDATE `{prefix_users}` SET `online` = 1, `sessid` = ?, `ipv4` = ? WHERE `id` = ?;",
                        [session_id(),common::$userip['v4'],(int)($_GET['id'])]);
                    common::setIpcheck("ident(" . common::$userid . "_" . (int)($_GET['id']) . ")");

                    $index = common::info($msg, "?action=user&amp;id=" . $_GET['id'] . "", 5, false);
                }
                break;
            case 'reidenty': // Get a old identy
                if(array_key_exists('admin_id',$_SESSION) &&
                    array_key_exists('admin_pwd',$_SESSION) &&
                    array_key_exists('admin_ip',$_SESSION) &&
                        !empty($_SESSION['admin_id']) &&
                        !empty($_SESSION['admin_pwd']) &&
                        !empty($_SESSION['admin_ip'])) {
                        //Check Data
                        if(common::data("level", $_SESSION['admin_id']) >= 4 &&
                            common::data("ipv4", $_SESSION['admin_id']) == $_SESSION['admin_ip'] &&
                            common::data("pwd", $_SESSION['admin_id']) == $_SESSION['admin_pwd']) {

                            $new_id = $_SESSION['admin_id'];
                            common::$sql['default']->update("UPDATE `{prefix_users}` SET `online` = 0, `sessid` = '' WHERE id = ?;", [common::$userid]); //Logout
                            session_regenerate_id();

                            //Clean reidenty
                            $_SESSION['admin_id'] = '';
                            $_SESSION['admin_pwd'] = '';
                            $_SESSION['admin_ip'] = '';

                            //Set User Data
                            $_SESSION['id'] = $new_id;
                            $_SESSION['pwd'] = stringParser::decode(common::data("pwd", $new_id));
                            $_SESSION['ip'] = common::$userip['v4'];
                            unset($new_id);

                            common::$sql['default']->update("UPDATE `{prefix_users}` SET `online` = 1, `sessid` = ?, `ipv4` = ? WHERE `id` = ?;",
                                [session_id(),common::$userip['v4'],$_SESSION['id']]);

                            $index = common::info("", "?action=userlobby", 0, true);
                        }
                }
                break;
            case 'update': //Edit a User
                if ($_POST && isset($_GET['user'])) {
                    $edituser = (int)($_GET['user']);

                    // Permissions Update
                    if (empty($_POST['perm'])) {
                        $_POST['perm'] = [];
                    }

                    $qry_fields = common::$sql['default']->show("SHOW FIELDS FROM `{prefix_permissions}`;");
                    $sql_update = '';
                    foreach($qry_fields as $get) {
                        if ($get['Field'] != 'id' && $get['Field'] != 'user' && $get['Field'] != 'pos' && $get['Field'] != 'intforum') {
                            $sql_qry = array_key_exists('p_' . $get['Field'], $_POST['perm']) ? '`' . $get['Field'] . '` = 1' : '`' . $get['Field'] . '` = 0';
                            $sql_update .= $sql_qry . ', ';
                        }
                    }

                    // Check User Permissions is exists
                    if (!common::$sql['default']->rows('SELECT `id` FROM `{prefix_permissions}` WHERE `user` = ? LIMIT 1;', [$edituser])) {
                        common::$sql['default']->insert("INSERT INTO `{prefix_permissions}` SET `user` = ?;", [$edituser]);
                    }

                    // Update Permissions
                    common::$sql['default']->update('UPDATE `{prefix_permissions}` SET '.substr($sql_update, 0, -2).' WHERE `user` = ?;', [$edituser]);

                    // Internal Boardpermissions Update
                    if (empty($_POST['board'])) {
                        $_POST['board'] = [];
                    }

                    // Boardpermissions Cleanup
                    $qry = common::$sql['default']->select('SELECT `id`,`forum` FROM `{prefix_forum_access}` WHERE `user` = ?;', [$edituser]);
                    foreach($qry as $get) {
                        if (!common::array_var_exists($get['forum'], $_POST['board'])) {
                            common::$sql['default']->delete('DELETE FROM `{prefix_forum_access}` WHERE `id` = ?;', [$get['id']]);
                        }
                    }

                    //Add new Boardpermissions
                    if (count($_POST['board']) >= 1) {
                        foreach ($_POST['board'] AS $boardpem) {
                            if (!common::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_access}` WHERE `user` = ? AND `forum` = ?;", [$edituser,$boardpem])) {
                                common::$sql['default']->insert("INSERT INTO `{prefix_forum_access}` SET `user` = ?, `forum` = ?;", [$edituser,$boardpem]);
                            }
                        }
                    }

                    common::$sql['default']->delete("DELETE FROM `{prefix_group_user}` WHERE `user` = ?;", [$edituser]);
                    common::$sql['default']->delete("DELETE FROM `{prefix_user_posis}` WHERE `user` = ?;", [$edituser]);

                    $sq = common::$sql['default']->select("SELECT `id` FROM `{prefix_groups}`;");
                    foreach($sq as $getsq) {
                        if (isset($_POST['squad' . $getsq['id']])) {
                            common::$sql['default']->insert("INSERT INTO `{prefix_group_user}` SET `user` = ?, `group`  = ?;",
                                [$edituser,(int)($_POST['squad' . $getsq['id']])]);
                        }

                        if (isset($_POST['squad' . $getsq['id']])) {
                            common::$sql['default']->insert("INSERT INTO {prefix_user_posis} SET `user` = ?, `posi` = ?, `group` = ?;",
                                [$edituser,(int)($_POST['sqpos' . $getsq['id']]),(int)($getsq['id'])]);
                        }
                    }

                    $level = (int)($_POST['level']);
                    if(common::permission("editusers") && common::data("level") != 4 && !common::rootAdmin(common::$userid) && $level == 4) {
                        $level = common::data("level",$edituser);
                    }

                    $newpwd = !empty($_POST['passwd']) ? "`pwd` = '" . common::pwd_encoder($_POST['passwd']) . "'," : "";
                    $update_level = $_POST['level'] == 'banned' ? 0 : $level;
                    $update_banned = $_POST['level'] == 'banned' ? 1 : 0;
                    common::$sql['default']->update("UPDATE {prefix_users} SET ".$newpwd." "
                        . "`nick`   = ?, "
                        . "`email`  = ?, "
                        . "`user`   = ?, "
                        . "`listck` = ?, "
                        . "`level`  = ?, "
                        . "`banned`  = ? "
                        . "WHERE `id` = ?;",
                        [stringParser::encode($_POST['nick']),stringParser::encode($_POST['email']),stringParser::encode($_POST['loginname']),(isset($_POST['listck']) ? (int)($_POST['listck']) : 0),
                            (int)($update_level),(int)($update_banned),$edituser]);

                    common::setIpcheck("upduser(" . common::$userid . "_" . $edituser . ")");
                }

                $index = common::info(_admin_user_edited, "?action=userlist");
                break;
            case 'updateme':
                common::$sql['default']->delete("DELETE FROM `{prefix_group_user}` WHERE `user` = ?;", [common::$userid]);
                common::$sql['default']->delete("DELETE FROM `{prefix_user_posis}` WHERE `user` = ?;", [common::$userid]);

                $squads = common::$sql['default']->select("SELECT `id` FROM `{prefix_groups}`;");
                foreach($squads as $getsq) {
                    if (isset($_POST['squad' . $getsq['id']])) {
                        common::$sql['default']->insert("INSERT INTO `{prefix_group_user}` SET `user`  = ?, `group` = ?;",
                            [(int)(common::$userid),(int)($_POST['squad' . $getsq['id']])]);
                    }

                    if (isset($_POST['squad' . $getsq['id']])) {
                        common::$sql['default']->insert("INSERT INTO `{prefix_user_posis}` SET `user` = ?, `posi` = ?, `group`  = ?",
                            [(int)(common::$userid),(int)($_POST['sqpos'.$getsq['id']]),(int)($getsq['id'])]);
                    }
                }

                $index = common::info(_admin_user_edited, "?action=user&amp;id=" . common::$userid . "");
                break;
            case 'delete':
                $delUID = (int)($_GET['id']);
                if ($_GET['verify'] == "yes") {
                    if (common::data("level", (int)($_GET['id'])) == 4 || common::data("level", (int)($_GET['id'])) == 3 || common::rootAdmin($delUID))
                        $index = common::error(_user_cant_delete_admin, 2);
                    else {
                        if($delUID >= 1) {
                            common::setIpcheck("deluser(" . common::$userid . "_" . $delUID . ")");
                            common::$sql['default']->update("UPDATE `{prefix_forum_posts}` SET `reg` = 0 WHERE `reg` = ?;", [$delUID]);
                            common::$sql['default']->update("UPDATE `{prefix_forum_threads}` SET `t_reg` = 0 WHERE `t_reg` = ?;", [$delUID]);
                            common::$sql['default']->update("UPDATE `{prefix_news_comments}` SET `reg` = 0 WHERE `reg` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_messages}` WHERE `von` = ? OR `an` = ?;", [$delUID,$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_news}` WHERE `autor` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_permissions}` WHERE `user` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_group_user}` WHERE `user` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_user_buddys}` WHERE `user` = ? OR `buddy` = ?;", [$delUID,$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_user_posis}` WHERE `user` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_users}` WHERE `id` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_user_stats}` WHERE `user` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_clicks_ips}` WHERE `uid` = ?;", [$delUID]);

                            ## Losche User-Upload Ordner ##
                            fileman::RemoveUserDir($delUID);

                            $index = common::info(_user_deleted, "?action=userlist", 5, false);
                        }
                    }
                }

                $smarty->caching = false;
                $smarty->assign('id',$delUID);
                $smarty->assign('user',common::autor($delUID));
                $index = $smarty->fetch('string:'._user_delete_verify);
                $smarty->clearAllAssign();
                break;
            case 'full_delete':
                $delUID = ((int)$_GET['id']);
                if ($_GET['verify'] == "yes") {
                    if (common::data("level", (int)($_GET['id'])) == 4 || common::data("level", (int)($_GET['id'])) == 3 || common::rootAdmin($delUID))
                        $index = common::error(_user_cant_delete_admin, 2);
                    else {
                        if($delUID >= 1) {
                            common::setIpcheck("deluser(" . common::$userid . "_" . $delUID . ")");
                            common::$sql['default']->update("DELETE FROM `{prefix_forum_posts}` WHERE `reg` = ?;", [$delUID]);
                            common::$sql['default']->update("DELETE FROM `{prefix_forum_threads}` WHERE `t_reg` = ?;", [$delUID]);
                            common::$sql['default']->update("DELETE FROM `{prefix_news_comments}` WHERE `reg` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_messages}` WHERE `von` = ? OR `an` = ?;", [$delUID,$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_news}` WHERE `autor` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_permissions}` WHERE `user` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_group_user}` WHERE `user` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_user_buddys}` WHERE `user` = ? OR `buddy` = ?;", [$delUID,$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_user_posis}` WHERE `user` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_users}` WHERE `id` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_user_stats}` WHERE `user` = ?;", [$delUID]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_clicks_ips}` WHERE `uid` = ?;", [$delUID]);

                            ## Losche User-Upload Ordner ##
                            fileman::RemoveUserDir($delUID);

                            $index = common::info(_user_deleted, "?action=userlist", 5 ,false);
                        }
                    }
                }

                $smarty->caching = false;
                $smarty->assign('id',$delUID);
                $smarty->assign('user',common::autor($delUID));
                $index = $smarty->fetch('string:'._user_delete_verify);
                $smarty->clearAllAssign();
                break;
            default:
                //Show edit user
                $get = common::$sql['default']->fetch("SELECT `id`,`user`,`nick`,`pwd`,`email`,`level`,`position`,`listck` "
                    . "FROM `{prefix_users}` "
                    . "WHERE `id` = ?;", [(int)($_GET['edit'])]);
                if (common::$sql['default']->rowCount()) {
                    $where = _user_profile_of . common::autor(((int)$_GET['edit']));
                    $qrysq = common::$sql['default']->select("SELECT `id`,`name` FROM `{prefix_groups}` ORDER BY `id`;");
                    $esquads = ''; $posi = "";
                    foreach($qrysq as $getsq) {
                        $qrypos = common::$sql['default']->select("SELECT `id`,`position` FROM `{prefix_positions}` ORDER BY `pid`;");
                        $posi = "";
                        foreach($qrypos as $getpos) {
                            $check = common::$sql['default']->rows("SELECT `id` FROM `{prefix_user_posis}` WHERE `posi` = ? AND `group` = ? AND `user` = ?;",
                                [$getpos['id'],$getsq['id'],(int)($_GET['edit'])]);
                            $posi .= common::select_field($getpos['id'],$check,stringParser::decode($getpos['position']));
                        }

                        $checksquser = common::$sql['default']->rows("SELECT `id` FROM `{prefix_group_user}` WHERE `user` = ? AND `group` = ?;",
                            [(int)($_GET['edit']),$getsq['id']]);
                        $check = $checksquser ? 'checked="checked"' : '';

                        $smarty->caching = false;
                        $smarty->assign('id',$getsq['id']);
                        $smarty->assign('check',$check);
                        $smarty->assign('eposi',$posi);
                        $smarty->assign('squad',stringParser::decode($getsq['name']));
                        $esquads .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/admin/admin_checkfield_squads.tpl');
                        $smarty->clearAllAssign();
                    }

                    $smarty->caching = false;
                    $smarty->assign('id',((int)$_GET['edit']));
                    $get_identy = $smarty->fetch('string:'._admin_user_get_identitat);
                    $smarty->clearAllAssign();

                    $smarty->caching = false;
                    $editpwd = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/admin/admin_editpwd.tpl');

                    //User Levels
                    $elevel = '';
                    $levels = ['banned' => _admin_level_banned, 1 => _status_user, 4 => _status_admin];
                    foreach ($levels as $id => $text) {
                        if(common::$chkMe != 4 && $id == 4)
                            continue;

                        $elevel .= common::select_field($id,($get['level'] == $id),$text);
                    }

                    $smarty->caching = false;
                    $smarty->assign('enick',stringParser::decode($get['nick']));
                    $smarty->assign('user',((int)$_GET['edit']));
                    $smarty->assign('email',stringParser::decode($get['email']));
                    $smarty->assign('loginname',stringParser::decode($get['user']));
                    $smarty->assign('squad',$esquads);
                    $smarty->assign('editpwd',$editpwd);
                    $smarty->assign('eposi',$posi);
                    $smarty->assign('getpermissions',common::getPermissions(((int)$_GET['edit'])));
                    $smarty->assign('getboardpermissions',common::getBoardPermissions(((int)$_GET['edit'])));
                    $smarty->assign('showpos',common::getrank(((int)$_GET['edit'])));
                    $smarty->assign('listck',(empty($get['listck']) ? '' : ' checked="checked"'));
                    $smarty->assign('alvl',$get['level']);
                    $smarty->assign('elevel',$elevel);
                    $smarty->assign('get',$get_identy);
                    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/admin/admin.tpl');
                    $smarty->clearAllAssign();
                } else
                    $index = common::error(_user_dont_exist, 1);
                break;
        }
    }
}