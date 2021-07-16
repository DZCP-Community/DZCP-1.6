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

/**
 * Usage {languages}
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_languages($params,Smarty_Internal_Template &$smarty) {
    $lang="";
    $files = common::get_files(basePath.'/inc/lang/',false,true, ['php']);
    foreach ($files as $file) {
        $file = preg_replace("#.php#", "",$file);
        if($file == 'global') continue;
        $image = '../inc/images/flaggen/nocountry.gif';
        foreach(common::SUPPORTED_PICTURE as $endung) {
            if(file_exists(basePath."/inc/images/flaggen/".$file.".".$endung)) {
                $image = "../inc/images/flaggen/".$file.".".$endung;
                break;
            }
        }

        $text = defined('_lang_'.$file) ? constant('_lang_'.$file) : '';
        $lang .= '<a href="?set_language='.$file.'"><img src="'.$image.'" alt="'.$text.'" title="'.$text.'" class="icon" /></a> ';
    }

    return $lang;
}