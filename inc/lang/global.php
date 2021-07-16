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

## Icons ##
$language_text['_hpicon'] = '<a href="{$hp}" target="_blank"><img src="../inc/images/hp.gif" alt="" title="{$hp}" class="icon" /></a>';
$language_text['_email_mailto'] = '<a href="mailto:{$email}">{$email}</a>';
$language_text['_emailicon'] = '<a href=\\"mailto:"+d+"\\"><img src=\\"../inc/images/email.gif\\" title="+d+" class=\\"icon\\" /></a>';
$language_text['_emailicon_non_mailto'] = '<a href="{$email}"><img src="../inc/images/email.gif" alt="" class="icon" /></a>';
$language_text['_emailicon_blank'] = '<img src="../inc/images/email.gif" alt="" class="icon" />';
$language_text['_hpicon_blank'] = '<img src="../inc/images/hp.gif" alt="" class="icon" />';
$language_text['_mficon_blank'] = '<img src="../inc/images/mf.gif" alt="" class="icon" />';
$language_text['_maleicon'] = '<img src="../inc/images/male.gif" alt="" class="icon" />';
$language_text['_femaleicon'] = '<img src="../inc/images/female.gif" alt="" class="icon" />';
$language_text['_yesicon'] = '<img src="../inc/images/yes.gif" alt="" class="icon" />';
$language_text['_noicon'] = '<img src="../inc/images/no.gif" alt="" class="icon" />';
$language_text['_newicon'] = '<img src="../inc/images/forum_newpost.gif" alt="" class="icon" />';
$language_text['_notnewicon'] = '<img src="../inc/images/notnew.gif" alt="" class="icon" />';
$language_text['_editicon_blank'] = '../inc/images/edit.gif';
$language_text['_admin_default_edit'] = '<a href="?action=admin&amp;edit={$id}"><img src="../inc/images/edit.gif" alt="" title="Edit" class="icon" /></a>';
$language_text['_admin_ck_edit'] = '<a href="?action=admin&amp;do=paycheck&amp;id={$id}"><img src="../inc/images/edit.gif" alt="" title="Edit" class="icon" /></a>';
$language_text['_msg_delete_sended'] = '<a href="?action=msg&amp;do=deletesended&amp;id={$id}"><i class="fas fa-trash-alt fa-lg"></i></a>';
$language_text['_forum_delete'] = '<a href="?action=post&amp;do=delete&amp;id={$id}"><i class="fas fa-trash-alt fa-lg"></i></a>';
$language_text['_newsc_delete'] = '<a href="?action=show&amp;id={$id}&amp;do=delete&cid={$cid}"><i class="fas fa-trash-alt fa-lg"></i></a>';
$language_text['_forum_zitat_preview'] = '<img src="../inc/lang/images/uk/images/quote.png" alt="" class="icon" style="cursor:pointer" />';
$language_text['_forum_button_admin_replys'] = '../inc/lang/images/uk/admin_reply.gif';
$language_text['_forum_button_replys'] = '../inc/lang/images/uk/reply.gif';

## Umfragen ##
$language_text['_votes_titel'] = '<a href="javascript:DZCP.toggle(\'{$vid}\')"><img src="../inc/images/{$icon}.gif" alt="" id="img{$vid}" class="icon" />{$intern}{$titel}</a>';
$language_text['_votes_balken'] = '<img src="../inc/images/vote.gif" width="{$width}%" height="4" alt="{$width}%" />';
$language_text['_closedicon_votes'] = '<img src="../inc/images/closed_votes.gif" alt="" class="icon" />';

## Admin ##
$language_text['_config_delete'] = '<a href="?admin={$what}&amp;do=delete&amp;id={$id}"><i class="fas fa-trash-alt fa-lg"></i></a>';
$language_text['_config_edit'] = '<a href="?admin={$what}&amp;do=edit&amp;id={$id}"><img src="../inc/images/edit.gif" alt="" class="icon" /></a>';
$language_text['_config_forum_kats_titel'] = '<a href="?admin=forum&amp;show=subkats&amp;id={$id}" style="display:block">{$kat}</a>';
$language_text['_config_newskats_img'] = '<img src="../inc/images/uploads/newskat/{$img}" alt="" />';
$language_text['_config_neskats_katbild_upload'] = '<a href="../upload/?action=newskats">upload</a>';
$language_text['_config_neskats_katbild_upload_edit'] = '<a href="../upload/?action=newskats&amp;edit={$id}">upload</a>';
$language_text['_dropdown_date_ts'] = '<select id="t_{$nr}" name="t_{$nr}" class="selectpicker">{$day}</select> <select id="m_{$nr}" name="m_{$nr}" class="selectpicker">{$month}</select> <select id="j_{$nr}" name="j_{$nr}" class="selectpicker">{$year}</select>';
$language_text['_dropdown_time_ts'] = '<select id="h_{$nr}" name="h_{$nr}" class="selectpicker">{$hour}</select> <select id="min_{$nr}" name="min_{$nr}" class="selectpicker">{$minute}</select>{$uhr}';

## User ##
$language_text['_to_squads'] = '<option value="{$id}" {$sel}>-> {$name}</option>';
$language_text['_buddys_yesicon'] = '<img src="../inc/images/buddys_yes.gif" alt="" class="icon" />';
$language_text['_buddys_noicon'] = '<img src="../inc/images/buddys_no.gif" alt="" class="icon" />';
$language_text['_user_mailto_texttop'] = '<img src=\\"../inc/images/mailto.gif\\" align=\\"texttop\\"> <a href=\\"mailto:"+d+"\\" target=\\"_blank\\">"+d+"</a>';
$language_text['_user_link_noreg'] = '<a class=\\"{$class}\\" href=\\"mailto:"+d+"\\">{$nick}</a>';
$language_text['_link_mailto'] = '<a href=\\"mailto:"+d+"\\">{$nick}</a>';