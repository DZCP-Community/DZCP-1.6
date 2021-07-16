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
    $where = _site_reg;
    if(!common::$chkMe) {
        // ########################################
        // POST
        // ########################################

        if (common::HasDSGVO() && common::$do == "add" && !common::$chkMe && common::isIP(common::$userip['v4']) && !common::$CrawlerDetect->isCrawler()) {
            $check_user = common::$sql['default']->rows("SELECT `id` FROM `{prefix_users}` WHERE `user`= ?;",
                [stringParser::encode($_POST['user'])]);

            $check_nick = common::$sql['default']->rows("SELECT `id` FROM `{prefix_users}` WHERE `nick`= ?;",
                [stringParser::encode($_POST['nick'])]);

            $check_email = common::$sql['default']->rows("SELECT `id` FROM `{prefix_users}` WHERE `email`= ?;",
                [stringParser::encode($_POST['email'])]);

            if(empty($_POST['user']) || empty($_POST['nick']) || empty($_POST['email'])
                || ($_POST['pwd'] != $_POST['pwd2']) || (settings::get("regcode") &&
                    !common::$securimage->check($_POST['secure'])) || $check_user || $check_nick || $check_email) {

                if (settings::get("regcode") && !common::$securimage->check($_POST['secure'])) {
                    notification::add_error(_error_invalid_regcode);
                }

                if ($_POST['pwd2'] != $_POST['pwd']) {
                    notification::add_error(_wrong_pwd);
                }

                if (!common::check_email($_POST['email'])) {
                    notification::add_error(_error_invalid_email);
                }

                if (empty($_POST['email'])) {
                    notification::add_error(_empty_email);
                }

                if ($check_email) {
                    notification::add_error(_error_email_exists);
                }

                if (empty($_POST['nick'])) {
                    notification::add_error(_empty_nick);
                }

                if ($check_nick) {
                    notification::add_error(_error_nick_exists);
                }

                if (empty($_POST['user'])) {
                    notification::add_error(_empty_user);
                }

                if ($check_user) {
                    notification::add_error(_error_user_exists);
                }

                if(notification::has()) {
                    javascript::set('AnchorMove', 'notification-box');
                }
            } else {
                if(empty($_POST['pwd'])) {
                    $mkpwd = common::mkpwd();
                    $pwd = common::pwd_encoder($mkpwd);
                    $msg = _info_reg_valid;
                } else {
                    $mkpwd = $_POST['pwd'];
                    $pwd = common::pwd_encoder($mkpwd);
                    $msg = _info_reg_valid_pwd;
                }

                ## Neuen User in die Datenbank schreiben ##
                common::$sql['default']->insert("INSERT INTO `{prefix_users}` "
                    . "SET `user`     = ?, "
                    . "`nick`     = ?, "
                    . "`email`    = ?, "
                    . "`ipv4`       = ?, "
                    . "`pwd`      = ?, "
                    . "`pwd_encoder` = ?, "
                    . "`actkey`   = ?, "
                    . "`regdatum` = ".($time=time()).", "
                    . "`level`    = ?, "
                    . "`profile_access` = 1,"
                    . "`time`     = ".$time.", "
                    . "`status`   = ?;",
                    [stringParser::encode(trim($_POST['user'])),
                        stringParser::encode(trim($_POST['nick'])),
                        stringParser::encode(trim($_POST['email'])),
                        common::$userip['v4'],
                        stringParser::encode($pwd),
                        settings::get('default_pwd_encoder'),
                        (settings::get('use_akl') ? ($guid=common::GenGuid()) : ''),
                        (settings::get('use_akl') ? 0 : 1),
                        (settings::get('use_akl') >= 1 ? 0 : 1)]);

                ## Lese letzte ID aus ##
                $insert_id = common::$sql['default']->lastInsertId();

                ## Lege User in der Permissions Tabelle an ##
                common::$sql['default']->insert("INSERT INTO `{prefix_permissions}` SET `user` = ?;", [$insert_id]);

                ## Lege User in der User-Statistik Tabelle an ##
                common::$sql['default']->insert("INSERT INTO `{prefix_user_stats}` SET `user` = ?, `lastvisit` = ?;", [$insert_id,$time]);

                ## Erstelle User-Upload Ordner ##
                fileman::CreateUserDir($insert_id);

                ## Ereignis in den Adminlog schreiben ##
                common::setIpcheck("reg(".$insert_id.")");

                ## E-Mail zusammenstellen und senden ##
                if(settings::get('use_akl') == 1) {
                    $akl_link = 'http://'.common::$httphost.'/user/?action=akl&do=activate&key='.$guid;
                    $akl_link_page = 'http://'.common::$httphost.'/user/?action=akl&do=activate';

                    $smarty->caching = false;
                    $smarty->assign('nick',$_POST['user']);
                    $smarty->assign('link_page','<a href="'.$akl_link_page.'" target="_blank">'.$akl_link_page.'</a>');
                    $smarty->assign('guid',$guid);
                    $smarty->assign('link','<a href="'.$akl_link.'" target="_blank">Link</a>');
                    $message = $smarty->fetch('string:'.BBCode::bbcode_email(stringParser::decode(settings::get('eml_akl_register'))));
                    $smarty->clearAllAssign();

                    common::sendMail(trim($_POST['email']),stringParser::decode(settings::get('eml_akl_register_subj')),$message);
                }

                $smarty->caching = false;
                $smarty->assign('user',trim($_POST['user']));
                $smarty->assign('pwd',$mkpwd);
                $message = $smarty->fetch('string:'.BBCode::bbcode_email(stringParser::decode(settings::get('eml_reg'))));
                $smarty->clearAllAssign();

                common::sendMail(trim($_POST['email']),stringParser::decode(settings::get('eml_reg_subj')),$message);

                ## Nachricht anzeigen und zum  Userlogin weiterleiten ##
                $smarty->caching = false;
                $smarty->assign('email',$_POST['email']);
                $info = $smarty->fetch('string:'.(settings::get('use_akl') ? (settings::get('use_akl') == 2 ? _info_reg_valid_akl_ad : _info_reg_valid_akl) : _info_reg_valid));
                $smarty->clearAllAssign();
                notification::add_success($info);
            }
        }

        // ########################################
        // SHOW
        // ########################################

        //Sicherheitsscode
        $regcode = "";
        if (settings::get("regcode")) {
            $smarty->caching = false;
            $regcode = $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/access/register_regcode.tpl');
        }

        //Index
        $smarty->caching = false;
        $smarty->assign('lock', !common::HasDSGVO(), true);
        $smarty->assign('dsgvo_url', 'dsfsdfsdfsdfsdfsdfsdfsdf', true);
        $smarty->assign('r_name', isset($_POST['user']) ? $_POST['user'] : '');
        $smarty->assign('r_nick', isset($_POST['nick']) ? $_POST['nick'] : '');
        $smarty->assign('r_email', isset($_POST['email']) ? $_POST['email'] : '');
        $smarty->assign('regcode', $regcode, true);
        $smarty->assign('notification_page', notification::get());
        $index = $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/access/register.tpl');
        $smarty->clearAllAssign();
    } else
        $index = common::error(_error_user_already_in, 1);
}