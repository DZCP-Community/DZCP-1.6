<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

ob_start();
define('basePath', dirname(__FILE__));
$thumbgen = true;
include(basePath . '/vendor/autoload.php');
include(basePath . "/inc/debugger.php");
include(basePath . "/inc/config.php");

use GUMP\GUMP;
use Phpfastcache\CacheManager;

$gump = GUMP::get_instance();
$_GET = $gump->sanitize($_GET);

if (!isset($_GET['img']) || empty($_GET['img']) || !extension_loaded('gd'))
    die('"gd" extension not loaded or "img" is empty');

//Error Output
if (!file_exists(basePath . '/' . $_GET['img'])) {
    $size = getimagesize(basePath . '/inc/images/no_preview.png');
    $file_exp = explode('.', $_GET['img']);
    $breite = $size[0];
    $hoehe = $size[1];

    $neueBreite = empty($_GET['width']) ? 100 : (int)($_GET['width']);
    $neueHoehe = (int)($hoehe * $neueBreite / $breite);

    header("Content-Type: image/png");
    $altesBild = imagecreatefrompng(basePath . '/inc/images/no_preview.png');
    $neuesBild = imagecreatetruecolor($neueBreite, $neueHoehe);
    imagealphablending($neuesBild, false);
    imagesavealpha($neuesBild, true);
    imagecopyresampled($neuesBild, $altesBild, 0, 0, 0, 0, $neueBreite, $neueHoehe, $breite, $hoehe);
    ob_start();
        imagepng($neuesBild);
    $bild = ob_get_contents();
    ob_end_clean();

    echo $bild;
    exit();
}

$rebuild = isset($_GET['rebuild']);
$size = getimagesize(basePath . '/' . $_GET['img']);
$file_exp = explode('.', $_GET['img']);
$breite = $size[0];
$hoehe = $size[1];

$neueBreite = empty($_GET['width']) ? 100 : (int)($_GET['width']);
$neueHoehe = (int)($hoehe * $neueBreite / $breite);
$cachehash = str_replace(['/','\\'],'_',$file_exp[0]) . '_minimize_' . $neueBreite . 'x' . $neueHoehe;
$picture_build = false;

// Cache
$cache = CacheManager::getInstance($config_cache['storage'], $config_cache['config'],'default');

switch ($size[2]) {
    case 1: ## GIF ##
        header("Content-Type: image/gif");
        $cachehash = md5($cachehash . '_gif');
        $CachedString = $cache->getItem($cachehash);
        if ($rebuild || !thumbgen_cache || is_null($CachedString->get())) {
            $altesBild = imagecreatefromgif(basePath . '/' . $_GET['img']);
            $neuesBild = imagecreatetruecolor($neueBreite, $neueHoehe);
            $CT = imagecolortransparent($altesBild);
            imagepalettecopy($neuesBild, $altesBild);
            imagefill($neuesBild, 0, 0, $CT);
            imagecolortransparent($neuesBild, $CT);
            imagecopyresampled($neuesBild, $altesBild, 0, 0, 0, 0, $neueBreite, $neueHoehe, $breite, $hoehe);
            ob_start();
            imagegif($neuesBild);
                $bild = ob_get_contents();
            ob_end_clean();

            if(thumbgen_cache) {
                $CachedString->set(bin2hex($bild))->expiresAfter(thumbgen_cache_time);
            }

            echo $bild;
            $picture_build = true;
        } else {
            echo hex2bin($CachedString->get());
        }
        break;
    default:
    case 2: ## JPEG ##
        header("Content-Type: image/jpeg");
        $cachehash = md5($cachehash . '_jpg');
        $CachedString = $cache->getItem($cachehash);
        if ($rebuild || !thumbgen_cache || is_null($CachedString->get())) {
            $altesBild = imagecreatefromjpeg(basePath . '/' . $_GET['img']);
            $neuesBild = imagecreatetruecolor($neueBreite, $neueHoehe);
            imagecopyresampled($neuesBild, $altesBild, 0, 0, 0, 0, $neueBreite, $neueHoehe, $breite, $hoehe);
            ob_start();
            imagejpeg($neuesBild, null, 100);
                $bild = ob_get_contents();
            ob_end_clean();

            if(thumbgen_cache) {
                $CachedString->set(bin2hex($bild))->expiresAfter(thumbgen_cache_time);
            }

            echo $bild;
            $picture_build = true;
        } else {
            echo hex2bin($CachedString->get());
        }
        break;
    case 3: ## PNG ##
        header("Content-Type: image/png");
        $cachehash = md5($cachehash . '_png');
        $CachedString = $cache->getItem($cachehash);
        if ($rebuild || !thumbgen_cache || is_null($CachedString->get())) {
            header("Content-Type: image/png");
            $altesBild = imagecreatefrompng(basePath . '/' . $_GET['img']);
            $neuesBild = imagecreatetruecolor($neueBreite, $neueHoehe);
            imagealphablending($neuesBild, false);
            imagesavealpha($neuesBild, true);
            imagecopyresampled($neuesBild, $altesBild, 0, 0, 0, 0, $neueBreite, $neueHoehe, $breite, $hoehe);
            ob_start();
            imagepng($neuesBild);
                $bild = ob_get_contents();
            ob_end_clean();

            if(thumbgen_cache) {
                $CachedString->set(bin2hex($bild))->expiresAfter(thumbgen_cache_time);
            }

            echo $bild;
            $picture_build = true;
        } else {
            echo hex2bin($CachedString->get());
        }
        break;
}

if(thumbgen_cache && $picture_build) {
    $cache->save($CachedString);
}

if ($picture_build && is_resource($altesBild)) {
    imagedestroy($altesBild);
}

if ($picture_build && is_resource($neuesBild)) {
    imagedestroy($neuesBild);
}

ob_end_flush();