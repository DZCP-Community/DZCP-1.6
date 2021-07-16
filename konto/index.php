<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START ##
if(!ob_start("ob_gzhandler")) ob_start();
define('basePath', dirname(dirname(__FILE__).'../'));

## INCLUDES ##
include(basePath."/inc/common.php");

## SETTINGS ##
$where = _site_konto;
$dir = "konto";
define('_Konto', true);
$smarty = common::getSmarty(); //Use Smarty

## SECTIONS ##
if (file_exists(basePath . "/konto/case_" . common::$action . ".php")) {
    require_once(basePath . "/konto/case_" . common::$action . ".php");
}

## INDEX OUTPUT ##
$title = common::$pagetitle." - ".$where;
common::page($index, $title, $where);

## SETTINGS ##
$where = _site_konto;
$title = $pagetitle . " - " . $where . "";
$dir = "konto";
