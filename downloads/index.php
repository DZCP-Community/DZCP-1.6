<?php
## OUTPUT BUFFER START ##
include("../inc/buffer.php");

## INCLUDES ##
include(basePath."/inc/debugger.php");
include(basePath."/inc/config.php");
include(basePath."/inc/bbcode.php");
include(basePath."/downloads/helper.php");

## SETTINGS ##
$where = _site_dl;
$dir = "downloads";
define('_Downloads', true);

$action = empty($action) ? 'default' : $action;
if(file_exists(basePath."/downloads/case_".$action.".php"))
    require_once(basePath."/downloads/case_".$action.".php");

## SETTINGS ##
$title = $pagetitle." - ".$where."";
page($index,$title,$where,'','downloads');
