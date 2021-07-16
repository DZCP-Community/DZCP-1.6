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

// Start session if no headers were sent
if(!headers_sent()) {
    /** Start Sessions */
    if(sessions_backend != 'php') {
        $session = new session();
        if(!$session->init())
            die('PHP-Sessions not started!');
    } else {
        session_set_cookie_params(session_get_cookie_params()["lifetime"], '/', 'dzcp.de', true, true);
        if(!session::is_session_started() && !session_start())
            die('PHP-Sessions not started!');
    }
}

function mtime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function secure($string) {
    $string = trim($string);
    $string = str_replace("#","&#35;",$string);
    $string = str_replace("(","&#40;",$string);
    $string = str_replace(")","&#41;",$string);
    $string = str_replace("<","&#60;",$string);
    return str_replace(">","&#62;",$string);
}

function secure_global_imput($string) {
    return str_ireplace(
            ['=','?','\'','"','','<','>',
                '(',')',';',',','.','+'],
            '', strtolower(trim($string)));
}

// set a backslash before a quote in $_POST, $_GET and $_COOKIE var, if magic_quotes_gpc is disabled in php.ini
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    add_stripslashes($_REQUEST);
    add_stripslashes($_GET);
    add_stripslashes($_POST);
    add_stripslashes($_COOKIE);

    if (is_array($_FILES)) {
        foreach ($_FILES AS $key => $val) {
            $_FILES["$key"]['tmp_name'] = str_replace('\\', '\\\\', $val['tmp_name']);

            // checks validation of uploaded files (only images are allowed!)
            if(!empty($val['tmp_name'])) {
                $end  = explode(".", $val['name']);
                $end  = strtolower($end[count($end)-1]);
                $info = getimagesize($val['tmp_name']);

                if($end != 'rar' && $end != 'zip') {
                    if(($info[2] == 1 || $info[2] == 2 || $info[2] == 3)
                    &&
                    ($end == 'jpg' || $end == 'jpeg' || $end == 'gif' || $end == 'png')
                    &&
                    !$val['error'])
                        $_FILES[$key] = $val;
                    else {
                        @unlink($val['tmp_name']);
                        $_FILES[$key] = 'notvalid';
                    }
                }
            }
        }

        add_stripslashes($_FILES);
    }
}

if(function_exists('set_magic_quotes_runtime')
   && version_compare(PHP_VERSION, '5.3.0', '<')) {
    @ini_set('magic_quotes_sybase', 0);
}

foreach (['_GET', '_POST'] AS $arrayname) {
    if (isset($GLOBALS["$arrayname"]['do']))
        $GLOBALS["$arrayname"]['do'] = trim($GLOBALS["$arrayname"]['do']);

    if (isset($GLOBALS["$arrayname"]['action']))
        $GLOBALS["$arrayname"]['action'] = trim($GLOBALS["$arrayname"]['action']);
}

function add_stripslashes(&$value, $depth = 0) {
    if (is_array($value)) {
        foreach ($value AS $key => $val) {
            if (is_string($val))
                $value["$key"] = stripslashes(secure($val));
            else if (is_array($val) && $depth < 10)
                add_stripslashes($value["$key"], $depth + 1);
        }
    }
}
