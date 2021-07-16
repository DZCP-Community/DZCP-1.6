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

$where = $where.': '._kalender_head;
switch (common::$do) {
    case 'add':
        if(isset($_POST['title'])) {
            if(empty($_POST['title']) || empty($_POST['event'])) {
                if(empty($_POST['title']))     
                    $show = common::error(_kalender_error_no_title,1);
                elseif(empty($_POST['event'])) 
                    $show = common::error(_kalender_error_no_event,1);
            } else {
                $time = mktime($_POST['h'],$_POST['min'],0,$_POST['m'],$_POST['t'],$_POST['j']);
                common::$sql['default']->insert("INSERT INTO `{prefix_events}` SET `datum` = ?, `title` = ?, `event` = ?;",
                    [(int)($time),stringParser::encode($_POST['title']),stringParser::encode($_POST['event'])]);

                $show = common::info(_kalender_successful_added,"?admin=kalender");
            }
        } else {
            $dropdown_date = common::dropdown_date(common::dropdown("day",date("d",time())),
                common::dropdown("month",date("m",time())),
                common::dropdown("year",date("Y",time())));

            $dropdown_time = common::dropdown_date(common::dropdown("hour",date("H",time())),
                common::dropdown("minute",date("i",time())),common::dropdown("year",date("Y",time())));

            $smarty->caching = false;
            $smarty->assign('dropdown_time',$dropdown_time);
            $smarty->assign('dropdown_date',$dropdown_date);
            $smarty->assign('what',_button_value_add);
            $smarty->assign('do',"addevent");
            $smarty->assign('k_event','');
            $smarty->assign('k_beschreibung','');
            $smarty->assign('head',_kalender_admin_head);
            $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_kalender.tpl');
            $smarty->clearAllAssign();
        }
    break;
    case 'edit':
        $get = common::$sql['default']->fetch("SELECT `datum`,`title`,`event` FROM `{prefix_events}` WHERE `id` = ?;", [(int)($_GET['id'])]);

        $dropdown_date = common::dropdown_date(common::dropdown("day",date("d",$get['datum'])),
            common::dropdown("month",date("m",$get['datum'])),
            common::dropdown("year",date("Y",$get['datum'])));

        $dropdown_time = common::dropdown_time(common::dropdown("hour",date("H",$get['datum'])),
            common::dropdown("minute",date("i",$get['datum'])));

        $smarty->caching = false;
        $smarty->assign('dropdown_time',$dropdown_time);
        $smarty->assign('dropdown_date',$dropdown_date);
        $smarty->assign('what',_button_value_edit);
        $smarty->assign('do',"editevent&amp;id=".$_GET['id']);
        $smarty->assign('k_event',stringParser::decode($get['title']));
        $smarty->assign('k_beschreibung',stringParser::decode($get['event']));
        $smarty->assign('head',_kalender_admin_head_edit);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_kalender.tpl');
        $smarty->clearAllAssign();

    break;
    case 'editevent':
        if(empty($_POST['title']) || empty($_POST['event'])) {
            if(empty($_POST['title']))     
                $show = common::error(_kalender_error_no_title,1);
            elseif(empty($_POST['event'])) 
                $show = common::error(_kalender_error_no_event,1);
        } else {
            $time = mktime($_POST['h'],$_POST['min'],0,$_POST['m'],$_POST['t'],$_POST['j']);
            common::$sql['default']->update("UPDATE `{prefix_events}` SET `datum` = ?, `title` = ?, `event` = ? WHERE `id` = ?;",
            [(int)($time),stringParser::encode($_POST['title']),stringParser::encode($_POST['event']),(int)($_GET['id'])]);
            $show = common::info(_kalender_successful_edited,"?admin=kalender");
        }
    break;
    case 'delete':
        common::$sql['default']->delete("DELETE FROM `{prefix_events}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        $show = common::info(_kalender_deleted,"?admin=kalender");
    break;
    default:
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_events}` ".common::orderby_sql(["event","datum"],'ORDER BY `datum` DESC').";");
        foreach($qry as $get) {
            $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_kalender);

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;

            $smarty->caching = false;
            $smarty->assign('datum',date("d.m.y H:i", $get['datum'])._uhr);
            $smarty->assign('event',stringParser::decode($get['title']));
            $smarty->assign('time',$get['datum']);
            $smarty->assign('class',$class);
            $smarty->assign('edit',$edit);
            $smarty->assign('delete',$delete);
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/kalender_show.tpl');
            $smarty->clearAllAssign();
        }

        if (empty($show)) {
            $show = '<tr><td colspan="4" class="contentMainSecond">' . _no_entrys . '</td></tr>';
        }

        $smarty->caching = false;
        $smarty->assign('show',$show);
        $smarty->assign('order_date', common::orderby('datum'));
        $smarty->assign('order_titel',common::orderby('event'));
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/kalender.tpl');
        $smarty->clearAllAssign();

    break;
}