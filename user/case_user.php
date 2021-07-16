<?php
/**
 * DZCP - deV!L`z ClanPortal - Mainpage ( dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * Diese Datei ist ein Bestandteil von dzcp.de
 * Diese Version wurde speziell von Lucas Brucksch (Codedesigns) für dzcp.de entworfen bzw. verändert.
 * Eine Weitergabe dieser Datei außerhalb von dzcp.de ist nicht gestattet.
 * Sie darf nur für die Private Nutzung (nicht kommerzielle Nutzung) verwendet werden.
 *
 * Homepage: http://www.dzcp.de
 * E-Mail: info@web-customs.com
 * E-Mail: lbrucksch@codedesigns.de
 * Copyright 2017 © CodeKing, my-STARMEDIA, Codedesigns
 */

if(defined('_UserMenu')) {
    $where = _user_profile_of.'autor_'.$_GET['id'];
    $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ?;", [(int)($_GET['id'])]);
    if (!common::$sql['default']->rowCount()) {
        $index = common::error(_user_dont_exist, 1);
    } else {
        if ((common::$userid != $get['id']) && (($get['profile_access'] >= 1 &&
                    common::checkme() == 'unlogged') || ($get['profile_access'] >= 2 &&
                    common::checkme() <= 1) || ($get['profile_access'] >= 3 && common::checkme() != 4))) {
            $index = common::error(_profile_access_error, 1);
        } else {
            if (common::count_clicks('userprofil', $get['id'])) {
                common::userstats_increase('profilhits',$get['id']);
            } //Update Userstats

            $sex = $get['sex'] == 1 ? _male : ($get['sex'] == 2 ? _female : '-');
            $hp = empty($get['hp']) ? "-" : "<a href=\"" . $get['hp'] . "\" target=\"_blank\">" . $get['hp'] . "</a>";
            $email = empty($get['email']) ? "-" : common::CryptMailto(stringParser::decode($get['email']), _user_mailto_texttop);

            //Private Massage
            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $smarty->assign('nick',stringParser::decode($get['nick']));
            $pn = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_pn_write.tpl');
            $smarty->clearAllAssign();

            //Level & Group
            if ($get['level'] != 1 || isset($_GET['sq'])) {
                $sq = common::$sql['default']->select("SELECT * FROM `{prefix_user_posis}` WHERE `user` = ?;", [$get['id']]);
                $cnt = common::cnt('{prefix_user_posis}', " WHERE `user` = ?",'id', [$get['id']]); $i = 1;

                if (common::$sql['default']->rowCount() && !isset($_GET['sq'])) {
                    $pos = '';
                    foreach($sq as $getsq) {
                        $br = "-";
                        if ($i == $cnt) {
                            $br = "";
                        }

                        $pos .= " ".common::getrank($get['id'], $getsq['group'], 1)." ".$br;
                        $i++;
                    }
                } elseif (isset($_GET['sq'])) {
                    $pos = common::getrank($get['id'], $_GET['sq'], 1);
                } else {
                    $pos = common::getrank($get['id']);
                }
                
                $pos = (empty($pos) ? '-' : $pos);
            }

            // Add-Userbuddy
            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $buddyadd = $smarty->fetch('file:['.common::$tmpdir.']page/buttons/button_buddy_add.tpl');
            $smarty->clearAllAssign();

            $edituser = "";
            if (common::permission("editusers")) {
                $edituser = common::getButtonEditSingle(0,"action=admin&amp;edit=".$get['id']);
            }

            $rlname = $get['rlname'] ? stringParser::decode($get['rlname']) : "-";
            $city = stringParser::decode($get['city']);
            $beschreibung = BBCode::parse_html((string)$get['beschreibung']);

            //User Profil
            $smarty->caching = false;
            $smarty->assign('country',common::flag($get['country']));
            $smarty->assign('city',(empty($city) ? '-' : $city));
            $smarty->assign('logins',common::userstats("logins", $get['id']));
            $smarty->assign('hits',common::userstats("hits", $get['id']));
            $smarty->assign('msgs',common::userstats("writtenmsg", $get['id']));
            $smarty->assign('forenposts',common::userstats("forumposts", $get['id']));
            $smarty->assign('votes',common::userstats("votes", $get['id']));
            $smarty->assign('regdatum',date("d.m.Y H:i", $get['regdatum']) . _uhr);
            $smarty->assign('lastvisit',date("d.m.Y H:i", common::userstats("lastvisit", $get['id'])) . _uhr);
            $smarty->assign('hp',$hp);
            $smarty->assign('buddyadd',$buddyadd);
            $smarty->assign('nick',common::autor($get['id']));
            $smarty->assign('rlname',$rlname);
            $smarty->assign('age',common::getAge($get['bday']));
            $smarty->assign('sex',$sex);
            $smarty->assign('email',$email);
            $smarty->assign('pn',$pn);
            $smarty->assign('edituser',$edituser);
            $smarty->assign('onoff',common::onlinecheck($get['id']));
            $smarty->assign('picture',common::userpic($get['id']));
            $smarty->assign('position',common::getrank($get['id']));
            $smarty->assign('ich',(empty($beschreibung) ? '-' : $beschreibung));
            $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/profil/profil_show.tpl');
            $smarty->clearAllAssign();
            unset($city,$hp,$buddyadd,$rlname,$sex,$email,$pn,$edituser,$status,$beschreibung);

            //Profil Header
            $smarty->caching = false;
            $smarty->assign('profilhits',common::userstats("profilhits", $get['id']));
            $smarty->assign('nick',common::autor($get['id']));
            $profil_head = $smarty->fetch('string:'._profil_head);
            $smarty->clearAllAssign();

            //Index
            $smarty->caching = false;
            $smarty->assign('profilhead',$profil_head);
            $smarty->assign('notification_page','');
            $smarty->assign('show',$show);
            $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/profil/profil.tpl');
            $smarty->clearAllAssign(); unset($profil_head,$show,$get);
        }
    }
}