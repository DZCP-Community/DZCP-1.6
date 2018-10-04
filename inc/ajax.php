<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START #
define('basePath', dirname(dirname(__FILE__) . '../'));
ob_start();
ob_implicit_flush(false);
if (version_compare(phpversion(), '7.0', '<')) {
    die('Bitte verwende PHP-Version 7.0 oder h&ouml;her.<p>Please use PHP-Version 7.0 or higher.');
}

$ajaxJob = true;

## INCLUDES ##
include(basePath . '/vendor/autoload.php');

use GUMP\GUMP;

$gump = GUMP::get_instance();
$_GET = $gump->sanitize($_GET);
$_POST = $gump->sanitize($_POST);

include(basePath . "/inc/debugger.php");
include(basePath . "/inc/config.php");
include(basePath . "/inc/bbcode.php");

## FUNCTIONS ##
require_once(basePath . "/inc/menu-functions/server.php");
require_once(basePath . "/inc/menu-functions/shout.php");
require_once(basePath . "/inc/menu-functions/teamspeak.php");
require_once(basePath . "/inc/menu-functions/kalender.php");
require_once(basePath . "/inc/menu-functions/team.php");

## SETTINGS ##
$dir = "sites";

//-> Steam Status
function steamIMG($steamID = '')
{
    global $cache;
    if (empty($steamID) || !steam_enable) return '-';
    if (!fsockopen_support() && !fsockopen_support_bypass) return '-';
    if (!$steam = SteamAPI::getUserInfos($steamID)) return '-'; //UserInfos
    if (!$steam || empty($steam) || !is_array($steam) || count($steam) <= 1) return '-';

    $CachedString = $cache->getItem('steam_avatar_' . $steamID);
    if (is_null($CachedString->get())) {
        if (($img_stream = get_external_contents($steam['user']['avatarIcon_url'], false, true)) && !empty($img_stream)) {
            $steam['user']['avatarIcon_url'] = 'data:image/png;base64,' . base64_encode($img_stream);
            if (steam_avatar_cache) {
                $CachedString->set(bin2hex($img_stream))->expiresAfter(steam_avatar_refresh);
                $cache->save($CachedString);
            }
        } else
            return '-';
    } else
        $steam['user']['avatarIcon_url'] = 'data:image/png;base64,' . base64_encode(hex2bin($CachedString->get()));

    switch ($steam['user']['onlineState']) {
        case 'in-game':
            $status_set = '2';
            $text_1 = _steam_in_game;
            $text_2 = $steam['user']['gameextrainfo'];
            break;
        case 'online':
            $status_set = '1';
            $text_1 = _steam_online;
            $text_2 = '';
            break;
        default:
            $status_set = '0';
            $text_1 = $steam['user']['runnedSteamAPI'] ? show(_steam_offline, array('time' => get_elapsed_time((int)$steam['user']['lastlogoff'], time(), 1))) : _steam_offline_simple;
            $text_2 = '';
            break;
    }

    return show((isset($_GET['list']) ? _steamicon_nouser : _steamicon), array('profile_url' => $steam['user']['profile_url'], 'username' => $steam['user']['nickname'], 'avatar_url' => $steam['user']['avatarIcon_url'],
        'text1' => $text_1, 'text2' => $text_2, 'status' => $status_set));
}

## SECTIONS ##
switch (isset($_GET['i']) ? $_GET['i'] : ''):
    case 'kalender';
        echo kalender($_GET['month'], $_GET['year']);
        break;
    case 'teams';
        echo team($_GET['tID']);
        break;
    case 'server';
        echo '<table class="hperc" cellspacing="0">' . server($_GET['serverID']) . '</table>';
        break;
    case 'shoutbox';
        echo '<table class="hperc" cellspacing="1">' . shout(true) . '</table>';
        break;
    case 'teamspeak';
        echo '<table class="hperc" cellspacing="0">' . teamspeak(true) . '</table>';
        break;
    case 'steam';
        echo steamIMG(trim($_GET['steamid']));
        break;
endswitch;

if (!mysqli_persistconns)
    $mysql->close(); //MySQL

$output = ob_get_contents();
if (debug_save_to_file)
    DebugConsole::save_log(); //Debug save to file

ob_end_clean();
ob_start('ob_gzhandler');
exit(isset($_GET['dev']) ? DebugConsole::show_logs() . $output : $output);
ob_end_flush();