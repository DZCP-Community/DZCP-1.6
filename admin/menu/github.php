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
$where = $where.': '._config_github;

switch (common::$do) {
    case 'new':
        if($_POST) {
            if (empty($_POST['link']) || empty($_POST['name'])) {
                if (empty($_POST['link']))
                    notification::add_error(_links_empty_link);
                elseif (empty($_POST['name']))
                    notification::add_error(_github_empty_name);
            } else {
                common::$sql['default']->insert("INSERT INTO `{prefix_projekts}` SET `name` = ?, `link` = ?, `enabled` = ?;",
                    [stringParser::encode($_POST['name']), stringParser::encode(common::links($_POST['link'])),((int)$_POST['enabled'])]);
                notification::add_success(_link_added,'global', '?admin=github',2);
            }
        }

        $smarty->caching = false;
        $smarty->assign('head',_links_admin_head);
        $smarty->assign('selected', !empty($_POST['selected']) && (int)$_POST['selected'] ? 'selected' : '');
        $smarty->assign('name',!empty($_POST['name']) ? $_POST['name'] : '');
        $smarty->assign('link',!empty($_POST['link']) ? $_POST['link'] : '');
        $smarty->assign('what',_button_value_add);
        $smarty->assign('do',"new");
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_github_links.tpl');
        $smarty->clearAllAssign();
    break;
    case 'edit':
        if(array_key_exists('id',$_GET)) {
            $id = (int)($_GET['id']);
            $_SESSION['last_edit_id'] = $id;
        }

        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_projekts}` WHERE `id` = ?;",[$_SESSION['last_edit_id']]);
        if($_POST) {
            if (empty($_POST['link']) || empty($_POST['name'])) {
                if (empty($_POST['link']))
                    notification::add_error(_links_empty_link);
                elseif (empty($_POST['name']))
                    notification::add_error(_github_empty_name);
            } else {
                common::$sql['default']->update("UPDATE `{prefix_projekts}` SET `name` = ?, `link` = ?, `enabled` = ? WHERE `id` = ?;",
                    [stringParser::encode($_POST['name']), stringParser::encode(common::links($_POST['link'])),((int)$_POST['enabled']),$_SESSION['last_edit_id']]);
                notification::add_success(_link_edited,'global', '?admin=github',2);
                unset($_SESSION['last_edit_id']);
            }
        }

        $smarty->caching = false;
        $smarty->assign('head',_links_admin_head_edit);
        $smarty->assign('selected',$get['enabled'] ? 'selected' : '');
        $smarty->assign('name', stringParser::decode($get['name']));
        $smarty->assign('link',stringParser::decode($get['link']));
        $smarty->assign('what',_button_value_edit);
        $smarty->assign('do',"edit");
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_github_links.tpl');
        $smarty->clearAllAssign();
    break;
    case 'delete':
        common::$sql['default']->delete("DELETE FROM `{prefix_projekts}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        $show = common::info(_link_deleted, "?admin=github");
    break;
    case 'public':
        $get = common::$sql['default']->fetch("SELECT `enabled` FROM `{prefix_projekts}` WHERE `id` = ?;",[(int)($_GET['id'])]);
        common::$sql['default']->update("UPDATE `{prefix_projekts}` SET `enabled` = ? WHERE `id` = ?;",[$get['enabled'] ? 0 : 1,(int)($_GET['id'])]);
        header("Location: ?admin=github");
        break;
    default:
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_projekts}` ORDER BY `name` ASC;");
        foreach($qry as $get) {
            $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_link);
            $public = ($get['enabled'] ? '<a href="?admin=github&amp;do=public&amp;id='.$get['id'].'"><img src="../inc/images/yes.gif" alt="" title="'._github_link_no.'" /></a>'
                : '<a href="?admin=github&amp;do=public&amp;id='.$get['id'].'"><img src="../inc/images/no.gif" alt="" title="'._github_link_yes.'" /></a>');

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $smarty->caching = false;
            $smarty->assign('link',common::links(stringParser::decode($get['link'])));
            $smarty->assign('name',stringParser::decode($get['name']));
            $smarty->assign('class',$class);
            $smarty->assign('public',$public);
            $smarty->assign('edit',$edit);
            $smarty->assign('delete',$delete);
            $smarty->assign('id',$get['id']);
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/github_links_show.tpl');
            $smarty->clearAllAssign();
        }

        if(empty($show)) {
            $show = '<tr><td colspan="3" class="contentMainSecond">'._no_entrys.'</td></tr>';
        }

        $smarty->caching = false;
        $smarty->assign('show',$show);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/github.tpl');
        $smarty->clearAllAssign();
    break;
}