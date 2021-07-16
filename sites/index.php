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

## OUTPUT BUFFER START ##
if(!ob_start("ob_gzhandler")) ob_start();
define('basePath', dirname(dirname(__FILE__).'../'));

## INCLUDES ##
include(basePath."/inc/common.php");

## Include PHP-Code ##
function phpParser($code,$php=false) {
    if($php && php_code_enabled) {
        ob_start(); unset($php);
        $html = preg_replace_callback("/\[php\](.*?)\[\/php\]/",
                create_function('$code','$code[1] = strip_tags($code[1]); return \'[base64]\'.base64_encode($code[1]).\'[/base64]\';'), $code);
        $code_output = trim("echo \"".addslashes($html)."\";"); unset($html);
        $code_output = preg_replace_callback("/\[base64\](.*?)\[\/base64\]/",
                create_function('$base64','return \'"; \'.base64_decode($base64[1]).\' echo "\';'), $code_output);
        eval($code_output); unset($code_output);
        $output_index = ob_get_contents();
        ob_end_clean();
        return $output_index;
    }
    
    return $code;
}

## SETTINGS ##
$dir = "sites";
$where = "";
$smarty = common::getSmarty(); //Use Smarty

## SECTIONS ##
switch (common::$action):
default:
    $get = common::$sql['default']->fetch("SELECT s1.*,s2.`internal` "
                            . "FROM `{prefix_sites}` AS `s1` "
                            . "LEFT JOIN `{prefix_navi}` AS `s2` "
                            . "ON s1.`id` = s2.`editor` "
                            . "WHERE s1.`id` = ?;", [(int)($_GET['show'])]);
    if(common::$sql['default']->rowCount()) {
        $navi_access = false;
        $navi = common::$sql['default']->fetch("SELECT s2.level FROM `{prefix_navi}` AS `s1` "
                . "LEFT JOIN `{prefix_navi_kats}` AS `s2` ON s1.`kat` = s2.`placeholder` "
                . "WHERE s1.`editor` = ?;", [$get['id']]);
        if(common::$sql['default']->rowCount()) {
            $navi_access = !(common::$chkMe >= $navi['level'] || common::admin_perms(common::$userid));
        }

        if(($get['internal'] && !common::$chkMe) || $navi_access) {
            $index = common::error(_error_wrong_permissions, 1);
        } else {
            $where = stringParser::decode($get['titel']);
            if($get['html']) {
                $inhalt = BBCode::parse_html(stringParser::encode(phpParser(stringParser::decode($get['text']),$get['php'])));
            } else { 
                $inhalt = phpParser(stringParser::decode($get['text']),$get['php']);
            }

            $smarty->caching = false;
            $smarty->assign('titel',stringParser::decode($get['titel']));
            $smarty->assign('inhalt',$inhalt);
            $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/sites.tpl');
            $smarty->clearAllAssign();
        }
    } else {
        $index = common::error(_sites_not_available,1);
    }
break;
case 'preview';
    header("Content-type: text/html; charset=utf-8");
    if(isset($_POST['html'])) {
        $inhalt = BBCode::parse_html(stringParser::encode(phpParser(stringParser::decode($_POST['inhalt']),(isset($_POST['php']) && common::permission('phpexecute')))));
    } else {
        $inhalt = phpParser(stringParser::decode($_POST['inhalt']),(isset($_POST['php']) && common::permission('phpexecute')));
    }

    $smarty->caching = false;
    $smarty->assign('titel',stringParser::decode($_POST['titel']));
    $smarty->assign('inhalt',$inhalt);
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/sites.tpl');
    $smarty->clearAllAssign();
    exit(utf8_encode('<table class="mainContent" cellspacing="1"'.$index.'</table>'));
break;
endswitch;

## INDEX OUTPUT ##
$title = common::$pagetitle." - ".$where;
common::page($index, $title, $where);