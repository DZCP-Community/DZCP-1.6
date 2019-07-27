<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if (defined('_Clanwars')) {
    if (permission("clanwars"))
        db("DELETE FROM " . $db['cw_player'] . " WHERE `cwid` = '" . (int)($_GET['id']) . "'");

    $index = info(_cw_players_reset, '?action=details&id=' . (int)($_GET['id']));
}