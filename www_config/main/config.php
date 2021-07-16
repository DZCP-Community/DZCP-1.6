<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

#########################################
//-> DZCP Settings Start
#########################################
define('use_default_timezone', true); // Verwendende die Zeitzone vom Web Server.
define('default_timezone', 'Europe/Berlin'); // Die zu verwendende Zeitzone selbst einstellen * 'use_default_timezone' auf false stellen *
define('show_empty_paginator', false); //Die Paginatoren sind immer sichtbar.

define('thumbgen_cache', true); // Sollen die verkleinerten Bilder der Thumbgen gespeichert werden.
define('thumbgen_cache_time', 60*60); // Wie lange sollen die verkleinerten Bilder der Thumbgen im Cache verbleiben.

define('cookie_expires', (60*60*24*30*12)); // Wie Lange sollen die Cookies des CMS ihre Gueltigkeit behalten.
define('cookie_domain', ''); // Die Domain, der das Cookie zur Verfugung steht.
define('cookie_dir', '/'); // Der Pfad auf dem Server, fur welchen das Cookie verfugbar sein wird.

define('autologin_expire', (14*24*60*60)); // Wie lange sollen die Autologins gultig bleiben, bis zum erneuten Login.

define('count_clicks_expires', (48*60*60)); // Wie Lange die IPs fur den Click-Counter gespeichert bleiben.

define('php_code_enabled', true); // Erlaubt es auf "Adminbereich: Seiten erstellen/verwalten", PHP Code zu verwenden. * Nur Aktivieren wenn es gebaucht wird! *

#########################################
//-> Sessions Settings Start * Expert *
#########################################
define('sessions_backend', 'php'); //Das zu verwendendes Backend: php,mysql,memcache,apc
define('sessions_encode_type', 'sha1'); //Verwende die sha1 codierung fuer session ids
define('sessions_encode', true); //Inhalt der Sessions zusatzlich verschlusseln
define('sessions_ttl_maxtime', (6*60*60)); //Live-Time der Sessions * 2h
define('sessions_memcache_host', '127.0.0.1'); //Server Adresse fur das Sessions Backend: memcache
define('sessions_memcache_port', 11311); //Server Port fur das Sessions Backend: memcache
define('sessions_name', 'DZCP-CS01'); //Server Port fur das Sessions Backend: memcache

define('sessions_sql_sethost', false); //Verwende eine externe Datenbank fur die Sessions
define('sessions_sql_driver', 'mysql'); //Welcher Datenbank Treiber soll verwendet werden
define('sessions_sql_host', ''); //SQL Host
define('sessions_sql_user', ''); //SQL Username
define('sessions_sql_pass', ''); //SQL Passwort
define('sessions_sql_db', ''); //SQL Database

class config {
    // Zeigt alle Fehler und Notices etc.
    static $view_error_reporting = true;

    // Zeigt JavaScript Aufrufe und Infos
    static $view_javascript_debug = true;

    // Schreibt die die Ausgaben der Debug Console in eine Datei
    static $debug_save_to_file = false;

    // Verwende feur Notices, etc. die Debug Console
    static $debug_dzcp_handler = true;

    //Umgeht die fsockopen Pruefung
    static $fsockopen_support_bypass = false;

    // Nach wie viel Sekunden soll der Download externer Quellen abgebrochen werden
    static $file_get_contents_timeout = 10;

    // Verwendet die CURL PHP Erweiterung, anstelle von file_get_contents() fur externe Zugriffe, wenn vorhanden
    static $use_curl = true;

    //Unterscheidet Groß und Kleinschreibung beim Captcha.
    static $captcha_case_sensitive = false;

    //Stellt den Usern einfache Rechenaufgaben anstelle eines Captcha Codes.
    static $captcha_mathematic = true;

    //Verwendet SoX fuer Captcha Audio Effecte
    static $captcha_audio_use_sox = false;
    static $captcha_audio_use_noise = true;
    static $captcha_degrade_audio = false;
    static $captcha_sox_binary_path =  'sox';

    // Wann soll der Newsfeed aktualisiert werden (In Sekunden)
    static $feed_update_time =  600;

    //Wenn eine SSL-Verbindung möglich ist, dann wird der Besucher automatisch umgeleitet
    static $use_ssl_auto_redirect = false;

    /* MYSQL */
    static $SQL_CONNECTION = ["prefix" => "dzcp_",
                                    "driver" => "mysql",
                                    "db_engine" => "default",
                                    "db" => '',
                                    "db_host" => '',
                                    "db_user" => '',
                                    "db_pw" => '',
                                    "persistent" => false];

    static $SQL_CONNECTION_SERVER = ["prefix" => "dzcp_server_",
                                    "driver" => "mysql",
                                    "db_engine" => "default",
                                    "db" => '',
                                    "db_host" => '',
                                    "db_user" => '',
                                    "db_pw" => '',
                                    "persistent" => false];

    static $rootAdmins = [1];

    static $extensions = ['image/jpeg','image/gif','image/png'];

    static $passwordComponents = ["ABCDEFGHIJKLMNOPQRSTUVWXYZ" , "abcdefghijklmnopqrstuvwxyz" , "0123456789" , "#$@!"];

    static $cryptkey = 'xxxxxxxxxxxyyyyyyyyyyyyyyyyyyyyy';

    static $use_system_cache = false;
    static $use_network_cache = false;
    static $use_less_cache = false;
    static $use_additional_dir = true;

    static $is_memcache = false;
    static $memcache_host = 'localhost';
    static $memcache_port = 11211;
	
	static $smarty_force_compile = true;
    static $smarty_debugging = false;
    static $smarty_caching = false;
    static $smarty_cache_lifetime = 60;
    static $smarty_allow_php_templates = true;

    static $is_redis = false;
    static $redis_host = 'localhost';
    static $redis_port = 7717;
    static $redis_password = '';
    static $redis_database = '';
    static $redis_timeout = '';

    static $upload_dir_permissions = '0755';
    static $upload_file_permissions = '0644';

    static $upload_forbidden_uploads = 'js jsp jsb mhtml mht xhtml xht php phtml php3 php4 php5 phps shtml jhtml pl sh py cgi exe application gadget hta cpl msc jar vb jse ws wsf wsc wsh ps1 ps2 psc1 psc2 msh msh1 msh2 inf reg scf msp scr dll msi vbs bat com pif cmd vxd cpl htpasswd htaccess';
    static $upload_allowed_uploads = '';

    static $dzcp_api_srv_add = 'https://api.dzcp.de';
    static $dzcp_api_indent = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

}
