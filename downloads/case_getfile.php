<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(settings("reg_dl") == "1" && $chkMe == "unlogged")
{
    $index = error(_error_unregistered,1);
} else {
    $qry = db("SELECT url FROM ".$db['downloads']."
               WHERE id = '".intval($_GET['id'])."'");
    $get = _fetch($qry);

    $file = preg_replace("#added...#Uis", "", $get['url']);

    if(preg_match("=added...=Uis",$get['url']) != FALSE)
        $dlFile = "files/".$file;
    else $dlFile = $get['url'];

    $upd = db("UPDATE ".$db['downloads']."
               SET `hits` = hits+1
               WHERE id = '".intval($_GET['id'])."'");
//download file
    header("Location: ".$dlFile);
}