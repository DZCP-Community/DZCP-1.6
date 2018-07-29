<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if (defined('_UserMenu')) {
    $where = _site_user_login;
    if ($do == "yes" && HasDSGVO()) {
        $validator = check_securelogin();
        if (!$validator['login']) {
            $index = error($validator['msg'], 1);
        } else {
            if (($get = checkpwd(re($validator['input']['user'], true),
                    re($validator['input']['pwd'], true))) != false) {
                if (!isBanned($get['id'])) {
                    if ($get['dsgvo_lock']) {
                        //User Locked
                        $_SESSION['user_has_dsgvo_lock'] = true;
                        $_SESSION['dsgvo_lock_permanent_login'] = $input['permanent'];
                        $_SESSION['dsgvo_lock_login_id'] = $get['id'];
                        if (!empty($get['language'])) {
                            $_SESSION['language'] = re($get['language']);
                        }

                        header("Location: ?action=userlock");
                    } else {
                        $permanent_key = '';
                        if ($input['permanent']) {
                            cookie::put('id', $get['id']);
                            $permanent_key = hash('sha256', mkpwd(12));
                            cookie::put('pkey', $permanent_key);
                            cookie::save();
                        }

                        ## Aktualisiere Datenbank ##
                        db("UPDATE `" . $db['users'] . "` SET `online` = 1, `sessid` = '" . session_id() . "', `ip` = '" . $userip . "', `pkey` = '" . $permanent_key . "' WHERE `id` = " . $get['id'] . ";");

                        $_SESSION['id'] = $get['id'];
                        $_SESSION['pwd'] = $get['pwd'];
                        $_SESSION['lastvisit'] = $get['time'];
                        $_SESSION['ip'] = $userip;
                        if (!empty($get['language'])) {
                            $_SESSION['language'] = re($get['language']);
                        }

                        db("UPDATE `" . $db['userstats'] . "` SET `logins` = (logins+1) WHERE `user` = " . $get['id'] . ";");
                        db("UPDATE `" . $db['users'] . "` SET `online` = 1, `sessid` = '" . session_id() . "', `ip` = '" . $userip . "', `pkey` = '" . $permanent_key . "' WHERE `id` = " . $get['id'] . ";");
                        setIpcheck("login(" . $get['id'] . ")");

                        header("Location: ?action=userlobby");
                    }
                } else
                    $index = error(_login_banned);
            } else {
                $qry = db("SELECT `id` FROM `" . $db['users'] . "` WHERE `user` = '" . up($validator['input']['user']) . "';");
                if (_rows($qry)) {
                    $get = _fetch($qry);
                    setIpcheck("trylogin(" . $get['id'] . ")");
                }

                cookie::put('id', '');
                cookie::put('pkey', '');
                $index = error(_login_pwd_dont_match);
            }
        }
    } else {
        if (!$chkMe) {
            $secure = config('securelogin') ? show($dir . "/secure",
                array("help" => _login_secure_help, "security" => _register_confirm)) : '';
            $index = show($dir . "/login", array("loginhead" => _login_head,
                "loginname" => _loginname,
                "secure" => $secure,
                "lostpwd" => _login_lostpwd,
                "permanent" => _login_permanent,
                "pwd" => _pwd));
        } else {
            $index = error(_error_user_already_in, 1);
            cookie::put('id', '');
            cookie::put('pkey', '');
        }
    }
}