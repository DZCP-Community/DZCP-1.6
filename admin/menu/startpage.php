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

switch (common::$do) {
    case 'delete':
        common::$sql['default']->delete("DELETE FROM `{prefix_startpage}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        notification::add_success(_admin_startpage_deleted, "?admin=startpage");
    break;
    case 'edit':
        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_startpage}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        if(isset($_POST['name']) && isset($_POST['url']) && isset($_POST['level'])) {
            if(empty($_POST['name']))
                notification::add_error(_admin_startpage_no_name);
            else if(empty($_POST['url']))
                notification::add_error(_admin_startpage_no_url);
            else {
                common::$sql['default']->update("UPDATE `{prefix_startpage}` SET `name` = ?, `url` = ?, `level` = ? WHERE `id` = ?;",
                        [stringParser::encode($_POST['name']),stringParser::encode($_POST['url']),(int)($_POST['level']),(int)($_GET['id'])]);
                
                notification::add_success(_admin_startpage_editd, "?admin=startpage");
            }
        }

        if(!notification::is_success()) {
            if(notification::has()) {
                javascript::set('AnchorMove', 'notification-box');
            }

            $errortable='';
            if(!empty($error)) {
                $smarty->caching = false;
                $smarty->assign('error', $error);
                $errortable = $smarty->fetch('file:['.common::$tmpdir.']errors/errortable.tpl');
                $smarty->clearAllAssign();
            }

            $smarty->caching = false;
            $smarty->assign('head',_admin_startpage_edit);
            $smarty->assign('do', "edit&amp;id=".$_GET['id']);
            $smarty->assign('name',(isset($_POST['name']) && !empty($_POST['name']) ? $_POST['name'] : stringParser::decode($get['name'])));
            $smarty->assign('url',(isset($_POST['url']) ? $_POST['url'] : stringParser::decode($get['url'])));
            $smarty->assign('level',common::level_select($get['level']));
            $smarty->assign('what',_button_value_edit);
            $smarty->assign('error',$errortable);
            $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/startpage_form.tpl');
            $smarty->clearAllAssign();
        }
    break;
    case 'new':
        if(isset($_POST['name']) && isset($_POST['url']) && isset($_POST['level'])) {
            if(empty($_POST['name']))
                notification::add_error(_admin_startpage_no_name);
            else if(empty($_POST['url']))
                notification::add_error(_admin_startpage_no_url);
            else {
                common::$sql['default']->insert("INSERT INTO `{prefix_startpage}` SET `name` = ?, `url` = ?, `level` = ?;",
                        [stringParser::encode($_POST['name']),stringParser::encode($_POST['url']),(int)($_POST['level'])]);
                
                notification::add_success(_admin_startpage_added, "?admin=startpage");
            }
        }

        if(!notification::is_success()) {
            if(notification::has()) {
                javascript::set('AnchorMove', 'notification-box');
            }

            $errortable='';
            if(!empty($error)) {
                $smarty->caching = false;
                $smarty->assign('error', $error);
                $errortable = $smarty->fetch('file:['.common::$tmpdir.']errors/errortable.tpl');
                $smarty->clearAllAssign();
            }

            $smarty->caching = false;
            $smarty->assign('head',_admin_startpage_add_head);
            $smarty->assign('do',"new");
            $smarty->assign('name',(isset($_POST['name']) ? $_POST['name'] : ''));
            $smarty->assign('url',(isset($_POST['url']) ? $_POST['url'] : ''));
            $smarty->assign('level',common::level_select());
            $smarty->assign('what',_button_value_add);
            $smarty->assign('error',$errortable);
            $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/startpage_form.tpl');
            $smarty->clearAllAssign();
        }
    break;
    default:
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_startpage}`;"); $color = 0; $show = '';
        foreach($qry as $get) {
            $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_entry);
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $smarty->caching = false;
            $smarty->assign('edit',$edit);
            $smarty->assign('name', stringParser::decode($get['name']));
            $smarty->assign('url',stringParser::decode($get['url']));
            $smarty->assign('class',$class);
            $smarty->assign('delete',$delete);
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/startpage_show.tpl');
            $smarty->clearAllAssign();;
        }

        if(empty($show)) {
            $smarty->caching = false;
            $smarty->assign('colspan',4);
            $show = $smarty->fetch('string:'._no_entrys_yet);
            $smarty->clearAllAssign();
        }

        $smarty->caching = false;
        $smarty->assign('show',$show);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/startpage.tpl');
        $smarty->clearAllAssign();
    break;
}