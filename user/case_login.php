<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_user_login;
    if($do == "yes") {
        if(config('securelogin') &&
            ((!array_key_exists('sec_login_page',$_SESSION) && !array_key_exists('login_menu',$_SESSION)) ||
            (($_POST['secure'] != $_SESSION['sec_login_page'] && $_POST['secure'] != $_SESSION['login_menu']) ||
                (empty($_SESSION['sec_login_page']) && empty($_SESSION['login_menu'])))))
            $index = error(_error_invalid_regcode, 1);
        else {
            if($get = checkpwd($_POST['user'],$_POST['pwd'])) {
                if(!isBanned($get['id'])) {
                    $permanent_key = '';
                    if(isset($_POST['permanent'])) {
                        cookie::put('id', $get['id']);
                        $permanent_key = hash('sha256', mkpwd(12));
                        cookie::put('pkey', $permanent_key);
                        cookie::save();
                    }

                    ## Aktualisiere Datenbank ##
                    db("UPDATE `".$db['users']."` SET `online` = 1, `sessid` = '".session_id()."', `ip` = '"._real_escape_string(encrypt($userip))."', `pkey` = '".$permanent_key."' WHERE `id` = ".$get['id'].";");

                    $_SESSION['id']         = $get['id'];
                    $_SESSION['pwd']        = $get['pwd'];
                    $_SESSION['lastvisit']  = $get['time'];
                    $_SESSION['ip']         = $userip;

                    db("UPDATE `".$db['userstats']."` SET `logins` = (logins+1) WHERE `user` = ".$get['id'].";");
                    db("UPDATE `".$db['users']."` SET `online` = 1, `sessid` = '".session_id()."', `ip` = '"._real_escape_string(encrypt($userip))."', `pkey` = '".$permanent_key."' WHERE `id` = ".$get['id'].";");
                    setIpcheck("login(".$get['id'].")");

                    header("Location: ?action=userlobby");
                }
                else
                    $index = error(_login_banned);
            } else {
                $qry = db("SELECT `id` FROM `".$db['users']."` WHERE `user` = '"._real_escape_string(encrypt($_POST['user']))."';");
                if(_rows($qry)) {
                    $get = _fetch($qry);
                    setIpcheck("trylogin(".$get['id'].")");
                }

                cookie::put('id', '');
                cookie::put('pkey', '');
                $index = error(_login_pwd_dont_match);
            }
        }
    } else {
        if(!$chkMe) {
            $secure = config('securelogin') ? show($dir."/secure", array("help" => _login_secure_help, "security" => _register_confirm)) : '';
            $index = show($dir."/login", array("loginhead" => _login_head,
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