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
$where = $where.': '._member_admin_header;

switch (common::$do) {
    case "add":
        if(isset($_POST)) {
            if(empty($_POST['group']))
                $show = common::error(_admin_squad_no_squad, 1);
            else {
                common::$sql['default']->insert("INSERT INTO `{prefix_groups}` SET `name` = ?,`beschreibung` = ?",
                    [stringParser::encode($_POST['group']),stringParser::encode($_POST['beschreibung'])]);

                ## Lese letzte ID aus ##
                $insert_id = common::$sql['default']->lastInsertId();

                ## Erstelle Gruppen-Upload Ordner ##
                fileman::CreateGroupDir($insert_id);

                $show = common::info(_admin_squad_add_successful, "?admin=gruppen");
            }
        }

        $smarty->caching = false;
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/groups_add.tpl');
        $smarty->clearAllAssign();

    break;
    case 'delete':
        common::$sql['default']->delete("DELETE FROM `{prefix_groups}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        common::$sql['default']->delete("DELETE FROM `{prefix_group_user}` WHERE `group` = ?;", [(int)($_GET['id'])]);

        ## Losche Gruppen-Upload Ordner ##
        fileman::RemoveGroupDir((int)($_GET['id']));

        $show = common::info(_admin_squad_deleted, "?admin=gruppen");
    break;
    case 'edit':
        if(isset($_POST)) {
            if (empty($_POST['group']))
                $show = common::error(_admin_squad_no_squad, 1);
            else {
                common::$sql['default']->update("UPDATE `{prefix_groups}` SET `name` = ?, `beschreibung` = ? WHERE `id` = ?;",
                    [stringParser::encode($_POST['group']),stringParser::encode($_POST['beschreibung']),(int)($_GET['id'])]);

                $show = common::info(_admin_squad_edit_successful, "?admin=gruppen");
            }
        }

        $get = common::$sql['default']->fetch("SELECT `id`,`name`,`beschreibung` FROM `{prefix_groups}` WHERE id = '".(int)($_GET['id'])."'");
        $smarty->caching = false;
        $smarty->assign('id',$get['id']);
        $smarty->assign('sgroup',stringParser::decode($get['name']));
        $smarty->assign('beschreibung',stringParser::decode($get['beschreibung']));
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/groups_edit.tpl');
        $smarty->clearAllAssign();

    break;
    default:
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_groups}` ORDER BY id"); $groups = '';
        foreach($qry as $get) {
            $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_team);

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $smarty->caching = false;
            $smarty->assign('squad',stringParser::decode($get['name']));
            $smarty->assign('edit',$edit);
            $smarty->assign('class',$class);
            $smarty->assign('delete',$delete);
            $groups .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/groups_show.tpl');
            $smarty->clearAllAssign();
        }

        $smarty->caching = false;
        $smarty->assign('groups',$groups);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/groups.tpl');
        $smarty->clearAllAssign();

    break;
}