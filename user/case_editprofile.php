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
    $where = _site_user_editprofil;
    if (!common::$chkMe) {
        $index = common::error(_error_have_to_be_logged, 1);
    } else {
            switch (common::$do) {
                case 'delete':
                    if(!common::rootAdmin(common::$userid)) {
                        $getdel = common::$sql['default']->fetch("SELECT `id`,`nick`,`email`,`hp` FROM `{prefix_users}` WHERE `id` = ?;", [common::$userid]);
                        if(common::$sql['default']->rowCount()) {
                            common::$sql['default']->update("UPDATE `{prefix_forum_threads}` SET `t_nick` = ?, `t_email` = ?, `t_hp` = ?, `t_reg` = 0, WHERE t_reg = ?;",
                            [$getdel['nick'],$getdel['email'],stringParser::encode(common::links($getdel['hp'])),$getdel['id']]);
                            common::$sql['default']->update("UPDATE `{prefix_forum_posts}` SET `nick` = ?, `email` = ?, `hp` = ?, WHERE `reg` = ?;",
                            [$getdel['nick'],$getdel['email'],stringParser::encode(common::links($getdel['hp'])),$getdel['id']]);
                            common::$sql['default']->update("UPDATE `{prefix_news_comments}` SET `nick` = ?,`email` = ?, `hp` = ?, `reg` = 0, WHERE `reg` = ?;",
                            [$getdel['nick'],$getdel['email'],stringParser::encode(common::links($getdel['hp'])),$getdel['id']]);
                            common::$sql['default']->update("UPDATE `{prefix_artikel_comments}` SET `nick` = ?, `email` = ?, `hp` = ?, `reg` = 0, WHERE `reg` = ?;",
                            [$getdel['nick'],$getdel['email'],stringParser::encode(common::links($getdel['hp'])),$getdel['id']]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_messages}` WHERE `von` = ? OR   `an`  = ?;", [$getdel['id'],$getdel['id']]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_news}` WHERE `autor` = ?;", [$getdel['id']]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_permissions}` WHERE `user` = ?;", [$getdel['id']]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_group_user}` WHERE `user` = ?;", [$getdel['id']]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_user_buddys}` WHERE `user` = ? OR `buddy` = ?;", [$getdel['id'],$getdel['id']]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_user_stats}` WHERE `user` = ?;", [$getdel['id']]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_users}` WHERE `id` = ?;", [$getdel['id']]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_user_stats}` WHERE `user` = ?;", [$getdel['id']]);
                            common::$sql['default']->delete("DELETE FROM `{prefix_clicks_ips}` WHERE `uid` = ?;", [$getdel['id']]);

                            ## Losche User-Upload Ordner ##
                            fileman::RemoveUserDir($getdel['id']);

                            $files = common::get_files(basePath."/inc/images/uploads/userpics/",false,true, common::SUPPORTED_PICTURE);
                            foreach ($files as $file) {
                                if(preg_match("#".$getdel['id']."_(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                                    $res = preg_match("#".$getdel['id']."_(.*)#",$file,$match);
                                    if (file_exists(basePath."/inc/images/uploads/userpics/".$getdel['id']."_".$match[1])) {
                                        unlink(basePath."/inc/images/uploads/userpics/".$getdel['id']."_".$match[1]);
                                    }
                                }
                            }

                            $files = common::get_files(basePath."/inc/images/uploads/useravatare/",false,true, common::SUPPORTED_PICTURE);
                            foreach ($files as $file) {
                                if(preg_match("#".$getdel['id']."_(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                                    $res = preg_match("#".$getdel['id']."_(.*)#",$file,$match);
                                    if (file_exists(basePath."/inc/images/uploads/useravatare/".$getdel['id']."_".$match[1])) {
                                        unlink(basePath."/inc/images/uploads/useravatare/".$getdel['id']."_".$match[1]);
                                    }
                                }
                            }

                            foreach (common::SUPPORTED_PICTURE as $tmpendung) {
                                if (file_exists(basePath . "/inc/images/uploads/userpics/" . (int)($getdel['id']) . "." . $tmpendung)) {
                                    @unlink(basePath . "/inc/images/uploads/userpics/" . (int)($getdel['id']) . "." . $tmpendung);
                                }

                                if (file_exists(basePath . "/inc/images/uploads/useravatare/" . (int)($getdel['id']) . "." . $tmpendung)) {
                                    @unlink(basePath . "/inc/images/uploads/useravatare/" . (int)($getdel['id']) . "." . $tmpendung);
                                }
                            }

                            common::dzcp_session_destroy();
                            $index = common::info(_info_account_deletet, '../news/', 5, false);
                        }
                    }
                break;
                default:
                    if(isset($_POST) && !isset($_GET['show']) && array_key_exists('user',$_POST)) {
                        $check_user = false; $check_nick = false; $check_email = false;
                        if (common::$sql['default']->rows("SELECT `id` FROM `{prefix_users}` WHERE (`user`= ? OR `nick`= ? OR `email`= ?) AND `id` != ?;",
                            [stringParser::encode($_POST['user']), stringParser::encode($_POST['nick']), stringParser::encode($_POST['email']), common::$userid])
                        ) {
                            $check_user = common::$sql['default']->rows("SELECT `id` FROM `{prefix_users}` WHERE `user` = ? AND `id` != ?;",
                                [stringParser::encode($_POST['user']), common::$userid]);
                            $check_nick = common::$sql['default']->rows("SELECT `id` FROM `{prefix_users}` WHERE `nick` = ? AND `id` != ?;",
                                [stringParser::encode($_POST['nick']), common::$userid]);
                            $check_email = common::$sql['default']->rows("SELECT `id` FROM `{prefix_users}` WHERE `email`= ? AND `id` != ?;",
                                [stringParser::encode($_POST['email']), common::$userid]);
                        }

                        if (!isset($_POST['user']) || empty($_POST['user'])) {
                            notification::add_error(_empty_user);
                        } elseif (!isset($_POST['nick']) || empty($_POST['nick'])) {
                            notification::add_error(_empty_user);
                        } elseif (!isset($_POST['email']) || empty($_POST['email'])) {
                            notification::add_error(_empty_email);
                        } elseif (!isset($_POST['email']) || !common::check_email($_POST['email'])) {
                            notification::add_error(_empty_user);
                        } elseif ($check_user) {
                            notification::add_error(_error_user_exists);
                        } elseif ($check_nick) {
                            notification::add_error(_error_nick_exists);
                        } elseif ($check_email) {
                            notification::add_error(_error_email_exists);
                        } else {
                            $newpwd = "";
                            if (isset($_POST['pwd']) && !empty($_POST['pwd'])) {
                                if (common::pwd_encoder($_POST['pwd']) == common::pwd_encoder($_POST['cpwd'])) {
                                    $_SESSION['pwd'] = common::pwd_encoder($_POST['pwd']);
                                    $newpwd = "`pwd` = '" . stringParser::encode($_SESSION['pwd']) . "',";
                                    $newpwd .= "`pwd_encoder` = " . settings::get('default_pwd_encoder') . ",";
                                } else {
                                    notification::add_error(_error_passwords_dont_match);
                                }
                            }

                            $bday = ($_POST['t'] && $_POST['m'] && $_POST['j'] ? common::cal($_POST['t']) . "." . common::cal($_POST['m']) . "." . $_POST['j'] : 0);
                            common::$sql['default']->update("UPDATE `{prefix_users}` SET " . $newpwd . " `country` = ?,`user` = ?, `nick` = ?, `rlname` = ?, `sex` = ?, "
                                    . "`bday` = ?, `email` = ?, `nletter` = ?, `pnmail` = ?, `city` = ?, `hp` = ?,"
                                    . "`signatur` = ?,`beschreibung` = ?, `startpage` = ?, `profile_access` = ?"
                                    . " WHERE id = ?;", [stringParser::encode($_POST['land']), stringParser::encode($_POST['user']),
                                    stringParser::encode($_POST['nick']), stringParser::encode($_POST['rlname']),
                                    (int)($_POST['sex']),
                                    (!$bday ? 0 : strtotime($bday)), stringParser::encode($_POST['email']), (int)($_POST['nletter']), (int)($_POST['pnmail']), stringParser::encode($_POST['city']),
                                    stringParser::encode(common::links($_POST['hp'])), stringParser::encode($_POST['sig']), stringParser::encode($_POST['ich']),
                                    (int)($_POST['startpage']), (int)($_POST['visibility_profile']), common::$userid]);

                            notification::add_success(_info_edit_profile_done);
                        }
                    }

                    $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ?;", [common::$userid]);
                    switch(isset($_GET['show']) ? $_GET['show'] : '') {
                        case 'almgr':
                            switch (common::$do) {
                                case 'self_add':
                                    $permanent_key = md5(common::mkpwd(8));
                                    if(common::$sql['default']->rows("SELECT `id` FROM `{prefix_autologin}` WHERE `host` = ?;", [gethostbyaddr(common::$userip['v4'])])) {
                                        //Update Autologin
                                        common::$sql['default']->update("UPDATE `{prefix_autologin}` SET "
                                                          . "`ssid` = ?, "
                                                          . "`pkey` = ?, "
                                                          . "`ipv4` = ?, "
                                                          . "`date` = ?, "
                                                          . "`update` = ?, "
                                                          . "`expires` = ? "
                                                    . "WHERE `host` = ?;", 
                                        [session_id(),$permanent_key,common::$userip['v4'],$time=time(),$time,autologin_expire,
                                              gethostbyaddr(common::$userip['v4'])]);
                                    } else {
                                        //Insert Autologin
                                        common::$sql['default']->insert("INSERT INTO `{prefix_autologin}` SET "
                                                               . "`uid` = ?,"
                                                               . "`ssid` = ?,"
                                                               . "`pkey` = ?,"
                                                               . "`ipv4` = ?,"
                                                               . "`name` = ?, "
                                                               . "`host` = ?,"
                                                               . "`date` = ?,"
                                                               . "`update` = 0,"
                                                               . "`expires` = ?;",
                                        [$get['id'],session_id(),$permanent_key,common::$userip['v4'],
                                            common::cut(gethostbyaddr(common::$userip['v4']),20), gethostbyaddr(common::$userip['v4']),
                                            $time=time(),autologin_expire]);
                                    }
                                    
                                    cookie::set('id', $get['id']);
                                    cookie::set('pkey', $permanent_key);
                                    cookie::save(); unset($permanent_key);
                                    notification::add_success(_info_almgr_self_added);
                                break;
                                case 'self_remove':
                                    if(common::$sql['default']->rows("SELECT `id` FROM `{prefix_autologin}` WHERE `host` = ? AND `ssid` = ?;", [gethostbyaddr(common::$userip['v4']), session_id()])) {
                                        common::$sql['default']->delete("DELETE FROM `{prefix_autologin}` WHERE `ssid` = ?;", [session_id()]);
                                        cookie::delete('pkey');
                                        cookie::delete('id');
                                        cookie::save();
                                        notification::add_success(_info_almgr_self_deletet);
                                    }
                                break;
                                case 'almgr_delete':
                                    if(common::$sql['default']->rows("SELECT `id` FROM `{prefix_autologin}` WHERE `id` = ?;", [(int)($_GET['id'])])) {
                                        common::$sql['default']->delete("DELETE FROM `{prefix_autologin}` WHERE `id` = ?;", [(int)($_GET['id'])]);
                                        cookie::delete('pkey');
                                        cookie::delete('id');
                                        cookie::save();
                                        notification::add_success(_info_almgr_deletet);
                                    }
                                break;
                                case 'almgr_edit':
                                    $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_autologin}` WHERE `id` = ?;", [(int)($_GET['id'])]);
                                    if(common::$sql['default']->rowCount()) {
                                        $smarty->caching = false;
                                        $smarty->assign('name',stringParser::decode($get['name']));
                                        $smarty->assign('id',stringParser::decode($get['id']));
                                        $smarty->assign('host',stringParser::decode($get['host']));
                                        $smarty->assign('ip',stringParser::decode($get['ipv4']));
                                        $smarty->assign('ssid',stringParser::decode($get['ssid']));
                                        $smarty->assign('pkey',stringParser::decode($get['pkey']));
                                        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/profil/edit_almgr_from.tpl');
                                        $smarty->clearAllAssign();
                                    }
                                break;
                                case 'almgr_edit_save':
                                    if(common::$sql['default']->rows("SELECT id FROM `{prefix_autologin}` WHERE `id` = ?;", [(int)($_GET['id'])])) {
                                        common::$sql['default']->update("UPDATE `{prefix_autologin}` SET `name` = ? WHERE `id` = ?;",
                                            [stringParser::encode($_POST['name']), (int)($_GET['id'])]);
                                        notification::add_success(_almgr_editd);
                                    }
                                break;
                            }
                            
                            if(empty($index)) {
                                $qry = common::$sql['default']->select("SELECT * FROM `{prefix_autologin}` WHERE `uid` = ?;",
                                    [common::$userid]); $almgr = ""; $color = 0;
                                if(common::$sql['default']->rowCount()) {
                                    foreach($qry as $get) {
                                        //delete button
                                        $smarty->caching = false;
                                        $smarty->assign('id',$get['id']);
                                        $delete = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/profil/almgr_delete.tpl');
                                        $smarty->clearAllAssign();

                                        //edit button
                                        $smarty->caching = false;
                                        $smarty->assign('id',$get['id']);
                                        $edit = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/profil/almgr_edit.tpl');
                                        $smarty->clearAllAssign();

                                        //show list
                                        $smarty->caching = false;
                                        $smarty->assign('delete',$delete);
                                        $smarty->assign('edit',$edit);
                                        $smarty->assign('color',$color);
                                        $smarty->assign('name',stringParser::decode($get['name']));
                                        $smarty->assign('host',stringParser::decode($get['host']));
                                        $smarty->assign('ip',$get['ipv4']);
                                        $smarty->assign('create',date('d.m.Y',$get['date']));
                                        $smarty->assign('lused',!$get['update'] ? '-' : date('d.m.Y',$get['update']));
                                        $smarty->assign('expires',date('d.m.Y',((!$get['update'] ? time() : $get['update'])+$get['expires'])));
                                        $almgr .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/profil/edit_almgr_show.tpl');
                                        $smarty->clearAllAssign();
                                        $color++;
                                    }
                                }

                                //Empty
                                if(empty($almgr)) {
                                    $smarty->caching = false;
                                    $smarty->assign('colspan',6);
                                    $almgr = $smarty->fetch('string:'._no_entrys_yet);
                                    $smarty->clearAllAssign();
                                }

                                if(empty($show)) {
                                    $smarty->caching = false;
                                    $smarty->assign('showalmgr',$almgr);
                                    $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/profil/edit_almgr.tpl');
                                    $smarty->clearAllAssign();
                                }
                            }
                        break;
                        default:
                            $sex = ($get['sex'] == 1 ? _pedit_male : ($get['sex'] == 2 ? _pedit_female : _pedit_sex_ka));
                            $levels = [0,1,2,3]; $perm_profile = "";
                            foreach ($levels as &$level) {
                                $selected = ($level == $get['profile_access']);
                                switch ($level) {
                                    case 0:
                                        $perm_profile .= common::select_field($level,$selected,_pedit_perm_public);
                                        break;
                                    case 1:
                                        $perm_profile .= common::select_field($level,$selected,_pedit_perm_user);
                                        break;
                                    case 2:
                                        $perm_profile .= common::select_field($level,$selected,_pedit_perm_member);
                                        break;
                                    case 3:
                                        $perm_profile .= common::select_field($level,$selected,_pedit_perm_admin);
                                        break;
                                }
                            }
                            
                            // Startpage
                            $sql_startpage = common::$sql['default']->select("SELECT `name`,`id` FROM `{prefix_startpage}`;");
                            $startpage = common::select_field(0,false,_userlobby);
                            if(common::$sql['default']->rowCount()) {
                                foreach($sql_startpage as $get_startpage) {
                                    $startpage .= common::select_field($get_startpage['id'],($get_startpage['id'] == $get['startpage']),stringParser::decode($get_startpage['name']));
                                }
                            }

                            $bdayday = 0; $bdaymonth = 0; $bdayyear = 0;
                            if (!empty($get['bday']) && $get['bday'])
                                list($bdayday, $bdaymonth, $bdayyear) = explode('.', date('d.m.Y', $get['bday']));

                            $dropdown_age = common::dropdown_date(common::dropdown("day", $bdayday, 1),
                                common::dropdown("month", $bdaymonth, 1),
                                common::dropdown("year", $bdayyear, 1));

                            $pnl = ($get['nletter'] ? 'checked="checked"' : '');
                            $pnm = ($get['pnmail'] ? 'checked="checked"' : '');

                            $pic = common::userpic($get['id']); $deletepic = '';
                            if (!preg_match("#nopic#", $pic))
                                $deletepic = "| " . _profil_delete_pic;

                            $avatar = common::useravatar($get['id']); $deleteava = '';
                            if (!preg_match("#noavatar#", $avatar))
                                $deleteava = "| " . _profil_delete_ava;

                            if (common::rootAdmin(common::$userid))
                                $delete = _profil_del_admin;
                            else {
                                $smarty->caching = false;
                                $smarty->assign('id',$get['id']);
                                $delete = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/profil/delete_account.tpl');
                                $smarty->clearAllAssign();
                            }

                            $smarty->caching = false;
                            $smarty->assign('country',common::show_countrys($get['country']));
                            $smarty->assign('city',stringParser::decode($get['city']));
                            $smarty->assign('pnl',$pnl);
                            $smarty->assign('pnm',$pnm);
                            $smarty->assign('pwd','');
                            $smarty->assign('dropdown_age',$dropdown_age);
                            $smarty->assign('ava',$avatar);
                            $smarty->assign('hp',stringParser::decode($get['hp']));
                            $smarty->assign('nick',stringParser::decode($get['nick']));
                            $smarty->assign('name',stringParser::decode($get['user']));
                            $smarty->assign('rlname',stringParser::decode($get['rlname']));
                            $smarty->assign('bdayday',$bdayday);
                            $smarty->assign('bdaymonth',$bdaymonth);
                            $smarty->assign('bdayyear',$bdayyear);
                            $smarty->assign('sex',$sex);
                            $smarty->assign('email',stringParser::decode($get['email']));
                            $smarty->assign('visibility_profile',$perm_profile);
                            $smarty->assign('sig',stringParser::decode($get['signatur']));
                            $smarty->assign('pic',$pic);
                            $smarty->assign('deleteava',$deleteava);
                            $smarty->assign('deletepic',$deletepic);
                            $smarty->assign('startpage',$startpage);
                            $smarty->assign('position',common::getrank($get['id']));
                            $smarty->assign('ich',stringParser::decode($get['beschreibung']));
                            $smarty->assign('delete',$delete);
                            $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/profil/edit_profil.tpl');
                            $smarty->clearAllAssign();
                        break;
                    }

                    if(empty($index)) {
                        //profil head
                        $smarty->caching = false;
                        $smarty->assign('nick',common::autor(common::$userid));
                        $profil_edit_head = $smarty->fetch('string:'._profil_edit_head);
                        $smarty->clearAllAssign();

                        //index
                        $smarty->caching = false;
                        /** @var TYPE_NAME $show */
                        $smarty->assign('show',$show);
                        $smarty->assign('notification_page',notification::get());
                        $smarty->assign('profil_edit_head',$profil_edit_head);
                        $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/profil/edit.tpl');
                        $smarty->clearAllAssign();
                    }
                break;
            }
        }
}