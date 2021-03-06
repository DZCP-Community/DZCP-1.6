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
include(basePath . "/user/helper.php");

## SETTINGS ##
$dir = "user";
$where = _site_user;
define('_UserMenu', true);

function custom_content($kid = 1)
{
    global $db;
    $custom_content = '';
    $i = 0;
    $qrycustom = db("SELECT * FROM `" . $db['profile'] . "` WHERE `kid` = " . (int)($kid) . " AND `shown` = 1 ORDER BY `id` ASC;");
    if (_rows($qrycustom) >= 1) {
        while ($getcustom = _fetch($qrycustom)) {
            $getcontent = db("SELECT `" . $getcustom['feldname'] . "` FROM `" . $db['users'] . "` WHERE `id` = " . (int)($_GET['id']) . " LIMIT 1;", false, true);
            if (!empty($getcontent[$getcustom['feldname']])) {
                switch ($getcustom['type']) {
                    case 2:
                        $custom_content .= show(_profil_custom_url, array("name" => pfields_name(re($getcustom['name'])), "value" => re($getcontent[$getcustom['feldname']])));
                        break;
                    case 3:
                        $custom_content .= show(_profil_custom_mail, array("name" => pfields_name(re($getcustom['name'])), "value" => eMailAddr(re($getcontent[$getcustom['feldname']]))));
                        break;
                    default:
                        $custom_content .= show(_profil_custom, array("name" => pfields_name(re($getcustom['name'])), "value" => re($getcontent[$getcustom['feldname']])));
                        break;
                }
            }

            $i++;
        }
    }

    return array('count' => $i, 'content' => $custom_content);
}

if (file_exists(basePath . "/user/case_" . $action . ".php"))
    require_once(basePath . "/user/case_" . $action . ".php");

## INDEX OUTPUT ##
$whereami = preg_replace_callback("#autor_(.*?)$#", function ($id) {
    return re(data("nick", $id[1]));
}, $where);
$title = $pagetitle . " - " . $whereami . "";
page($index, $title, $where);