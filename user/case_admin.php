<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if (defined('_UserMenu')) {
    if (!permission("editusers")) {
        $index = error(_error_wrong_permissions, 1);
    } else {
        $edit_userid = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
        $identy_userid = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        //Self
        if ($edit_userid && $edit_userid == $userid) {
            $qrysq = db("SELECT `id`,`name` FROM `" . $db['squads'] . "` ORDER BY `pos`;");
            $esquads = '';
            while ($getsq = _fetch($qrysq)) {
                $qrypos = db("SELECT `id`,`position` FROM `" . $db['pos'] . "` ORDER BY `pid`;");
                $posi = "";
                while ($getpos = _fetch($qrypos)) {
                    $check = db("SELECT `id` FROM `" . $db['userpos'] . "` WHERE `posi` = " . $getpos['id'] . " AND `squad` = " . $getsq['id'] . " AND `user` = " . $userid . ";", true);
                    $sel = $check ? 'selected="selected"' : '';
                    $posi .= show(_select_field_posis, array("value" => $getpos['id'], "sel" => $sel, "what" => re($getpos['position'])));
                }

                $check = db("SELECT `id` FROM `" . $db['squaduser'] . "` WHERE `user` = " . $userid . " AND `squad` = " . $getsq['id'] . ";", true) ? 'checked="checked"' : '';
                $esquads .= show(_checkfield_squads, array("id" => $getsq['id'], "check" => $check, "eposi" => $posi, "noposi" => _user_noposi, "squad" => re($getsq['name'])));
            }

            $index = show($dir . "/admin_self", array("squadhead" => _admin_user_squadhead,
                "showpos" => getrank($userid),
                "esquad" => $esquads,
                "nothing" => _nothing,
                "value" => _button_value_edit,
                "eposi" => $posi,
                "squad" => _member_admin_squad,
                "posi" => _profil_position));
        } else if (($edit_userid || $identy_userid) && data("level", $userid) == 4) {
            switch ($do) {
                case "identy":
                    $sql = db("SELECT `id` FROM `" . $db['users'] . "` WHERE `id` = " . $identy_userid . ";");
                    if ($identy_userid >= 1 && _rows($sql)) {
                        if ((rootAdmin($identy_userid) && !rootAdmin($userid))
                            || (data("level", $identy_userid) == 4 && !rootAdmin($userid)) ||
                            !data("level", $userid) == 4) {
                            $index = error(_identy_admin, 1);
                        } else if (data("dsgvo_lock", $identy_userid)) {
                            $index = error(_admin_dsgvo_indent_lock, 1);
                        } else {
                            $msg = show(_admin_user_get_identy, array("nick" => autor($identy_userid)));
                            $_SESSION['identy_id'] = $userid; //Save Last ID

                            db("UPDATE " . $db['users'] . " SET `online` = 0, `sessid` = '' WHERE `id` = " . $userid . ";"); //Logout
                            session_regenerate_id();

                            $_SESSION['id'] = $_GET['id'];
                            $_SESSION['pwd'] = data("pwd", $identy_userid);
                            $_SESSION['ip'] = data("ip", $identy_userid);
                            $_SESSION['identy_ip'] = $_SESSION['ip'];

                            db("UPDATE " . $db['users'] . " SET `online` = 1, `sessid` = '" . session_id() . "' WHERE `id` = " . $identy_userid . ";");
                            setIpcheck("ident(" . $userid . "_" . $identy_userid . ")");

                            $index = info($msg, "?action=userlobby");
                        }
                    }
                    break;
                case "update":
                    if (!rootAdmin($edit_userid) || (!rootAdmin($edit_userid) && rootAdmin($userid))) {
                        if (isset($_POST) && $edit_userid) {
                            // Permissions Update
                            $sql_query = "UPDATE `" . $db['permissions'] . "` SET";
                            $perm = db("SHOW COLUMNS FROM " . $db['permissions'] . ";");
                            while ($perm_key = _fetch($perm)) {
                                if ($perm_key['Field'] == 'id' || $perm_key['Field'] == 'user' || $perm_key['Field'] == 'pos')
                                    continue;

                                $sql_query .= " `" . $perm_key['Field'] . "` = " . (isset($_POST['perm']['p_' . $perm_key['Field']]) ? 1 : 0) . ",";
                            }
                            $sql_query = substr($sql_query, 0, strlen($sql_query) - 1);
                            $sql_query .= " WHERE `user` = " . $edit_userid . ";";
                            db($sql_query);

                            // internal boardpermissions
                            db("DELETE FROM " . $db['f_access'] . " WHERE `user` = '" . $edit_userid . "'");
                            if (!empty($_POST['board'])) {
                                foreach ($_POST['board'] AS $v) {
                                    db("INSERT INTO `" . $db['f_access'] . "` SET `user` = " . $edit_userid . ", `forum` = '" . $v . "';");
                                }
                            }

                            db("DELETE FROM `" . $db['squaduser'] . "` WHERE `user` = " . $edit_userid . ";");
                            db("DELETE FROM `" . $db['userpos'] . "` WHERE `user` = " . $edit_userid . ";");

                            $sq = db("SELECT `id` FROM `" . $db['squads'] . "`;");
                            while ($getsq = _fetch($sq)) {
                                if (isset($_POST['squad' . $getsq['id']])) {
                                    db("INSERT INTO `" . $db['squaduser'] . "` SET `user` = " . $edit_userid . ", `squad` = " .
                                        ((int)$_POST['squad' . $getsq['id']]) . ";");
                                }

                                if (isset($_POST['squad' . $getsq['id']])) {
                                    db("INSERT INTO `" . $db['userpos'] . "` SET `user` = " . $edit_userid . ", `posi` = "
                                        . ((int)$_POST['sqpos' . $getsq['id']]) . ", `squad` = " . ((int)$getsq['id']) . ";");
                                }
                            }

                            $newpwd = !empty($_POST['passwd']) ? "`pwd` = '" . hash('sha256', $_POST['passwd']) . "', `pwd_md5` = 0," : "";
                            $update_level = $_POST['level'] == 'banned' ? 0 : $_POST['level'];
                            $update_banned = $_POST['level'] == 'banned' ? 1 : 0;
                            db("UPDATE `" . $db['users'] . "` SET " . $newpwd .
                                " `nick` = '" . up($_POST['nick']) .
                                "', `email` = '" . up($_POST['email']) .
                                "', `user` = '" . up($_POST['loginname']) .
                                "',`listck` = " . (isset($_POST['listck']) ? ((int)$_POST['listck']) : 0) .
                                ", `level`  = " . ((int)$update_level) .
                                ", `banned`  = " . ((int)$update_banned) .
                                " WHERE `id` = " . $edit_userid . ";");

                            setIpcheck("upduser(" . $userid . "_" . $edit_userid . ")");
                        }

                        $index = info(_admin_user_edited, "?action=userlist");
                    } else {
                        $index = error(_error_edit_admin, 1);
                    }
                    break;
                case 'updateme':
                    db("DELETE FROM `" . $db['squaduser'] . "` WHERE `user` = " . $userid . ";");
                    db("DELETE FROM `" . $db['userpos'] . "` WHERE `user` = " . $userid . ";");

                    $squads = db("SELECT id FROM " . $db['squads']);
                    while ($getsq = _fetch($squads)) {
                        if (isset($_POST['squad' . $getsq['id']])) {
                            db("INSERT INTO `" . $db['squaduser'] . "` SET `user`  = " . ((int)$userid) .
                                ",`squad` = " . ((int)$_POST['squad' . $getsq['id']]) . ";");
                        }

                        if (isset($_POST['squad' . $getsq['id']])) {
                            db("INSERT INTO `" . $db['userpos'] . "` SET `user` = " . ((int)$userid) .
                                ", `posi`  = " . ((int)$_POST['sqpos' . $getsq['id']]) .
                                ", `squad` = " . ((int)$getsq['id']) . ";");
                        }
                    }

                    $index = info(_admin_user_edited, "?action=user&amp;id=" . $userid . "");
                    break;
                case 'delete':
                    $index = show(_user_delete_verify, array("user" => autor($identy_userid), "id" => $identy_userid));
                    if (isset($_GET['verify']) && $_GET['verify'] == "yes") {
                        if ((data("level", $identy_userid) == 4 || rootAdmin($identy_userid) || data("level", $identy_userid) == 3) && !rootAdmin($userid))
                            $index = error(_user_cant_delete_admin, 2);
                        else {
                            $getdel = db("SELECT * FROM `" . $db['users'] . "` WHERE `id` = " . $identy_userid . ";", false, true);
                            setIpcheck("deluser(" . $userid . "_" . $getdel['id'] . ")");

                            $ips = array();
                            $ips[] = $getdel['ip'];

                            $qry = db("SELECT * FROM `" . $db['f_threads'] . "` WHERE `t_reg` = " . $getdel['id'] . ";");
                            while ($get = _fetch($qry)) {
                                $ips[$get['ip']] = true;
                                if (!db("SELECT `id` FROM `" . $db['f_posts'] . "` WHERE `sid` = " . $get['id'] . ";", true)) {
                                    //Delete Thread, no Posts!
                                    db("DELETE FROM `" . $db['f_threads'] . "` WHERE `id` = " . $get['id'] . ";");
                                    db("DELETE FROM `" . $db['f_abo'] . "` WHERE `fid` = " . $get['id'] . ";");
                                } else {
                                    //Suche Zitate und anonymisieren
                                    $qry = db("SELECT `text`,`id` FROM `" . $db['f_posts'] . "` WHERE `sid` = " . $get['id'] . ";");
                                    while ($get_post = _fetch($qry)) {
                                        $text = re($get_post['text']);
                                        $text = str_replace(array(re(data('nick', $get['t_reg'])), utor($get['t_reg'])), __dsgvo_deleted_user, $text);
                                        db("UPDATE `" . $db['f_posts'] . "` SET `text` = '" . up($text) . "' WHERE `id` = " . $get_post['id'] . ";");
                                    }

                                    //Anonym User for Thread
                                    db("UPDATE `" . $db['f_threads'] . "` SET " .
                                        "`t_nick` = '', " .
                                        "`t_reg` = 0, " .
                                        "`t_email` = '', " .
                                        "`t_text` = '', " .
                                        "`edited` = '', " .
                                        "`t_hp` = '', " .
                                        "`ip` = '', " .
                                        "`dsgvo` = 1, " .
                                        "WHERE `id` = " . $get['id'] . ";");
                                }
                            }

                            //Save IPS
                            $qry = db("SELECT `ip` FROM `" . $db['f_posts'] . "` WHERE `reg` = " . $getdel['id'] . ";");
                            while ($get = _fetch($qry)) {
                                $ips[] = $get['ip'];
                            }

                            $qry = db("SELECT `ip` FROM `" . $db['gb'] . "` WHERE `reg` = " . $getdel['id'] . ";");
                            while ($get = _fetch($qry)) {
                                $ips[] = $get['ip'];
                            }

                            $qry = db("SELECT `ip` FROM `" . $db['acomments'] . "` WHERE `reg` = " . $getdel['id'] . ";");
                            while ($get = _fetch($qry)) {
                                $ips[] = $get['ip'];
                            }

                            $qry = db("SELECT `ip` FROM `" . $db['cw_comments'] . "` WHERE `reg` = " . $getdel['id'] . ";");
                            while ($get = _fetch($qry)) {
                                $ips[] = $get['ip'];
                            }

                            $qry = db("SELECT `ip` FROM `" . $db['newscomments'] . "` WHERE `reg` = " . $getdel['id'] . ";");
                            while ($get = _fetch($qry)) {
                                $ips[] = $get['ip'];
                            }

                            db("DELETE FROM `" . $db['f_posts'] . "` WHERE `reg` = " . $getdel['id'] . " OR `email` = '" . $getdel['email'] . "';");
                            db("DELETE FROM `" . $db['f_abo'] . "` WHERE `user` = " . $getdel['id'] . ";");
                            db("DELETE FROM `" . $db['f_access'] . "` WHERE `user` = " . $getdel['id'] . ";");
                            db("DELETE FROM `" . $db['newscomments'] . "` WHERE `reg` = " . $getdel['id'] . ";");
                            db("DELETE FROM `" . $db['acomments'] . "` WHERE `reg` = " . $getdel['id'] . ";");
                            db("DELETE FROM `" . $db['msg'] . "` WHERE `von` = " . (int)($getdel['id']) . " OR `an` = " . $getdel['id'] . ";");
                            db("DELETE FROM `" . $db['news'] . "` WHERE `autor` = " . (int)($getdel['id']) . ";");
                            db("DELETE FROM `" . $db['permissions'] . "` WHERE `user` = " . (int)($getdel['id']) . ";");
                            db("DELETE FROM `" . $db['squaduser'] . "` WHERE `user` = " . (int)($getdel['id']) . ";");
                            db("DELETE FROM `" . $db['buddys'] . "` WHERE `user` = " . (int)($getdel['id']) . " OR `buddy` = " . $getdel['id'] . ";");
                            db("DELETE FROM `" . $db['usergb'] . "` WHERE reg = " . (int)($getdel['id']) . ";");
                            db("DELETE FROM `" . $db['userpos'] . "` WHERE `user` = " . (int)($getdel['id']) . ";");
                            db("DELETE FROM `" . $db['users'] . "` WHERE `id` = " . (int)($getdel['id']) . ";");
                            db("DELETE FROM `" . $db['userstats'] . "` WHERE `user` = " . (int)($getdel['id']) . ";");
                            db("DELETE FROM `" . $db['shout'] . "` WHERE `email` = '" . $getdel['email'] . "';");
                            db("DELETE FROM `" . $db['gb'] . "` WHERE `email` = '" . $getdel['email'] . "';");
                            db("DELETE FROM `" . $db['cw_comments'] . "` WHERE `email` = '" . $getdel['email'] . "' OR `reg` = " . $getdel['id'] . ";");
                            db("DELETE FROM `" . $db['cw_player'] . "` WHERE `member` = " . $getdel['id'] . ";");
                            db("DELETE FROM `" . $db['clankasse'] . "` WHERE `member` = " . $getdel['id'] . ";");
                            db("DELETE FROM `" . $db['away'] . "` WHERE `userid` = " . $getdel['id'] . ";");
                            db("DELETE FROM `" . $db['usergallery'] . "` WHERE `user` = " . $getdel['id'] . ";");

                            //IP-Check Loop
                            foreach ($ips as $ip) {
                                if(is_array($ip))
                                    continue;

                                if(!empty($ip)) {
                                    if (!validateIpV4Range((string)$ip, ['[192].[168].[0-255].[0-255]', '[127].[0].[0-255].[0-255]',
                                        '[10].[0-255].[0-255].[0-255]', '[172].[16-31].[0-255].[0-255]'])) {
                                        db("DELETE FROM `" . $db['acomments'] . "` WHERE `ip` = '" . $ip . "';");
                                        db("DELETE FROM `" . $db['c_ips'] . "` WHERE `ip` = '" . $ip . "';");
                                        db("DELETE FROM `" . $db['c_who'] . "` WHERE `ip` = '" . $ip . "';");
                                        db("DELETE FROM `" . $db['cw_comments'] . "` WHERE `ip` = '" . $ip . "';");
                                        db("DELETE FROM `" . $db['ipcheck'] . "` WHERE `ip` = '" . $ip . "' OR `user_id` = " . $getdel['id'] . ";");
                                        db("DELETE FROM `" . $db['newscomments'] . "` WHERE `ip` = '" . $ip . "';");
                                        db("DELETE FROM `" . $db['shout'] . "` WHERE `ip` = '" . $ip . "';");
                                        db("DELETE FROM `" . $db['usergb'] . "` WHERE `ip` = '" . $ip . "';");
                                    }
                                }
                            } unset($ips);

                            foreach ($picformat as $tmpendung) {
                                if (file_exists(basePath . "/inc/images/uploads/userpics/" . (int)($getdel['id']) . "." . $tmpendung))
                                    @unlink(basePath . "/inc/images/uploads/userpics/" . (int)($getdel['id']) . "." . $tmpendung);

                                if (file_exists(basePath . "/inc/images/uploads/useravatare/" . (int)($getdel['id']) . "." . $tmpendung))
                                    @unlink(basePath . "/inc/images/uploads/useravatare/" . (int)($getdel['id']) . "." . $tmpendung);
                            }

                            $index = info(_user_deleted, "?action=userlist");
                        }
                    }
                    break;
                default:
                    if (!rootAdmin($edit_userid) || rootAdmin($userid)) {
                        $qry = db("SELECT `id`,`user`,`nick`,`pwd`,`email`,`level`,`position`,`listck` FROM `" . $db['users'] . "` WHERE `id` = " . $edit_userid . ";");
                        if (_rows($qry)) {
                            $where = _user_profile_of . 'autor_' . $edit_userid;
                            $get = _fetch($qry);
                            $selu = $get['level'] == 1 ? 'selected="selected"' : '';
                            $selt = $get['level'] == 2 ? 'selected="selected"' : '';
                            $selm = $get['level'] == 3 ? 'selected="selected"' : '';
                            $sela = $get['level'] == 4 ? 'selected="selected"' : '';

                            $qrysq = db("SELECT `id`,`name` FROM `" . $db['squads'] . "` ORDER BY `pos`;");
                            $esquads = '';
                            while ($getsq = _fetch($qrysq)) {
                                $qrypos = db("SELECT `id`,`position` FROM `" . $db['pos'] . "` ORDER BY `pid`;");
                                $posi = "";
                                while ($getpos = _fetch($qrypos)) {
                                    $check = db("SELECT `id` FROM `" . $db['userpos'] . "` WHERE `posi` = " . $getpos['id'] .
                                        " AND `squad` = " . $getsq['id'] . " AND `user` = " . (int)($_GET['edit']) . ";", true);

                                    $sel = $check ? 'selected="selected"' : '';
                                    $posi .= show(_select_field_posis, array("value" => $getpos['id'], "sel" => $sel, "what" => re($getpos['position'])));
                                }

                                $checksquser = db("SELECT `squad` FROM `" . $db['squaduser'] . "` WHERE `user` = " . $edit_userid . " AND `squad` = " . $getsq['id'] . ";", true);

                                $check = $checksquser ? 'checked="checked"' : '';
                                $esquads .= show(_checkfield_squads, array("id" => $getsq['id'],
                                    "check" => $check,
                                    "eposi" => $posi,
                                    "noposi" => _user_noposi,
                                    "squad" => re($getsq['name'])));
                            }

                            $get_identy = show(_admin_user_get_identitat, array("id" => $edit_userid));
                            $editpwd = show($dir . "/admin_editpwd", array("pwd" => _new_pwd, "epwd" => ""));

                            if ($chkMe == 4) {
                                $elevel = show(_elevel_admin_select, array("selu" => $selu,
                                    "selt" => $selt,
                                    "selm" => $selm,
                                    "sela" => $sela,
                                    "ruser" => _status_user,
                                    "banned" => _admin_level_banned,
                                    "trial" => _status_trial,
                                    "member" => _status_member,
                                    "admin" => _status_admin));
                            } elseif (permission("editusers")) {
                                $elevel = show(_elevel_perm_select, array("selu" => $selu,
                                    "selt" => $selt,
                                    "selm" => $selm,
                                    "ruser" => _status_user,
                                    "banned" => _admin_level_banned,
                                    "trial" => _status_trial,
                                    "member" => _status_member));
                            }

                            $qry = db("SELECT * FROM `" . $db['dsgvo_log'] . "` WHERE `uid` = " . $get['id'] . ";");
                            if (_rows($qry)) {
                                $get_dsgvo = _fetch($qry);
                                $dsgvo = show($dir . "/admin_dsgvo_tb", array("ip" => $get_dsgvo['ip'],
                                    "date" => date('d.m.Y - H:i:s', $get_dsgvo['date']),
                                    "agent" => $get_dsgvo['agent']));
                            } else {
                                $dsgvo = _admin_dsgvo_lock;
                            }

                            $index = show($dir . "/admin", array("enick" => re($get['nick']),
                                "user" => $edit_userid,
                                "value" => _button_value_edit,
                                "eemail" => re($get['email']),
                                "eloginname" => $get['user'],
                                "esquad" => $esquads,
                                "editpwd" => $editpwd,
                                "eposi" => $posi,
                                "rechte" => _config_positions_rights,
                                "getpermissions" => getPermissions($edit_userid),
                                "getboardpermissions" => getBoardPermissions($edit_userid),
                                "forenrechte" => _config_positions_boardrights,
                                "showpos" => getrank($_GET['edit']),
                                "nothing" => _nothing,
                                "listck" => (empty($get['listck']) ? '' : ' checked="checked"'),
                                "clankasse" => _user_list_ck,
                                "auth_info" => _admin_user_clanhead_info,
                                "alvl" => $get['level'],
                                "elevel" => $elevel,
                                "level_info" => _level_info,
                                "gallery" => _admin_user_gallery,
                                "dsgvo" => $dsgvo,
                                "dsgvo_log" => _admin_dsgvo_log,
                                "yes" => _yes,
                                "no" => _no,
                                "cw_info" => _cw_info,
                                "edithead" => _admin_user_edithead,
                                "personalhead" => _admin_user_personalhead,
                                "squadhead" => _admin_user_squadhead,
                                "clanhead" => _admin_user_clanhead,
                                "nick" => _nick,
                                "email" => _email,
                                "loginname" => _loginname,
                                "identitat" => _admin_user_identitat,
                                "get" => $get_identy,
                                "squad" => _member_admin_squad,
                                "newsletter" => _member_admin_newsletter,
                                "downloads" => _member_admin_downloads,
                                "links" => _member_admin_links,
                                "votes" => _member_admin_votes,
                                "votesadmin" => _member_admin_votesadmin,
                                "gb" => _member_admin_gb,
                                "forum" => _member_admin_forum,
                                "intnews" => _member_admin_intnews,
                                "intforum" => _member_admin_intforums,
                                "forums" => _forum,
                                "access" => _access,
                                "news" => _member_admin_news,
                                "clanwars" => _member_admin_clanwars,
                                "posi" => _profil_position,
                                "level" => _admin_user_level,
                                "ck" => _admin_user_clankasse,
                                "sl" => _admin_user_serverliste,
                                "eu" => _admin_user_edituser,
                                "et" => _admin_user_edittactics,
                                "esq" => _admin_user_editsquads,
                                "eserver" => _admin_user_editserver,
                                "ek" => _admin_user_editkalender));
                        } else
                            $index = error(_user_dont_exist, 1);
                    } else {
                        $index = error(_error_edit_admin, 1);
                    }
                    break;
            }
        } else if ($edit_userid && data("level", $userid) == 4 && rootAdmin($edit_userid)) {
            $index = error(_error_edit_admin, 1);
        }
    }
}
