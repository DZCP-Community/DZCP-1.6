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

if(defined('_Artikel')) {
    if(common::$do == 'edit') {
        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_artikel_comments}` WHERE `id` = ?;", [(int)($_GET['cid'])]);

        $get_id = '?';
        $get_userid = $get['reg'];
        $get_date = $get['datum'];
        $regCheck = false;

        if($get['reg']) {
            $regCheck = true;
            $pUId = $get['reg'];
        }

        //-> Editby Text
        $smarty->caching = false;
        $smarty->assign('autor',common::cleanautor(common::$userid));
        $smarty->assign('time',date("d.m.Y H:i", time()));
        $editedby = $smarty->fetch('string:'._edited_by);
        $smarty->clearAllAssign();
    } else {
        $get_id = common::cnt("{prefix_artikel_comments}", " WHERE `artikel` = ?;","id", [(int)($_GET['id'])])+1;
        $get_userid = common::$userid;
        $get_date = time();
        $regCheck = false;
        $editedby = '';

        if(!common::$chkMe) {
            $regCheck = true;
            $pUId = common::$userid;
        }
    }

    if($regCheck) {
        $get_hp = isset($_POST['hp']) ? $_POST['hp'] : '';
        $get_email = isset($_POST['email']) ? $_POST['email'] : '';
        $get_nick = isset($_POST['nick']) ? $_POST['nick'] : '';

        //-> Homepage Link
        $hp = "";
        if (!empty($get_hp)) {
            $smarty->caching = false;
            $smarty->assign('hp',common::links($get_hp));
            $hp = $smarty->fetch('string:'._hpicon_forum);
            $smarty->clearAllAssign();
        } unset($get_hp);

        $email = $get_email ? '<br />'.common::CryptMailto($get_email,_emailicon_forum) : "";
        $onoff = ""; $avatar = "";
        
        $smarty->caching = true;
        $smarty->assign('nick',stringParser::decode($get_nick));
        $smarty->assign('email',$get_email);
        $nick = $smarty->fetch('string:'._link_mailto,common::getSmartyCacheHash('_link_mailto_'.$get_email.'_'.stringParser::decode($get_nick)));
        $smarty->clearAllAssign();
    } else {
        $hp = "";
        $email = "";
        $onoff = common::onlinecheck($get_userid);
        $nick = common::cleanautor($get_userid);
    }

    $smarty->caching = false;
    $smarty->assign('postid',$get_id);
    $smarty->assign('datum',date("d.m.Y", $get_date));
    $smarty->assign('zeit',date("H:i", $get_date));
    $smarty->assign('edit','');
    $smarty->assign('delete','');
    $titel = $smarty->fetch('string:'._eintrag_titel);
    $smarty->clearAllAssign();

    $smarty->caching = false;
    $smarty->assign('titel',$titel);
    $smarty->assign('comment',BBCode::parse_html((string)$_POST['comment']));
    $smarty->assign('nick',common::autor($get_userid));
    $smarty->assign('editby',BBCode::parse_html((string)$editedby));
    $smarty->assign('email',$email);
    $smarty->assign('hp',$hp);
    $smarty->assign('avatar',common::useravatar($get_userid));
    $smarty->assign('onoff',$onoff);
    $smarty->assign('rank',common::getrank($get_userid));
    $smarty->assign('ip',common::$userip['v4']._only_for_admins);
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/comments_show.tpl');
    $smarty->clearAllAssign();

    common::update_user_status_preview();
    header("Content-Type: text/html; charset=utf-8");
    exit(utf8_encode('<table class="mainContent" cellspacing="1">'.$index.'</table>'));
}