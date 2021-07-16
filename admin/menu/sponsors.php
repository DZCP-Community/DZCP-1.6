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

    $where = $where.': '._config_sponsors;
      if(common::$do == "new")
      {
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_sponsoren}` ORDER BY pos");
        foreach($qry as $get) {
            $positions .= common::select_field(($get['pos']+1),false,_nach.' '.stringParser::decode($get['name']));
        }

          $smarty->caching = false;
          $smarty->assign('head',_sponsors_admin_head);
          $smarty->assign('error','');
          $smarty->assign('name',_sponsors_admin_name);
          $smarty->assign('sname','');
          $smarty->assign('link',_links_link);
          $smarty->assign('slink','');
          $smarty->assign('beschreibung',_beschreibung);
          $smarty->assign('sbeschreibung','');
          $smarty->assign('site',_sponsors_admin_site);
          $smarty->assign('addsite',_sponsors_admin_addsite);
          $smarty->assign('schecked','');
          $smarty->assign('snone',"none");
          $smarty->assign('add_site',_sponsors_admin_add_site);
          $smarty->assign('upload',_sponsors_admin_upload);
          $smarty->assign('url',_sponsors_admin_url);
          $smarty->assign('site_link','');
          $smarty->assign('sitepic','');
          $smarty->assign('banner',_sponsors_admin_banner);
          $smarty->assign('addbanner',_sponsors_admin_addbanner);
          $smarty->assign('bchecked','');
          $smarty->assign('bnone','none');
          $smarty->assign('add_banner',_sponsors_admin_add_banner);
          $smarty->assign('banner_link','');
          $smarty->assign('bannerpic','');
          $smarty->assign('box',_sponsors_admin_box);
          $smarty->assign('addbox',_sponsors_admin_addbox);
          $smarty->assign('xchecked','');
          $smarty->assign('xnone','none');
          $smarty->assign('add_box',_sponsors_admin_add_box);
          $smarty->assign('box_link','');
          $smarty->assign('boxpic','');
          $smarty->assign('pos',_position);
          $smarty->assign('first',_admin_first);
          $smarty->assign('positions',$positions);
          $smarty->assign('posname',$posname);
          $smarty->assign('what',_button_value_add);
          $smarty->assign('do',"add");
          $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_sponsors.tpl');
          $smarty->clearAllAssign();
      } elseif(common::$do == "add") {
        if(empty($_POST['name']) || empty($_POST['link']) || empty($_POST['beschreibung']))
        {
          if(empty($_POST['beschreibung']))
          {$smarty->caching = false;
            $smarty->assign('error',_sponsors_empty_beschreibung);
            $error = $smarty->fetch('file:['.common::$tmpdir.']errors/errortable.tpl');
            $smarty->clearAllAssign();}

              if(empty($_POST['link']))
              { $smarty->caching = false;
                $smarty->assign('error',_sponsors_empty_link);
                $error = $smarty->fetch('file:['.common::$tmpdir.']errors/errortable.tpl');
                $smarty->clearAllAssign();}

              if(empty($_POST['name']))
              { $smarty->caching = false;
                $smarty->assign('error',_sponsors_empty_name);
                $error = $smarty->fetch('file:['.common::$tmpdir.']errors/errortable.tpl');
                $smarty->clearAllAssign();}


          $pos = common::$sql['default']->select("SELECT pos,name FROM `{prefix_sponsoren}` ORDER BY pos");
          foreach($pos as $getpos) {
            if($getpos['name'] != $_POST['posname']) {
                $mp = common::$sql['default']->fetch("SELECT pos FROM `{prefix_sponsoren}` WHERE name != '".$_POST['posname']."' AND pos = '".(int)(($_POST['position']-1))."'");
                $positions .= common::select_field(($getpos['pos']+1),($getpos['pos'] == $mp['pos']),_nach.' '.stringParser::decode($getpos['name']));
            }
          }

            if(isset($_POST['site']))
            {
              $schecked = 'checked="checked"';
              $snone = "";
            } else {
              $schecked = "";
              $snone = "none";
            }
            if(isset($_POST['banner']))
            {
              $bchecked = 'checked="checked"';
              $bnine = "";
            } else {
              $bchecked = "";
              $bnone = "none";
            }
            if(isset($_POST['box']))
            {
              $xchecked = 'checked="checked"';
              $xnone = "";
            } else {
              $xchecked = "";
              $xnone = "none";
            }

            $smarty->caching = false;
            $smarty->assign('head',_sponsors_admin_head);
            $smarty->assign('error',$error);
            $smarty->assign('name',_sponsors_admin_name);
            $smarty->assign('sname',$_POST['name']);
            $smarty->assign('link',_links_link);
            $smarty->assign('slink',$_POST['link']);
            $smarty->assign('beschreibung',_beschreibung);
            $smarty->assign('sbeschreibung',stringParser::decode($_POST['beschreibung']));
            $smarty->assign('site',_sponsors_admin_site);
            $smarty->assign('addsite',_sponsors_admin_addsite);
            $smarty->assign('schecked',$schecked);
            $smarty->assign('snone',$snone);
            $smarty->assign('add_site',_sponsors_admin_add_site);
            $smarty->assign('upload',_sponsors_admin_upload);
            $smarty->assign('url',_sponsors_admin_url);
            $smarty->assign('site_link',$_POST['slink']);
            $smarty->assign('sitepic','');
            $smarty->assign('banner',_sponsors_admin_banner);
            $smarty->assign('addbanner',_sponsors_admin_addbanner);
            $smarty->assign('bchecked',$bchecked);
            $smarty->assign('bnone',$bnone);
            $smarty->assign('add_banner',_sponsors_admin_add_banner);
            $smarty->assign('banner_link',$_POST['blink']);
            $smarty->assign('bannerpic','');
            $smarty->assign('box',_sponsors_admin_box);
            $smarty->assign('addbox',_sponsors_admin_addbox);
            $smarty->assign('xchecked',$xchecked);
            $smarty->assign('xnone',$xnone);
            $smarty->assign('add_box',_sponsors_admin_add_box);
            $smarty->assign('box_link',$_POST['xlink']);
            $smarty->assign('boxpic','');
            $smarty->assign('pos',_position);
            $smarty->assign('first',_admin_first);
            $smarty->assign('positions',$positions);
            $smarty->assign('posname',$_POST['posname']);
            $smarty->assign('what',_button_value_add);
            $smarty->assign('do',"add");
            $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_sponsors.tpl');
            $smarty->clearAllAssign();
        } else {
          if($_POST['position'] == 1 || $_POST['position'] == 2) $sign = ">= ";
          else $sign = "> ";

            common::$sql['default']->update("UPDATE `{prefix_sponsoren}`
                      SET `pos` = pos+1
                      WHERE pos ".$sign." '".(int)($_POST['position'])."'");

            common::$sql['default']->insert("INSERT INTO `{prefix_sponsoren}`
                     SET `name`         = '".stringParser::encode($_POST['name'])."',
                                     `link`         = '".stringParser::encode(common::links($_POST['link']))."',
                                     `beschreibung` = '".stringParser::encode($_POST['beschreibung'])."',
                                     `site`         = '".(int)($_POST['site'])."',
                                     `slink`        = '".$_POST['slink']."',
                                     `banner`       = '".(int)($_POST['banner'])."',
                         `blink`        = '".$_POST['blink']."',
                         `box`           = '".(int)($_POST['box'])."',
                         `xlink`         = '".stringParser::encode($_POST['xlink'])."',
                                     `pos`            = '".(int)($_POST['position'])."'");

          $id = common::$sql['default']->lastInsertId();

          $tmp1 = $_FILES['sdata']['tmp_name'];
          $type1 = $_FILES['sdata']['type'];
          $end1 = explode(".", $_FILES['sdata']['name']);
          $end1 = strtolower($end1[count($end1)-1]);

          if(!empty($tmp1))
          {
            $img1 = getimagesize($tmp1);
                        if($type1 == "image/gif" || $type1 == "image/png" || $type1 == "image/jpeg" || !$img1[0])
            {
              @copy($tmp1, basePath."/banner/sponsors/site_".$id.".".strtolower($end1));
              @unlink($_FILES['sdata']['tmp_name']);
            }
              common::$sql['default']->update("UPDATE `{prefix_sponsoren}` SET `send` = '".$end1."' WHERE id = '".(int)($id)."'");
          }

                  $tmp2 = $_FILES['bdata']['tmp_name'];
          $type2 = $_FILES['bdata']['type'];
          $end2 = explode(".", $_FILES['bdata']['name']);
          $end2 = strtolower($end2[count($end2)-1]);
          $img2 = getimagesize($tmp2);
          if(!empty($tmp2))
          {
            if($type2 == "image/gif" || $type2 == "image/png" || $type2 == "image/jpeg" || !$img2[0])
            {
              @copy($tmp2, basePath."/banner/sponsors/banner_".$id.".".strtolower($end2));
              @unlink($_FILES['bdata']['tmp_name']);
            }
              common::$sql['default']->update("UPDATE `{prefix_sponsoren}` SET `bend` = '".$end2."' WHERE id = '".(int)($id)."'");
          }

                  $tmp3 = $_FILES['xdata']['tmp_name'];
          $type3 = $_FILES['xdata']['type'];
          $end3 = explode(".", $_FILES['xdata']['name']);
          $end3 = strtolower($end3[count($end3)-1]);

          if(!empty($tmp3))
          {
            $img3 = getimagesize($tmp3);
                        if($type3 == "image/gif" || $type3 == "image/png" || $type3 == "image/jpeg" || !$img3[0])
            {
              @copy($tmp3, basePath."/banner/sponsors/box_".$id.".".strtolower($end3));
              @unlink($_FILES['xdata']['tmp_name']);
            }
              common::$sql['default']->update("UPDATE `{prefix_sponsoren}` SET `xend` = '".$end3."' WHERE id = '".(int)($id)."'");
          }

          $show = common::info(_sponsor_added, "?admin=sponsors");
        }
      } elseif(common::$do == "edit") {

        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_sponsoren}` WHERE id = '".(int)($_GET['id'])."'");

          $pos = common::$sql['default']->select("SELECT pos,name FROM `{prefix_sponsoren}` ORDER BY pos");
          foreach($pos as $getpos) {
            if($getpos['name'] != $get['name']) {
                $mp = common::$sql['default']->fetch("SELECT pos FROM `{prefix_sponsoren}` WHERE name != '".$get['name']."' AND pos = '".(int)(($get['pos']-1))."'");
                $positions .= common::select_field($getpos['pos']+1,($getpos['pos'] == $mp['pos']),_nach.' '.stringParser::decode($getpos['name']));
            }
          }

        if($get['site'] == 1)
        {
          $schecked = 'checked="checked"';
          $snone = "";
        } else {
          $schecked = "";
          $snone = "none";
        }
        if($get['banner'] == 1)
        {
          $bchecked = 'checked="checked"';
          $bnone = "";
        } else {
          $bchecked = "";
          $bnone = "none";
        }
        if($get['box'] == 1)
        {
          $xchecked = 'checked="checked"';
          $xnone = "";
        } else {
          $xchecked = "";
          $xnone = "none";
        }

    foreach(common::SUPPORTED_PICTURE AS $end)
    {
      if(file_exists(basePath.'/banner/sponsors/site_'.$get['id'].'.'.$end))
            {
                $sitepic = '<img src="../banner/sponsors/site_'.$get['id'].'.'.$end.'" alt="" width="50%" />';
          break;
            }
    }

    foreach(common::SUPPORTED_PICTURE AS $end)
    {
            if(file_exists(basePath.'/banner/sponsors/banner_'.$get['id'].'.'.$end))
            {
                $bannerpic = '<img src="../banner/sponsors/banner_'.$get['id'].'.'.$end.'" alt="" width="50%" />';
                break;
            }
    }

    foreach(common::SUPPORTED_PICTURE AS $end)
    {
            if(file_exists(basePath.'/banner/sponsors/box_'.$get['id'].'.'.$end))
            {
                $boxpic = '<img src="../banner/sponsors/box_'.$get['id'].'.'.$end.'" alt="" width="50%" />';
                break;
            }
    }

          $smarty->caching = false;
          $smarty->assign('head',_sponsors_admin_head);
          $smarty->assign('error','');
          $smarty->assign('name',_sponsors_admin_name);
          $smarty->assign('sname',$get['name']);
          $smarty->assign('link',_links_link);
          $smarty->assign('slink',$get['link']);
          $smarty->assign('beschreibung',_beschreibung);
          $smarty->assign('sbeschreibung',stringParser::decode($get['beschreibung']));
          $smarty->assign('site',_sponsors_admin_site);
          $smarty->assign('addsite',_sponsors_admin_addsite);
          $smarty->assign('schecked',$schecked);
          $smarty->assign('snone',$snone);
          $smarty->assign('add_site',_sponsors_admin_add_site);
          $smarty->assign('upload',_sponsors_admin_upload);
          $smarty->assign('url',_sponsors_admin_url);
          $smarty->assign('site_link',$get['slink']);
          $smarty->assign('sitepic',$sitepic);
          $smarty->assign('banner',_sponsors_admin_banner);
          $smarty->assign('addbanner',_sponsors_admin_addbanner);
          $smarty->assign('bchecked',$bchecked);
          $smarty->assign('bnone',$bnone);
          $smarty->assign('add_banner',_sponsors_admin_add_banner);
          $smarty->assign('banner_link',$get['blink']);
          $smarty->assign('bannerpic',$bannerpic);
          $smarty->assign('box',_sponsors_admin_box);
          $smarty->assign('addbox',_sponsors_admin_addbox);
          $smarty->assign('xchecked',$xchecked);
          $smarty->assign('xnone',$xnone);
          $smarty->assign('add_box',_sponsors_admin_add_box);
          $smarty->assign('box_link',$get['xlink']);
          $smarty->assign('boxpic',$boxpic);
          $smarty->assign('pos',_position);
          $smarty->assign('first',_admin_first);
          $smarty->assign('positions',$positions);
          $smarty->assign('posname',$posname);
          $smarty->assign('what',_button_value_edit);
          $smarty->assign('do',"editsponsor&amp;id=".$_GET['id']."");
          $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_sponsors.tpl');
          $smarty->clearAllAssign();
      } elseif(common::$do == "editsponsor") {
      if(empty($_POST['name']) || empty($_POST['link']) || empty($_POST['beschreibung']))
      {
          if(empty($_POST['beschreibung']))
              $smarty->caching = false;
          $smarty->assign('error',_sponsors_empty_beschreibung);
          $error = $smarty->fetch('file:['.common::$tmpdir.']errors/errortable.tpl');
          $smarty->clearAllAssign();

          if(empty($_POST['link']))
              $smarty->caching = false;
          $smarty->assign('error',_sponsors_empty_link);
          $error = $smarty->fetch('file:['.common::$tmpdir.']errors/errortable.tpl');
          $smarty->clearAllAssign();

          if(empty($_POST['name']))
              $smarty->caching = false;
          $smarty->assign('error',_sponsors_empty_name);
          $error = $smarty->fetch('file:['.common::$tmpdir.']errors/errortable.tpl');
          $smarty->clearAllAssign();

          $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_sponsoren}` WHERE id = '".(int)($_GET['id'])."'");
          $pos = common::$sql['default']->select("SELECT pos,name FROM `{prefix_sponsoren}` ORDER BY pos");
          foreach($pos as $getpos) {
            if($getpos['name'] != $get['name']) {
                $mp = common::$sql['default']->fetch("SELECT pos FROM `{prefix_sponsoren}` WHERE name != '".$get['name']."' AND pos = '".(int)(($_POST['position']-1))."'");
                $positions .= common::select_field(($getpos['pos']+1),($getpos['pos'] == $mp['pos']),_nach.' '.stringParser::decode($getpos['name']));
            }
          }

            if(isset($_POST['site']))
            {
              $schecked = 'checked="checked"';
              $snone = "";
            } else {
              $schecked = "";
              $snone = "none";
            }
            if(isset($_POST['banner']))
            {
              $bchecked = 'checked="checked"';
              $bnone = "";
            } else {
              $bchecked = "";
              $bnine = "none";
            }
            if(isset($_POST['box']))
            {
              $xchecked = 'checked="checked"';
              $xnone = "";
            } else {
              $xchecked = "";
              $xnone = "none";
            }

            foreach(common::SUPPORTED_PICTURE AS $end) {
                if(file_exists(basePath.'/banner/sponsors/site_'.$get['id'].'.'.$end))
                {
                    $sitepic = '<img src="../banner/sponsors/site_'.$get['id'].'.'.$end.'" alt="" width="50%" />';
                    break;
                }
            }

            foreach(common::SUPPORTED_PICTURE AS $end) {
                if(file_exists(basePath.'/banner/sponsors/banner_'.$get['id'].'.'.$end))
                {
                    $bannerpic = '<img src="../banner/sponsors/banner_'.$get['id'].'.'.$end.'" alt="" width="50%" />';
                    break;
                }
            }

            foreach(common::SUPPORTED_PICTURE AS $end) {
                if(file_exists(basePath.'/banner/sponsors/box_'.$get['id'].'.'.$end))
                {
                    $boxpic = '<img src="../banner/sponsors/box_'.$get['id'].'.'.$end.'" alt="" width="50%" />';
                    break;
                }
            }

          $smarty->caching = false;
          $smarty->assign('head',_sponsors_admin_head);
          $smarty->assign('error',$error);
          $smarty->assign('name',_sponsors_admin_name);
          $smarty->assign('sname',$_POST['name']);
          $smarty->assign('link',_links_link);
          $smarty->assign('slink',$_POST['link']);
          $smarty->assign('beschreibung',_beschreibung);
          $smarty->assign('sbeschreibung',stringParser::decode($_POST['beschreibung']));
          $smarty->assign('site',_sponsors_admin_site);
          $smarty->assign('addsite',_sponsors_admin_addsite);
          $smarty->assign('schecked',$schecked);
          $smarty->assign('snone',$snone);
          $smarty->assign('add_site',_sponsors_admin_add_site);
          $smarty->assign('upload',_sponsors_admin_upload);
          $smarty->assign('url',_sponsors_admin_url);
          $smarty->assign('site_link',$_POST['slink']);
          $smarty->assign('sitepic',$sitepic);
          $smarty->assign('banner',_sponsors_admin_banner);
          $smarty->assign('addbanner',_sponsors_admin_addbanner);
          $smarty->assign('bchecked',$bchecked);
          $smarty->assign('bnone',$bnone);
          $smarty->assign('add_banner',_sponsors_admin_add_banner);
          $smarty->assign('banner_link',$_POST['blink']);
          $smarty->assign('bannerpic',$bannerpic);
          $smarty->assign('box',_sponsors_admin_box);
          $smarty->assign('addbox',_sponsors_admin_addbox);
          $smarty->assign('xchecked',$xchecked);
          $smarty->assign('xnone',$xnone);
          $smarty->assign('add_box',_sponsors_admin_add_box);
          $smarty->assign('box_link',$_POST['xlink']);
          $smarty->assign('boxpic',$boxpic);
          $smarty->assign('pos',_position);
          $smarty->assign('first',_admin_first);
          $smarty->assign('positions',$positions);
          $smarty->assign('posname',$_POST['posname']);
          $smarty->assign('what',_button_value_edit);
          $smarty->assign('do',"editsponsor&amp;id=".$_GET['id']."");
          $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_sponsors.tpl');
          $smarty->clearAllAssign();
        } else {
          $get = common::$sql['default']->fetch("SELECT pos FROM `{prefix_sponsoren}` WHERE id = '".(int)($_GET['id'])."'");

          if($_POST['position'] != $get['pos'])
          {
            if($_POST['position'] == 1 || $_POST['position'] == 2) $sign = ">= ";
            else $sign = "> ";

              common::$sql['default']->update("UPDATE `{prefix_sponsoren}`
                        SET `pos` = pos+1
                        WHERE pos ".$sign." '".(int)($_POST['position'])."'");
          }

          if($_POST['position'] == "lazy") $newpos = "";
          else $newpos = "`pos` = '".(int)($_POST['position'])."'";

          common::$sql['default']->update("UPDATE `{prefix_sponsoren}`
                       SET      `name`         = '".stringParser::encode($_POST['name'])."',
                             `link`         = '".stringParser::encode(common::links($_POST['link']))."',
                             `beschreibung` = '".stringParser::encode($_POST['beschreibung'])."',
                             `site`         = '".(int)($_POST['site'])."',
                             `slink`        = '".$_POST['slink']."',
                             `banner`       = '".(int)($_POST['banner'])."',
                             `blink`        = '".$_POST['blink']."',
                             `box`           = '".(int)($_POST['box'])."',
                             `xlink`         = '".stringParser::encode($_POST['xlink'])."',
                             ".$newpos."
                       WHERE id = '".(int)($_GET['id'])."'");

          $id = (int)($_GET['id']);

                  $tmp1 = $_FILES['sdata']['tmp_name'];
          $type1 = $_FILES['sdata']['type'];
          $end1 = explode(".", $_FILES['sdata']['name']);
          $end1 = strtolower($end1[count($end1)-1]);

          if(!empty($tmp1))
          {
            $img1 = getimagesize($tmp1);
                        if($type1 == "image/gif" || $type1 == "image/png" || $type1 == "image/jpeg" || !$img1[0])
            {
                          if(file_exists(basePath."/banner/sponsors/site_".$id.".gif"))
                @unlink(basePath."/banner/sponsors/site_".$id.".gif");
              elseif(file_exists(basePath."/banner/sponsors/site_".$id.".jpg"))
                @unlink(basePath."/banner/sponsors/site_".$id.".jpg");
                          elseif(file_exists(basePath."/banner/sponsors/site_".$id.".png"))
                @unlink(basePath."/banner/sponsors/site_".$id.".png");

              @copy($tmp1, basePath."/banner/sponsors/site_".$id.".".strtolower($end1));
              @unlink($_FILES['sdata']['tmp_name']);
            }
              common::$sql['default']->update("UPDATE `{prefix_sponsoren}` SET `send` = '".$end1."' WHERE id = '".(int)($id)."'");
          }

                  $tmp2 = $_FILES['bdata']['tmp_name'];
          $type2 = $_FILES['bdata']['type'];
          $end2 = explode(".", $_FILES['bdata']['name']);
          $end2 = strtolower($end2[count($end2)-1]);

          if(!empty($tmp2))
          {
            $img2 = getimagesize($tmp2);
                        if($type2 == "image/gif" || $type2 == "image/png" || $type2 == "image/jpeg" || !$img2[0])
            {
              if(file_exists(basePath."/banner/sponsors/banner_".$id.".gif"))
                @unlink(basePath."/banner/sponsors/banner_".$id.".gif");
              elseif(file_exists(basePath."/banner/sponsors/banner_".$id.".jpg"))
                @unlink(basePath."/banner/sponsors/banner_".$id.".jpg");
                          elseif(file_exists(basePath."/banner/sponsors/banner_".$id.".png"))
                @unlink(basePath."/banner/sponsors/banner_".$id.".png");

                          @copy($tmp2, basePath."/banner/sponsors/banner_".$id.".".strtolower($end2));
              @unlink($_FILES['bdata']['tmp_name']);
            }
              common::$sql['default']->update("UPDATE `{prefix_sponsoren}` SET `bend` = '".$end2."' WHERE id = '".(int)($id)."'");
          }

                  $tmp3 = $_FILES['xdata']['tmp_name'];
          $type3 = $_FILES['xdata']['type'];
          $end3 = explode(".", $_FILES['xdata']['name']);
          $end3 = strtolower($end3[count($end3)-1]);

          if(!empty($tmp3))
          {
            $img3 = getimagesize($tmp3);
                        if($type3 == "image/gif" || $type3 == "image/png" || $type3 == "image/jpeg" || !$img3[0])
            {
              if(file_exists(basePath."/banner/sponsors/box_".$id.".gif"))
                @unlink(basePath."/banner/sponsors/box_".$id.".gif");
              elseif(file_exists(basePath."/banner/sponsors/box_".$id.".jpg"))
                @unlink(basePath."/banner/sponsors/box_".$id.".jpg");
                          elseif(file_exists(basePath."/banner/sponsors/box_".$id.".png"))
                @unlink(basePath."/banner/sponsors/box_".$id.".png");

                          @copy($tmp3, basePath."/banner/sponsors/box_".$id.".".strtolower($end3));
              @unlink($_FILES['xdata']['tmp_name']);
            }
              common::$sql['default']->update("UPDATE `{prefix_sponsoren}` SET `xend` = '".$end3."' WHERE id = '".(int)($id)."'");
          }

          $show = common::info(_sponsor_edited, "?admin=sponsors");
        }
      } elseif(common::$do == "delete") {
          common::$sql['default']->delete("DELETE FROM `{prefix_sponsoren}`
                   WHERE id = '".(int)($_GET['id'])."'");

        $show = common::info(_sponsor_deleted, "?admin=sponsors");
      } else {
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_sponsoren}` ORDER BY pos");
        foreach($qry as $get) {
            $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_link);

          $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;

            $smarty->caching = false;
            $smarty->assign('link', common::cut(stringParser::decode($get['link']),40));
            $smarty->assign('class',$class);
            $smarty->assign('name',$get['name']);
            $smarty->assign('edit',$edit);
            $smarty->assign('delete',$delete);
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/sponsors_show.tpl');
            $smarty->clearAllAssign();
        }

          $smarty->caching = false;
          $smarty->assign('head',_sponsor_head);
          $smarty->assign('show',$show);
          $smarty->assign('sname',_sponsor_name);
          $smarty->assign('slink',_links_link);
          $smarty->assign('add',_sponsors_admin_add);
          $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/sponsors.tpl');
          $smarty->clearAllAssign();
      }