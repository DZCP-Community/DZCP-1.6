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

function smarty_function_welcome($params,Smarty_Internal_Template &$smarty) {
    $return = "<script language=\"javascript\" type=\"text/javascript\">
                 date = new Date();
                 hour = date.getHours();
                 if(hour>=18)      document.write('"._welcome_18."');
                 else if(hour>=13) document.write('"._welcome_13."');
                 else if(hour>=11) document.write('"._welcome_11."');
                 else if(hour>=5)  document.write('"._welcome_5."');
                 else if(hour>=0)  document.write('"._welcome_0."');
             </script>";

    if(!common::$chkMe)
        return $return.' '._welcome_guest;

    return $return.' '.common::autor(common::$userid, "welcome");
}