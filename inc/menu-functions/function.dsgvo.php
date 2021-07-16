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
 * @param $params
 * @param Smarty_Internal_Template $smarty
 * @return string
 * @throws SmartyException
 */

function smarty_function_dsgvo($params,Smarty_Internal_Template &$smarty) {
    $dsgvo_texts = $smarty->fetch('file:['.common::$tmpdir.']dsgvo/dsgvo_de.tpl');
    $smarty->assign('content',$dsgvo_texts);
    return $smarty->fetch('file:['.common::$tmpdir.']menu/dsgvo/dsgvo.tpl');
}