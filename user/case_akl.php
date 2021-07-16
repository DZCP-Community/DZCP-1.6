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
    switch (common::$do) {
        case 'send':
            if (isset($_SESSION['akl_id']) && !empty($_SESSION['akl_id'])) {
                $get = common::$sql['default']->fetch("SELECT `user`,`id`,`email`,`level`,`actkey` FROM `{prefix_users}` WHERE `id` = ?;",
                        [$_SESSION['akl_id']]);
            } else {
                $get = common::$sql['default']->fetch("SELECT `user`,`id`,`email`,`level`,`actkey` FROM `{prefix_users}` WHERE `email` = ?;",
                        [isset($_GET['email']) ? stringParser::encode($_GET['email']) : '']);
            }

            if(common::$sql['default']->rowCount()) {
                if(!$get['level'] && !empty($get['actkey'])) {
                    common::userstats_increase('akl',$get['id']);
                    common::$sql['default']->update("UPDATE `{prefix_users}` SET `actkey` = ? WHERE `id` = ?;",
                            [stringParser::encode($guid = common::GenGuid()),$get['id']]);
                    $akl_link = 'http://'.common::$httphost.'/user/?action=akl&do=activate&key='.$guid;
                    $akl_link_page = 'http://'.common::$httphost.'/user/?action=akl&do=activate';

                    $smarty->caching = false;
                    $smarty->assign('nick',stringParser::decode($get['user']));
                    $smarty->assign('link_page','<a href="'.$akl_link_page.'" target="_blank">'.$akl_link_page.'</a>');
                    $smarty->assign('guid',$guid);
                    $smarty->assign('link','<a href="'.$akl_link.'" target="_blank">Link</a>');
                    $message = $smarty->fetch('string:'.BBCode::bbcode_email(settings::get('eml_akl_register')));
                    $smarty->clearAllAssign();

                    common::sendMail(stringParser::decode($get['email']), stringParser::decode(settings::get('eml_akl_register_subj')), $message);

                    $smarty->caching = false;
                    $smarty->assign('email',stringParser::decode($get['email']));
                    $info = $smarty->fetch('string:'._reg_akl_sended);
                    $smarty->clearAllAssign();
                    $index = common::info($info, "?action=login",5,false);
                } else if (!$get['level'] && empty($get['actkey'])) {
                    $index = common::info(_reg_akl_locked, "../news/", 5, false);
                } else {
                    common::$sql['default']->update("UPDATE `{prefix_users}` SET `actkey` = '' WHERE `id` = ?;", [$get['id']]);
                    $index = common::info(_reg_akl_activated, "../news/", 5, false);
                }
            } else
                $index = common::info(_reg_akl_email_nf, "../news/", 5, false);
        break;
        case 'activate':
            if ((isset($_GET['key']) && !empty($_GET['key'])) || (isset($_POST['key']) && !empty($_POST['key']))) {
                $get = common::$sql['default']->fetch("SELECT `id` FROM `{prefix_users}` WHERE `actkey` = ?;",
                    [strtoupper(trim(isset($_POST['key']) ? $_POST['key'] : $_GET['key']))]);
                if (common::$sql['default']->rowCount()) {
                    common::$sql['default']->update("UPDATE `{prefix_users}` SET `level` = 1, `status` = 1, `actkey` = '' WHERE `id` = ?;",
                        [$get['id']]);
                    $index = common::info(_reg_akl_valid, "../user/?action=login");
                } else {
                    $index = common::info(_reg_akl_invalid, "../user/?action=akl");
                }
            } else {
                $smarty->caching = false;
                $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/access/activate_code.tpl');
            }
            break;
        default:
            $smarty->caching = false;
            $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/access/activate_code.tpl');
        break;
    }
}