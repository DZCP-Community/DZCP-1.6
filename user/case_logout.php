<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if (defined('_UserMenu')) {
    $where = _site_user_logout;
    if(isset($_GET['reinit'])) {
        header("Location: ../news/");
        $_SESSION['DSGVO'] = true;
        $_SESSION['do_show_dsgvo'] = true;
        exit();
    }

    if (HasDSGVO()) {
        if (array_key_exists('identy_id', $_SESSION)) {
            //Admin
            if (!empty($_SESSION['identy_id'])) {
                db("UPDATE " . $db['users'] . " SET `online` = 0, `sessid` = '' WHERE `id` = " . $userid . ";"); //Logout old user
                session_regenerate_id();

                $_SESSION['id'] = (int)$_SESSION['identy_id'];
                $_SESSION['pwd'] = data("pwd", (int)($_SESSION['identy_id']));
                $_SESSION['identy_ip'] = '';
                $_SESSION['identy_id'] = '';
                $_SESSION['ip'] = visitorIp();
                $_SESSION['lastvisit'] = userstats("lastvisit",$_SESSION['id']);

                db("UPDATE " . $db['users'] . " SET `online` = 1, `sessid` = '" . session_id() . "', `time` = ".time()." WHERE `id` = " .$_SESSION['id'].";");
                header("Location: ../user/?action=userlobby");
                exit();
            }
        }

        if ($chkMe && $userid) {
            db("UPDATE `" . $db['users'] . "` SET `online` = 0, `pkey` = '', `sessid` = '', `time` = ".time()." WHERE `id` = ".$userid.";");
            setIpcheck("logout(" . $userid . ")");
            cookie::clear();
            session_unset();
            session_destroy();
            $_SESSION = [];
            $_SESSION['DSGVO'] = true;
            $_SESSION['do_show_dsgvo'] = true;
        }

        header("Location: ../user/?action=logout&reinit");
        exit();
    }
}