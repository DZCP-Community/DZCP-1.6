<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    if($userid) {
        $_SESSION['lastvisit'] = time();
        db("UPDATE `".$db['userstats']."` SET `lastvisit` = ".((int)$_SESSION['lastvisit'])." WHERE `user` = ".$userid.";");

        //Update Cache
        $get = db("SELECT * FROM `".$db['userstats']."` WHERE `user` = ".(int)($userid).";",false,true);
        dbc_index::setIndex('userstats_'.$userid, $get);
        unset($get);
    }

    header("Location: ?action=userlobby");
}