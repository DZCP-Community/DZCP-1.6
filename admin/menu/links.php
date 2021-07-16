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
$where = $where.': '._config_links;

switch (common::$do) {
    case 'new':
        $smarty->caching = false;
        $smarty->assign('head',_links_admin_head);
        $smarty->assign('bchecked','checked="checked"');
        $smarty->assign('tchecked','');
        $smarty->assign('bnone','');
        $smarty->assign('llink','');
        $smarty->assign('lbeschreibung','');
        $smarty->assign('ltext','');
        $smarty->assign('what',_button_value_add);
        $smarty->assign('do',"add");
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_links.tpl');
        $smarty->clearAllAssign();
    break;
    case 'add':
        if(empty($_POST['link']) || empty($_POST['beschreibung']) || (isset($_POST['banner']) && empty($_POST['text']))) {
            if(empty($_POST['link']))             
                $show = common::error(_links_empty_link, 1);
            elseif(empty($_POST['beschreibung'])) 
                $show = common::error(_links_empty_beschreibung, 1);
            elseif(empty($_POST['text']))         
                $show = common::error(_links_empty_text, 1);
        } else {
            common::$sql['default']->insert("INSERT INTO `{prefix_links}` SET `url` = ?, `text` = ?, `banner` = ?, `beschreibung` = ?;",
                  [stringParser::encode(common::links($_POST['link'])),stringParser::encode($_POST['text']),stringParser::encode($_POST['banner']),stringParser::encode($_POST['beschreibung'])]);
            $show = common::info(_link_added, "?admin=links");
        }
    break;
    case 'edit':
        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_links}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        
        $tchecked = (!$get['banner'] ? 'checked="checked"' : '');
        $bchecked = ($get['banner'] ? 'checked="checked"' : '');
        $bnone = ($get['banner'] ? '' : "display:none");

        $smarty->caching = false;
        $smarty->assign('head',_links_admin_head_edit);
        $smarty->assign('bchecked',$bchecked);
        $smarty->assign('tchecked',$tchecked);
        $smarty->assign('bnone',$bnone);
        $smarty->assign('llink',common::links(stringParser::decode($get['url'])));
        $smarty->assign('lbeschreibung',stringParser::decode($get['beschreibung']));
        $smarty->assign('ltext',stringParser::decode($get['text']));
        $smarty->assign('what',_button_value_edit);
        $smarty->assign('do',"editlink&amp;id=".$_GET['id']."");
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_links.tpl');
        $smarty->clearAllAssign();

    break;
    case 'editlink':
        if(empty($_POST['link']) || empty($_POST['beschreibung']) || (isset($_POST['banner']) && empty($_POST['text']))) {
          if(empty($_POST['link']))             
              $show = common::error(_links_empty_link, 1);
          elseif(empty($_POST['beschreibung'])) 
              $show = common::error(_links_empty_beschreibung, 1);
          elseif(empty($_POST['text']))         
              $show = common::error(_links_empty_text, 1);
        } else {
            common::$sql['default']->update("UPDATE `{prefix_links}` SET `url` = ?, `text` = ?, `banner` = ?, `beschreibung` = ? WHERE id = ?;",
                    [stringParser::encode(common::links($_POST['link'])),stringParser::encode($_POST['text']),stringParser::encode($_POST['banner']),stringParser::encode($_POST['beschreibung']),(int)($_GET['id'])]);
            $show = common::info(_link_edited, "?admin=links");
        }
    break;
    case 'delete':
        common::$sql['default']->delete("DELETE FROM `{prefix_links}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        $show = common::info(_link_deleted, "?admin=links");
    break;
    default:
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_links}` ORDER BY `banner` DESC;");
        foreach($qry as $get) {
            $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_link);

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $smarty->caching = false;
            $smarty->assign('link',common::cut(stringParser::decode($get['url']),40));
            $smarty->assign('class',$class);
            $smarty->assign('edit',$edit);
            $smarty->assign('delete',$delete);
            $smarty->assign('id',$get['id']);
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/links_show.tpl');
            $smarty->clearAllAssign();
        }

        if(empty($show)) {
            $show = '<tr><td colspan="3" class="contentMainSecond">'._no_entrys.'</td></tr>';
        }

        $smarty->caching = false;
        $smarty->assign('show',$show);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/links.tpl');
        $smarty->clearAllAssign();
    break;
}