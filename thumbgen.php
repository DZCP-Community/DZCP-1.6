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

if(!ob_start("ob_gzhandler")) ob_start();

define('basePath', dirname(__FILE__));
define('is_thumbgen',true);

## INCLUDES ##
include(basePath."/inc/debugger.php");
include(basePath."/inc/configs/config.php");

if(!isset($_GET['img']) || empty($_GET['img']) || !extension_loaded('gd'))
    die('"gd" extension not loaded or "img" is empty');

if(!file_exists(basePath.'/'.$_GET['img']))
    die('"'.basePath.'/'.$_GET['img'].'" file is not exists');

$size       = getimagesize(basePath.'/'.$_GET['img']);
$file_exp   = explode('.',$_GET['img']);
$breite     = $size[0];
$hoehe      = $size[1];

$neueBreite = empty($_GET['width']) ? 100 : (int)$_GET['width'];
$neueHoehe = empty($_GET['height']) ? ((int)($hoehe*$neueBreite/$breite)) : (int)($_GET['height']);
$file_cache = basePath.'/'.$file_exp[0].'_minimize_'.$neueBreite.'x'.$neueHoehe;
$picture_build = false;

switch($size[2]) {
    case 1: ## GIF ##
        header("Content-Type: image/gif");
        $file_cache = $file_cache.'.gif';
        if(!thumbgen_cache || !file_exists($file_cache) || time() - filemtime($file_cache) > thumbgen_cache_time) {
            $altesBild = imagecreatefromgif(basePath.'/'.$_GET['img']);
            $neuesBild = imagecreatetruecolor($neueBreite,$neueHoehe);
            $CT = imagecolortransparent($altesBild);
            imagepalettecopy($neuesBild, $altesBild);
            imagefill($neuesBild, 0, 0, $CT);
            imagecolortransparent($neuesBild, $CT);
            imageantialias($neuesBild, true);
            imagecopyresampled($neuesBild, $altesBild,0,0,0,0, $neueBreite, $neueHoehe, $breite, $hoehe);
            thumbgen_cache ? imagegif($neuesBild,$file_cache) : imagegif($neuesBild);
            $picture_build = true;
        }
    break;
    default:
    case 2: ## JPEG ##
        header("Content-Type: image/jpeg");
        $file_cache = $file_cache.'.jpg';
        if(!thumbgen_cache || !file_exists($file_cache) || time() - @filemtime($file_cache) > thumbgen_cache_time) {
            $altesBild = imagecreatefromjpeg(basePath.'/'.$_GET['img']);
            $neuesBild = imagecreatetruecolor($neueBreite,$neueHoehe);
            imageantialias($neuesBild, true);
            imagecopyresampled($neuesBild, $altesBild,0,0,0,0, $neueBreite, $neueHoehe, $breite, $hoehe);
            thumbgen_cache ? imagejpeg($neuesBild, $file_cache, 100) : imagejpeg($neuesBild, null, 100);
            $picture_build = true;
        }
    break;
    case 3: ## PNG ##
        header("Content-Type: image/png");
        $file_cache = $file_cache.'.png';
        if(!thumbgen_cache || !file_exists($file_cache) || time() - @filemtime($file_cache) > thumbgen_cache_time) {
            header("Content-Type: image/png");
            $altesBild = imagecreatefrompng(basePath.'/'.$_GET['img']);
            $neuesBild = imagecreatetruecolor($neueBreite,$neueHoehe);
            imagealphablending($neuesBild, false);
            imagesavealpha($neuesBild,true); 
            imageantialias($neuesBild, true);
            imagecopyresampled($neuesBild, $altesBild,0,0,0,0, $neueBreite, $neueHoehe, $breite, $hoehe);
            thumbgen_cache ? imagepng($neuesBild,$file_cache) : imagepng($neuesBild);
            $picture_build = true;
        }
    break;
}

if ($picture_build && is_resource($altesBild)) {
    imagedestroy($altesBild);
}

if ($picture_build && is_resource($neuesBild)) {
    imagedestroy($neuesBild);
}

if (thumbgen_cache && file_exists($file_cache)) {
    echo file_get_contents($file_cache);
}