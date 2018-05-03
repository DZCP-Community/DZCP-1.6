<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START ##
include("../inc/buffer.php");

## INCLUDES ##
include(basePath."/inc/debugger.php");
include(basePath."/inc/config.php");
include(basePath."/inc/bbcode.php");

## SETTINGS ##
$where = _site_server;
$dir = "server";
define('_Server', true);

if(file_exists(basePath."/server/case_".$action.".php"))
    require_once(basePath."/server/case_".$action.".php");
else
    require_once(basePath."/server/case_default.php");

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);