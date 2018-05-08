<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(defined('_Votes')) {
    $get = db("SELECT `id`,`intern`,`closed` FROM `".$db['votes']."` WHERE `id` = ".(int)($_GET['id']).";",false,true);
    if(!$get['intern'] || ($get['intern'] && $chkMe)) {
        $qryv = db("SELECT `user_id`,`created` FROM `".$db['ipcheck']."` WHERE `what` = 'vid_".$get['id']."' ORDER BY time DESC;");
        if($chkMe == 4 || $get['closed'] || permission('votesadmin') || db("SELECT `id` FROM `".$db['ipcheck']."` WHERE `user_id` = ".$userid." "
                . "AND `what` = 'vid_".$get['id']."'",true)) {
            while($getv = _fetch($qryv)) {
                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                $show .= show($dir."/voted_show", array("user" => $getv['user_id'] ? autor($getv['user_id']) : _gast,
                                                        "date" => date("d.m.y H:i",$getv['created'])._uhr,
                                                        "class" => $class));
            }
        }

        if(empty($show))
            $show = show(_no_entrys_yet, array("colspan" => "2"));

        $index = show($dir."/voted", array("head" => _voted_head,
                                           "user" => _user,
                                           "date" => _datum,
                                           "show" => $show));
    } else
        $index = error(_error_vote_show,1);
}