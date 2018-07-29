<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if (defined('_UserMenu')) {
    $where = _site_user_logout;
    if (HasDSGVO()) {
        if (array_key_exists('identy_id', $_SESSION)) {
            if (!empty($_SESSION['identy_id'])) {
                db("UPDATE " . $db['users'] . " SET `online` = 0, `sessid` = '' WHERE `id` = " . $userid . ";"); //Logout
                session_regenerate_id();

                $_SESSION['id'] = $_SESSION['identy_id'];
                $_SESSION['pwd'] = data("pwd", (int)($_SESSION['identy_id']));
                $_SESSION['identy_ip'] = '';
                $_SESSION['identy_id'] = '';
                $_SESSION['ip'] = visitorIp();

                db("UPDATE " . $db['users'] . " SET `online` = 1, `sessid` = '" . session_id() . "' WHERE `id` = " . (int)($_GET['id']));
                header("Location: ../user/?action=userlobby");
                exit();
            }
        }

        if ($chkMe && $userid) {
            db("UPDATE " . $db['users'] . " SET online = '0', pkey = '', sessid = '' WHERE id = '" . $userid . "'");
            setIpcheck("logout(" . $userid . ")");
            cookie::clear();
            session_destroy();
        }

        header("Location: ../news/");
        exit();
    }
}