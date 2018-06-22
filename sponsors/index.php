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
$dir = "sponsors";
$where = _site_sponsor;

## SECTIONS ##
switch ($action):
    default:
        $qry = db("SELECT * FROM " . $db['sponsoren'] . " WHERE site = 1 ORDER BY pos");
        while ($get = _fetch($qry)) {
            if (empty($get['slink'])) {
                $banner = show(_sponsors_bannerlink, array("id" => $get['id'],
                    "title" => str_replace('http://', '', re($get['link'])),
                    "banner" => "../banner/sponsors/site_" . $get['id'] . "." . re($get['send'])));
            } else {
                $banner = show(_sponsors_bannerlink, array("id" => $get['id'],
                    "title" => str_replace('http://', '', re($get['link'])),
                    "banner" => $get['slink']));
            }

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst";
            $color++;
            $show .= show($dir . "/sponsors_show", array("class" => $class,
                "beschreibung" => bbcode(re($get['beschreibung'])),
                "hits" => $get['hits'],
                "hit" => _hits,
                "banner" => $banner));
        }

        $index = show($dir . "/sponsors", array("head" => _sponsor_head,
            "show" => $show));
        break;
    case 'link';
        $get = db("SELECT link FROM " . $db['sponsoren'] . "
                   WHERE id = '" . (int)($_GET['id']) . "'", false, true);

        db("UPDATE " . $db['sponsoren'] . "
            SET `hits` = hits+1
            WHERE id = '" . (int)($_GET['id']) . "'");

        header("Location: " . $get['link']);
        break;
endswitch;

## INDEX OUTPUT ##
$title = $pagetitle . " - " . $where . "";
page($index, $title, $where);