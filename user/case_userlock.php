<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
//Check is DSGVO Set?
    if(isset($_GET['dsgvo-lock'])) {
        switch ((int)$_GET['dsgvo-lock']) {
            case 1:
                $get = db("SELECT * FROM `".$db['users']."` WHERE `id` = ".$_SESSION['dsgvo_lock_login_id'].";",false,true);
                $_SESSION['user_has_dsgvo_lock'] = false;
                $permanent_key = '';
                if ($_SESSION['dsgvo_lock_permanent_login']) {
                    cookie::put('id', $get['id']);
                    $permanent_key = hash('sha256', mkpwd(12));
                    cookie::put('pkey', $permanent_key);
                    cookie::save();
                }

                ## Aktualisiere Datenbank ##
                db("UPDATE `" . $db['users'] . "` SET `online` = 1, `dsgvo_lock` = 0, `sessid` = '" . session_id() . "', `ip` = '" . $userip . "', `pkey` = '" . $permanent_key . "' WHERE `id` = " . $get['id'] . ";");

                $_SESSION['id'] = $get['id'];
                $_SESSION['pwd'] = $get['pwd'];
                $_SESSION['lastvisit'] = $get['time'];
                $_SESSION['ip'] = $userip;

                db("UPDATE `" . $db['userstats'] . "` SET `logins` = (logins+1) WHERE `user` = " . $get['id'] . ";");
                db("UPDATE `" . $db['users'] . "` SET `online` = 1, `sessid` = '" . session_id() . "', `ip` = '" . $userip . "', `pkey` = '" . $permanent_key . "' WHERE `id` = " . $get['id'] . ";");
                setIpcheck("login(" . $get['id'] . ")");

                header("Location: ../user/?action=userlobby");
                exit();
                break;
            default:
                //TODO: Delete User

                header("Location: ../news/");
                exit();
        }
    }
}