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
$where = $where.': '._partners_head;

      if(common::$do == "add")
      {
        $files = common::get_files(basePath.'/banner/partners/',false,true);
        for($i=0; $i<count($files); $i++)
        {
            $smarty->caching = false;
            $smarty->assign('icon',$files[$i]);
            $smarty->assign('sel','');
            $banners .= $smarty->fetch('string:<option value="{$icon}" {$sel}>{$icon}</option>');
            $smarty->clearAllAssign();
        }

          $smarty->caching = false;
          $smarty->assign('do',"addbutton");
          $smarty->assign('head',_partners_add_head);
          $smarty->assign('nothing','');
          $smarty->assign('banner',_partners_button);
          $smarty->assign('link',_link);
          $smarty->assign('e_link','');
          $smarty->assign('e_textlink','');
          $smarty->assign('or',_or);
          $smarty->assign('textlink',_partnerbuttons_textlink);
          $smarty->assign('banners',$banners);
          $smarty->assign('what',_button_value_add);
          $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_partners.tpl');
          $smarty->clearAllAssign();
      } elseif(common::$do == "addbutton") {
        if(empty($_POST['link']))
        {
          $show = common::error(_empty_url, 1);
        } else {
          common::$sql['default']->insert("INSERT INTO `{prefix_partners}` SET `link` = ?, `banner` = ?, `textlink` = ?;",
                  [stringParser::encode(common::links($_POST['link'])),stringParser::encode(empty($_POST['textlink']) ? $_POST['banner'] : $_POST['textlink']),(int)(empty($_POST['textlink']) ? 0 : 1)]);

          $show = common::info(_partners_added, "?admin=partners");
        }
      } elseif(common::$do == "edit") {
        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_partners}` WHERE `id` = ?;", [(int)($_GET['id'])]);

        $files = common::get_files(basePath.'/banner/partners/',false,true);
        for($i=0; $i<count($files); $i++)
        {
          if(stringParser::decode($get['banner']) == $files[$i]) $sel = 'selected="selected"';
          else $sel = "";

            $smarty->caching = false;
            $smarty->assign('icon',$files[$i]);
            $smarty->assign('sel',$sel);
            $banners .= $smarty->fetch('string:<option value="{$icon}" {$sel}>{$icon}</option>');
            $smarty->clearAllAssign();
        }
          $smarty->caching = false;
          $smarty->assign('do',"editbutton&amp;id=".$get['id']."");
          $smarty->assign('head',_partners_edit_head);
          $smarty->assign('nothing','');
          $smarty->assign('banner',_partners_button);
          $smarty->assign('link',_link);
          $smarty->assign('e_link',stringParser::decode($get['link']));
          $smarty->assign('e_textlink',(empty($get['textlink']) ? '' : stringParser::decode($get['banner'])));
          $smarty->assign('or',_or);
          $smarty->assign('textlink',_partnerbuttons_textlink);
          $smarty->assign('banners',$banners);
          $smarty->assign('what',_button_value_edit);
          $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_partners.tpl');
          $smarty->clearAllAssign();
      } elseif(common::$do == "editbutton") {
        if(empty($_POST['link'])) {
          $show = common::error(_empty_url);
        } else {
          common::$sql['default']->update("UPDATE `{prefix_partners}` SET `link` = ?, `banner` = ?, `textlink` = ? WHERE `id` = ?;",
                  [stringParser::encode(common::links($_POST['link'])),
                      stringParser::encode(empty($_POST['textlink']) ? $_POST['banner'] : $_POST['textlink']),
                      (int)(empty($_POST['textlink']) ? 0 : 1),(int)($_GET['id'])]);
          $show = common::info(_partners_edited, "?admin=partners");
        }
      } elseif(common::$do == "delete") {
        common::$sql['default']->delete("DELETE FROM `{prefix_partners}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        $show = common::info(_partners_deleted,"?admin=partners");
      } else {
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_partners}` ORDER BY id;");
        foreach ($qry as $get) {
            $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_entry);

          $rlink = common::links(stringParser::decode($get['link']));
          $button = '<img src="../banner/partners/'.stringParser::decode($get['banner']).'" alt="'.$rlink.'" title="'.$rlink.'" />';
          $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $smarty->caching = false;
            $smarty->assign('class',$class);
            $smarty->assign('button',(empty($get['textlink']) ? $button : '<div style="text-align: center;">'._partnerbuttons_textlink.': <b>'.stringParser::decode($get['banner']).'</b></div>'));
            $smarty->assign('link',stringParser::decode($get['link']));
            $smarty->assign('id',$get['id']);
            $smarty->assign('edit',$edit);
            $smarty->assign('delete',$delete);
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/partners_show.tpl');
            $smarty->clearAllAssign();
        }

          $smarty->caching = false;
          $smarty->assign('head',_partners_head);
          $smarty->assign('add',_partners_link_add);
          $smarty->assign('show',$show);
          $smarty->assign('edit',_editicon_blank);
          $smarty->assign('del',_deleteicon_blank);
          $smarty->assign('link',_link);
          $smarty->assign('button',_partners_button);
          $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/partners.tpl');
          $smarty->clearAllAssign();
      }