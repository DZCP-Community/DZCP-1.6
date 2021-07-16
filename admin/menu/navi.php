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

include_once (basePath."/inc/menu-functions/function.navi.php");

$where = $where.': '._navi_head;
switch (common::$do) {
    case 'add':
        $qry = common::$sql['default']->select("SELECT s2.*, s1.name AS katname, s1.placeholder FROM `{prefix_navi_kats}` AS s1 LEFT JOIN `{prefix_navi}` AS s2 ON s1.`placeholder` = s2.`kat` ORDER BY s1.name, s2.pos;");
        $thiskat = ""; $position = "";
        foreach($qry as $get) {
            if($thiskat != stringParser::decode($get['placeholder'])) {
                $position .= '<option class="selectpicker" value="lazy">'.stringParser::decode($get['katname']).'</option>'.
                             '<option value="'.stringParser::decode($get['placeholder']).'-1">-> '._admin_first.'</option>';
            }

            $thiskat = stringParser::decode($get['placeholder']);
            $position .= empty($get['name']) ? ''
                : '<option value="'.stringParser::decode($get['placeholder']).'-'.($get['pos']+1).'">'.
                _nach.' -> '.navi_name(stringParser::decode($get['name'])).'</option>';
        }

        $smarty->caching = false;
        $smarty->assign('do',"addnavi");
        $smarty->assign('what','add');
        $smarty->assign('n_name','');
        $smarty->assign('n_url','');
        $smarty->assign('atarget','');
        $smarty->assign('position',$position);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_navi.tpl');
        $smarty->clearAllAssign();
        unset($thiskat,$position,$qry,$get);
    break;
    case 'editkat':
        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_navi_kats}` WHERE `id` = ?;",[(int)($_GET['id'])]);

        $smarty->caching = false;
        $smarty->assign('is_edit',true);
        $smarty->assign('name',stringParser::decode($get['name']));
        $smarty->assign('placeholder',str_replace('nav_', '', stringParser::decode($get['placeholder'])));
        $smarty->assign('level_user',$get['level']);
        $smarty->assign('do','updatekat&amp;id='.$get['id']);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_navi_kats.tpl');
        $smarty->clearAllAssign();
    break;
    case 'addnavi':
        if(empty($_POST['name']))
        {
            $show = common::error(_navi_no_name,1);
        } elseif(empty($_POST['url'])) {
            $show = common::error(_navi_no_url,1);
        } elseif($_POST['pos'] == "lazy") {
            $show = common::error(_navi_no_pos,1);
        } else {
            $kat = preg_replace('/-(\d+)/','',$_POST['pos']);
            $pos = preg_replace("=nav_(.*?)-=","",$_POST['pos']);

            common::$sql['default']->update("UPDATE `{prefix_navi}` SET `pos` = (pos+1) WHERE `pos` ".($_POST['pos'] == "1" || "2" ? ">= " : "> ")." ".(int)($pos).";");

            common::$sql['default']->insert("INSERT INTO `{prefix_navi}` SET `pos` = ?,`kat` = ?,`name` = ?,`url` = ?,`shown` = 1,`target` = ?,`internal` = ?,`type` = 2,`wichtig` = ?;",
                [(int)($pos),stringParser::encode($kat),stringParser::encode($_POST['name']),stringParser::encode($_POST['url']),(int)($_POST['target']),
                    (int)($_POST['internal']),(int)($_POST['wichtig'])]);

            $show = common::info(_navi_added,"?admin=navi");
        }
    break;
    case 'delete':
        $get = common::$sql['default']->fetch("SELECT `editor`,`id` FROM `{prefix_navi}` WHERE `id` = ?;",[(int)($_GET['id'])]);
        if(common::$sql['default']->rowCount()) {
            common::$sql['default']->delete("DELETE FROM `{prefix_sites}` WHERE `id` = ?;",[(int)($get['editor'])]);
            common::$sql['default']->delete("DELETE FROM `{prefix_navi}` WHERE `id` = ?;",[(int)($get['id'])]);
        }
        $show = common::info(_navi_deleted, "?admin=navi");
    break;
    case 'edit':
        $qry = common::$sql['default']->select("SELECT s2.*, s1.name AS katname, s1.placeholder "
            . "FROM `{prefix_navi_kats}` AS s1 "
            . "LEFT JOIN `{prefix_navi}` AS s2 "
            . "ON s1.`placeholder` = s2.`kat` "
            . "ORDER BY s1.name, s2.pos;");

        $thiskat = ''; $position  = ''; $i = 1;
        foreach($qry as $get) {
            if($thiskat != $get['kat']) {
                $position .= common::select_field('lazy',false,stringParser::decode($get['katname']));
                $position .= common::select_field(stringParser::decode($get['placeholder']).'-1',false,_admin_first);
            }

            $thiskat = $get['kat'];
            $sel[$i] = ($get['id'] == $_GET['id']);

            if(!empty($get['name']))
                $position .= common::select_field(stringParser::decode($get['placeholder']).'-'.($get['pos']+1),
                    $sel[$i],_nach.' -> '.navi_name(stringParser::decode($get['name'])));
            $i++;
        }

        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_navi}` WHERE `id` = ?;",[(int)($_GET['id'])]);
        $smarty->caching = false;
        $smarty->assign('data',$get,true); //As Array
        $smarty->assign('name',stringParser::decode($get['name']));
        $smarty->assign('url',stringParser::decode($get['url']));
        $smarty->assign('position',$position);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_navi_edit.tpl');
        $smarty->clearAllAssign();
    break;
    case 'editlink':
        $pos = preg_replace("=nav_(.+)-=","",$_POST['pos']);
        common::$sql['default']->update("UPDATE `{prefix_navi}` SET `pos` = (pos+1) WHERE `pos` ".($_POST['pos'] == "1" || "2" ? ">= " : "> ")." '".(int)($pos)."';");

        $kat = preg_replace('/-(\d+)/','',$_POST['pos']);
        common::$sql['default']->update("UPDATE `{prefix_navi}` SET `pos` = ?,`kat` = ?,`name` = ?,`url` = ?,`target` = ?,`shown` = ?,`internal` = ?,`wichtig` = ? WHERE `id` = ?;",
            [(int)($pos),stringParser::encode($kat),stringParser::encode($_POST['name']),stringParser::encode($_POST['url']),
                (int)($_POST['target']),(int)($_POST['sichtbar']),(int)($_POST['internal']),(int)($_POST['wichtig']),(int)($_GET['id'])]);

        $show = common::info(_navi_edited,"?admin=navi");
    break;
    case 'menu':
        common::$sql['default']->update("UPDATE `{prefix_navi}` SET `shown` = ? WHERE `id` = ?;",
            [(int)($_GET['set']),(int)($_GET['id'])]);
        header("Location: ?admin=navi");
        break;
    case 'intern':
        common::$sql['default']->update("UPDATE `{prefix_navi_kats}` SET `intern` = ? WHERE `id` = ?;",
            [(int)($_GET['set']),(int)($_GET['id'])]);
        header("Location: ?admin=navi");
        break;
    case 'updatekat':
        common::$sql['default']->update("UPDATE `{prefix_navi_kats}` SET `name` = ?, `placeholder` = ?,`level` = ? WHERE `id` = ?;",
            [stringParser::encode($_POST['name']),"nav_".stringParser::encode(trim($_POST['placeholder'])),(int)($_POST['level']),(int)($_GET['id'])]);
        $show = common::info(_menukat_updated, '?admin=navi');
        break;
    case 'deletekat':
        common::$sql['default']->delete("DELETE FROM `{prefix_navi_kats}` WHERE `id` = ?;",
            [(int)($_GET['id'])]);
        $show = common::info(_menukat_deleted, '?admin=navi');
        break;
    case 'addkat':
        $smarty->caching = false;
        $smarty->assign('is_edit',false);
        $smarty->assign('name','');
        $smarty->assign('placeholder','');
        $smarty->assign('level_user',1);
        $smarty->assign('do','insertkat');
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_navi_kats.tpl');
        $smarty->clearAllAssign();
        break;
    case 'insertkat':
        common::$sql['default']->insert("INSERT INTO `{prefix_navi_kats}` SET `name` = ?, `placeholder` = ?,`level` = ?;",
            [stringParser::encode($_POST['name']),"nav_".stringParser::encode(trim($_POST['placeholder'])),(int)($_POST['intern'])]);
        $show = common::info(_menukat_inserted, '?admin=navi');
        break;
    default:
        $color = 0;
        $qry_kat = common::$sql['default']->select("SELECT `name`,`placeholder` FROM `{prefix_navi_kats}` ORDER BY `name`;");
        foreach($qry_kat as $get_kat) {
            $kat = $get_kat['name'];
            $show .= '<tr><td align="center" colspan="8" class="contentHead"><span class="fontBold">'.$get_kat['name'].'</span></td></tr>';

            $qry_nav = common::$sql['default']->select("SELECT * FROM `{prefix_navi}` WHERE `kat` = ? ORDER BY `pos`;",[$get_kat['placeholder']]);

            if(common::$sql['default']->rowCount()) {
                foreach ($qry_nav as $get_nav) {
                    $delete = common::button_delete_single($get_nav['id'], "admin=" . $admin . "&amp;do=delete", _button_title_del, _confirm_del_navi);
                    $edit = "&nbsp;";
                    $type = _navi_space;
                    if ($get_nav['type']) {
                        $type = stringParser::decode($get_nav['name']);
                        $edit = common::getButtonEditSingle($get_nav['id'], "admin=" . $admin . "&amp;do=edit");
                        $delete = common::button_delete_single($get_nav['id'], "admin=" . $admin . "&amp;do=delete", _button_title_del, _confirm_del_navi);
                    }

                    $shown = _noicon;
                    $set = 1;
                    if ($get_nav['shown']) {
                        $shown = _yesicon;
                        $set = 0;
                    }

                    $smarty->caching = false;
                    $smarty->assign('color', $color);
                    $smarty->assign('name', $type);
                    $smarty->assign('id', $get_nav['id']);
                    $smarty->assign('set', $set);
                    $smarty->assign('url', common::cut($get_nav['url'], 34));
                    $smarty->assign('kat', stringParser::decode($get_kat['name']));
                    $smarty->assign('shown', $shown);
                    $smarty->assign('edit', $edit);
                    $smarty->assign('del', $delete);
                    $show .= $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/navi_show.tpl');
                    $smarty->clearAllAssign();
                    $color++;
                }
            } else {
                $show .= '<tr><td colspan="6" class="contentMainFirst">'._no_entrys.'</td></tr>';
            }
        }

        //default
        $show_kats = ""; $color = 0;
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_navi_kats}` ORDER BY `name` ASC");
        foreach($qry as $get) {
            $edit = ''; $delete = '';
            $type = stringParser::decode($get['name']);
            if($get['placeholder'] != 'nav_admin')
            {
                $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=editkat");
                $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=deletekat",_button_title_del,_confirm_del_menu);
            }

            $smarty->caching = false;
            $smarty->assign('name',$type,true);
            $smarty->assign('intern',(empty($get['intern']) ? _noicon : _yesicon));
            $smarty->assign('id',$get['id']);
            $smarty->assign('set', (empty($get['intern']) ? 1 : 0));
            $smarty->assign('placeholder', str_replace('nav_', '', stringParser::decode($get['placeholder'])));
            $smarty->assign('color',$color);
            $smarty->assign('edit',$edit);
            $smarty->assign('del',$delete);
            $show_kats .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/navi_kats.tpl');
            $smarty->clearAllAssign(); $color++;
        }

        $smarty->caching = false;
        $smarty->assign('show',$show,true);
        $smarty->assign('show_kats',$show_kats,true);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/navi.tpl');
        $smarty->clearAllAssign();
    break;
}