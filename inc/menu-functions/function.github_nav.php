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

function smarty_function_github_nav($params,Smarty_Internal_Template &$smarty) {
    $output = '';
    $qry = common::$sql['default']->select("SELECT `name`,`link` FROM `{prefix_projekts}` WHERE `enabled` = 1 ORDER BY `name` ASC");
    foreach($qry as $get) {
        $expl = explode(' ',stringParser::decode($get['name']));
        if(count($expl) >= 2) {
            $version = $expl[0];
            $text = $expl[1];
        } else {
            $version = stringParser::decode($get['name']);
            $text = null;
        }

        $output .= '<li><a href="'.stringParser::decode($get['link']).'">'.$version.' <span style="color: red;"> '.$text.'</span></a></li>';
    }

    return $output;
}