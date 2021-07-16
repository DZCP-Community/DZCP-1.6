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

if (!defined('_Tutorials')) exit();

header("Content-type: text/html; charset=utf-8");
$form = show("page/editor_regged", array("nick" => autor($userid),
    "von" => _autor));
$add = show("page/comments_add", array("titel" => _tutorials_comments_add_head,
    "bbcodehead" => _bbcode,
    "form" => $form,
    "show" => "none",
    "what" => _button_value_add,
    "ip" => _iplog_info,
    "preview" => _preview,
    "sec" => $dir,
    "b1" => $u_b1,
    "b2" => $u_b2,
    "security" => _register_confirm,
    "action" => "",
    "prevurl" => "",
    "lang" => $language,
    "id" => $_GET['id'],
    "postemail" => "",
    "posthp" => "",
    "postnick" => "",
    "posteintrag" => "",
    "error" => "",
    "eintraghead" => _eintrag));
$seiten = nav($entrys,$settings['max_comments'],"?action=tutorials&amp;id=".$_GET['id']."");
$comments = show($dir."/comments",array("head" => _comments_head,
    "show" => $comments,
    "seiten" => $seiten,
    "add" => $add));

$index = show($dir."/show_tutorial", array("head" => _tutorials." - ".$_POST['bezeichnung'],
    "hits" => _tutorials_hits,
    "bewertung" => _tutorials_bewertung,
    "pic" => "<img src=\"../inc/images/admin/cwscreen.jpg\" alt=\"\" />",
    "beschreibung" => bbcode($_POST['beschreibung']),
    "tutorial" => bbcode($_POST['tutorial']),
    "v_hits" => '0',
    "v_bewertung" => tutorials_rating_bar(0,0,0,0),
    "comments" => ""));

echo utf8_encode('<table class="mainContent" cellspacing="1">'.$index.'</table>');
exit;