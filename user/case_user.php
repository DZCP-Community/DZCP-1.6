<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _user_profile_of.'autor_'.$_GET['id'];
    if(!db("SELECT id FROM `".$db['users']."` WHERE `id` = ".(int)($_GET['id']).";",true) ? true : false)
        $index = error(_user_dont_exist, 1);
    else {
        $get = db("SELECT * FROM `" . $db['users'] . "` WHERE `id` = " . (int)($_GET['id']) . ";", false, true);
        if ($get['dsgvo_lock'] && !permission("editusers") && $get['id'] != $userid) {
            //Has Lock
            $index = error(_user_dont_dsgvo, 1);
        } else if ($chkMe < $get['show'] && !permission("editusers") && $get['id'] != $userid) {
            //Not show profile
            $index = error(_user_profile_dont_show, 1);
        } else {
            db("UPDATE `" . $db['userstats'] . "` SET `profilhits` = (profilhits+1) WHERE `user` = " . $get['id'] . ";");

            $sex = '-';
            if ($get['sex'] == 1)
                $sex = _male;
            elseif ($get['sex'] == 2)
                $sex = _female;

            $hp = empty($get['hp']) ? "-" : "<a href=\"" . $get['hp'] . "\" target=\"_blank\">" . re($get['hp']) . "</a>";
            $email = empty($get['email']) ? "-" : "<img src=\"../inc/images/mailto.gif\" alt=\"\" align=\"texttop\"> <a href=\"mailto:" . eMailAddr(re($get['email'])) . "\" target=\"_blank\">" . eMailAddr(re($get['email'])) . "</a>";
            $pn = show(_pn_write, array("id" => $_GET['id'], "nick" => re($get['nick'])));
            $psnu = empty($get['psnid']) ? "-" : show(_psnicon, array("id" => str_replace(" ", "%20", re($get['psnid'])), "img" => "1", "css" => ""));
            $originu = empty($get['originid']) ? '-' : show(_originicon, array("id" => str_replace(" ", "%20", re($get['originid'])), "img" => "1", "css" => ""));
            $battlenetu = empty($get['battlenetid']) ? '-' : show(_battleneticon, array("id" => str_replace(" ", "%20", re($get['battlenetid'])), "img" => "1", "css" => ""));
            $bday = (!$get['bday'] || empty($get['bday'])) ? "-" : date('d.m.Y', $get['bday']);

            $icq = "-"; $icqnr = '';
            if (!empty($get['icq'])) {
                $icq = show(_icqstatus, array("uin" => $get['icq']));
                $icqnr = re($get['icq']);
            }

            $buddyadd = show(_addbuddyicon, array("id" => $_GET['id']));

            $edituser = "";
            if (permission("editusers")) {
                $edituser = show("page/button_edit_single", array("id" => "",
                    "action" => "action=admin&amp;edit=" . $_GET['id'],
                    "title" => _button_title_edit));
                $edituser = str_replace("&amp;id=", "", $edituser);
            }

            $rlname = $get['rlname'] ? re($get['rlname']) : "-";
            $skypename = $get['skypename'] ? '<div id="SkypeButton_Call_' . re($get['skypename']) . '"><script type="text/javascript">Skype.ui({"name": "dropdown", "element": "SkypeButton_Call_' . re($get['skypename']) . '", "participants": ["' . re($get['skypename']) . '"]});</script></div>' : '-';
            $steam = (!empty($get['steamid']) && steam_enable ? '<div id="infoSteam_' . md5(re($get['steamid'])) . '"><div style="width:100%;text-align:center"><img src="../inc/images/ajax-loader-mini.gif" alt="" /></div><script language="javascript" type="text/javascript">DZCP.initDynLoader("infoSteam_' . md5(re($get['steamid'])) . '","steam","&steamid=' . re($get['steamid']) . '");</script></div>' : '-');

            $city = re($get['city']);
            $beschreibung = bbcode(re($get['beschreibung']));

            $show = show($dir . "/profil_show", array(
                "about" => _profil_about,
                "country" => flag($get['country']),
                "pcity" => _profil_city,
                "city" => (empty($city) ? '-' : $city),
                "stats_hits" => _profil_pagehits,
                "stats_profilhits" => _profil_profilhits,
                "stats_msgs" => _profil_msgs,
                "stats_lastvisit" => _profil_last_visit,
                "stats_forenposts" => _profil_forenposts,
                "stats_logins" => _profil_logins,
                "stats_reg" => _profil_registered,
                "stats_votes" => _profil_votes,
                "logins" => userstats("logins", $_GET['id']),
                "hits" => userstats("hits", $_GET['id']),
                "msgs" => userstats("writtenmsg", $_GET['id']),
                "forenposts" => userstats("forumposts", $_GET['id']),
                "votes" => userstats("votes", $_GET['id']),
                "regdatum" => date("d.m.Y H:i", $get['regdatum']) . _uhr,
                "lastvisit" => date("d.m.Y H:i", userstats("lastvisit", $_GET['id'])) . _uhr,
                "contact" => _profil_contact,
                "preal" => _profil_real,
                "psteam" => _steam,
                "xboxl" => _xboxstatus,
                "xboxavatarl" => _xboxuserpic,
                "psnl" => _psnstatus,
                "skypel" => _skypestatus,
                "originl" => _originstatus,
                "battlenetl" => _battlenetstatus,
                "php" => _hp,
                "hp" => $hp,
                "pnick" => _nick,
                "pbday" => _profil_bday,
                "page" => _profil_age,
                "psex" => _profil_sex,
                "gamestuff" => _profil_gamestuff,
                "psnn" => re($get['psnid']),
                "originn" => re($get['originid']),
                "battlenett" => re($get['battlenetid']),
                "buddyadd" => $buddyadd,
                "userstats" => _profil_userstats,
                "nick" => autor($get['id']),
                "rlname" => $rlname,
                "bday" => $bday,
                "age" => getAge($get['bday']),
                "sex" => $sex,
                "email" => $email,
                "icq" => $icq,
                "icqnr" => $icqnr,
                "skypename" => $skypename,
                "skype" => re($get['skypename']),
                "pn" => $pn,
                "edituser" => $edituser,
                "psnid" => $psnu,
                "originid" => $originu,
                "battlenetid" => $battlenetu,
                "steam" => $steam,
                "onoff" => onlinecheck($get['id']),
                "picture" => userpic($get['id']),
                "sonst" => _profil_sonst,
                "pich" => _profil_ich,
                "pposition" => _profil_position,
                "pstatus" => _profil_status,
                "position" => getrank($get['id']),
                "ich" => (empty($beschreibung) ? '-' : $beschreibung)));

            $profil_head = show(_profil_head, array("profilhits" => userstats("profilhits", $_GET['id'])));
            $index = show($dir . "/profil", array("profilhead" => $profil_head, "show" => $show, "nick" => autor($_GET['id'])));
        }
    }
}