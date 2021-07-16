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
    $where = _site_user_login;
    if(common::$do == "yes") {
        ## Prufe ob der Secure Code aktiviert ist und richtig eingegeben wurde ##
        switch (isset($_GET['from']) ? $_GET['from'] : 'default') {
            case 'menu': common::$securimage->namespace = 'menu_login'; break;
            default: common::$securimage->namespace = 'default'; break;
        }

        if (settings::get('securelogin') && (!isset($_POST['secure']) || !common::$securimage->check($_POST['secure']))) {
            $index = common::error(config::$captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode);
        } else {
            $get = common::$sql['default']->fetch("SELECT `id`,`user`,`nick`,`pwd`,`pwd_encoder`,`email`,`level`,`time` "
                        . "FROM `{prefix_users}` "
                        . "WHERE `user` = ? AND `level` != 0;", 
                [stringParser::encode($_POST['user'])]);

            $login = false; $pwd = '';
            if($get['id'] >= 1 && !empty($_POST['pwd'])) {
                $pwd = common::pwd_encoder($_POST['pwd'],$get['pwd_encoder']);
                $login = true;
            }

            if($get['id'] >= 1 && $login && stringParser::decode($get['pwd']) == $pwd) {
                if (!common::isBanned($get['id'])) {
                    //Update Password encoding
                    if($get['pwd_encoder'] != settings::get('default_pwd_encoder')) {
                        common::$sql['default']->update("UPDATE `{prefix_users}` SET `pwd` = ?, `pwd_encoder` = ? "
                                . "WHERE `id` = ?;", [($pass = common::pwd_encoder($_POST['pwd'])),
                                    settings::get('default_pwd_encoder'), $get['id']]);
                        $get['pwd'] = $pass;
                        $get['pwd_encoder'] = settings::get('default_pwd_encoder');
                    }

                    $permanent_key = '';
                    if (isset($_POST['permanent'])) {
                        cookie::set('id', $get['id']);
                        $permanent_key = md5(common::mkpwd(8));
                        $gethostbyaddr = gethostbyaddr(common::$userip['v4']);
                        if (common::$sql['default']->rows("SELECT `id` FROM `{prefix_autologin}` WHERE `host` = ?;", [$gethostbyaddr]) >= 1) {
                            //Update Autologin
                            common::$sql['default']->update("UPDATE `{prefix_autologin}` "
                                    . "SET `ssid` = ?,"
                                    . "`pkey` = ?,"
                                    . "`ipv4` = ?,"
                                    . "`date` = ?,"
                                    . "`update` = ?,"
                                    . "`expires` = ? "
                                    . "WHERE `host` = ?;",
                            [session_id(), $permanent_key, common::$userip['v4'], $time = time(), $time, autologin_expire, $gethostbyaddr]);
                        } else {
                            //Insert Autologin
                            common::$sql['default']->insert("INSERT INTO `{prefix_autologin}` "
                                    . "SET `uid` = ?, "
                                    . "`ssid` = ?, "
                                    . "`pkey` = ?, "
                                    . "`ipv4` = ?, "
                                    . "`name` = ?, "
                                    . "`host` = ?, "
                                    . "`date` = ?, "
                                    . "`update` = 0, "
                                    . "`expires` = ?;",
                            [$get['id'], session_id(), $permanent_key, common::$userip['v4'],
                                common::cut($gethostbyaddr, 20), $gethostbyaddr, time(), autologin_expire]);
                        }

                        cookie::set('pkey', $permanent_key);
                        cookie::save();
                    }

                    //Set Sessions
                    $_SESSION['id'] = $get['id'];
                    $_SESSION['pwd'] = $get['pwd'];
                    $_SESSION['lastvisit'] = $get['time'];
                    $_SESSION['ip'] = common::$userip['v4'];

                    common::userstats_increase('logins',$get['id']);
                    common::$sql['default']->update("UPDATE `{prefix_users}` SET `online` = 1, `sessid` = ?, `ipv4` = ? WHERE `id` = ?;", [session_id(), common::$userip['v4'], $get['id']]);
                    common::setIpcheck("login(" . $get['id'] . ")");

                    //-> Aktualisiere Ip-Count Tabelle
                    $qry = common::$sql['default']->select("SELECT `id` FROM `{prefix_clicks_ips}` WHERE `ipv4` = ? AND `uid` = 0;", [common::$userip['v4']]);
                    if (common::$sql['default']->rowCount() >= 1) {
                        foreach ($qry as $get_ci) {
                            common::$sql['default']->update("UPDATE `{prefix_clicks_ips}` SET `uid` = ? WHERE `id` = ?;", [$get['id'], $get_ci['id']]);
                        }
                    }

                    header("Location: ?action=userlobby");
                } else {
                    $index = common::error(_login_banned);
                }
            } else {
                $get = common::$sql['default']->fetch("SELECT `id` FROM `{prefix_users}` WHERE `user` = ?;", [stringParser::encode($_POST['user'])]);
                if(common::$sql['default']->rowCount()) {
                    common::setIpcheck("trylogin(".$get['id'].")");
                }

                cookie::set('id', '');
                cookie::set('pkey', '');
                $index = common::error(_login_pwd_dont_match);
            }
        }
    } else {
        if (!common::$chkMe) {
            //Sicherheitsscode
            $secure='';
            if(settings::get('securelogin')) {
                $smarty->caching = false;
                $secure = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/access/secure.tpl');
            }

            //Index
            $smarty->caching = false;
            $smarty->assign('secure',$secure);
            $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/access/login.tpl');
            $smarty->clearAllAssign();
        } else {
            $index = common::error(_error_user_already_in, 1);
        }
    }
}