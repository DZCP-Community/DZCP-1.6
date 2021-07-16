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

$where = $where.': '._editor_head;

switch(common::$do) {
    case 'add':
        $qry = common::$sql['default']->select("SELECT s2.*, s1.`name` AS `katname`, s1.`placeholder` "
                . "FROM `{prefix_navi_kats}` AS `s1` "
                . "LEFT JOIN `{prefix_navi}` AS `s2` "
                . "ON s1.`placeholder` = s2.`kat` "
                . "ORDER BY s1.`name`, s2.`pos`;");

        $thiskat = ''; $position = '';
        foreach($qry as $get) {
            if($thiskat != $get['kat']) {
                $position .= '<option class="selectpicker" value="lazy">'.stringParser::decode($get['katname']).'</option>
                              <option value="'.stringParser::decode($get['placeholder']).'-1">-> '._admin_first.'</option>';
            }

            $thiskat = $get['kat'];
            $sel = ($get['editor'] == (isset($_GET['id']) ? $_GET['id'] : 0)) ? 'selected="selected"' : '';
            $position .= empty($get['name']) ? '' : '<option value="'.stringParser::decode($get['placeholder']).'-'.($get['pos']+1).'" '.$sel.'>'._nach.' -> '.common::navi_name(stringParser::decode($get['name'])).'</option>';
        }

        $smarty->caching = false;
        $smarty->assign('head',_editor_add_head);
        $smarty->assign('what',_button_value_add);
        $smarty->assign('titel',_titel);
        $smarty->assign('preview',_preview);
        $smarty->assign('e_titel','');
        $smarty->assign('e_inhalt','');
        $smarty->assign('checked','');
        $smarty->assign('checked_php','');
        $smarty->assign('disabled_php', (php_code_enabled ? '' : ' disabled'));
        $smarty->assign('pos',_position);
        $smarty->assign('name',_editor_linkname);
        $smarty->assign('n_name','');
        $smarty->assign('position',$position);
        $smarty->assign('ja',_yes);
        $smarty->assign('nein',_no);
        $smarty->assign('wichtig',_navi_wichtig);
        $smarty->assign('error','');
        $smarty->assign('allow_html',_editor_allow_html);
        $smarty->assign('inhalt',_inhalt);
        $smarty->assign('do',"addsite");
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_editor.tpl');
        $smarty->clearAllAssign();

    break;
    case 'addsite':
        if(empty($_POST['titel']) || empty($_POST['inhalt']) || $_POST['pos'] == "lazy") {
            if(empty($_POST['titel']))
                $error = _empty_titel;
            elseif(empty($_POST['inhalt']))
                $error = _empty_editor_inhalt;
            elseif($_POST['pos'] == "lazy")
                $error = _navi_no_pos;

            $smarty->caching = false;
            $smarty->assign('error',$error);
            $error = $smarty->fetch('file:['.common::$tmpdir.']/errors/errortable.tpl');
            $smarty->clearAllAssign();

            $checked = isset($_POST['html']) ? 'checked="checked"' : '';
            $checked_php = isset($_POST['php']) ? 'checked="checked"' : '';
            $kat_ = preg_replace('/-(\d+)/','',$_POST['pos']);
            $pos_ = preg_replace("=nav_(.*?)-=","",$_POST['pos']);

            $qry = common::$sql['default']->select("SELECT s2.*, s1.`name` AS `katname`, s1.`placeholder` "
                    . "FROM `{prefix_navi_kats}` AS `s1` "
                    . "LEFT JOIN `{prefix_navi}` AS `s2` "
                    . "ON s1.`placeholder` = s2.`kat` "
                    . "ORDER BY s1.`name`, s2.`pos`;");
            $thiskat = ''; $position = '';
            foreach($qry as $get) {
                if($thiskat != $get['kat']) {
                    $position .= '<option class="selectpicker" value="lazy">'.stringParser::decode($get['katname']).'</option>
                    <option value="'.stringParser::decode($get['placeholder']).'-1">-> '._admin_first.'</option>';
                }

                $thiskat = $get['kat'];
                $sel = ($get['kat'] == $kat_ && ($get['pos']+1) == $pos_) ? 'selected="selected"' : '';
                $position .= empty($get['name']) ? '' : '<option value="'.stringParser::decode($get['placeholder']).'-'.($get['pos']+1).'" '.$sel.'>'._nach.' -> '.common::navi_name(stringParser::decode($get['name'])).'</option>';
            }

            $smarty->caching = false;
            $smarty->assign('head',_editor_add_head);
            $smarty->assign('what',_button_value_add);
            $smarty->assign('preview',_preview);
            $smarty->assign('error',$error);
            $smarty->assign('checked',$checked);
            $smarty->assign('checked_php',$checked_php);
            $smarty->assign('disabled_php',(php_code_enabled ? '' : ' disabled'));
            $smarty->assign('pos',_position);
            $smarty->assign('ja',_yes);
            $smarty->assign('nein',_no);
            $smarty->assign('name',_editor_linkname);
            $smarty->assign('position',$position);
            $smarty->assign('n_name',stringParser::decode($_POST['name']));
            $smarty->assign('wichtig',_navi_wichtig);
            $smarty->assign('titel',_titel);
            $smarty->assign('e_titel',stringParser::decode($_POST['titel']));
            $smarty->assign('e_inhalt',stringParser::decode($_POST['inhalt']));
            $smarty->assign('allow_html',_editor_allow_html);
            $smarty->assign('inhalt',_inhalt);
            $smarty->assign('do',"addsite");
            $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_editor.tpl');
            $smarty->clearAllAssign();
        } else {
            $_POST['html'] = (isset($_POST['html']) ? $_POST['html'] : 0);
            $_POST['php'] = (isset($_POST['php']) ? $_POST['php'] : 0);
            common::$sql['default']->insert("INSERT INTO `{prefix_sites}` SET `titel` = ?, `text` = ?, `html` = ?, `php` = ?;",
                    [stringParser::encode($_POST['titel']),stringParser::encode($_POST['inhalt']),(int)($_POST['html']),(php_code_enabled ? (int)($_POST['php']) : 0)]);

            $insert_id = common::$sql['default']->lastInsertId();
            $sign = (isset($_POST['pos']) && ($_POST['pos'] == "1" || $_POST['pos'] == "2")) ? ">= " : "> ";
            $kat = preg_replace('/-(\d+)/','',$_POST['pos']);
            $pos = preg_replace("=nav_(.*?)-=","",$_POST['pos']);
            $url = "../sites/?show=".$insert_id."";

            common::$sql['default']->update("UPDATE `{prefix_navi}` SET `pos` = (pos+1) WHERE `pos` ".$sign." ?;", [(int)($pos)]);
            common::$sql['default']->insert("INSERT INTO `{prefix_navi}` SET `pos` = ?, `kat` = ?, `name` = ?, `url` = ?, `shown` = 1, `type` = 3, `editor` = ?, `wichtig` = 0;",
                    [(int)($pos),stringParser::encode($kat),stringParser::encode($_POST['name']),stringParser::encode($url),(int)($insert_id)]);

            $show = common::info(_site_added, "?admin=editor");
        }
    break;
    case 'edit':
        $gets = common::$sql['default']->fetch("SELECT * FROM `{prefix_sites}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        $qry = common::$sql['default']->select("SELECT s2.*, s1.`name` AS `katname`, s1.`placeholder` "
                . "FROM `{prefix_navi_kats}` AS `s1` "
                . "LEFT JOIN `{prefix_navi}` AS `s2` "
                . "ON s1.`placeholder` = s2.`kat` "
                . "ORDER BY s1.`name`, s2.`pos`;");

        $thiskat = ''; $position = '';
        foreach($qry as $get) {
            if($thiskat != $get['kat']) {
                $position .= '<option class="selectpicker" value="lazy">'.stringParser::decode($get['katname']).'</option>
                  <option value="'.stringParser::decode($get['placeholder']).'-1">-> '._admin_first.'</option>';
            }

            $thiskat = $get['kat'];
            $sel = ($get['editor'] == $_GET['id']) ? 'selected="selected"' : '';
            $position .= empty($get['name']) ? '' : '<option value="'.stringParser::decode($get['placeholder']).'-'.($get['pos']+1).'" '.$sel.'>'._nach.' -> '.common::navi_name(stringParser::decode($get['name'])).'</option>';
        }

        $getn = common::$sql['default']->fetch("SELECT `name` FROM `{prefix_navi}` WHERE `editor` = ?;", [(int)($_GET['id'])]);
        $checked = ($gets['html'] ? 'checked="checked"' : '');
        $checked_php = $gets['php'] ? 'checked="checked"' : '';


        $smarty->caching = false;
        $smarty->assign('head',_editor_edit_head);
        $smarty->assign('what',_button_value_edit);
        $smarty->assign('preview',_preview);
        $smarty->assign('titel',_titel);
        $smarty->assign('e_titel',stringParser::decode($gets['titel']));
        $smarty->assign('e_inhalt',stringParser::decode($gets['inhalt']));
        $smarty->assign('checked',$checked);
        $smarty->assign('checked_php',$checked_php);
        $smarty->assign('disabled_php',(php_code_enabled ? '' : ' disabled'));
        $smarty->assign('pos',_position);
        $smarty->assign('name',_editor_linkname);
        $smarty->assign('n_name',stringParser::decode($getn['name']));
        $smarty->assign('position',$position);
        $smarty->assign('ja',_yes);
        $smarty->assign('nein',_no);
        $smarty->assign('wichtig',_navi_wichtig);
        $smarty->assign('error','');
        $smarty->assign('allow_html',_editor_allow_html);
        $smarty->assign('inhalt',_inhalt);
        $smarty->assign('do', "editsite&amp;id=".$_GET['id']."");
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_editor.tpl');
        $smarty->clearAllAssign();

    break;
    case 'editsite':
        if(empty($_POST['titel']) || empty($_POST['inhalt']) || $_POST['pos'] == "lazy") {
            if(empty($_POST['titel']))
                $error = _empty_titel;
            elseif(empty($_POST['inhalt']))
                $error = _empty_editor_inhalt;
            elseif($_POST['pos'] == "lazy")
                $error = _navi_no_pos;
            $smarty->caching = false;
            $smarty->assign('error',$error);
            $error = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'errors/errortable.tpl');
            $smarty->clearAllAssign();
            $checked = isset($_POST['html']) ? 'checked="checked"' : '';
            $checked_php = isset($_POST['php']) ? 'checked="checked"' : '';

            $qry = common::$sql['default']->select("SELECT s2.*, s1.`name` AS `katname`, s1.`placeholder` "
                    . "FROM `{prefix_navi_kats}` AS `s1` "
                    . "LEFT JOIN `{prefix_navi}` AS `s2` "
                    . "ON s1.`placeholder` = s2.`kat` "
                    . "ORDER BY s1.`name`, s2.`pos`;");

            $thiskat = ''; $position = '';
            foreach($qry as $get) {
                if($thiskat != $get['kat']) {
                    $position .= '<option class="selectpicker" value="lazy">'.stringParser::decode($get['katname']).'</option>'
                            . '<option value="'.stringParser::decode($get['placeholder']).'-1">-> '._admin_first.'</option>';
                }

                $thiskat = $get['kat'];
                $sel = (isset($_GET['id']) && $get['editor'] == $_GET['id']) ? 'selected="selected"' : '';
                $position .= empty($get['name']) ? '' : '<option value="'.stringParser::decode($get['placeholder']).'-'.($get['pos']+1).'" '.$sel.'>'._nach.' -> '.common::navi_name(stringParser::decode($get['name'])).'</option>';
            }

            $smarty->caching = false;
            $smarty->assign('head',_editor_edit_head);
            $smarty->assign('what',_button_value_edit);
            $smarty->assign('preview',_preview);
            $smarty->assign('error',$error);
            $smarty->assign('checked',$checked);
            $smarty->assign('checked_php',$checked_php);
            $smarty->assign('disabled_php',(php_code_enabled ? '' : ' disabled'));
            $smarty->assign('pos',_position);
            $smarty->assign('ja',_yes);
            $smarty->assign('nein',_no);
            $smarty->assign('name',_editor_linkname);
            $smarty->assign('position',$position);
            $smarty->assign('n_name',stringParser::decode($_POST['name']));
            $smarty->assign('wichtig',_navi_wichtig);
            $smarty->assign('titel',_titel);
            $smarty->assign('e_titel',stringParser::decode($_POST['titel']));
            $smarty->assign('e_inhalt',stringParser::decode($_POST['inhalt']));
            $smarty->assign('allow_html',_editor_allow_html);
            $smarty->assign('inhalt',_inhalt);
            $smarty->assign('do',"editsite&amp;id=".$_GET['id']."");
            $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_editor.tpl');
            $smarty->clearAllAssign();
        } else {
            $_POST['html'] = isset($_POST['html']) ? $_POST['html'] : 0;
            $_POST['php'] = isset($_POST['php']) ? $_POST['php'] : 0;
            common::$sql['default']->update("UPDATE `{prefix_sites}` SET `titel` = ?,`text` = ?,`html` = ?, `php` = ? WHERE `id` = ?;",
                    [stringParser::encode($_POST['titel']),stringParser::encode($_POST['inhalt']),(int)($_POST['html']),(php_code_enabled ? (int)($_POST['php']) : 0),(int)($_GET['id'])]);

            $sign = (isset($_POST['pos']) && ($_POST['pos'] == "1" || $_POST['pos'] == "2")) ? ">= " : "> ";
            $kat = preg_replace('/-(\d+)/','',$_POST['pos']);
            $pos = preg_replace("=nav_(.*?)-=","",$_POST['pos']);

            $url = "../sites/?show=".$_GET['id'];
            common::$sql['default']->update("UPDATE `{prefix_navi}` SET `pos` = (pos+1) WHERE `pos` ".$sign." ?;", [(int)($pos)]);
            common::$sql['default']->update("UPDATE `{prefix_navi}` SET `pos` = ?, `kat` = ?, `name` = ?,`url` = ? WHERE `editor` = ?;",
                    [(int)($pos),stringParser::encode($kat),stringParser::encode($_POST['name']),stringParser::encode($url),(int)($_GET['id'])]);

            $show = common::info(_site_edited, "?admin=editor");
        }
    break;
    case 'delete':
        common::$sql['default']->delete("DELETE FROM `{prefix_sites}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        common::$sql['default']->delete("DELETE FROM `{prefix_navi}` WHERE `editor` = ?;", [(int)($_GET['id'])]);
        $show = common::info(_editor_deleted, "?admin=editor");
    break;
    default:
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_sites}`;");
        foreach($qry as $get) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_site);

            $smarty->caching = false;
            $smarty->assign('name', "<a href='../sites/?show=".$get['id']."'>".stringParser::decode($get['titel'])."</a>");
            $smarty->assign('del',$delete);
            $smarty->assign('edit',$edit);
            $smarty->assign('class',$class);
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/editor_show.tpl');
            $smarty->clearAllAssign();
        }

        if(empty($show)) {
            $smarty->caching = false;
            $smarty->assign('colspan',4);
            $show = $smarty->fetch('string:'._no_entrys_yet);
            $smarty->clearAllAssign();
        }

        $smarty->caching = false;
        $smarty->assign('show',$show);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/editor.tpl');
        $smarty->clearAllAssign();

    break;
}