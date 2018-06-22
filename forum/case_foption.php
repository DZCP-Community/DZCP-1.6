<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if (defined('_Forum')) {
    if ($do == "fabo") {
        if (isset($_POST['f_abo'])) {
            db("INSERT INTO `" . $db['f_abo'] . "` SET `user` = " . $userid . ", `fid`  = " . (int)($_GET['id']) . ", `datum`  = " . time() . ";");
        } else {
            db("DELETE FROM `" . $db['f_abo'] . "` WHERE `user` = " . $userid . " AND `fid` = " . (int)($_GET['id']) . ";");
        }
        $index = info(_forum_fabo_do, "?action=showthread&amp;id=" . $_GET['id'] . "");
    }
}