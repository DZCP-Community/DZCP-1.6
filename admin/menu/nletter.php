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

    $where = $where.': '._nletter;
        if(common::$do == 'preview')
    {
        $smarty->caching = false;
        $smarty->assign('head',_nletter_prev_head);
        $smarty->assign('text',bbcode_nletter($_POST['eintrag']));
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/nletter_prev.tpl');
        $smarty->clearAllAssign();
      exit('<table class="mainContent" cellspacing="1">'.$show.'</table>');
    } elseif(common::$do == "send") {
        if(empty($_POST['eintrag']) || $_POST['to'] == "-")
          {
            if(empty($_POST['eintrag'])) $error = _empty_eintrag;
            elseif($_POST['to'] == "-") $error = _empty_to;

              $smarty->caching = false;
              $smarty->assign('error',$error);
              $error = $smarty->fetch('file:['.common::$tmpdir.']errors/errortable.tpl');
              $smarty->clearAllAssign();

            $qry = common::$sql['default']->select("SELECT id,name FROM `{prefix_groups}` ORDER BY name");
            foreach($qry as $get) {
              if($_POST['to'] == $get['id']) $selsq = 'selected="selected"';
              else $selsq = "";

                $smarty->caching = false;
                $smarty->assign('id',$get['id']);
                $smarty->assign('sel',$selsq);
                $smarty->assign('name',stringParser::decode($get['name']));
                $squads .= $smarty->fetch('string:'._to_squads);
                $smarty->clearAllAssign();
            }

        if($_POST['to'] == "reg") $selr = 'selected="selected"';
        elseif($_POST['to'] == "member") $selm = 'selected="selected"';
        elseif($_POST['to'] == "leader") $sell = 'selected="selected"';


              $smarty->caching = false;
              $smarty->assign('von',common::$userid);
              $smarty->assign('an',_to);
              $smarty->assign('who',_msg_global_who);
              $smarty->assign('reg',_msg_global_reg);
              $smarty->assign('selr',$selr);
              $smarty->assign('selm',$selm);
              $smarty->assign('sell',$sell);
              $smarty->assign('value',_button_value_nletter);
              $smarty->assign('preview',_preview);
              $smarty->assign('allmembers',_msg_global_all);
              $smarty->assign('all_leader',_msg_all_leader);
              $smarty->assign('leader',_msg_leader);
              $smarty->assign('squad',_msg_global_squad);
              $smarty->assign('squads',$squads);
              $smarty->assign('posteintrag',stringParser::decode($_POST['eintrag']));
              $smarty->assign('titel',_nletter_head);
              $smarty->assign('nickhead',_nick);
              $smarty->assign('error',$error);
              $smarty->assign('eintraghead',_eintrag);
              $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/nletter.tpl');
              $smarty->clearAllAssign();
          } else {
        if($_POST['to'] == "reg")
        {
            $smarty->caching = false;
            $smarty->assign('text', bbcode_nletter($_POST['eintrag']));
            $message = $smarty->fetch('string:'.BBCode::bbcode_email(settings::get('eml_nletter')));
            $smarty->clearAllAssign();
            $subject =stringParser::decode(settings::get('eml_nletter_subj'));

              $qry = common::$sql['default']->select("SELECT email FROM `{prefix_users}` WHERE nletter = 1");
              foreach($qry as $get) {
                  common::sendMail(stringParser::decode($get['email']),$subject,$message);
              }


            common::userstats_increase('writtenmsg');

              $show = common::info(_msg_reg_answer_done, "?admin=nletter");

        } elseif($_POST['to'] == "member") {
            $smarty->caching = false;
            $smarty->assign('text', bbcode_nletter($_POST['eintrag']));
            $message = $smarty->fetch('string:'.BBCode::bbcode_email(settings::get('eml_nletter')));
            $smarty->clearAllAssign();

            $subject =stringParser::decode(settings::get('eml_nletter_subj'));

          $qry = common::$sql['default']->select("SELECT email FROM `{prefix_users}` WHERE level >= 2");
          foreach($qry as $get) {
              common::sendMail(stringParser::decode($get['email']),$subject,$message);
          }

            common::userstats_increase('writtenmsg');

              $show = common::info(_msg_member_answer_done, "?admin=nletter");
        } else {
            $smarty->caching = false;
            $smarty->assign('text', bbcode_nletter($_POST['eintrag']));
            $message = $smarty->fetch('string:'.BBCode::bbcode_email(settings::get('eml_nletter')));
            $smarty->clearAllAssign();

            $subject =stringParser::decode(settings::get('eml_nletter_subj'));

          $qry = common::$sql['default']->select("SELECT s2.email FROM `{prefix_group_user}` AS s1
                     LEFT JOIN `{prefix_users}` AS s2
                     ON s1.user = s2.id
                     WHERE s1.group = '".$_POST['to']."'");
          foreach($qry as $get) {
              common::sendMail(stringParser::decode($get['email']),$subject,$message);
          }

            common::userstats_increase('writtenmsg');

              $show = common::info(_msg_squad_answer_done, "?admin=nletter");
        }
      }
    } else {
          $qry = common::$sql['default']->select("SELECT id,name FROM `{prefix_groups}` ORDER BY name"); $squads = '';
          foreach($qry as $get) {
              $smarty->caching = false;
              $smarty->assign('id',$get['id']);
              $smarty->assign('sel','');
              $smarty->assign('name',stringParser::decode($get['name']));
              $squads .= $smarty->fetch('string:'._to_squads);
              $smarty->clearAllAssign();
          }

            $smarty->caching = false;
            $smarty->assign('von',common::$userid);
            $smarty->assign('an',_to);
            $smarty->assign('selr','');
            $smarty->assign('selm','');
            $smarty->assign('who',_msg_global_who);
            $smarty->assign('squads',$squads);
            $smarty->assign('preview',_preview);
            $smarty->assign('reg',_msg_global_reg);
            $smarty->assign('allmembers',_msg_global_all);
            $smarty->assign('all_leader',_msg_all_leader);
            $smarty->assign('leader',_msg_leader);
            $smarty->assign('squad',_msg_global_squad);
            $smarty->assign('titel',_nletter_head);
            $smarty->assign('value',_button_value_nletter);
            $smarty->assign('nickhead',_nick);
            $smarty->assign('eintraghead',_eintrag);
            $smarty->assign('error','');
            $smarty->assign('posteintrag','');
            $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/nletter.tpl');
            $smarty->clearAllAssign();
      }