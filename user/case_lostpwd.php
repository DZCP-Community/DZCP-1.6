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
    $where = _site_user_lostpwd;
    if (!common::$chkMe) {
        if (common::$do == "sended") {
            $get = common::$sql['default']->fetch("SELECT `id`,`user`,`level`,`email`,`nick` FROM `{prefix_users}` WHERE `user` = ? AND `email` = ?;",
                [stringParser::encode($_POST['user']), stringParser::encode($_POST['email'])]);
            if (common::$sql['default']->rowCount() && (isset($_POST['secure']) || common::$securimage->check($_POST['secure']))) {
                common::$sql['default']->update("UPDATE `{prefix_users}` SET `lostpwd_key` = ? WHERE `id` = ?;",
                    [stringParser::encode($guid = common::GenGuid()),$get['id']]);

                $lpwd_link = 'http://'.common::$httphost.'/user/?action=lostpwd&do=set&key='.$guid;
                $smarty->caching = false;
                $smarty->assign('nick',stringParser::decode($get['nick']));
                $smarty->assign('link','<a href="'.$lpwd_link.'" target="_blank">Link</a>');
                $message = $smarty->fetch('string:'.BBCode::bbcode_email(stringParser::decode(settings::get('eml_lpwd_key'))));
                $smarty->clearAllAssign();

                common::sendMail(stringParser::decode($get['email']), stringParser::decode(settings::get('eml_lpwd_key_subj')), $message);
                notification::add_success(_lostpwd_valid_sended);
            } else {
                common::setIpcheck("trypwd(" . $get['id'] . ")");
                if (settings::get('securelogin') && isset($_POST['secure']) && !common::$securimage->check($_POST['secure'])) {
                    notification::add_error(config::$captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode);
                } else {
                    notification::add_error(_lostpwd_failed);
                }
            }
        } else if(common::$do == "set") {
            if(isset($_GET['key']) && !empty($_GET['key']))
            $get = common::$sql['default']->fetch("SELECT `user`,`id`,`email`,`nick` FROM `{prefix_users}` WHERE `lostpwd_key` = ?;",
                [stringParser::encode($_GET['key'])]);
            if(common::$sql['default']->rowCount()) {
                $pwd = common::mkpwd();
                common::$sql['default']->update("UPDATE `{prefix_users}` SET `pwd` = ?, `pwd_encoder` = ?, `lostpwd_key` = '' WHERE `id` = ?;",
                    [common::pwd_encoder($pwd),settings::get('default_pwd_encoder'),$get['id']]);
                common::setIpcheck("pwd(" . $get['id'] . ")");

                $smarty->caching = false;
                $smarty->assign('nick',stringParser::decode($get['nick']));
                $smarty->assign('user',stringParser::decode($get['user']));
                $smarty->assign('pwd',$pwd);
                $message = $smarty->fetch('string:'.BBCode::bbcode_email(stringParser::decode(settings::get('eml_pwd'))));
                $smarty->clearAllAssign();

                common::sendMail($get['email'],stringParser::decode(settings::get('eml_pwd_subj')), $message);
                notification::add_success(_lostpwd_valid);
            }
        }

        $smarty->caching = false;
        $smarty->assign('dsgvo_url', 'dsfsdfsdfsdfsdfsdfsdfsdf', true);
        $smarty->assign('lock', !common::HasDSGVO(), true);
        $smarty->assign('notification_page', notification::get());
        $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/access/lostpwd.tpl');
        $smarty->clearAllAssign();
    } else {
        $index = common::error(_error_user_already_in, 1);
    }
}