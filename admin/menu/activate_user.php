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

if(_adminMenu != 'true') exit;
$where = $where.': '._config_activate_user;

switch (common::$do) {
    case 'activate':
        common::$sql['default']->update("UPDATE `{prefix_users}` SET `level` = 1, `status` = 1, `actkey` = '' WHERE `id` = ?;", [(int)$_GET['id']]);
        $show = common::info(_actived, "?admin=activate_user", 2);
    break;
    case 'delete':
        if(($id = isset($_GET['id']) ? $_GET['id'] : false) != false) {
            common::$sql['default']->delete("DELETE FROM `{prefix_users}` WHERE `id` = ?;", [(int)$id]);
            common::$sql['default']->delete("DELETE FROM `{prefix_permissions}` WHERE `user` = ?;", [(int)$id]);
            common::$sql['default']->delete("DELETE FROM `{prefix_user_stats}` WHERE `user` = ?;", [(int)$id]);
            $show = common::info(_user_deleted, "?admin=activate_user", 3);
        }
    break;
    case 'delete-all':
        if(isset($_POST['userid']) && count($_POST['userid']) >= 1) {
            foreach($_POST['userid'] as $id) {
                common::$sql['default']->delete("DELETE FROM `{prefix_users}` WHERE `id` = ?;", [(int)$id]);
                common::$sql['default']->delete("DELETE FROM `{prefix_permissions}` WHERE `user` = ?;", [(int)$id]);
                common::$sql['default']->delete("DELETE FROM `{prefix_user_stats}` WHERE `user` = ?;", [(int)$id]);
            }

            $show = common::info(_users_deleted, "?admin=activate_user", 4);
        }
    break;
    case 'enable-all':
        if(isset($_POST['userid']) && count($_POST['userid']) >= 1) {
            foreach ($_POST['userid'] as $id) {
                common::$sql['default']->update("UPDATE `{prefix_users}` SET `level` = 1, `status` = 1, `actkey` = '' WHERE `id` = ?;", [(int)$id]);
            }

            $show = common::info(_actived_all, "?admin=activate_user", 3);
        }
    break;
    case 'resend':
        if(($id = isset($_GET['id']) ? $_GET['id'] : false) != false) {
            $get = common::$sql['default']->fetch("SELECT `user`,`id`,`email` FROM `{prefix_users}` WHERE `id` = ?;", [$id]);
            common::userstats_increase('akl',$get['id']);
            common::$sql['default']->update("UPDATE `{prefix_users}` SET `actkey` = ? WHERE `id` = ?;", [($guid=common::GenGuid()),$get['id']]);
            $akl_link = 'http://'.common::$httphost.'/user/?action=akl&do=activate&key='.$guid;
            $akl_link_page = 'http://'.common::$httphost.'/user/?action=akl&do=activate';

            $smarty->caching = false;
            $smarty->assign('nick',stringParser::decode($get['user']));
            $smarty->assign('link_page','<a href="'.$akl_link_page.'" target="_blank">'.$akl_link_page.'</a>');
            $smarty->assign('guid',$guid);
            $smarty->assign('link','<a href="'.$akl_link.'" target="_blank">Link</a>');
            $message = $smarty->fetch('string:'.BBCode::bbcode_email(settings::get('eml_akl_register')));
            $smarty->clearAllAssign();

            $smarty->caching = false;
            $smarty->assign('email',$get['email']);
            $resend = $smarty->fetch('string:'._admin_akl_resend);
            $smarty->clearAllAssign();

            common::sendMail(stringParser::decode($get['email']), stringParser::decode(settings::get('eml_akl_register_subj')), $message);

            $show = common::info($resend, "?admin=activate_user", 4);
        }
    break;
    case 'send-all':
        if(isset($_POST['userid']) && count($_POST['userid']) >= 1) {
            $emails = ''; $i = 0;
            foreach($_POST['userid'] as $id) {
                $get = common::$sql['default']->fetch("SELECT `user`,`id`,`email` FROM `{prefix_users}` WHERE `id` = ?;", [$id]);
                common::userstats_increase('akl',$get['id']);
                common::$sql['default']->update("UPDATE `{prefix_users}` SET `actkey` = '".($guid=common::GenGuid())."' WHERE `id` = ?;", [$get['id']]);
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
                $emails .= (!$i ? $get['email'] : ', '.$get['email']); $i++;
            }

            $smarty->caching = false;
            $smarty->assign('email',$emails);
            $resend = $smarty->fetch('string:'._admin_akl_resend);
            $smarty->clearAllAssign();

            $show = common::info($resend, "?admin=activate_user", 8);
        }
    break;
    default:
        $qry = common::$sql['default']->select("SELECT `id`,`bday` FROM `{prefix_users}` WHERE `level` = 0 AND `actkey` IS NOT NULL ORDER BY nick;");
        $activate = ''; $color = 1;
        foreach($qry as $get) {

            $smarty->caching = false;
            $smarty->assign('email','?admin=activate_user&amp;do=resend&amp;id='.$get['id']);
            $resend = $smarty->fetch('string:'._emailicon_non_mailto);
            $smarty->clearAllAssign();

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;

            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $smarty->assign('action',"../user/?action=admin&edit=");
            $smarty->assign('title',_button_title_edit);
            $edit = $smarty->fetch('file:['.common::$tmpdir.']page/buttons/button_edit_akl.tpl');
            $smarty->clearAllAssign();

            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $smarty->assign('action',"admin=activate_user&amp;do=activate&amp;id=");
            $smarty->assign('title',_button_title_akl);
            $akl = $smarty->fetch('file:['.common::$tmpdir.']page/buttons/button_akl.tpl');
            $smarty->clearAllAssign();

            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $smarty->assign('action',"admin=activate_user&amp;do=delete");
            $smarty->assign('title',_button_title_del);
            $delete = $smarty->fetch('file:['.common::$tmpdir.']page/buttons/button_delete.tpl');
            $smarty->clearAllAssign();

            $smarty->caching = false;
            $smarty->assign('nick',common::autor($get['id'],'', 0, '',25));
            $smarty->assign('akt',$akl);
            $smarty->assign('resend',$resend);
            $smarty->assign('age',common::getAge($get['bday']));
            $smarty->assign('sended',common::userstats('akl',$get['id']));
            $smarty->assign('edit',$edit);
            $smarty->assign('delete',$delete);
            $smarty->assign('class',$class);
            $smarty->assign('id', $get['id']);
            $smarty->assign('onoff',common::onlinecheck($get['id']));
            $activate .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/activate_user_show.tpl');
            $smarty->clearAllAssign();
        }

        if(empty($activate)) {
            $activate = '<tr><td colspan="9" class="contentMainSecond">'._no_entrys.'</td></tr>';
        }

        $smarty->caching = false;
        $smarty->assign('value',_button_value_search);
        $smarty->assign('show',$activate);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/activate_user.tpl');
        $smarty->clearAllAssign();
    break;
}