<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
//Check is DSGVO Set?
    if(isset($_GET['dsgvo-lock'])) {
        switch ((int)$_GET['dsgvo-lock']) {
            case 1:
                $get = db("SELECT * FROM `".$db['users']."` WHERE `id` = ".$_SESSION['dsgvo_lock_login_id'].";",false,true);
                $_SESSION['user_has_dsgvo_lock'] = false;
                $permanent_key = '';
                if ($_SESSION['dsgvo_lock_permanent_login']) {
                    cookie::put('id', $get['id']);
                    $permanent_key = hash('sha256', mkpwd(12));
                    cookie::put('pkey', $permanent_key);
                    cookie::save();
                }

                ## Aktualisiere Datenbank ##
                db("INSERT INTO `" . $db['dsgvo_log'] . "` SET `uid` = ".$get['id'].",`ip` = '".$userip."', `date` = ".time().", `agent` = '".$_SERVER['HTTP_USER_AGENT']."';");
                db("UPDATE `" . $db['users'] . "` SET `online` = 1, `dsgvo_lock` = 0, `sessid` = '" . session_id() . "', `ip` = '" . $userip . "', `pkey` = '" . $permanent_key . "' WHERE `id` = " . $get['id'] . ";");

                $_SESSION['id'] = $get['id'];
                $_SESSION['pwd'] = $get['pwd'];
                $_SESSION['lastvisit'] = $get['time'];
                $_SESSION['ip'] = $userip;

                db("UPDATE `" . $db['userstats'] . "` SET `logins` = (logins+1) WHERE `user` = " . $get['id'] . ";");
                db("UPDATE `" . $db['users'] . "` SET `online` = 1, `sessid` = '" . session_id() . "', `ip` = '" . $userip . "', `pkey` = '" . $permanent_key . "' WHERE `id` = " . $get['id'] . ";");
                setIpcheck("login(" . $get['id'] . ")");

                header("Location: ../user/?action=userlobby");
                exit();
                break;
            default:
                //Alles löschen
                $getdel = db("SELECT * FROM `".$db['users']."` WHERE `id` = ".$_SESSION['dsgvo_lock_login_id'].";",false,true);

                $ips = array();
                $ips[$getdel['ip']] = true;

                $qry = db("SELECT * FROM `".$db['f_threads']."` WHERE `t_reg` = ".$getdel['id'].";");
                while ($get = _fetch($qry)) {
                    $ips[$get['ip']] = true;
                    if (!db("SELECT `id` FROM `" . $db['f_posts'] . "` WHERE `sid` = " . $get['id'] . ";", true)) {
                        //Delete Thread, no Posts!
                        db("DELETE FROM `" . $db['f_threads'] . "` WHERE `id` = " . $get['id'] . ";");
                        db("DELETE FROM `" . $db['f_abo'] . "` WHERE `fid` = " . $get['id'] . ";");
                    } else {
                        //Suche Zitate und anonymisieren
                        $qry = db("SELECT `text`,`id` FROM `" . $db['f_posts'] . "` WHERE `sid` = " . $get['id'] . ";");
                        while ($get_post = _fetch($qry)) {
                            $text = re($get_post['text']);
                            $text = str_replace(array(re(data('nick', $get['t_reg'])), utor($get['t_reg'])), __dsgvo_deleted_user, $text);
                            db("UPDATE `" . $db['f_posts'] . "` SET `text` = '" . up($text) . "' WHERE `id` = " . $get_post['id'] . ";");
                        }

                        //Anonym User for Thread
                        db("UPDATE `" . $db['f_threads'] . "` SET " .
                            "`t_nick` = '', " .
                            "`t_reg` = 0, " .
                            "`t_email` = '', " .
                            "`t_text` = '', " .
                            "`edited` = '', " .
                            "`t_hp` = '', " .
                            "`ip` = '', " .
                            "`dsgvo` = 1, " .
                            "WHERE `id` = " . $get['id'] . ";");
                    }
                }

                //Save IPS
                $qry = db("SELECT `ip` FROM `".$db['f_posts']."` WHERE `reg` = ".$getdel['id'].";");
                while ($get = _fetch($qry)) {
                    $ips[$get['ip']] = true;
                }

                $qry = db("SELECT `ip` FROM `".$db['gb']."` WHERE `reg` = ".$getdel['id'].";");
                while ($get = _fetch($qry)) {
                    $ips[$get['ip']] = true;
                }

                $qry = db("SELECT `ip` FROM `".$db['acomments']."` WHERE `reg` = ".$getdel['id'].";");
                while ($get = _fetch($qry)) {
                    $ips[$get['ip']] = true;
                }

                $qry = db("SELECT `ip` FROM `".$db['cw_comments']."` WHERE `reg` = ".$getdel['id'].";");
                while ($get = _fetch($qry)) {
                    $ips[$get['ip']] = true;
                }

                $qry = db("SELECT `ip` FROM `".$db['newscomments']."` WHERE `reg` = ".$getdel['id'].";");
                while ($get = _fetch($qry)) {
                    $ips[$get['ip']] = true;
                }

                db("DELETE FROM `".$db['f_posts']."` WHERE `reg` = ".$getdel['id']." OR `email` = '".$getdel['email']."';");
                db("DELETE FROM `".$db['f_abo']."` WHERE `user` = ".$getdel['id'].";");
                db("DELETE FROM `".$db['f_access']."` WHERE `user` = ".$getdel['id'].";");
                db("DELETE FROM `".$db['newscomments']."` WHERE `reg` = ".$getdel['id'].";");
                db("DELETE FROM `".$db['acomments']."` WHERE `reg` = ".$getdel['id'].";");
                db("DELETE FROM `".$db['msg']."` WHERE `von` = ".(int)($getdel['id'])." OR `an` = ".$getdel['id'].";");
                db("DELETE FROM `".$db['news']."` WHERE `autor` = ".(int)($getdel['id']).";");
                db("DELETE FROM `".$db['permissions']."` WHERE `user` = ".(int)($getdel['id']).";");
                db("DELETE FROM `".$db['squaduser']."` WHERE `user` = ".(int)($getdel['id']).";");
                db("DELETE FROM `".$db['buddys']."` WHERE `user` = ".(int)($getdel['id'])." OR `buddy` = ".$getdel['id'].";");
                db("DELETE FROM `".$db['usergb']."` WHERE reg = ".(int)($getdel['id']).";");
                db("DELETE FROM `".$db['userpos']."` WHERE `user` = ".(int)($getdel['id']).";");
                db("DELETE FROM `".$db['users']."` WHERE `id` = ".(int)($getdel['id']).";");
                db("DELETE FROM `".$db['userstats']."` WHERE `user` = ".(int)($getdel['id']).";");
                db("DELETE FROM `".$db['shout']."` WHERE `email` = '".$getdel['email']."';");
                db("DELETE FROM `".$db['gb']."` WHERE `email` = '".$getdel['email']."';");
                db("DELETE FROM `".$db['cw_comments']."` WHERE `email` = '".$getdel['email']."' OR `reg` = ".$getdel['id'].";");
                db("DELETE FROM `".$db['cw_player']."` WHERE `member` = ".$getdel['id'].";");
                db("DELETE FROM `".$db['clankasse']."` WHERE `member` = ".$getdel['id'].";");
                db("DELETE FROM `".$db['away']."` WHERE `userid` = ".$getdel['id'].";");
                db("DELETE FROM `".$db['usergallery']."` WHERE `user` = ".$getdel['id'].";");

                //IP-Check Loop
                foreach ($ips as $ip => $null) {
                    if(!validateIpV4Range($ip, '[192].[168].[0-255].[0-255]') && !validateIpV4Range($ip, '[127].[0].[0-255].[0-255]') &&
                        !validateIpV4Range($ip, '[10].[0-255].[0-255].[0-255]') && !validateIpV4Range($ip, '[172].[16-31].[0-255].[0-255]')) {
                        db("DELETE FROM `" . $db['acomments'] . "` WHERE `ip` = '" . $ip . "';");
                        db("DELETE FROM `" . $db['c_ips'] . "` WHERE `ip` = '" . $ip . "';");
                        db("DELETE FROM `" . $db['c_who'] . "` WHERE `ip` = '" . $ip . "';");
                        db("DELETE FROM `" . $db['cw_comments'] . "` WHERE `ip` = '" . $ip . "';");
                        db("DELETE FROM `" . $db['ipcheck'] . "` WHERE `ip` = '" . $ip . "' OR `user_id` = " . $getdel['id'] . ";");
                        db("DELETE FROM `" . $db['newscomments'] . "` WHERE `ip` = '" . $ip . "';");
                        db("DELETE FROM `" . $db['shout'] . "` WHERE `ip` = '" . $ip . "';");
                        db("DELETE FROM `" . $db['usergb'] . "` WHERE `ip` = '" . $ip . "';");
                    }
                } unset($ips);

                foreach($picformat as $tmpendung) {
                    if(file_exists(basePath."/inc/images/uploads/userpics/".(int)($getdel['id']).".".$tmpendung))
                        @unlink(basePath."/inc/images/uploads/userpics/".(int)($getdel['id']).".".$tmpendung);

                    if(file_exists(basePath."/inc/images/uploads/useravatare/".(int)($getdel['id']).".".$tmpendung))
                        @unlink(basePath."/inc/images/uploads/useravatare/".(int)($getdel['id']).".".$tmpendung);
                }

                $index = info(_info_edit_gallery_done, "../news/");
                exit();
        }
    }
}