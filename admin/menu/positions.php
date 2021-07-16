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

if(_adminMenu != 'true') exit();
$where = $where.': '._admin_pos;

switch (common::$do) {
    case 'edit':
        $qry = common::$sql['default']->select("SELECT `pid`,`position` FROM `{prefix_positions}` ORDER BY `pid` DESC;"); $positions = '';
        foreach($qry as $get) {
            $positions .= common::select_field(($get['pid']+1),false,_nach.' '.stringParser::decode($get['position']));
        }

        $id = (int)($_GET['id']);
        $get = common::$sql['default']->fetch("SELECT `position`,`color` FROM `{prefix_positions}` WHERE `id` = ?;", [$id]);
        $smarty->caching = false;
        $smarty->assign('newhead',_pos_edit_head);
        $smarty->assign('do',"editpos&amp;id=".$id."");
        $smarty->assign('kat',stringParser::decode($get['position']));
        $smarty->assign('color',stringParser::decode($get['color']));
        $smarty->assign('getpermissions',common::getPermissions($id, 1));
        $smarty->assign('getboardpermissions', common::getBoardPermissions($id, 1));
        $smarty->assign('positions',$positions);
        $smarty->assign('what',_button_value_edit);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_pos.tpl');
        $smarty->clearAllAssign();
        unset($positions,$qry,$get);
    break;
    case 'editpos':
        if(empty($_POST['kat'])) {
            $show = common::error(_pos_empty_kat,1);
        } else {
            $id = (int)($_GET['id']);
            if($_POST['pos'] != 'lazy') {
                $posid = (int)($_POST['pos']);
                common::$sql['default']->update("UPDATE `{prefix_positions}` SET `pid` = (pid+1) WHERE `pid` " . ($_POST['pos'] == "1" || $_POST['pos'] == "2" ? ">= " : "> ")
                    . " ?;", [(int)($_POST['pos'])]);
            }

            common::$sql['default']->update("UPDATE `{prefix_positions}` SET `position` = ? ".
                    ($_POST['pos'] == "lazy" ? "" : ",`pid` = ".(int)($_POST['pos'])).", `color` = ? WHERE `id` = ?;",
                    [stringParser::encode($_POST['kat']),stringParser::encode($_POST['color']),$id]);

            // Permissions Update
            if(empty($_POST['perm'])) {
                $_POST['perm'] = [];
            }

            $qry_fields = common::$sql['default']->show("SHOW FIELDS FROM `{prefix_permissions}`;"); $sql_update = '';
            foreach($qry_fields as $get) {
                if($get['Field'] != 'id' && $get['Field'] != 'user' && $get['Field'] != 'pos' && $get['Field'] != 'intforum') {
                    $qry = array_key_exists('p_'.$get['Field'], $_POST['perm']) ? '`'.$get['Field'].'` = 1' : '`'.$get['Field'].'` = 0';
                    $sql_update .= $qry.', ';
                }
            }

            // Check group Permissions is exists
            if(!common::$sql['default']->rows('SELECT `id` FROM `{prefix_permissions}` WHERE `pos` = ? LIMIT 1;', [$id])) {
                common::$sql['default']->insert("INSERT INTO `{prefix_permissions}` SET `pos` = ?;", [$id]);
            }

            // Update Permissions
            common::$sql['default']->update('UPDATE `{prefix_permissions}` SET '.substr($sql_update, 0, -2).' WHERE `pos` = ? LIMIT 1;', [$id]);

            // Internal Boardpermissions Update
            if(empty($_POST['board'])) {
                $_POST['board'] = [];
            }

            // Cleanup Boardpermissions
            $qry = common::$sql['default']->select('SELECT `id`,`forum` FROM `{prefix_forum_access}` WHERE `pos` = ?;', [$id]);
            foreach($qry as $get) {
                if(!common::array_var_exists($get['forum'],$_POST['board'])) {
                    common::$sql['default']->delete('DELETE FROM `{prefix_forum_access}` WHERE `id` = ?;', [$get['id']]);
                }
            }

            //Add new Boardpermissions
            if(count($_POST['board']) >= 1) {
                foreach($_POST['board'] AS $boardpem) { 
                    if(!common::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_access}` WHERE `pos` = ? AND `forum` = ?;", [$id,$boardpem])) {
                        common::$sql['default']->insert("INSERT INTO `{prefix_forum_access}` SET `pos` = ?, `forum` = ?;", [$id,$boardpem]);
                    }
                }
            }

            $show = common::info(_pos_admin_edited, "?admin=positions");
        }
    break;
    case 'delete':
        $get = common::$sql['default']->fetch("SELECT `id` FROM `{prefix_positions}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        if(common::$sql['default']->rowCount()) {
            common::$sql['default']->delete("DELETE FROM `{prefix_positions}` WHERE `id` = ?;", [$get['id']]);
            common::$sql['default']->delete("DELETE FROM `{prefix_permissions}` WHERE `pos` = ?;", [$get['id']]);
            $show = common::info(_pos_admin_deleted, "?admin=positions");
        }
    break;
    case 'new':
        $qry = common::$sql['default']->select("SELECT `pid`,`position` FROM `{prefix_positions}` ORDER BY `pid` DESC;"); $positions = '';
        foreach($qry as $get) {
            $positions .= common::select_field(($get['pid']+1),false,_nach.' '.stringParser::decode($get['position']));
        }

        $smarty->caching = false;
        $smarty->assign('newhead',_pos_new_head);
        $smarty->assign('do',"add");
        $smarty->assign('getpermissions',common::getPermissions());
        $smarty->assign('getboardpermissions',common::getBoardPermissions());
        $smarty->assign('nothing','');
        $smarty->assign('positions',$positions);
        $smarty->assign('kat','');
        $smarty->assign('color', "#000000");
        $smarty->assign('what',_button_value_add);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_pos.tpl');
        $smarty->clearAllAssign();
        unset($positions,$qry,$get);
    break;
    case 'add':
        if(empty($_POST['kat'])) {
            $show = common::error(_pos_empty_kat,1);
        } else {
            common::$sql['default']->update("UPDATE `{prefix_positions}` SET `pid` = (pid+1) WHERE `pid`;".
                    ($_POST['pos'] == "1" || $_POST['pos'] == "2" ? ">= " : "> ")." ?;",
                    [(int)($_POST['pos'])]);
            common::$sql['default']->insert("INSERT INTO `{prefix_positions}` SET `pid` = ?, `position` = ?, `color` = ?;",
                [(int)($_POST['pos']),stringParser::encode($_POST['kat']),stringParser::encode($_POST['color'])]);
            
            $posID = common::$sql['default']->lastInsertId();
            $qry = common::$sql['default']->show("SHOW FIELDS FROM `{prefix_permissions}`;"); $sql_update = '';
            foreach($qry as $get) {
                if($get['Field'] != 'id' && $get['Field'] != 'user' && $get['Field'] != 'pos' && $get['Field'] != 'intforum') {
                    $qry = array_key_exists('p_'.$get['Field'], $_POST['perm']) ? '`'.$get['Field'].'` = 1' : '`'.$get['Field'].'` = 0';
                    $sql_update .= $qry.', ';
                }
            }
            
            // Add Permissions
            common::$sql['default']->insert('INSERT INTO `{prefix_permissions}` SET '.$sql_update.'`pos` = ?;', [$posID]);

            // Internal Boardpermissions Update
            if(empty($_POST['board'])) {
                $_POST['board'] = [];
            }

            //Add new Boardpermissions
            if(count($_POST['board']) >= 1) {
                foreach($_POST['board'] AS $boardpem) { 
                    if(!common::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_access}` WHERE `pos` = ? AND `forum` = ?;", [$posID,$boardpem])) {
                        common::$sql['default']->insert("INSERT INTO `{prefix_forum_access}` SET `pos` = ?, `forum` = ?;", [$posID,$boardpem]);
                    }
                }
            }

            $show = common::info(_pos_admin_added, "?admin=positions");
        }
    break;
    default:
        $qry = common::$sql['default']->select("SELECT `id`,`position` FROM `{prefix_positions}` ORDER BY `pid` DESC;"); $show_pos = '';
        foreach($qry as $get) {
            $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_entry);

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $smarty->caching = false;
            $smarty->assign('edit',$edit);
            $smarty->assign('name',stringParser::decode($get['position']));
            $smarty->assign('class',$class);
            $smarty->assign('delete',$delete);
            $show_pos .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/positions_show.tpl');
            $smarty->clearAllAssign();
        }

        if(empty($show_pos)) {
            $smarty->caching = false;
            $smarty->assign('colspan',3);
            $show_pos = $smarty->fetch('string:'._no_entrys_yet);
            $smarty->clearAllAssign();
        }

        $smarty->caching = false;
        $smarty->assign('show',$show_pos);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/positions.tpl');
        $smarty->clearAllAssign();
        unset($show_pos,$qry,$get);
    break;
}