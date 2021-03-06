<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START ##
include("../inc/buffer.php");

## INCLUDES ##
include(basePath . "/inc/debugger.php");
include(basePath . "/inc/config.php");
include(basePath . "/inc/bbcode.php");

## SETTINGS ##
$dir = "impressum";
$where = _site_impressum;

## SECTIONS ##
$index = show($dir . "/impressum", array("head" => _impressum_head,
    "domain" => _impressum_domain,
    "autor" => _impressum_autor,
    "disclaimer_head" => _impressum_disclaimer,
    "disclaimer" => _impressum_txt,
    "show_domain" => re(settings('i_domain')),
    "show_autor" => bbcode(re(settings('i_autor')))));

## INDEX OUTPUT ##
$title = $pagetitle . " - " . $where . "";
page($index, $title, $where);