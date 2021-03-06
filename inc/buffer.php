<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

ob_start();
ob_implicit_flush(false);
define('basePath', dirname(dirname(__FILE__) . '../'));

if (version_compare(phpversion(), '7.0', '<')) {
    die('Bitte verwende PHP-Version 7.0 oder h&ouml;her.<p>Please use PHP-Version 7.0 or higher.');
}

include(basePath . '/vendor/autoload.php');

use GUMP\GUMP;

function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$time_start = getmicrotime();

//Filter Sanitize
$gump = GUMP::get_instance();
$blacklist = array('comment', 'newstext', 'eintrag',
    'artikel', 'reason', 'text', 'ich', 'sig', 'bericht');
$filter = $gump->sanitize($_POST);
foreach ($_POST as $key => $var) {
    if (in_array($key, $blacklist) ||
        defined('_Admin')) {
        $filter[$key] = $_POST[$key];
    }
}

$_POST = $filter;
unset($filter, $key, $var);
$_GET = $gump->sanitize($_GET);

function gz_output($output = '')
{
    $gzip_compress_level = (!defined('buffer_gzip_compress_level') ? 4 : buffer_gzip_compress_level);
    if (function_exists('ini_set'))
        ini_set('zlib.output_compression_level', $gzip_compress_level);

    if (buffer_show_licence_bar) {
        $licence_bar = '<div class="licencebar"> <table style="margin:auto" cellspacing="0"><tr><td class="licencebar" nowrap="nowrap">Powered by <a class="licencebar" href="https://www.dzcp.de" target="_blank" title="deV!L`z Clanportal">DZCP - deV!L`z&nbsp;Clanportal V' . _version .
            (_edition == 'dev' ? ' - Development Editon' : (_edition == 'cb' ?  ' - Custom Build' : '')) . '</a></td></tr> </table> </div>';

        if (!file_exists(basePath . '/_codeking.licence'))
            $output = str_ireplace('</body>', $licence_bar . "\r\n</body>", $output);
    }

    ob_end_clean();
    ob_start('ob_gzhandler');
    $output .= "\r\n<!--This CMS is powered by deV!L`z Clanportal V" . _version . " - www.dzcp.de-->";
    echo $output . "\r\n" . "<!-- [GZIP => Level " . $gzip_compress_level . "] " . sprintf("%01.2f", ((strlen(gzcompress($output, $gzip_compress_level))) / 1024)) . " kBytes | uncompressed: " . sprintf("%01.2f", ((strlen($output)) / 1024)) . " kBytes -->";
    ob_end_flush();
    exit();
}