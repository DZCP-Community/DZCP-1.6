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

if(defined('_News') && common::$chkMe >= 1) {
    //-> Edit news comment
    if(common::$do == 'edit') {
        $get = common::$sql['default']->fetch("SELECT `reg`,`datum` FROM `{prefix_news_comments}` WHERE `id` = ?;", [(int)($_GET['cid'])]);
        $get_postid = isset($_GET['cid']) && $_GET['cid'] >= 1 ? $_GET['cid'] : 1;
        $get_userid = $get['reg'];
        $get_date = $get['datum'];
        $regCheck = !$get['reg'] ? false : true;

        //-> Editby Text
        if($get['reg'] != common::$userid) {
            $smarty->caching = false;
            $smarty->assign('uid', common::$userid);
            $smarty->assign('time', date("d.m.Y H:i", time()));
            $editedby = $smarty->fetch('string:' . _edited_by); //buggy use bbcode
            $smarty->clearAllAssign();
        }
    } else { //-> Add new news comment
        $get_postid = common::cnt('{prefix_news_comments}', " WHERE `news` = ?","id",[(int)($_GET['id'])])+1;
        $get_userid = common::$userid;
        $get_date = time();
        $regCheck = common::$chkMe >= 1 ? true : false;
        $editedby = '';
    }

    //-> Homepage Link
    $get_hp = common::data('hp',$get_userid); $hp = "";
    if (!empty($get_hp)) {
        $smarty->caching = false;
        $smarty->assign('hp',common::links(stringParser::decode($get_hp)));
        $hp = $smarty->fetch('string:'._hpicon_forum);
        $smarty->clearAllAssign();
    } unset($get_hp);

    //-> Post titel
    $smarty->caching = false;
    $smarty->assign('postid',$get_postid);
    $smarty->assign('datum',date("d.m.Y", $get_date));
    $smarty->assign('zeit',date("H:i", $get_date));
    $smarty->assign('edit','');
    $smarty->assign('delete','');
    $titel = $smarty->fetch('string:'._eintrag_titel);
    $smarty->clearAllAssign();

    //-> Post Index
    $smarty->caching = false;
    $smarty->assign('titel',$titel);
    $smarty->assign('comment',BBCode::parse_html((string)$_POST['comment']));
    $smarty->assign('nick',common::cleanautor($get_userid));
    $smarty->assign('hp',$hp);
    $smarty->assign('editby',BBCode::parse_html($editedby,false));
    $smarty->assign('avatar',common::useravatar($get_userid));
    $smarty->assign('onoff',common::onlinecheck($get_userid));
    $smarty->assign('rank',common::getrank($get_userid));
    $smarty->assign('ip','');
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/comments_show.tpl');
    $smarty->clearAllAssign();

    //-> Update & Output
    common::update_user_status_preview();
    header('Content-Type: text/html; charset=utf-8');
    echo html_entity_decode('<table class="mainContent" cellspacing="1">'.$index.'</table>');
    exit();
}