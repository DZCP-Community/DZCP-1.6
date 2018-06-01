<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_user_editprofil;
    if(!$chkMe)
        $index = error(_error_have_to_be_logged, 1);
    else {
        if($do == "edit")    {
            $check_user = db_stmt("SELECT id FROM ".$db['users']." WHERE `user`= ? AND id != ?",
                array('is', $userid, up($_POST['user'])),true,false);

            $check_nick = db_stmt("SELECT id FROM ".$db['users']." WHERE `nick`= ? AND id != ?",
                array('is', $userid, up($_POST['nick'])),true,false);

            $check_email = db_stmt("SELECT id FROM ".$db['users']." WHERE `email`= ? AND id != ?",
                array('is', $userid, up($_POST['email'])),true,false);

            if(empty($_POST['user']))
            {
                $index = error(_empty_user, 1);
            } elseif(empty($_POST['nick'])) {
                $index = error(_empty_nick, 1);
            } elseif(empty($_POST['email'])) {
                $index = error(_empty_email, 1);
            } elseif(!check_email($_POST['email'])) {
                $index = error(_error_invalid_email, 1);
            } elseif($check_user) {
                $index = error(_error_user_exists, 1);
            } elseif($check_nick) {
                $index = error(_error_nick_exists, 1);
            } elseif($check_email) {
                $index = error(_error_email_exists, 1);
            } else {
                if ($_POST['pwd'])
                {
                    if ($_POST['pwd'] == $_POST['cpwd'])
                    {
                        $newpwd = "pwd = '".hash('sha256',$_POST['pwd'])."',";
                        $index = info(_info_edit_profile_done, "?action=user&amp;id=".$userid."");
                        $_SESSION['pwd'] = hash('sha256',$_POST['pwd']);

                        if(db("SELECT * FROM `".$db['users']."` WHERE `id` = ".$userid." AND `pwd_md5` = 1;",true)) {
                            db("UPDATE `".$db['users']."` SET `pwd_md5` = 0 WHERE `id` = ".$userid.";");
                        }
                    }
                    else
                        $index = error(_error_passwords_dont_match, 1);
                } else {
                    $newpwd = "";
                    $index = info(_info_edit_profile_done, "?action=user&amp;id=".$userid."");
                }

                $bday = ($_POST['t'] && $_POST['m'] && $_POST['j'] ? cal($_POST['t']).".".cal($_POST['m']).".".$_POST['j'] : 0);
                $qry = db("UPDATE `".$db['users']."` SET ".$newpwd."
                  `country`      = '"._real_escape_string($_POST['land'])."',
                  `user`         = '".up($_POST['user'])."',
                  `nick`         = '".up($_POST['nick'])."',
                  `rlname`       = '".up($_POST['rlname'])."',
                  `sex`          = ".((int)$_POST['sex']).",
                  `status`       = ".((int)$_POST['status']).",
                  `bday`         = '".(!$bday ? 0 : strtotime($bday))."',
                  `email`        = '".up($_POST['email'])."',
                  `nletter`      = ".((int)$_POST['nletter']).",
                  `pnmail`       = ".((int)$_POST['pnmail']).",
                  `city`         = '".up($_POST['city'])."',
                  `hp`           = '".up(links($_POST['hp']))."',
                  `psnid`        = '".up(trim($_POST['psnid']))."',
                  `originid`     = '".up(trim($_POST['originid']))."',
                  `battlenetid`  = '".up(trim($_POST['battlenetid']))."',
                  `steamid`      = '".up(trim($_POST['steamid']))."',
				  `skypename`    = '".up(trim($_POST['skypename']))."',
                  `signatur`     = '".up($_POST['sig'])."',
                  `beschreibung` = '".up($_POST['ich'])."',
                  `show`         = ".((int)($_POST['visibility_profile']))." 
                   WHERE `id` = ".$userid.";");
            }
        } elseif($do == "delete") {
            $getdel = db("SELECT * FROM `".$db['users']."` WHERE `id` = ".$userid.";",false,true);

            db("UPDATE ".$db['f_threads']."
                                     SET `t_nick`   = '".$getdel['nick']."',
                                             `t_email`  = '".$getdel['email']."',
                                             `t_hp`     = '".links($getdel['hp'])."',
                                             `t_reg`    = 0
                                     WHERE t_reg = '".(int)($getdel['id'])."'");

            db("UPDATE ".$db['f_posts']."
                                     SET `nick`   = '".$getdel['nick']."',
                                             `email`  = '".$getdel['email']."',
                                             `hp`            = '".links($getdel['hp'])."',
                                             `reg`        = '0'
                                     WHERE reg = '".(int)($getdel['id'])."'");

            db("UPDATE ".$db['newscomments']." SET `nick` = 'not_reg', `email` = '', `hp` = '', `comment` = 'not_reg', `editby` = '', `ip` = '0.0.0.0', `reg` = 0 WHERE `reg` = ".(int)($getdel['id']).";");
            db("UPDATE ".$db['acomments']." SET `nick` = 'not_reg', `email` = '', `hp` = '', `comment` = 'not_reg', `editby` = '', `reg` = 0 WHERE `reg` = ".(int)($getdel['id']).";");

            db("DELETE FROM `".$db['f_abo']."` WHERE `user` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['f_access']."` WHERE `user` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['msg']."` WHERE `von` = ".(int)($getdel['id'])." OR `an` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['news']."` WHERE `autor` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['permissions']."` WHERE `user` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['squaduser']."` WHERE `user` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['buddys']."` WHERE `user` = ".(int)($getdel['id'])." OR `buddy` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['userpos']."` WHERE `user` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['users']."` WHERE `id` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['userstats']."` WHERE `user` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['cw_comments']."` WHERE `email` = '".$getdel['email']."' OR `reg` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['shout']."` WHERE `email` = '".$getdel['email']."';");
            db("DELETE FROM `".$db['gb']."` WHERE `email` = '".$getdel['email']."';");
            db("DELETE FROM `".$db['cw_player']."` WHERE `member` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['away']."` WHERE `userid` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['usergallery']."` WHERE `user` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['ipcheck']."` WHERE `user_id` = ".$getdel['id'].";");

            db("UPDATE ".$db['usergb']." SET `reg` = 0 WHERE reg = ".(int)($getdel['id'])."");

            foreach($picformat as $tmpendung) {
                if(file_exists(basePath."/inc/images/uploads/userpics/".(int)($getdel['id']).".".$tmpendung))
                    @unlink(basePath."/inc/images/uploads/userpics/".(int)($getdel['id']).".".$tmpendung);

                if(file_exists(basePath."/inc/images/uploads/useravatare/".(int)($getdel['id']).".".$tmpendung))
                    @unlink(basePath."/inc/images/uploads/useravatare/".(int)($getdel['id']).".".$tmpendung);
            }

            $index = info(_info_account_deletet, '../news/');
        } elseif($do == "full_delete") {
            //Alles löschen
            $getdel = db("SELECT * FROM `".$db['users']."` WHERE `id` = ".$userid.";",false,true);

            $ips = array();
            $ips[$getdel['ip']] = true;

            $qry = db("SELECT * FROM `".$db['f_threads']."` WHERE `t_reg` = ".$getdel['id'].";");
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
            $qry = db("SELECT `ip` FROM `".$db['f_posts']."` WHERE `reg` = ".$getdel['id'].";");
            while ($get = _fetch($qry)) {
                $ips[$get['ip']] = true;
            }

            $qry = db("SELECT `ip` FROM `".$db['gb']."` WHERE `reg` = ".$getdel['id'].";");
            while ($get = _fetch($qry)) {
                $ips[$get['ip']] = true;
            }

            $qry = db("SELECT `ip` FROM `".$db['acomments']."` WHERE `reg` = ".$getdel['id'].";");
            while ($get = _fetch($qry)) {
                $ips[$get['ip']] = true;
            }

            $qry = db("SELECT `ip` FROM `".$db['cw_comments']."` WHERE `reg` = ".$getdel['id'].";");
            while ($get = _fetch($qry)) {
                $ips[$get['ip']] = true;
            }

            $qry = db("SELECT `ip` FROM `".$db['newscomments']."` WHERE `reg` = ".$getdel['id'].";");
            while ($get = _fetch($qry)) {
                $ips[$get['ip']] = true;
            }

            db("DELETE FROM `".$db['f_posts']."` WHERE `reg` = ".$getdel['id']." OR `email` = '".$getdel['email']."';");
            db("DELETE FROM `".$db['f_abo']."` WHERE `user` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['f_access']."` WHERE `user` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['newscomments']."` WHERE `reg` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['acomments']."` WHERE `reg` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['msg']."` WHERE `von` = ".(int)($getdel['id'])." OR `an` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['news']."` WHERE `autor` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['permissions']."` WHERE `user` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['squaduser']."` WHERE `user` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['buddys']."` WHERE `user` = ".(int)($getdel['id'])." OR `buddy` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['usergb']."` WHERE reg = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['userpos']."` WHERE `user` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['users']."` WHERE `id` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['userstats']."` WHERE `user` = ".(int)($getdel['id']).";");
            db("DELETE FROM `".$db['shout']."` WHERE `email` = '".$getdel['email']."';");
            db("DELETE FROM `".$db['gb']."` WHERE `email` = '".$getdel['email']."';");
            db("DELETE FROM `".$db['cw_comments']."` WHERE `email` = '".$getdel['email']."' OR `reg` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['cw_player']."` WHERE `member` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['clankasse']."` WHERE `member` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['away']."` WHERE `userid` = ".$getdel['id'].";");
            db("DELETE FROM `".$db['usergallery']."` WHERE `user` = ".$getdel['id'].";");

            //IP-Check Loop
            foreach ($ips as $ip => $null) {
                if(!validateIpV4Range($ip, array('[192].[168].[0-255].[0-255]','[127].[0].[0-255].[0-255]',
                    '[10].[0-255].[0-255].[0-255]','[172].[16-31].[0-255].[0-255]'))) {
                    db("DELETE FROM `" . $db['acomments'] . "` WHERE `ip` = '" . $ip . "';");
                    db("DELETE FROM `" . $db['c_ips'] . "` WHERE `ip` = '" . $ip . "';");
                    db("DELETE FROM `" . $db['c_who'] . "` WHERE `ip` = '" . $ip . "';");
                    db("DELETE FROM `" . $db['cw_comments'] . "` WHERE `ip` = '" . $ip . "';");
                    db("DELETE FROM `" . $db['ipcheck'] . "` WHERE `ip` = '" . $ip . "' OR `user_id` = " . $getdel['id'] . ";");
                    db("DELETE FROM `" . $db['newscomments'] . "` WHERE `ip` = '" . $ip . "';");
                    db("DELETE FROM `" . $db['shout'] . "` WHERE `ip` = '" . $ip . "';");
                    db("DELETE FROM `" . $db['usergb'] . "` WHERE `ip` = '" . $ip . "';");
                }
            } unset($ips);

            foreach($picformat as $tmpendung) {
                if(file_exists(basePath."/inc/images/uploads/userpics/".(int)($getdel['id']).".".$tmpendung))
                    @unlink(basePath."/inc/images/uploads/userpics/".(int)($getdel['id']).".".$tmpendung);

                if(file_exists(basePath."/inc/images/uploads/useravatare/".(int)($getdel['id']).".".$tmpendung))
                    @unlink(basePath."/inc/images/uploads/useravatare/".(int)($getdel['id']).".".$tmpendung);
            }

            $index = info(_info_edit_gallery_done, "../news/");
            exit();
        } else { //Show Profil
            $get = db("SELECT * FROM `".$db['users']."` WHERE `id` = ".$userid.";",false,true);

            //Sex
            $sex = _pedit_sex_ka;
            switch ($get['sex']) {
                case 1:
                    $sex = _pedit_male;
                    break;
                case 2:
                    $sex = _pedit_female;
                    break;
            }

            switch ($get['show']) {
                case 0:
                    $perm_profile = str_replace('value="0"', 'value="0" selected="selected"', _pedit_perm_profile);
                    break;
                case 1:
                    $perm_profile = str_replace('value="1"', 'value="1" selected="selected"', _pedit_perm_profile);
                    break;
                case 2:
                    $perm_profile = str_replace('value="2"', 'value="2" selected="selected"', _pedit_perm_profile);
                    break;
                default:
                case 4:
                    $perm_profile = str_replace('value="4"', 'value="4" selected="selected"', _pedit_perm_profile);
                    break;
            }

            $status = $get['status'] ? _pedit_aktiv : _pedit_inaktiv;

            $bdayday=0; $bdaymonth=0; $bdayyear=0;
            if(!empty($get['bday']) && $get['bday'])
                list($bdayday, $bdaymonth, $bdayyear) = explode('.', date('d.m.Y',$get['bday']));

            $dropdown_age = show(_dropdown_date, array( "day" => dropdown("day",$bdayday,1),
                "month" => dropdown("month",$bdaymonth,1),
                "year" => dropdown("year",$bdayyear,1)));

            $pnl = ''; $pnm = ''; $deleteava = ''; $deletepic = '';

            if($get['nletter'] == 1)
                $pnl = 'checked="checked"';

            if($get['pnmail'] == 1)
                $pnm = 'checked="checked"';

            $pic = userpic($get['id']);
            $avatar = useravatar($get['id']);

            if(!preg_match("#nopic#",$pic))
                $deletepic = "| "._profil_delete_pic;

            if(!preg_match("#noavatar#",$avatar))
                $deleteava = "| "._profil_delete_ava;

            if(rootAdmin($userid)) {
                $delete = _profil_del_admin;
                $delete_full = _profil_del_admin;
            } else {
                $delete = show("page/button_delete_account", array("id" => $get['id'],
                    "action" => "action=editprofile&amp;do=delete",
                    "value" => _button_title_del_account,
                    "del" => convSpace(_confirm_del_account)));

                $delete_full = show("page/button_delete_account", array("id" => $get['id'],
                    "action" => "action=editprofile&amp;do=full_delete",
                    "value" => _button_title_del_account,
                    "del" => convSpace(_confirm_del_account)));
            }

            $show = show($dir."/edit_profil", array("hardware" => _profil_hardware,
                "hphead" => _profil_hp,
                "visibility" => _pedit_visibility,
                "pvisibility_gb" => _pedit_visibility_gb,
                "pvisibility_gallery" => _pedit_visibility_gallery,
                "country" => show_countrys($get['country']),
                "pcountry" => _profil_country,
                "about" => _profil_about,
                "picturehead" => _profil_pic,
                "contact" => _profil_contact,
                "preal" => _profil_real,
                "pnick" => _nick,
                "pemail1" => _email,
                "php" => _hp,
                "pava" => _profil_avatar,
                "pbday" => _profil_bday,
                "psex" => _profil_sex,
                "pname" => _loginname,
                "ppwd" => _new_pwd,
                "cppwd" => _pwd2,
                "psig" => _profil_sig,
                "ppic" => _profil_ppic,
                "psnidl" => _psnid,
                "skypeidl" => _skypeid,
                "originidl" => _originid,
                "battlenetidl" => _battlenetid,
                "pcity" => _profil_city,
                "city" => re($get['city']),
                "psteamid" => _steamid,
                "v_steamid" => re($get['steamid']),
                "skypename" => re($get['skypename']),
                "nletter" => _profil_nletter,
                "pnmail" => _profil_pnmail,
                "pnl" => $pnl,
                "pnm" => $pnm,
                "pwd" => "",
                "dropdown_age" => $dropdown_age,
                "ava" => $avatar,
                "hp" => links(re($get['hp'])),
                "nick" => re($get['nick']),
                "name" => re($get['user']),
                "rlname" => re($get['rlname']),
                "bdayday" => $bdayday,
                "bdaymonth" => $bdaymonth,
                "bdayyear" =>$bdayyear,
                "sex" => $sex,
                "email" => re($get['email']),
                "visibility_profile" => $perm_profile,
                "sig" => re_bbcode($get['signatur']),
                "psnid" => re($get['psnid']),
                "originid" => re($get['originid']),
                "battlenetid" => re($get['battlenetid']),
                "pic" => $pic,
                "editpic" => _profil_edit_pic,
                "editava" => _profil_edit_ava,
                "deleteava" => $deleteava,
                "deletepic" => $deletepic,
                "pich" => _profil_ich,
                "pstatus" => _profil_status,
                "position" => getrank($get['id']),
                "value" => _button_value_edit,
                "status" => $status,
                "sonst" => _profil_sonst,
                "ich" => re_bbcode($get['beschreibung']),
                "deletehead" => _profil_del_account_head,
                "del" => _profil_del_account,
                "del_full" => _profil_del_account_full,
                "delete" => $delete,
                "delete_full" => $delete_full));


            $index = show($dir."/edit", array("profilhead" => _profil_edit_head,
                "editgallery" => _profil_edit_gallery_link,
                "editprofil" => _profil_edit_profil_link,
                "nick" => autor($get['id']),
                "show" => $show));
        }
    }
}