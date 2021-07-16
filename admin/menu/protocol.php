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

$where = $where.': '._protocol;
switch (common::$do) {
    case 'deletesingle':
        common::$sql['default']->delete("DELETE FROM `{prefix_ip_action}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        header("Location: ".common::GetServerVars('HTTP_REFERER'));
    break;
    default:
        if(isset($_POST['run']) == 'delete') {
            common::$sql['default']->delete("DELETE FROM `{prefix_ip_action}` WHERE `time` != 0;");
            notification::add_success(_protocol_deleted);
        }
        
        $params = [];
        if(!empty($_GET['sip'])) {
            $search = "WHERE `ipv4` = ? AND `time` != 0 AND `what` NOT REGEXP 'vid_'";
            array_push($params, stringParser::encode($_GET['sip']));
            $swhat = $_GET['sip'];
        } else {
            $search = "WHERE `time` != 0 AND `what` NOT REGEXP 'vid_'";
            $swhat = _info_ip;
        }

        $entrys = common::cnt('{prefix_ip_action}', $search, 'id', $params); $maxprot = 30;
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_ip_action}` ".$search." ORDER BY `id` DESC LIMIT ".(common::$page - 1)*$maxprot.",".$maxprot.";",$params);
        foreach($qry as $get) {
              $action = "";
              $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
              $date = date("d.m.y H:i", $get['time'])._uhr;

            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $smarty->assign('action',"admin=protocol&amp;do=deletesingle");
            $smarty->assign('title',_button_title_del);
            $delete = $smarty->fetch('file:['.common::$tmpdir.']page/buttons/button_delete.tpl');
            $smarty->clearAllAssign();

            if(preg_match("#\(#",$get['what'])) {
                $a = preg_replace("#^(.*?)\((.*?)\)#is","$1",$get['what']);
                $wid = preg_replace("#^(.*?)\((.*?)\)#is","$2",$get['what']);

                if($a == 'fid')
                    $action = 'wrote in <b>board</b>';
                elseif($a == 'ncid')
                    $action = 'wrote <b>comment</b> in <b>news</b> with <b>ID</b> '.$wid;
                elseif($a == 'artid')
                    $action = 'wrote <b>comment</b> in <b>article</b> with <b>ID</b> '.$wid;
                elseif($a == 'vid')
                    $action = 'voted <b>poll</b> with <b>ID '.$wid.'</b>';
                elseif($a == 'mgbid')
                    $action = common::autor((int)$wid).' got a userbook entry';
                elseif($a == 'createuser') {
                    $ids = explode("_", $wid);
                    $action = '<b style="color:red">ADMIN:</b> '.common::autor((int)$ids[0]).' <b>added</b> user '.common::autor((int)$ids[1]);
                } elseif($a == 'upduser') {
                    $ids = explode("_", $wid);
                    $action = '<b style="color:red">ADMIN:</b> '.common::autor((int)$ids[0]).' <b>edited</b> user '.common::autor((int)$ids[1]);
                } elseif($a == 'deluser') {
                    $ids = explode("_", $wid);
                    $action = '<b style="color:red">ADMIN:</b> '.common::autor((int)$ids[0]).' <b>deleted</b> user';
                } elseif($a == 'ident') {
                    $ids = explode("_", $wid);
                    $action = '<b style="color:red">ADMIN:</b> '.common::autor((int)$ids[0]).' took <b>identity</b> from user '.common::autor((int)$ids[1]);
                } elseif($a == 'logout')
                    $action = common::autor((int)$wid).' <b>logged out</b>';
                elseif($a == 'login')
                    $action = common::autor((int)$wid).' <b>logged in</b>';
                elseif($a == 'trypwd')
                    $action = 'failed to <b>reset password</b> from '.common::autor((int)$wid);
                elseif($a == 'pwd')
                    $action = '<b>reseted password</b> from '.common::autor((int)$wid);
                elseif($a == 'reg')
                    $action = common::autor((int)$wid).' <b>signed up</b>';
                elseif($a == 'trylogin')
                    $action = 'failed to <b>login</b> in '.common::autor((int)$wid).'`s account';
                else 
                    $action = '<b style="color:red">undefined:</b> <b>'.$a.'</b>';
            } else {
                if($get['what'] == 'gb')
                    $action = 'wrote in <b>guestbook</b>';
                else 
                    $action = '<b style="color:red">undefined:</b> <b>'.$a.'</b>';
            }

            $smarty->caching = false;
            $smarty->assign('datum',$date);
            $smarty->assign('class',$class);
            $smarty->assign('delete',$delete);
            $smarty->assign('user', $get['ipv4']);
            $smarty->assign('action',$action);
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/protocol_show.tpl');
            $smarty->clearAllAssign();
        }

        if(empty($show))
            $show = '<tr><td colspan="3" class="contentMainSecond">'._no_entrys.'</td></tr>';

        $sip = (isset($_GET['sip']) && !empty($_GET['sip'])) ? "&amp;sip=".$_GET['sip'] : "";
        $smarty->caching = false;
        $smarty->assign('show',$show);
        $smarty->assign('search',$swhat);
        $smarty->assign('nav',common::nav($entrys,$maxprot,"?admin=protocol".$sip));
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/protocol.tpl');
        $smarty->clearAllAssign();
    break;
}