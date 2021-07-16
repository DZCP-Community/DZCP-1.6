<?php
/**
 * DZCP - deV!L`z ClanPortal - Server ( api.dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * DZCP - deV!L`z ClanPortal - Server
 * Homepage: https://www.dzcp.de
 * E-Mail: lbrucksch@hammermaps.de
 * Author Lucas Brucksch
 * Copyright 2021 © Codedesigns
 */

define('SCRIPT_PATH', dirname($_SERVER["SCRIPT_FILENAME"]));

require_once SCRIPT_PATH."/config.php";
require_once SCRIPT_PATH."/vendor/autoload.php";

//System
require_once SCRIPT_PATH."/system/BaseSystemAbstract.php";
require_once SCRIPT_PATH."/system/BaseSession.php";
require_once SCRIPT_PATH."/system/BaseSystem.php";

$baseSystem = new BaseSystem(false);

$baseSystem->__run();

$baseSystem->__shutdown();

