<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 * Menu: User of the Moment
 */
function uotm() {
    global $db,$picformat;

    $files = get_files(basePath.'/inc/images/uploads/userpics',false,true,$picformat,false,array(),'minimize');
    shuffle($files);

    $uotm = '';
    if(count($files) != 0) {
        $userid = (int)($files[rand(0, count($files) - 1)]);
        $qry = db("SELECT `id`,`bday` FROM ".$db['users']." WHERE `id` = '".$userid."'");
        if(_rows($qry)) {
            $get = _fetch($qry);
            if(config('allowhover') == 1)
                $info = 'onmouseover="DZCP.showInfo(\''.fabo_autor($get['id']).'\', \''._age.'\', \''.getAge($get['bday']).'\', \''.hoveruserpic($get['id']).'\')" onmouseout="DZCP.hideInfo()"';


            $uotm = show("menu/uotm", array("uid" => $userid,
                                            "upic" => userpic($get['id'], 130, 161),
                                            "info" => $info));
        }
    }

    return empty($uotm) ? '' : '<table class="navContent" cellspacing="0">'.$uotm.'</table>';
}