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

define('rootPath', dirname(__FILE__,3));
define('configPath', '/usr/home/dzcpad/www_config');

//-> Ladezeit berechen
function getmicrotime() {
    list($usec,$sec) = explode(" ",microtime());
    return((float)$usec+(float)$sec);
}
define('start_time',getmicrotime());

if(!defined('is_ajax')) { define('is_ajax', false); }
if(!defined('is_thumbgen')) { define('is_thumbgen', false); }
if(!defined('is_admin')) { define('is_admin', false); }

if(!file_exists(basePath."/vendor/autoload.php")) {
    die("The folder '/vendor' was not found! Run composer to install the missing packets! 'php composer install'");
}

## INCLUDES ##
require_once(basePath."/vendor/autoload.php");
require_once(basePath."/inc/debugger.php");
require_once(configPath."/main/config.php");
require_once(basePath."/inc/database.php");
require_once(basePath.'/inc/crypt.php');
require_once(basePath.'/inc/sessions.php');
require_once(basePath.'/inc/secure.php');
require_once(basePath."/inc/cookie.php");
require_once(basePath."/inc/javascript.php");
require_once(basePath."/inc/stringParser.php");
require_once(basePath."/inc/sfs.php");
require_once(basePath."/inc/bbcode.php");
require_once(basePath."/inc/cache.php");
require_once(basePath."/inc/fileman.php");
require_once(basePath."/inc/netapi.php");
require_once(basePath .'/inc/securimage/securimage_color.php');
require_once(basePath .'/inc/securimage/securimage.php');
require_once(basePath .'/inc/notification.php');
require_once(basePath.'/inc/settings.php');
require_once(basePath.'/inc/netapi.php');

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Phine\Country\Loader\Loader;

//Global Strings
$index = ''; $show = ''; $color = 0;

new common(); //Main Construct

require_once(basePath.'/inc/sfs.php');

if(config::$use_additional_dir) {
//-> Neue Kernel Funktionen einbinden, sofern vorhanden
    if ($functions_files = common::get_files(basePath . '/inc/additional-kernel/', false, true, ['php'])) {
        foreach ($functions_files as $func) {
            include_once(basePath . '/inc/additional-kernel/' . $func);
        }
        unset($functions_files, $func);
    }
}

/**
 * Class common
 */
class common {
    //Public
    public static $database = NULL;
    public static $sql = [];
    public static $securimage = NULL;
    public static $httphost = NULL;
    public static $userip = [];
    public static $userid = 0;
    public static $smarty = NULL;
    public static $gump = NULL;
    public static $sid = NULL;
    public static $domain = NULL;
    public static $pagetitle = NULL;
    public static $sdir = NULL;
    public static $reload = 3600;
    public static $maxpicwidth = 90;
    public static $maxfilesize = NULL;
    public static $UserAgent = NULL;
    public static $designpath = NULL;
    public static $tmpdir = NULL;
    public static $chkMe = 0;
    public static $CrawlerDetect = NULL;
    public static $less = NULL;
    public static $mobile = NULL;
    public static $cache = NULL;
    public static $action = 'default';
    public static $page = 1;
    public static $do = '';
    public static $search_forum = false;
    public static $BBCode = NULL;
    public static $country = NULL;
    public static $api = NULL;
    public static $is_addons = false;

    /**
     * @var APIClientMethods
     */
    public static $server = NULL;

    //Consts
    const FORUM_DOUBLE_POST_INSERT = 0;
    const FORUM_DOUBLE_POST_TH_ADD = 1;
    const FORUM_DOUBLE_POST_PO_ADD = 2;

    const IPV6_NULL_ADDR = 'xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx';
    const IPV4_NULL_ADDR = '0.0.0.0';

    const SUPPORTED_PICTURE = ["jpg", "jpeg", "gif", "png"];

    //Functions
    /**
     * common constructor.
     */
    public function __construct()
    {
        //->Set default timezone
        if (function_exists("date_default_timezone_set") && function_exists("date_default_timezone_get") && use_default_timezone) {
            date_default_timezone_set(date_default_timezone_get());
        } else if (!use_default_timezone) {
            date_default_timezone_set(default_timezone);
        } else {
            date_default_timezone_set("Europe/Berlin");
        }

        //->Set Debugger
        if(!is_thumbgen) {
            if(config::$view_error_reporting) {
                error_reporting(E_ALL);

                if (function_exists('ini_set')) {
                    ini_set('display_errors', 1);
                }

                DebugConsole::initCon();

                if (config::$debug_dzcp_handler) {
                    set_error_handler('dzcp_error_handler');
                }
            } else {
                if (function_exists('ini_set')) {
                    ini_set('display_errors', 0);
                }

                error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

                if (config::$debug_dzcp_handler) {
                    set_error_handler('dzcp_error_handler');
                }
            }
        }

        //Filter 404
        $filter404 = strtolower(self::GetServerVars("REQUEST_URI"));
        if (strpos($filter404, 'index.php/') !== false ||
            strpos($filter404, 'ajax.php/') !== false) {
            header("HTTP/1.0 404 Not Found");
            exit();
        } unset($filter404);

        //Weiterleitung zu einer SSL Verbindung
        if(!self::isSecure() && config::$use_ssl_auto_redirect && !is_ajax && !is_thumbgen) {
            if(self::ping_port(self::GetServerVars('HTTP_HOST'),443,0.2)) {
                header("Location: https://" . self::GetServerVars('HTTP_HOST') .
                    self::GetServerVars('REQUEST_URI'));
                exit();
            }
        }

        //Ist die domain addons.dzcp.de
        if (strpos(strtolower($_SERVER['SERVER_NAME']), 'addons.dzcp.de') !== false) {
            self::$is_addons = true;
        } else {
            self::$is_addons = false;
        }

        //Set DSGVO to false
        if (!array_key_exists('DSGVO', $_SESSION)) {
            $_SESSION['DSGVO'] = false;
        }

        //Set DSGVO Lock
        if (!array_key_exists('user_has_dsgvo_lock', $_SESSION)) {
            $_SESSION['user_has_dsgvo_lock'] = false;
        }

        //Check is DSGVO Set?
        if (isset($_GET['dsgvo'])) {
            switch ((int)$_GET['dsgvo']) {
                case 1:
                    $_SESSION['DSGVO'] = true;
                    $_SESSION['do_show_dsgvo'] = true;
                    header("Location: " . self::GetServerVars('HTTP_REFERER'));
                    break;
                default:
                    $_SESSION['DSGVO'] = false;
                    $_SESSION['do_show_dsgvo'] = true;
                    $_SESSION['user_has_dsgvo_lock'] = false;
                    header("Location: " . self::GetServerVars('HTTP_REFERER'));
            }
        }

        //-> Global
        self::$action = isset($_GET['action']) ? secure_global_imput($_GET['action']) : (isset($_POST['action']) ? secure_global_imput($_POST['action']) : 'default');
        self::$page = isset($_GET['page']) ? (int)(trim($_GET['page'])) : (isset($_POST['page']) ? (int)(trim($_POST['page'])) : 1);
        self::$do = isset($_GET['do']) ? secure_global_imput($_GET['do']) : (isset($_POST['do']) ? secure_global_imput($_POST['do']) : '');

		self::$search_forum = false;

        //->Crawler Detect
        self::$CrawlerDetect = new CrawlerDetect();

        //->Init-Database
        self::$gump = new GUMP();

        //->Init-Mobile_Detect
        self::$mobile = new Mobile_Detect();

        //->Init-CacheManager
        self::$cache = new Cache();

        //->Init-Nbbc_BBCode
        self::$BBCode = new Nbbc\BBCode();

        //->Init-Database
        self::$database = new database();
        self::$database->setConfig('default',config::$SQL_CONNECTION);
        self::$sql['default'] = self::$database->getInstance();

        //->Network API
        self::$api = new dzcp_network_api();

        //->Lade Einstellungen
        settings::load();

        //->Lade Securimage
        if(!is_thumbgen) {
            self::$securimage = new Securimage();
        }

        //-> Cookie initialisierung
        if(self::HasDSGVO()) {
            cookie::init(false, '/', '.dzcp.de');
        }

        //-> JS initialisierung
        if(!is_thumbgen && !is_ajax) {
            javascript::set('AnchorMove', '');
            javascript::set('debug', (config::$view_error_reporting && config::$view_javascript_debug));
        }

        //-> Language auslesen oder default setzen
        if(self::HasDSGVO()) {
            $_SESSION['language'] = (cookie::get('language') != false ?
                (file_exists(basePath . '/inc/lang/' . cookie::get('language') . '.php') ?
                    cookie::get('language') :
                    settings::get('language')) :
                settings::get('language'));
        } else {
            if(!array_key_exists('language',$_SESSION) || empty($_SESSION['language'])) {
                $_SESSION['language'] = settings::get('language');
            }
        }

        if(!is_thumbgen) {
            $subfolder = basename(dirname(dirname(self::GetServerVars('PHP_SELF')) . '../'));
            self::$httphost = self::GetServerVars('HTTP_HOST') . (empty($subfolder) ? '' : '/' . $subfolder);
            unset($subfolder);
        }

        //Less Parser
        $options = ['compress' => true, 'sourceMap' => false];
        self::$less = new Less_Parser($options);
        unset($options);

        //Country class
        self::$country = new Loader();

        //Set User IP & einzelne Definitionen
        self::$userip = self::HasDSGVO() ? self::visitorIp() : ['v4' => self::IPV4_NULL_ADDR, 'v6' => self::IPV6_NULL_ADDR];
        self::$domain = str_replace('www.','',self::$httphost);
        self::$pagetitle = stringParser::decode(settings::get('pagetitel'));
        self::$sdir = stringParser::decode(settings::get('tmpdir'));
        self::$reload = 3600 * 24;
        self::$maxpicwidth = 90;
        self::$maxfilesize = @ini_get('upload_max_filesize');
        self::$UserAgent = trim(self::GetServerVars('HTTP_USER_AGENT'));
        self::$sid = (float)rand()/(float)getrandmax();

        //Smarty Template-system
        self::$smarty = self::getSmarty(true);

        if(self::HasDSGVO()) {
            // IP Prufung * No IPV6 Support *
            //self::check_ip();

            //Nachrichten Check
            self::check_msg_emal();
        }

        //-> Auslesen der Cookies und automatisch anmelden
        if(self::HasDSGVO()) {
            if (cookie::get('id') != false && cookie::get('pkey') != false && empty($_SESSION['id']) && !self::checkme()) {
                //-> Permanent Key aus der Datenbank suchen
                $get_almgr = self::$sql['default']->fetch("SELECT `id`,`uid`,`update`,`expires` FROM `{prefix_autologin}` WHERE `pkey` = ? AND `uid` = ?;", [cookie::get('pkey'), cookie::get('id')]);
                if (self::$sql['default']->rowCount()) {
                    if ((!$get_almgr['update'] || (time() < ($get_almgr['update'] + $get_almgr['expires'])))) {
                        //-> User aus der Datenbank suchen
                        $get = self::$sql['default']->fetch("SELECT `id`,`user`,`nick`,`pwd`,`email`,`level`,`time`,`dsgvo_lock` FROM `{prefix_users}` WHERE `id` = ? AND `level` != 0;", [cookie::get('id')]);
                        if (self::$sql['default']->rowCount()) {
                            if ($get['dsgvo_lock']) {
                                $_SESSION['user_has_dsgvo_lock'] = true;
                                $_SESSION['dsgvo_lock_permanent_login'] = true;
                                $_SESSION['dsgvo_lock_login_id'] = $get['id'];
                                if (!array_key_exists('dsgvo_lock_login_id', $_SESSION))
                                    header("Location: ?action=userlock");
                            } else {
                                //-> Generiere neuen permanent-key
                                $permanent_key = md5(self::mkpwd(8));
                                cookie::set('pkey', $permanent_key);
                                cookie::save();

                                //Update Autologin
                                self::$sql['default']->update("UPDATE `{prefix_autologin}` SET `ssid` = ?, `pkey` = ?, `ipv4` = ?, `host` = ?, `update` = ?, `expires` = ? WHERE `id` = ?;",
                                    [session_id(), $permanent_key, self::$userip['v4'], gethostbyaddr(self::$userip['v4']), time(), autologin_expire, $get_almgr['id']]);

                                //-> Schreibe Werte in die Server Sessions
                                $_SESSION['id'] = $get['id'];
                                $_SESSION['pwd'] = $get['pwd'];
                                $_SESSION['lastvisit'] = $get['time'];
                                $_SESSION['ip'] = self::$userip['v4'];
                                $_SESSION['admin_id'] = '';
                                $_SESSION['admin_pwd'] = '';
                                $_SESSION['admin_ip'] = '';
                                $_SESSION['akl_id'] = 0;

                                if (self::data("ipv4", $get['id']) != $_SESSION['ip']) {
                                    $_SESSION['lastvisit'] = self::data("time", $get['id']);
                                }

                                if (empty($_SESSION['lastvisit'])) {
                                    $_SESSION['lastvisit'] = self::data("time", $get['id']);
                                }

                                //-> Aktualisiere Datenbank
                                self::$sql['default']->update("UPDATE `{prefix_users}` SET `online` = 1, `sessid` = ?, `ipv4` = ? WHERE `id` = ?;",
                                    [session_id(), self::$userip['v4'], $get['id']]);

                                //-> Aktualisiere die User-Statistik
                                self::userstats_increase('logins', $get['id']);

                                //-> Aktualisiere Ip-Count Tabelle
                                foreach (self::$sql['default']->select("SELECT `id` FROM `{prefix_clicks_ips}` WHERE `ipv4` = ? AND `uid` = 0;", [self::$userip['v4']]) as $get_ci) {
                                    self::$sql['default']->update("UPDATE `{prefix_clicks_ips}` SET `uid` = ? WHERE `id` = ?;", [$get['id'], $get_ci['id']]);
                                }

                                unset($get, $permanent_key, $get_almgr, $get_ci); //Clear Mem
                            }
                        } else {
                            self::dzcp_session_destroy();
                            $_SESSION['id'] = '';
                            $_SESSION['pwd'] = '';
                            $_SESSION['ip'] = '';
                            $_SESSION['lastvisit'] = '';
                            $_SESSION['pkey'] = '';
                            $_SESSION['admin_id'] = '';
                            $_SESSION['admin_pwd'] = '';
                            $_SESSION['admin_ip'] = '';
                            $_SESSION['akl_id'] = 0;
                        }
                    } else {
                        self::$sql['default']->delete("DELETE FROM `{prefix_autologin}` WHERE `id` = ?;", [$get_almgr['id']]);
                        self::dzcp_session_destroy();
                    }
                }
            }
        }

        //-> Sprache aendern
        if (isset($_GET['set_language']) && !empty($_GET['set_language'])) {
            if (file_exists(basePath . "/inc/lang/" . $_GET['set_language'] . ".php")) {
                $_SESSION['language'] = $_GET['set_language'];
                if(self::HasDSGVO()) {
                    cookie::set('language', $_GET['set_language']);
                    cookie::save();
                }
            }

           header("Location: " . stringParser::decode(self::GetServerVars('HTTP_REFERER')));
           exit();
        }

        if(!array_key_exists('language',$_SESSION)) {
            $_SESSION['language'] = stringParser::decode(settings::get('language'));
        }

        self::lang($_SESSION['language']); //Lade Sprache
        self::$userid = 0;
        self::$chkMe = 0;

        if(self::HasDSGVO()) {
            self::$userid = self::userid();
            self::$chkMe = self::checkme();

            if(!self::$chkMe && (!empty($_SESSION['id']) || !empty($_SESSION['pwd']))) {
                $_SESSION['id']        = '';
                $_SESSION['pwd']       = '';
                $_SESSION['ip']        = self::$userip['v4'];
                $_SESSION['lastvisit'] = time();
                $_SESSION['language'] = stringParser::decode(settings::get('language'));
                $_SESSION['admin_id'] = '';
                $_SESSION['admin_pwd'] = '';
                $_SESSION['admin_ip'] =  '';
                $_SESSION['akl_id']    = 0;
            }

            //-> Prueft ob der User gebannt ist, oder die IP des Clients warend einer offenen session veraendert wurde.
            if (self::$chkMe && self::$userid && !empty($_SESSION['ip'])) {
                if ($_SESSION['ip'] != self::visitorIp()['v4'] || self::isBanned(self::$userid, false)) {
                    self::dzcp_session_destroy();
                    header("Location: ../news/");
                }
            }
        }

        /*
         * Aktualisiere die Client DNS & User Agent
         */
        if(session_id() && self::HasDSGVO()) {
            $userdns = self::DNSToIp(self::$userip['v4']);
            if(self::$sql['default']->rows("SELECT `id` FROM `{prefix_iptodns}` WHERE `update` <= ? AND `sessid` = ?;", [time(),session_id()])) {
                self::$sql['default']->update("UPDATE `{prefix_iptodns}` SET `time` = ?, `update` = ?, `ipv4` = ?, `agent` = ?, `dns` = ?, `bot` = ?, `bot_name` = ? WHERE `sessid` = ?;",
                    [(time()+10*60),(time()+60),self::$userip['v4'],stringParser::encode(self::$UserAgent),stringParser::encode($userdns),(self::$CrawlerDetect->isCrawler() ? 1 : 0),
                        stringParser::encode(self::$CrawlerDetect->getMatches()),session_id()]);
            } else if(!self::$sql['default']->rows("SELECT `id` FROM `{prefix_iptodns}` WHERE `sessid` = ?;", [session_id()])) {
                self::$sql['default']->insert("INSERT INTO `{prefix_iptodns}` SET `sessid` = ?, `time` = ?, `ipv4` = ?, `agent` = ?, `dns` = ?, `bot` = ?, `bot_name` = ?;",
                    [session_id(),(time()+10*60),self::$userip['v4'],stringParser::encode(self::$UserAgent),stringParser::encode($userdns),
                        (self::$CrawlerDetect->isCrawler() ? 1 : 0),stringParser::encode(self::$CrawlerDetect->getMatches())]);
                unset($bot);
            }

            //-> Cleanup DNS DB
            $qryDNS = self::$sql['default']->select("SELECT `id`,`ipv4` FROM `{prefix_iptodns}` WHERE `time` <= ?;", [time()]);
            if(self::$sql['default']->rowCount()) {
                foreach($qryDNS as $getDNS) {
                    self::$sql['default']->delete("DELETE FROM `{prefix_iptodns}` WHERE `id` = ?;", [$getDNS['id']]);
                    self::$sql['default']->delete("DELETE FROM `{prefix_counter_whoison}` WHERE `ipv4` = ?;", [$getDNS['ipv4']]);
                } unset($getDNS);
            } unset($qryDNS);

            /*
             * Pruft ob mehrere Session IDs von der gleichen DNS kommen, sollte der Useragent keinen Bot Tag enthalten, wird ein Spambot angenommen.
             */
            $get_sb_array = self::$sql['default']->select("SELECT `id`,`ipv4`,`bot`,`agent` FROM `{prefix_iptodns}` WHERE `dns` LIKE ?;", [stringParser::encode($userdns)]);
            if(self::$sql['default']->rowCount() >= 3 && !self::validateIpV4Range(self::$userip['v4'], '[192].[168].[0-255].[0-255]') &&
                !self::validateIpV4Range(self::$userip['v4'], '[127].[0].[0-255].[0-255]') &&
                !self::validateIpV4Range(self::$userip['v4'], '[10].[0-255].[0-255].[0-255]') &&
                !self::validateIpV4Range(self::$userip['v4'], '[172].[16-31].[0-255].[0-255]')) {
                foreach ($get_sb_array as $get_sb) {
                    if (!$get_sb['bot'] && !self::$CrawlerDetect->isCrawler(stringParser::decode($get_sb['agent']))) {
                        if (!self::$sql['default']->rows("SELECT `id` FROM `{prefix_ipban}` WHERE `ipv4` = ? LIMIT 1;", [self::$userip['v4']])) {
                            $data_array = [];
                            $data_array['confidence'] = '';
                            $data_array['frequency'] = '';
                            $data_array['lastseen'] = '';
                            $data_array['banned_msg'] = stringParser::encode('SpamBot detected by System * No BotAgent *');
                            $data_array['agent'] = $get_sb['agent'];
                            self::$sql['default']->insert("INSERT INTO `{prefix_ipban}` SET `time` = ?, `ipv4` = ?, `data` = ?, `typ` = 3;", [time(), $get_sb['ipv4'], serialize($data_array)]);
                           // self::check_ip(); // IP Prufung * No IPV6 Support *
                            unset($data_array);
                        }
                    }
                }
            }

            unset($get_sb,$get_sb_array);
        }

        //-> Templateswitch
        self::sysTemplateswitch();

        //Init new BBCode
        $bbcode = new BBCode();
        $bbcode->getInstance();
        unset($bbcode);

        //-> User Hits und Lastvisit aktualisieren
        if(self::HasDSGVO() && self::$userid >= 1 && !is_ajax && !is_thumbgen && isset($_SESSION['lastvisit'])) {
            self::$sql['default']->update("UPDATE `{prefix_user_stats}` SET `hits` = (hits+1), `lastvisit` = ? WHERE `user` = ?;",
                [(int)($_SESSION['lastvisit']),(int)(self::$userid)]);
        }
    }

    /**
     * Setzt das aktive Template
     */
    private static function sysTemplateswitch() {
        $files = self::get_files(basePath.'/inc/_templates_/',true);
        if(isset($_GET['tmpl_set'])) {
            foreach ($files as $templ) {
                $cache_hash = md5('templateswitch_xml_'.$templ);
                if(!common::$cache->AutoMemExists($cache_hash) || !config::$use_system_cache) {
                    $xml = simplexml_load_file(basePath.'/inc/_templates_/'.$templ.'/template.xml');
                    if(!empty((string)$xml->permissions) && (string)$xml->permissions != 'null') {
                        if(common::permission((string)$xml->permissions) || ((int)$xml->level >= 1 && common::$chkMe >= (int)$xml->level)) {
                            if($templ == $_GET['tmpl_set']) {
                                $_SESSION['tmpdir'] = $templ;
                                if(self::HasDSGVO()) {
                                    cookie::set('tmpdir', $templ);
                                    cookie::save();
                                }
                                header("Location: ".self::GetServerVars('HTTP_REFERER'));
                            }
                        }
                    } else if((int)$xml->level >= 1 && common::$chkMe >= (int)$xml->level) {
                        if($templ == $_GET['tmpl_set']) {
                            $_SESSION['tmpdir'] = $templ;
                            if(self::HasDSGVO()) {
                                cookie::set('tmpdir', $templ);
                                cookie::save();
                            }
                            header("Location: ".self::GetServerVars('HTTP_REFERER'));
                        }
                    } else if(!(int)$xml->level){
                        if($templ == $_GET['tmpl_set']) {
                            $_SESSION['tmpdir'] = $templ;
                            if(self::HasDSGVO()) {
                                cookie::set('tmpdir', $templ);
                                cookie::save();
                            }
                            header("Location: ".self::GetServerVars('HTTP_REFERER'));
                        }
                    }
                } else {
                    $data = json_decode(common::$cache->AutoMemGet($cache_hash),true);
                    if(!empty($data['permissions']) && (string)$data['permissions'] != 'null') {
                        if(common::permission((string)$data['permissions']) || ((int)$data['level'] >= 1 && (int)$data['level'])) {
                            if($templ == $_GET['tmpl_set']) {
                                $_SESSION['tmpdir'] = $templ;
                                if(self::HasDSGVO()) {
                                    cookie::set('tmpdir', $templ);
                                    cookie::save();
                                }
                                header("Location: ".self::GetServerVars('HTTP_REFERER'));
                            }
                        }
                    } else if((int)$data['level'] >= 1 &&
                        common::$chkMe >= (int)$data['level']) {
                        if($templ == $_GET['tmpl_set']) {
                            $_SESSION['tmpdir'] = $templ;
                            if(self::HasDSGVO()) {
                                cookie::set('tmpdir', $templ);
                                cookie::save();
                            }
                            header("Location: ".self::GetServerVars('HTTP_REFERER'));
                        }
                    } else if(!(int)$data['level']){
                        if($templ == $_GET['tmpl_set']) {
                            $_SESSION['tmpdir'] = $templ;
                            if(self::HasDSGVO()) {
                                cookie::set('tmpdir', $templ);
                                cookie::save();
                            }
                            header("Location: ".self::GetServerVars('HTTP_REFERER'));
                        }
                    }
                }
            }

            unset($xml,$templ);
        }

        if(cookie::get('tmpdir') != false && cookie::get('tmpdir') != NULL && self::HasDSGVO()) {
            if (file_exists(basePath . "/inc/_templates_/" . cookie::get('tmpdir'))) {
                $cache_hash = md5('templateswitch_xml_'.cookie::get('tmpdir'));
                if(!common::$cache->AutoMemExists($cache_hash) || !config::$use_system_cache) {
                    $xml = simplexml_load_file(basePath.'/inc/_templates_/'.cookie::get('tmpdir').'/template.xml');
                    if(config::$use_system_cache) {
                        common::$cache->AutoMemSet($cache_hash, json_encode($xml), cache::TIME_TEMPLATE_XML);
                    }
                    if(!empty((string)$xml->permissions) && (string)$xml->permissions != 'null') {
                        if(common::permission((string)$xml->permissions) || ((int)$xml->level >= 1 && common::$chkMe >= (int)$xml->level)) {
                            self::$tmpdir = cookie::get('tmpdir');
                        }
                    } else if((int)$xml->level >= 1 && common::$chkMe >= (int)$xml->level) {
                        self::$tmpdir = cookie::get('tmpdir');
                    } else if(!(int)$xml->level) {
                        self::$tmpdir = cookie::get('tmpdir');
                    }
                } else {
                    $data = json_decode(common::$cache->AutoMemGet($cache_hash),true);
                    if(!empty($data['permissions']) && (string)$data['permissions'] != 'null') {
                        if(common::permission((string)$data['permissions']) || ((int)$data['level'] >= 1 && common::$chkMe >= (int)$data['level'])) {
                            self::$tmpdir = cookie::get('tmpdir');
                        }
                    } else if((int)$data['level'] >= 1 && common::$chkMe >= (int)$data['level']) {
                        self::$tmpdir = cookie::get('tmpdir');
                    } else if(!(int)$data['level']) {
                        self::$tmpdir = cookie::get('tmpdir');
                    }
                }
            } else {
                if(file_exists(basePath . "/inc/_templates_/" .stringParser::decode(settings::get('tmpdir'))."/template.xml")) {
                    self::$tmpdir = stringParser::decode(settings::get('tmpdir'));
                } else {
                    foreach ($files as $id => $dir) {
                        if(file_exists(basePath . "/inc/_templates_/" .$dir."/template.xml")) {
                            self::$tmpdir = $files[$id];
                            break;
                        }
                    }
                }
            }

            if(self::$is_addons) {
                $_SESSION['tmpdir'] = 'addons';
            } else {
                $_SESSION['tmpdir'] = self::$tmpdir;
            }
        } else {
            if(array_key_exists('tmpdir',$_SESSION) && !empty($_SESSION['tmpdir']))
            {
                if (file_exists(basePath . "/inc/_templates_/" .$_SESSION['tmpdir'])) {
                    self::$tmpdir = $_SESSION['tmpdir'];
                } else {
                    if(file_exists(basePath . "/inc/_templates_/" .stringParser::decode(settings::get('tmpdir'))."/template.xml")) {
                        self::$tmpdir = stringParser::decode(settings::get('tmpdir'));
                    } else {
                        foreach ($files as $id => $dir) {
                            if(file_exists(basePath . "/inc/_templates_/" .$dir."/template.xml")) {
                                self::$tmpdir = $files[$id];
                                break;
                            }
                        }
                    }
                }

                if(self::$is_addons) {
                    self::$tmpdir = 'addons';
                }
            }
            else
            {
                if (file_exists(basePath . "/inc/_templates_/" . self::$sdir)) {
                    self::$tmpdir = self::$sdir;
                } else {
                    if(file_exists(basePath . "/inc/_templates_/" .stringParser::decode(settings::get('tmpdir'))."/template.xml")) {
                        self::$tmpdir = stringParser::decode(settings::get('tmpdir'));
                    } else {
                        foreach ($files as $id => $dir) {
                            if(file_exists(basePath . "/inc/_templates_/" .$dir."/template.xml")) {
                                self::$tmpdir = $files[$id];
                                break;
                            }
                        }
                    }
                }

                if(self::$is_addons) {
                    self::$tmpdir = 'addons';
                }
            }
        }

        self::$designpath = '../inc/_templates_/'.self::$tmpdir; //Set designpath
    }

    /**
     * Prüft ob die DSGVO akzeptiert wurde
     * @return bool
     */
    public static function HasDSGVO(): bool
    {
        if(array_key_exists('DSGVO',$_SESSION) && $_SESSION['DSGVO'])
            return true;

        return false;
    }

    public static function isSecure(): bool
    {
        $isSecure = false;
        if (self::GetServerVars('HTTPS') && self::GetServerVars('HTTPS') == 'on') {
            $isSecure = true;
        } elseif ((self::GetServerVars('HTTP_X_FORWARDED_PROTO') && self::GetServerVars('HTTP_X_FORWARDED_PROTO') == 'https') ||
            (self::GetServerVars('HTTP_X_FORWARDED_SSL') && self::GetServerVars('HTTP_X_FORWARDED_SSL') == 'on')) {
            $isSecure = true;
        }

        return $isSecure;
    }

    /**
     * @return string
     */
    public static function getTplImgDir() {
        return "inc/_templates_/".self::$tmpdir.'/images';
    }

    /**
     * @param bool $new_instance (optional) Erstellt eine neue unabhängige Smarty-Instanz.
     * @return null|Smarty
     * @access      public
     * @static
     */
    public static function getSmarty(bool $new_instance = false): ?Smarty
    {
        if($new_instance) {
            $smarty = new Smarty;
            if(method_exists('Smarty_CacheResource_FastCache','read') && config::$use_system_cache) {
                $smarty->registerCacheResource('fastcache', new Smarty_CacheResource_FastCache());
                $smarty->caching_type = 'fastcache';
            }

            $smarty->force_compile = config::$smarty_force_compile;
            $smarty->debugging = config::$smarty_debugging;
            $smarty->caching = config::$smarty_caching;
            $smarty->cache_lifetime = config::$smarty_cache_lifetime;
            $smarty->allow_php_templates = config::$smarty_allow_php_templates;

            $smarty->setTemplateDir(basePath.'/inc/_templates_')
                ->setCompileDir(basePath.'/inc/_templates_c_')
                ->setCacheDir(basePath.'/inc/_cache_')
                ->setPluginsDir([basePath.'/inc/menu-functions',
                    basePath.'/vendor/smarty/libs/plugins'])
                ->setConfigDir(basePath.'/inc/configs');

            if($folders = self::get_files(basePath.'/inc/_templates_',true)) {
                foreach($folders as $folder) {
                    $smarty->addTemplateDir(basePath.'/inc/_templates_/'.strtolower($folder),strtolower($folder));
                } unset($folders,$folder);
            }

            return $smarty;
        }

        return self::$smarty;
    }

    /**
     * @name        getSmartyCacheHash()
     * @access      public
     * @static
     * @param string $tag
     * @return string
     */
    public static function getSmartyCacheHash(string $tag) {
        return md5($tag.'_'.$_SESSION['language']);
    }

    /**
     * Insert User in Network or Memory Cache
     * @param int $uid
     * @param bool $refresh (optional)
     * @return array
     */
    public static function getUserIndex(int $uid=0, bool $refresh = false) {
        if(empty($uid) || !$uid) return [];
        $cache_hash = md5('user_'.$uid);
        if(!self::$cache->AutoMemExists($cache_hash) || !config::$use_system_cache || $refresh) {
            $get = self::$sql['default']->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ?;", [$uid]);
            if(self::$sql['default']->rowCount()) {
                if (config::$use_system_cache) {
                    self::$cache->AutoMemSet($cache_hash, $get, cache::TIME_USERINDEX);
                }

                return $get;
            }

            return [];
        } else {
            return self::$cache->AutoMemGet($cache_hash);
        }
    }

    /**
     * Nickausgabe mit Profillink oder Emaillink (reg/nicht reg)
     * @name        autor()
     * @access      public
     * @static
     * @param int $uid
     * @param string $class (optional)
     * @param string $nick (optional)
     * @param string $email (optional)
     * @param int $cut (optional)
     * @param string $add (optional)
     * @return string
     * @throws SmartyException
     */
    public static function autor(int $uid=0,string $class="",string $nick="",string $email="",int $cut=100,string $add=""): string {
        $uid = (!$uid ? self::$userid : $uid);
        if(!$uid) return '* No UserID! *';
        $user = self::getUserIndex($uid); //Load user from Mem/Netcache
        if(count($user) > 2) {
            $country = self::flag(stringParser::decode($user['country']));
            $nickname = (!empty($cut) ? self::cut(stringParser::decode($user['nick']), $cut, true, false) : stringParser::decode($user['nick']));
        } else {
            $nickname = (!empty($cut) ? self::cut(stringParser::decode($nick), $cut, true, false) : stringParser::decode($nick));
            return self::CryptMailto($email,_user_link_noreg, ["nick" => $nickname, "class" => $class]);
        }

        $smarty = self::getSmarty(true);
        $smarty->caching = true;
        $smarty->cache_lifetime = 30;
        $smarty->assign('id',$uid);
        $smarty->assign('country',$country);
        $smarty->assign('class',$class);
        $smarty->assign('get',$add);
        $smarty->assign('nick',$nickname);
        $user = $smarty->fetch('file:['.common::$tmpdir.']user/user_link.tpl',
            self::getSmartyCacheHash('user_link_'.$uid.$class.$cut.$add));
        $smarty->clearAllAssign();
        return $user;
    }

    /**
     * Nickausgabe mit Profillink (reg + position farbe)
     * @name        autorcolerd()
     * @access      public
     * @static
     * @param int $uid
     * @param string $class (optional)
     * @param int $cut (optional)
     * @param bool $refresh (optional)
     * @return mixed|string
     * @throws SmartyException
     */
    public static function autorcolerd(int $uid=0,string $class="",int $cut = 100, bool $refresh = false) {
        $uid = (!$uid ? self::$userid : $uid);
        if(!$uid) return '* No UserID! *';
        $user = self::getUserIndex($uid,$refresh); //Load user from Mem/Netcache
        if(count($user) > 2) {
            $get = self::$sql['default']->fetch("SELECT `id`,`color` FROM `{prefix_positions}` WHERE `id` = ?;", [$user['position']]);
            if(!$user['position'] || !self::$sql['default']->rowCount()) {
                return self::autor($uid,$class,'','',$cut);
            }

            $smarty = self::getSmarty(true);
            $smarty->caching = true;
            $smarty->cache_lifetime = 30;
            $smarty->assign('id',$uid);
            $smarty->assign('country',self::flag(stringParser::decode($user['country'])));
            $smarty->assign('class',$class);
            $smarty->assign('color',stringParser::decode($get['color']));
            $smarty->assign('nick',(!empty($cut) ? self::cut(stringParser::decode($user['nick']), $cut, true, false) : stringParser::decode($user['nick'])));
            $autor = $smarty->fetch('file:['.common::$tmpdir.']user/user_link_colerd.tpl',
                self::getSmartyCacheHash('user_link_colerd_'.$uid.$class.$cut));
            $smarty->clearAllAssign();
            return $autor;
        }

        return '';
    }

    /**
     * @name cleanautor()
     * @access public
     * @static
     * @param int $uid
     * @param string $class (optional)
     * @param string $nick (optional)
     * @param string $email (optional)
     * @return mixed|string
     * @throws SmartyException
     */
    public static function cleanautor(int $uid=0, $class="", $nick="", $email="") {
        $uid = (!$uid ? self::$userid : $uid);
        if(!$uid) return '* No UserID! *';
        $user = self::getUserIndex($uid); //Load user from Mem/Netcache
        if(count($user) > 2) {
            $smarty = self::getSmarty(true);
            $smarty->caching = true;
            $smarty->cache_lifetime = 30;
            $smarty->assign('id',$uid);
            $smarty->assign('country',self::flag(stringParser::decode($user['country'])));
            $smarty->assign('class',$class);
            $smarty->assign('nick',stringParser::decode($user['nick']));
            $user = $smarty->fetch('file:['.common::$tmpdir.']user/user_link_preview.tpl',
                self::getSmartyCacheHash('user_link_preview_'.$uid.$class));
            $smarty->clearAllAssign();
            return $user;
        }

        return self::CryptMailto($email, _user_link_noreg, ["nick" => stringParser::decode($nick), "class" => $class]);
    }

    /**
     * @param int $uid
     * @param bool $refresh (optional)
     * @return string
     * @access public
     * @static
     */
    public static function rawautor(int $uid=0, bool $refresh = false) {
        $uid = (!$uid ? self::$userid : $uid);
        if(!$uid) return '* No UserID! *';
        $user = self::getUserIndex($uid,$refresh); //Load user from Mem/Netcache
        if(count($user) > 2) {
            return self::rawflag(stringParser::decode($user['country']))." ".
                self::jsconvert(stringParser::decode($user['nick']));
        }

        return self::rawflag(null);
    }

    /**
     * Nickausgabe ohne Profillink oder Emaillink fr das ForenAbo
     * @param int $uid
     * @param bool $refresh (optional)
     * @return mixed|string
     * @access      public
     * @static
     */
    public static function fabo_autor(int $uid, bool $refresh = false) {
        $uid = (!$uid ? self::$userid : $uid);
        if(!$uid) return '* No UserID! *';
        $user = self::getUserIndex($uid,$refresh); //Load user from Mem/Netcache
        if(count($user) > 2) {
            return stringParser::decode($user['nick']);
        }

        return '';
    }

    /**
     * Sonderzeichen für JS maskieren
     * @param string $txt (optional)
     * @return mixed
     */
    public static function jsconvert(string $txt) {
        return str_replace(["'","&#039;","\"","\r","\n"], ["\'","\'","&quot;","",""],$txt);
    }

    /**
     * interner Forencheck
     * @param int $id
     * @return bool
     */
    public static function forum_intern(int $id=0) {
        if(!self::$chkMe) {
            $fget = self::$sql['default']->fetch("SELECT s1.`intern`,s2.`id` FROM `{prefix_forum_kats}` AS `s1` LEFT JOIN `{prefix_forum_sub_kats}` AS `s2` ON s2.`sid` = s1.`id` WHERE s2.`id` = ?;",
                [(int)($id)]);
            return (!$fget['intern']);
        } else if(self::$chkMe == 4) {
            return true;
        } else {
            $team = self::$sql['default']->rows("SELECT s1.`id` FROM `{prefix_forum_access}` AS `s1` LEFT JOIN `{prefix_user_posis}` AS `s2` ON s1.`pos` = s2.`posi` WHERE s2.`user` = ? AND s2.`posi` != 0 AND s1.`forum` = ?;",
                [(int)(self::$userid),(int)($id)]);
            $user = self::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_access}` WHERE `user` = ? AND `forum` = ?;",
                [(int)(self::$userid),(int)($id)]);
            return ($user || $team);
        }
    }

    /**
     * Einzelne Userdaten ermitteln
     * @param string $what
     * @param int $uid
     * @param bool $refresh (optional)
     * @return string
     */
    public static function data(string $what='id', int $uid=0, bool $refresh = false) {
        $uid = (!$uid ? self::$userid : $uid);
        if(!$uid) return '* No UserID! *';
        $user = self::getUserIndex($uid,$refresh); //Load user from Mem/Netcache
        if(count($user) > 2 && array_key_exists($what,$user)) {
            return (string)$user[$what];
        }

        return '';
    }

    /**
     *
     * @param string $address
     * @param bool $ip6 (optional)
     * @return bool|string
     */
    public static function DNSToIp(string $address='', bool $ip6 = false) {
        if(!filter_var(gethostbyname($address), FILTER_VALIDATE_IP)) {
            return false;
        }

        if($ip6) {
            $dns = dns_get_record($address, DNS_AAAA);
            foreach ($dns as $record) {
                if ($record["type"] == "AAAA") {
                    $ip6[] = $record["ipv6"];
                }
            }

            if (count($ip6) < 1) {
                if (!($result = gethostbyname($address))) {
                    return false;
                }
            } else {
                $result = $ip6[0];
            }
        } else {
            if (!($result = gethostbyname($address))) {
                return false;
            }
        }

        if ($result !== $address) {
            return $result;
        }

        return false;
    }

    /**
     * Errormmeldung ausgeben
     * @param string $error
     * @param int $back (optional)
     * @return mixed|string
     * @throws SmartyException
     */
    public static function error(string $error,int $back=3) {
        $smarty = self::getSmarty(true);
        $smarty->caching = false;
        $smarty->assign('error',$error);
        $smarty->assign('back',$back);
        $error = $smarty->fetch('file:['.common::$tmpdir.']errors/error.tpl');
        $smarty->clearAllAssign();
        return $error;
    }

    /**
     * Email wird auf korrekten Syntax
     * @param string $email
     * @param string $field (optional)
     * @return bool
     * @throws Exception
     */
    public static function check_email(string $email, string $field = 'email') {
        $rules = [$field => 'required|valid_email'];
        $filters = [$field => 'trim|sanitize_email'];
        return self::$gump->validate(self::$gump->filter([$field=>$email], $filters), $rules) === TRUE ? true : false;
    }

    /**
     * Bilder verkleinern
     * @param string $img
     * @param int $group
     * @param int $picID
     * @return string
     */
    private static $IMG_AUTO_INT = 0;
    public static function img_size(string $img, int $group = 1, int $picID = 0) {
        if(!$picID)
            self::$IMG_AUTO_INT++;

        return "<a href=\"../".$img."\" rel=\"lightbox[".$group."_".($picID >= 1 ? $picID : self::$IMG_AUTO_INT)."]\"><img src=\"../thumbgen.php?img=".$img."\" alt=\"\" /></a>";
    }

    /**
     * Bilder verkleinern
     * @param string $img
     * @param int $group
     * @param int $picID
     * @return string
     */
    public static function img_size_static(string $img, int $group = 1, int $picID = 0) {
        if(!$picID)
            self::$IMG_AUTO_INT++;

        return "<a href=\"../".$img."\" rel=\"lightbox[".$group."_".($picID >= 1 ? $picID : self::$IMG_AUTO_INT)."]\"><img src=\"https://static.dzcp.de/thumbgen.php?img=".$img."\" alt=\"\" /></a>";
    }

    /**
     *  JS Basierend - Blaetterfunktion
     * [Previous][1][Next]
     * [Previous][1][2][3][4][Next]
     * [Previous][1][2][3][4][...][20][Next]
     * [Previous][1][...][16][17][18][19][20][Next]
     * [Previous][1][...][13][14][15][16][...][20][Next]
     * @param $entrys
     * @param $perpage
     * @param string $urlpart
     * @return string
     */
    public static function nav(int $entrys,int $perpage,string $urlpart=''): string {
        if(!$entrys || !$perpage) {
            $entrys = 1;
            $perpage = 10;
        }

        $total_pages  = ceil($entrys / $perpage);
        $urlpart_extended = empty($urlpart) ? '?' : '&';
        $hash = md5($entrys.$perpage.$urlpart_extended);

        if(!show_empty_paginator && $total_pages == 1) {
            return '';
        }

        $url = html_entity_decode($urlpart).$urlpart_extended."page=";
        $js = javascript::getArray('pagination');
        $js[$hash] = [
            'currentPage' => self::$page,
            'url' => $url,
            'total_pages'=> $total_pages,
            'visible_pages'=> 10,
            'previous' => _paginator_previous,
            'next' => _paginator_next,
            'first' => _paginator_first,
            'last' => _paginator_last

        ];
        javascript::setArray('pagination',$js);
        return '<nav aria-label="Page navigation"><ul class="pagination justify-content-center" id="'.$hash.'"></ul></nav>';
    }

    /**
     * Liste der Laender ausgeben
     * @param string $selected_country
     * @return string
     */
    public static function show_countrys(string $selected_country="") {
        $countries = self::$country->loadCountries(); $options = '';
        foreach ($countries as $country) {
            $selected = ($selected_country == strtolower($country->getAlpha2Code()) ? ' selected="selected"' :
                (empty($selected_country) && strtolower($country->getAlpha2Code()) == 'de' ? ' selected="selected"' : ''));
            $options .= '<option'.$selected.' value="'.strtolower($country->getAlpha2Code()).'">'.$country->getShortName().'</option>';
        }

        return '<select id="land" name="land" class="selectpicker">'.$options.'</select>';
    }

    /**
     * Funktion um bei DB-Eintraegen URLs einem http:// zuzuweisen
     * @param string $hp
     * @return string
     */
    public static function links(string $hp): string
    {
        if(!empty($hp)) {
            //SSL
            $count = 0;
            $hp = str_replace("https://", "", $hp, $count);
            if ($count >= 1) {
                return 'https://' . $hp;
            }

            $count = 0;
            $hp = str_replace("http://", "", $hp, $count);
            if ($count >= 1) {
                return 'http://' . $hp;
            }

            return 'http://' . $hp;
        }

        return $hp;
    }

    /**
     * Funktion um Passwoerter generieren zu lassen
     * @param int $passwordLength (optional)
     * @param bool $specialcars (optional)
     * @return string
     */
    public static function mkpwd(int $passwordLength=8,bool $specialcars=true): string
    {
        $componentsCount = count(config::$passwordComponents);

        if(!$specialcars && $componentsCount == 4) {
            unset(config::$passwordComponents[3]);
            $componentsCount = count(config::$passwordComponents);
        }

        shuffle(config::$passwordComponents); $password = '';
        for ($pos = 0; $pos < $passwordLength; $pos++) {
            $componentIndex = ($pos % $componentsCount);
            $componentLength = strlen(config::$passwordComponents[$componentIndex]);
            $random = rand(0, $componentLength-1);
            $password .= config::$passwordComponents[$componentIndex]{ $random };
        }

        unset($random,$componentLength,$componentIndex);
        return $password;
    }

    /**
     * Einzelne Userstatistiken ermitteln
     * @param string $what
     * @param int $uid
     * @param bool $refresh (optional)
     * @return int
     */
    public static function userstats(string $what='id', int $uid=0, bool $refresh = false) {
        if (!$uid) { $uid = self::$userid; }
        $cache_hash = md5('userstats_'.$uid);
        if(!self::$cache->AutoMemExists($cache_hash) || !config::$use_system_cache || $refresh) {
            $stats = self::$sql['default']->fetch("SELECT * FROM `{prefix_user_stats}` WHERE `user` = ?;", [$uid]);
            if(!common::$sql['default']->rowCount()) {
                self::$sql['default']->insert("INSERT INTO `{prefix_user_stats}` SET `user` = ?;", [$uid]);
                self::$cache->AutoMemSet($cache_hash, self::$sql['default']->fetch("SELECT * FROM `{prefix_user_stats}` WHERE `user` = ?;",
                    [$uid]),cache::TIME_USERSTATS);
                return 0;
            } else {
                self::$cache->AutoMemSet($cache_hash, $stats,cache::TIME_USERSTATS);
            }
        }

        return (int)self::$cache->AutoMemGet($cache_hash)[$what];
    }

    /**
     * Einzelne Userstatistiken erhöhen
     * @param string $what (optional)
     * @param int $uid (optional)
     * @return int
     */
    public static function userstats_increase(string $what='profilhits',int $uid=0) {
        if (!$uid) { $uid = self::$userid; }
        $cache_hash = md5('userstats_'.$uid);
        if(self::$cache->AutoMemExists($cache_hash) && config::$use_system_cache) {
            //Update Database
            self::$sql['default']->update("UPDATE `{prefix_user_stats}` SET `".$what."` = (".$what."+1) WHERE `user` = ?;", [$uid]);

            //Update Cache
            $data = self::$cache->AutoMemGet($cache_hash);
            $data[$what] = ($data[$what]+1);
            self::$cache->AutoMemSet($cache_hash, $data,cache::TIME_USERSTATS);
        } else {
            self::$sql['default']->fetch("SELECT * FROM `{prefix_user_stats}` WHERE `user` = ?;", [$uid]);
            if(!common::$sql['default']->rowCount()) {
                //Update Database
                self::$sql['default']->insert("INSERT INTO `{prefix_user_stats}` SET `user` = ?;", [$uid]);
                self::$sql['default']->update("UPDATE `{prefix_user_stats}` SET `".$what."` = (".$what."+1) WHERE `user` = ?;", [$uid]);

                //Update Cache
                if(config::$use_system_cache) {
                    self::$cache->AutoMemSet($cache_hash, self::$sql['default']->fetch("SELECT * FROM `{prefix_user_stats}` WHERE `user` = ?;",
                        [$uid]),cache::TIME_USERSTATS);
                }
                return 0;
            } else {
                //Update Database
                self::$sql['default']->update("UPDATE `{prefix_user_stats}` SET `".$what."` = (".$what."+1) WHERE `user` = ?;", [$uid]);

                //Update Cache
                if(config::$use_system_cache) {
                    self::$cache->AutoMemSet($cache_hash, self::$sql['default']->fetch("SELECT * FROM `{prefix_user_stats}` WHERE `user` = ?;",
                        [$uid]), cache::TIME_USERSTATS);
                }
            }
        }

        return 0;
    }

    /**
     * Funktion zum versenden von Emails
     * @param string $mailto
     * @param string $subject
     * @param string $content
     * @return bool
     * @throws phpmailerException
     */
    public static function sendMail(string $mailto,string $subject,string $content) {
            $mail = new PHPMailer;
            $mail->CharSet = 'utf-8';
            switch (settings::get('mail_extension')) {
                case 'smtp':
                    $mail->isSMTP();
                    $mail->Host = stringParser::decode(settings::get('smtp_hostname'));
                    $mail->Port = (int)(settings::get('smtp_port'));
                    switch (settings::get('smtp_tls_ssl')) {
                        case 1:
                            if($mail->Port == 25)
                                $mail->Port = 465;

                            $mail->SMTPSecure = 'tls';
                            break;
                        case 2:
                            if($mail->Port == 25)
                                $mail->Port = 465;

                            $mail->SMTPSecure = 'ssl';
                            break;
                        default:
                            $mail->SMTPSecure = '';
                            break;
                    }
                    $mail->SMTPAuth = (empty(settings::get('smtp_username')) && empty(settings::get('smtp_password')) ? false : true);
                    $mail->Username = stringParser::decode(settings::get('smtp_username'));
                    $mail->Password = session::decode(settings::get('smtp_password'));
                    break;
                case 'sendmail':
                    $mail->isSendmail();
                    $mail->Sendmail = stringParser::decode(settings::get('sendmail_path'));
                    break;
            }

            $mail->From = ($mailfrom =stringParser::decode(settings::get('mailfrom')));
            $mail->FromName = $mailfrom;
            $mail->AddAddress(preg_replace('/(\\n+|\\r+|%0A|%0D)/i', '',$mailto));
            $mail->Subject = $subject;
            $mail->msgHTML($content);
            $mail->setLanguage(($_SESSION['language']=='deutsch')?'de':'en', basePath.'/vendor/phpmailer/phpmailer/language/');
            return $mail->Send();
    }

    /**
     * Userpic ausgeben
     * @param int $uid
     * @param int $width (optional)
     * @param int $height (optional)
     * @return mixed|string
     * @throws SmartyException
     */
    public static function userpic(int $uid,int $width=170,int $height=210) {
        $smarty = self::getSmarty(true);
        foreach (common::SUPPORTED_PICTURE as $endung) {
            if (file_exists(basePath . "/inc/images/uploads/userpics/" . $uid . "." . $endung)) {
                $smarty->caching = true;
                $smarty->cache_lifetime = 300;
                $smarty->force_compile = false;
                $smarty->assign('id', $uid);
                $smarty->assign('endung', $endung);
                $smarty->assign('width', $width);
                $smarty->assign('height', $height);
                $pic = $smarty->fetch('file:[' . common::$tmpdir . ']page/userpic_link.tpl',
                    self::getSmartyCacheHash('userpic_'.$uid.$width.$height));
                $smarty->clearAllAssign();
                break;
            } else {
                $smarty->caching = false;
                $smarty->assign('width', $width);
                $smarty->assign('height', $height);
                $pic = $smarty->fetch('file:[' . common::$tmpdir . ']page/no_userpic.tpl');
                $smarty->clearAllAssign();
            }
        }

        return $pic;
    }

    /**
     * Useravatar ausgeben
     * @param int $uid
     * @param int $width (optional)
     * @param int $height (optional)
     * @return mixed|string
     * @throws SmartyException
     */
    public static function useravatar(int $uid=0, int $width=100,int $height=100) {
        $smarty = self::getSmarty(true);
        $uid = ($uid == 0 ? self::$userid : $uid);
        foreach(common::SUPPORTED_PICTURE as $endung) {
            if (file_exists(basePath . "/inc/images/uploads/useravatare/" . $uid . "." . $endung)) {
                $smarty->caching = true;
                $smarty->cache_lifetime = 300;
                $smarty->assign('id',$uid);
                $smarty->assign('endung',$endung);
                $smarty->assign('width',$width);
                $smarty->assign('height',$height);
                $pic = $smarty->fetch('file:['.common::$tmpdir.']page/userava_link.tpl',
                    self::getSmartyCacheHash('useravatar_'.$uid.$width.$height));
                $smarty->clearAllAssign();
                break;
            } else {
                $smarty->caching = false;
                $smarty->assign('width',$width);
                $smarty->assign('height',$height);
                $pic = $smarty->fetch('file:['.common::$tmpdir.']page/no_userava.tpl');
                $smarty->clearAllAssign();
            }
        }

        return $pic;
    }

    /**
     * @param string $day
     * @param string $month
     * @param string $year
     * @param string $class
     * @return string
     * @throws SmartyException
     */
    public static function dropdown_date(string $day, string $month, string $year, string $class = ''): string {
        $class = !empty($class) ? ' '.$class : '';
        $smarty = self::getSmarty(true);
        $smarty->caching = false;
        $smarty->assign('day',$day);
        $smarty->assign('month',$month);
        $smarty->assign('year',$year);
        $smarty->assign('class',$class);
        $dropdown_date = $smarty->fetch('file:['.common::$tmpdir.']page/dropdown_date.tpl');
        $smarty->clearAllAssign();
        return $dropdown_date;
    }

    /**
     * @param string $hour
     * @param string $minute
     * @param string $class
     * @return string
     * @throws SmartyException
     */
    public static function dropdown_time(string $hour, string $minute, string $class = ''): string {
        $class = !empty($class) ? ' '.$class : '';
        $smarty = self::getSmarty(true);
        $smarty->caching = false;
        $smarty->assign('hour',$hour);
        $smarty->assign('minute',$minute);
        $smarty->assign('class',$class);
        $dropdown_time = $smarty->fetch('file:['.common::$tmpdir.']page/dropdown_time.tpl');
        $smarty->clearAllAssign();
        return $dropdown_time;
    }

    /**
     * Umfrageantworten selektieren
     * @param $what
     * @param $vid
     * @return string
     */
    public static function voteanswer(string $what, int $vid) {
        $cache_hash = md5($what.$vid);
        if(!self::$cache->MemExists($cache_hash)) {
            $data = self::$sql['default']->select("SELECT `what`,`sel` FROM `{prefix_vote_results}` WHERE `vid` = ?;", [(int)($vid)]);
            foreach ($data as $value) {
                if (strtolower($value['what']) == strtolower($what)) {
                    self::$cache->MemSet($cache_hash,$value['sel'],cache::TIME_VOTE_ANSWER);
                    return $value['sel'];
                }
            }
        } else {
            return self::$cache->MemGet($cache_hash);
        }

        return '';
    }

    /**
     * Checkt versch. Dinge anhand der Hostmaske eines Users
     * @param $what
     * @param string $time
     * @return bool
     */
    public static function ipcheck(string $what,int $time = 0) {
        $get = self::$sql['default']->fetch("SELECT `time`,`what` FROM `{prefix_ip_action}` WHERE `what` = ? AND ".
            "(`ipv4` = ? OR ( `ipv6` != ? AND `ipv6` = ?)) ".
            "ORDER BY `time` DESC;",
            [$what,self::$userip['v4'],self::IPV6_NULL_ADDR,self::$userip['v6']]);
        if(self::$sql['default']->rowCount()) {
            if (preg_match("#vid#", $get['what'])) {
                return true;
            } else {
                if($get['time'] + $time < time()) {
                    self::$sql['default']->delete("DELETE FROM `{prefix_ip_action}` WHERE `what` = ? AND ".
                        "(`ipv4` = ? OR ( `ipv6` != ? AND `ipv6` = ?)) ".
                        "AND time+?<?;",
                        [$what,self::$userip['v4'],self::IPV6_NULL_ADDR,self::$userip['v6'],$time,time()]);
                }

                return ($get['time'] + $time > time() ? true : false);
            }
        }

        return false;
    }

    /**
     * Setzt bei einem Tag >10 eine 0 vorran (Kalender)
     * @param int $i
     * @return string
     */
    public static function cal(int $i) {
        if (preg_match("=10|20|30=Uis", $i) == FALSE) {
            $i = preg_replace("=0=", "", $i);
        }

        if ($i < 10) {
            $tag_nr = (string)"0" . $i;
        } else {
            $tag_nr = (string)$i;
        }

        return $tag_nr;
    }

    /**
     * Geburtstag errechnen
     * @param int $bday
     * @return string
     */
    public static function getAge(int $bday) {
        if (!empty($bday) && $bday) {
            $bday = date('d.m.Y', $bday);
            list($tiday, $iMonth, $iYear) = explode(".", $bday);
            $iCurrentDay = date('j');
            $iCurrentMonth = date('n');
            $iCurrentYear = date('Y');
            if (($iCurrentMonth > $iMonth) || (($iCurrentMonth == $iMonth) && ($iCurrentDay >= $tiday))) {
                return (string)$iCurrentYear - $iYear;
            } else {
                return (string)$iCurrentYear - ($iYear + 1);
            }
        }

        return '-';
    }

    public static function check_msg_emal() {
        if(!is_ajax && !is_thumbgen && !self::$CrawlerDetect->isCrawler() && !self::$sql['default']->rows("SELECT `id` FROM `{prefix_iptodns}` WHERE `sessid` = ? AND `bot` = 1;",
                [session_id()])) {
            $qry = self::$sql['default']->select("SELECT s1.`an`,s1.`page`,s1.`titel`,s1.`sendmail`,s1.`id` AS `mid`, "
                . "s2.`id`,s2.`nick`,s2.`email`,s2.`pnmail` FROM `{prefix_messages}` AS `s1` "
                . "LEFT JOIN `{prefix_users}` AS `s2` ON s2.`id` = s1.`an` WHERE `page` = 0 AND `sendmail` = 0;");
            if(self::$sql['default']->rowCount()) {
                $smarty = self::getSmarty(true);
                foreach($qry as $get) {
                    if($get['pnmail']) {
                        self::$sql['default']->update("UPDATE `{prefix_messages}` SET `sendmail` = 1 WHERE `id` = ?;", [$get['mid']]);

                        $smarty->caching = false;
                        $smarty->assign('domain',self::$httphost);
                        $subj = $smarty->fetch('string:'.stringParser::decode(settings::get('eml_pn_subj')));
                        $smarty->clearAllAssign();

                        $smarty->caching = false;
                        $smarty->assign('nick',stringParser::decode($get['nick']));
                        $smarty->assign('domain',self::$httphost);
                        $smarty->assign('titel',stringParser::decode($get['titel']));
                        $smarty->assign('clan',stringParser::decode(settings::get('clanname')));
                        $message = $smarty->fetch('string:'.BBCode::bbcode_email(settings::get('eml_pn')));
                        $smarty->clearAllAssign();

                        self::sendMail(stringParser::decode($get['email']), $subj, $message);
                    }
                }
            }
        }
    }

    /**
     * Gibt einen Edit button aus ( button_edit_single.tpl )
     * @param int $id
     * @param string $action
     * @param string $title
     * @return string
     * @throws SmartyException
     */
    public static function getButtonEditSingle(int $id=0,string $action='',string $title=_button_title_edit): string
    {
        $smarty = self::getSmarty(true); //Use Smarty
        $smarty->caching = true;
        $smarty->assign('id', $id);
        $smarty->assign('action', $action);
        $smarty->assign('title', $title);
        $edit = $smarty->fetch('file:[' . common::$tmpdir . ']page/buttons/button_edit_single.tpl',
            self::getSmartyCacheHash('button_'.$id.'_'.$action));
        $smarty->clearAllAssign();
        return $edit;
    }

    /**
     * Checkt ob ein Ereignis neu ist
     * @param int $datum
     * @param bool $output
     * @param int $datum2
     * @return bool|string
     */
    public static function check_new(int $datum = 0, bool $output=false, int $datum2 = 0) {
        if(self::$userid) {
            $lastvisit = self::userstats('lastvisit', self::$userid);
            if ($datum >= $lastvisit || $datum2 >= $lastvisit) {
                return (!$output ? true : $output);
            }
        }

        return (!$output ? false : '');
    }

    /**
     * DropDown Mens Date/Time
     * @param string $what
     * @param int $wert
     * @param int $age
     * @return string
     */
    public static function dropdown(string $what, int $wert, int $age = 0) {
        $return = '';
        switch(strtolower($what)) {
            case 'day':
                $return = ($age == 1 ? '<option value="" class="selectpicker">'._day.'</option>'."\n" : '');
                for($i=1; $i<32; $i++) {
                    if ($i == $wert) {
                        $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
                    } else {
                        $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
                    }
                }
            break;
            case 'month':
                $return = ($age == 1 ? '<option value="" class="selectpicker">'._month.'</option>'."\n" : '');
                for($i=1; $i<13; $i++) {
                    if ($i == $wert) {
                        $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
                    } else {
                        $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
                    }
                }
            break;
            case 'year':
                if($age == 1) {
                    $return ='<option value="" class="selectpicker">'._year.'</option>'."\n";
                    for($i=date("Y",time())-80; $i<date("Y",time())-10; $i++) {
                        if ($i == $wert) {
                            $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
                        } else {
                            $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
                        }
                    }
                } else {
                    for($i=date("Y",time())-3; $i<date("Y",time())+3; $i++) {
                        if ($i == $wert) {
                            $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
                        } else {
                            $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
                        }
                    }
                }
            break;
            case 'hour':
                for($i=0; $i<24; $i++) {
                    if ($i == $wert) {
                        $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
                    } else {
                        $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
                    }
                }
            break;
            case 'minute':
                for($i="00"; $i<60; $i++) {
                    if($i == 0 || $i == 15 || $i == 30 || $i == 45) {
                        if ($i == $wert) {
                            $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
                        } else {
                            $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
                        }
                    }
                }
            break;
        }

        unset($i);
        return $return;
    }

    /**
     * Truncates text / Funktion um Ausgaben zu kuerzen
     *
     * Cuts a string to the length of $length and replaces the last characters
     * with the ending if the text is longer than length.
     * ( CakePhp Export ) *https://cakephp.org/*
     *
     * @param string  $text String to truncate.
     * @param integer $length Length of returned string, including ellipsis.
     * @param bool    $dots Using dots on end of string.
     * @param bool    $html use as HTML txt.
     * @param string  $ending Ending to be appended to the trimmed string.
     * @param boolean $exact If false, $text will not be cut mid-word
     * @param boolean $considerHtml If true, HTML tags would be handled correctly
     * @return string Trimmed string.
     */
    public static function cut(string $text, int $length = 100, bool $dots = true,bool $html = true,string $ending = '',bool $exact = false,bool $considerHtml = true) {
        if($length === 0)
            return '';

        $ending = $dots || !empty($ending) ? (!empty($ending) ? $ending : '...') : '';

        if(!$html) {
            if(strlen($text) <= $length) {
                return $text;
            }

            return substr($text, 0, $length).$ending;
        }

        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }

            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags = [];
            $truncate = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                        // if tag is a closing tag
                    } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                        // if tag is an opening tag
                    } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }

                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length+$content_length> $length) {
                    // the number of characters which are left
                    $left = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1]+1-$entities_length <= $left) {
                                $left--;
                                $entities_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate .= $line_matchings[2];
                    $total_length += $content_length;
                }
                // if the maximum length is reached, get off the loop
                if($total_length>= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = substr($text, 0, $length - strlen($ending));
            }
        }

        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }

        // add the defined ending to the text
        $truncate .= $ending;
        if($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }
        return $truncate;
    }

    /**
     * Ausgabe der Position des einzelnen Members
     * @param int $tid
     * @param int $squad (optional)
     * @param bool $profil (optional)
     * @return string
     */
    public static function getrank(int $tid=0, int $squad=0, bool $profil=false): string
    {
        $tid = (!$tid ? self::$userid : $tid);
        if(!$tid) return '* No UserID! *';
        if($squad) {
            if ($profil) {
                $qry = self::$sql['default']->select("SELECT s1.`posi`,s2.`name` FROM `{prefix_user_posis}` AS `s1` LEFT JOIN `{prefix_groups}` AS `s2` ON s1.`group` = s2.`id` "
                    . "WHERE s1.`user` = ? AND s1.`group` = ? AND s1.`posi` != 0;",[(int)($tid),(int)($squad)]);
            } else {
                $qry = self::$sql['default']->select("SELECT `posi` FROM `{prefix_user_posis}` WHERE `user` = ? AND `group` = ? AND `posi` != 0;", [(int)($tid),(int)($squad)]);
            }

            if(self::$sql['default']->rowCount()) {
                foreach($qry as $get) {
                    $position = self::$sql['default']->fetch("SELECT `position` FROM `{prefix_positions}` WHERE `id` = ?;", [(int)($get['posi'])],'position');
                    $squadname = (!empty($get['name']) ? '<b>' . $get['name'] . ':</b> ' : '');
                    return ($squadname.$position);
                }
            } else {
                $get = self::$sql['default']->fetch("SELECT `level`,`banned` FROM `{prefix_users}` WHERE `id` = ?;", [(int)($tid)]);
                if (!$get['level'] && !$get['banned']) {
                    return _status_unregged;
                } elseif ($get['level'] == 1) {
                    return _status_user;
                } elseif ($get['level'] == 2) {
                    return _status_trial;
                } elseif ($get['level'] == 3) {
                    return _status_member;
                } elseif ($get['level'] == 4) {
                    return _status_admin;
                } elseif (!$get['level'] && $get['banned']) {
                    return _status_banned;
                } else {
                    return _gast;
                }
            }
        } else {
            $get = self::$sql['default']->fetch("SELECT s1.*,s2.`position` FROM `{prefix_user_posis}` AS `s1` LEFT JOIN `{prefix_positions}` AS `s2` "
                . "ON s1.`posi` = s2.`id` WHERE s1.`user` = ? AND s1.`posi` != 0 ORDER BY s2.pid ASC;", [(int)($tid)]);
            if(self::$sql['default']->rowCount()) {
                return $get['position'];
            } else {
                $get = self::$sql['default']->fetch("SELECT `level`,`banned` FROM `{prefix_users}` WHERE `id` = ?;", [(int)($tid)]);
                if (!$get['level'] && !$get['banned']) {
                    return _status_unregged;
                } elseif ($get['level'] == 1) {
                    return _status_user;
                } elseif ($get['level'] == 2) {
                    return _status_trial;
                } elseif ($get['level'] == 3) {
                    return _status_member;
                } elseif ($get['level'] == 4) {
                    return _status_admin;
                } elseif (!$get['level'] && $get['banned']) {
                    return _status_banned;
                } else {
                    return _gast;
                }
            }
        }

        return '';
    }

    /**
     * Gibt Informationen uber Server und Ausfuhrungsumgebung zuruck
     * @param string $var
     * @return string
     */
    public static function GetServerVars(string $var) {
        if (array_key_exists($var, $_SERVER) && !empty($_SERVER[$var])) {
            return utf8_encode($_SERVER[$var]);
        } else if (array_key_exists($var, $_ENV) && !empty($_ENV[$var])) {
            return utf8_encode($_ENV[$var]);
        }

        if($var=='HTTP_REFERER') { //Fix for empty HTTP_REFERER
            return self::GetServerVars('REQUEST_SCHEME').'://'.self::GetServerVars('HTTP_HOST').
            self::GetServerVars('DOCUMENT_URI');
        }

        return false;
    }

    /**
     * Funktion um Dateien aus einem Verzeichnis auszulesen
     * @name        get_files()
     * @access      public
     * @static
     * @param bool $only_dir (optional)
     * @param bool $only_files (optional)
     * @param array $file_ext (optional)
     * @param bool $refresh (optional)
     * @param bool $preg_match (optional)
     * @param array $blacklist (optional)
     * @param bool $blacklist_word (optional)
     * @return array|bool
     */
    public static function get_files(string $dir=null, bool $only_dir=false, bool $only_files=false, array $file_ext= [], bool $refresh = false, bool $preg_match=false, array $blacklist= [], bool $blacklist_word=false) {
        /* CACHE */
        $ext_cache = '';
        foreach ($file_ext as $ext) {
            $ext_cache .= $ext;
        }

        $dir = self::FixPath($dir);
        $cache_hash = md5($dir.$only_dir.$only_files.$ext_cache.$preg_match.$blacklist_word);
        unset($ext_cache,$ext);
        /* CACHE */

        $files = [];
        if (!file_exists($dir) && !is_dir($dir))
            return $files;

        if(!self::$cache->MemExists($cache_hash) || !config::$use_system_cache || $refresh) {
            if ($handle = @opendir($dir)) {
                if ($only_dir) {
                    while (false !== ($file = readdir($handle))) {
                        if ($file != '.' && $file != '..' && !is_file($dir . '/' . $file)) {
                            if (!count($blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && ($preg_match ? preg_match($preg_match, $file) : true))
                                $files[] = $file;
                            else {
                                if (!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && ($preg_match ? preg_match($preg_match, $file) : true))
                                    $files[] = $file;
                            }
                        }
                    } //while end
                } else if ($only_files) {
                    while (false !== ($file = readdir($handle))) {
                        if ($file != '.' && $file != '..' && is_file($dir . '/' . $file)) {
                            if (!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && !count($file_ext) && ($preg_match ? preg_match($preg_match, $file) : true))
                                $files[] = $file;
                            else {
                                ## Extension Filter ##
                                $exp_string = array_reverse(explode(".", $file));
                                if (!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && in_array(strtolower($exp_string[0]), $file_ext) && ($preg_match ? preg_match($preg_match, $file) : true))
                                    $files[] = $file;
                            }
                        }
                    } //while end
                } else {
                    while (false !== ($file = readdir($handle))) {
                        if ($file != '.' && $file != '..' && is_file($dir . '/' . $file)) {
                            if (!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && !count($file_ext) && ($preg_match ? preg_match($preg_match, $file) : true))
                                $files[] = $file;
                            else {
                                ## Extension Filter ##
                                $exp_string = array_reverse(explode(".", $file));
                                if (!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && in_array(strtolower($exp_string[0]), $file_ext) && ($preg_match ? preg_match($preg_match, $file) : true))
                                    $files[] = $file;
                            }
                        } else {
                            if (!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && $file != '.' && $file != '..' && ($preg_match ? preg_match($preg_match, $file) : true))
                                $files[] = $file;
                        }
                    } //while end
                }

                if (is_resource($handle))
                    closedir($handle);

                if (!count($files))
                    return false;

                if(config::$use_system_cache) {
                    self::$cache->MemSet($cache_hash, $files, 10);
                }

                return $files;
            } else
                return false;
        } else {
            return self::$cache->MemGet($cache_hash);
        }
    }

    /**
     * Ersetzt backslashs gegen forward slashs
     * @param string $path
     * @return string
     */
    public static function FixPath(string $path): string
    {
        return mb_ereg_replace('[\\\/]+', '/', $path);
    }

    /**
     * Generiert eine XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX unique id
     * @return string
     */
    public static function GenGuid(): string
    {
        $s = strtoupper(md5(uniqid(rand(),true)));
        return substr($s,0,8) .'-'.substr($s,8,4).'-'.substr($s,12,4).'-'.substr($s,16,4).'-'. substr($s,20);
    }

    /**
     * Gibt die gespeicherte UserIP aus (IPv4 & IPv6)
     * @param array $getc
     * @return string
     */
    public static function getPostedIP(array $getc) {
        if(self::$chkMe == 4 || self::permission('ipban')) {
            $IP = '';
            if(!empty($getc['ipv6']) && stringParser::decode($getc['ipv6']) != self::IPV6_NULL_ADDR) {
                $IP .= 'IPv6: '.stringParser::decode($getc['ipv6']);
            }

            if(!empty($getc['ipv6']) && stringParser::decode($getc['ipv6']) != self::IPV6_NULL_ADDR &&
                !empty($getc['ipv4']) && stringParser::decode($getc['ipv4']) != self::IPV4_NULL_ADDR) {
                $IP .= 'IPv4: '.stringParser::decode($getc['ipv4']).' | IPv6: '.stringParser::decode($getc['ipv6']);
            }

            if(!empty($getc['ipv4']) && stringParser::decode($getc['ipv4']) != self::IPV4_NULL_ADDR) {
                $IP .= 'IPv4: '.stringParser::decode($getc['ipv4']);
            }

            return $IP;
        }

        return _logged;
    }

    /**
     * Checkt welcher User gerade noch online ist
     * @param int $tid
     * @return string
     */
    public static function onlinecheck(int $tid = 0): string {
        $tid = !$tid ? self::$userid : $tid;
        $cache_hash = md5('onlinecheck_'.$tid);
        $status = "<img src=\"../inc/images/offline.png\" alt=\"\" class=\"icon\" />";
        if(!self::$cache->AutoMemExists($cache_hash) || !config::$use_system_cache) {
            $row = self::$sql['default']->rows("SELECT `id` FROM `{prefix_users}` WHERE `id` = ? AND (time+1800)>? AND `online` = 1;", [(int)($tid),time()]);
            if($row) {
                $status = "<img src=\"../inc/images/online.png\" alt=\"\" class=\"icon\" />";
            }

            if(config::$use_system_cache && $row) {
                self::$cache->AutoMemSet($cache_hash, $status,cache::TIME_ONLINE_CHECK);
            }
        } else {
            return self::$cache->AutoMemGet($cache_hash);
        }

        return $status;
    }

    /**
     * Session fuer den letzten Besuch setzen
     * @param int $userid
     */
    public static function set_lastvisit(int $userid = 0) {
        $userid = !$userid ? self::$userid : $userid;
        if(!self::$sql['default']->rows("SELECT `id` FROM `{prefix_users}` WHERE `id` = ? AND (time+1800)>?;", [(int)($userid),time()])) {
            $_SESSION['lastvisit'] = (int)(self::data("time"));
        }
    }

    /**
     * Pruft eine IP gegen eine IP-Range
     * @param string $ip
     * @param string $range $range
     * @return bool
     */
    public static function validateIpV4Range(string $ip,string $range) {
        if (!is_array($range)) {
            $counter = 0;
            $tip = explode('.', $ip);
            $rip = explode('.', $range);
            foreach ($tip as $targetsegment) {
                $rseg = $rip[$counter];
                $rseg = preg_replace('=(\[|\])=', '', $rseg);
                $rseg = explode('-', $rseg);
                if (!isset($rseg[1])) {
                    $rseg[1] = $rseg[0];
                }

                if ($targetsegment < $rseg[0] || $targetsegment > $rseg[1]) {
                    return false;
                }
                $counter++;
            }
        } else {
            foreach ($range as $range_num) {
                $counter = 0;
                $tip = explode('.', $ip);
                $rip = explode('.', $range_num);
                foreach ($tip as $targetsegment) {
                    $rseg = $rip[$counter];
                    $rseg = preg_replace('=(\[|\])=', '', $rseg);
                    $rseg = explode('-', $rseg);
                    if (!isset($rseg[1])) {
                        $rseg[1] = $rseg[0];
                    }

                    if ($targetsegment < $rseg[0] || $targetsegment > $rseg[1]) {
                        return false;
                    }
                    $counter++;
                }
            }
        }

        return true;
    }

    /**
     * Gibt die IP des Besuchers / Users zuruck
     * Forwarded IP Support - IPV4 & IPV6
     */
    public static function visitorIp() {
        if (array_key_exists('admin_ip', $_SESSION)) {
            if (!empty($_SESSION['admin_ip']))
                return $_SESSION['admin_ip'];
        }

        $SetIP = ['v4' => self::IPV4_NULL_ADDR, 'v6' => self::IPV6_NULL_ADDR];
        $ServerVars = ['REMOTE_ADDR','HTTP_CLIENT_IP','HTTP_X_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR','HTTP_FORWARDED','HTTP_VIA','HTTP_X_COMING_FROM','HTTP_COMING_FROM'];
        foreach ($ServerVars as $ServerVar) {
            if($IP=self::detectIP($ServerVar)) {
                //IP-Version 4
                if($SetIP['v4'] == self::IPV4_NULL_ADDR &&
                    self::isIP($IP, false) && !self::isIP($IP, true)) {
                    $SetIP['v4'] = $IP;
                }

                //IP-Version 6
                if($SetIP['v6'] == self::IPV6_NULL_ADDR &&
                    self::isIP($IP, true) && !self::isIP($IP, false)) {
                    $SetIP['v6'] = $IP;
                }
            }
        }

        return $SetIP;
    }

    /**
     * @param $var
     * @return bool|string
     */
    public static function detectIP($var) {
        if(!empty($var) && ($REMOTE_ADDR = self::GetServerVars($var)) && !empty($REMOTE_ADDR)) {
            $REMOTE_ADDR = trim($REMOTE_ADDR);
            if (self::isIP($REMOTE_ADDR) || self::isIP($REMOTE_ADDR, true)) {
                return $REMOTE_ADDR;
            }
        }

        return false;
    }

    /**
     * Check given ip for ipv6 or ipv4.
     * @param    string        $ip
     * @param    boolean       $v6 (optional)
     * @return   boolean
     */
    public static function isIP(string $ip,bool $v6=false) {
        if(!$v6) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? true : false;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? true : false;
    }

    /**
     * Pruft ob die IP gesperrt und gultig ist
     */
    public static function check_ip() {
        if(!self::isIP(self::$userip['v4'], true)) {
            if((!self::isIP(self::$userip['v4']) && !self::isIP(self::$userip,true)) || self::$userip == false || empty(self::$userip)) {
                self::dzcp_session_destroy();
                die('Deine IP ist ung&uuml;ltig!<p>Your IP is invalid!');
            }

            if(empty(self::$UserAgent)) {
                self::dzcp_session_destroy();
                die("Script wird nicht ausgef&uuml;hrt, da kein User Agent &uuml;bermittelt wurde.\n");
            }

            //Banned IP
            $ips = [];
            if(self::$cache->AutoMemExists('ip_check')) {
                $ips = self::$cache->AutoMemGet('ip_check');
                if(!is_array($ips)){
                    $ips = [];
                }
            }

            if(array_key_exists(md5(self::$userip['v4']),$ips) && $ips[md5(self::$userip['v4'])]) {
                return;
            }

            foreach(self::$sql['default']->select("SELECT `id`,`typ`,`data` FROM `{prefix_ipban}` WHERE `ipv4` = ? AND `enable` = 1;", [self::$userip['v4']]) as $banned_ip) {
                if($banned_ip['typ'] == 2 || $banned_ip['typ'] == 3) {
                    self::dzcp_session_destroy();
                    $banned_ip['data'] = unserialize($banned_ip['data']);
                    die('Deine IP ist gesperrt!<p>Your IP is banned!<p>MSG: '.$banned_ip['data']['banned_msg']);
                }
            } unset($banned_ip);

            /*
            if((ini_get('allow_url_fopen') == 1) && self::isIP(self::$userip['v4']) && !self::validateIpV4Range(self::$userip['v4'], '[192].[168].[0-255].[0-255]') &&
                !self::validateIpV4Range(self::$userip['v4'], '[127].[0].[0-255].[0-255]') &&
                !self::validateIpV4Range(self::$userip['v4'], '[10].[0-255].[0-255].[0-255]') &&
                !self::validateIpV4Range(self::$userip['v4'], '[172].[16-31].[0-255].[0-255]')) {
                sfs::check(); //SFS Update
                if(sfs::is_spammer()) {
                    self::$sql['default']->delete("DELETE FROM `{prefix_iptodns}` WHERE `sessid` = ?;",
                        [session_id()]);
                    self::dzcp_session_destroy();
                    die('Deine IP-Adresse ist auf <a href="http://www.stopforumspam.com/" target="_blank">http://www.stopforumspam.com/</a> gesperrt, die IP wurde zu oft fÃ¼r Spam Angriffe auf Webseiten verwendet.<p>
                            Your IP address is known on <a href="http://www.stopforumspam.com/" target="_blank">http://www.stopforumspam.com/</a>, your IP has been used for spam attacks on websites.');
                }
            }
            */

            $ips[md5(self::$userip['v4'])] = true;
            if(config::$use_system_cache) {
                self::$cache->AutoMemSet('ip_check', $ips, cache::TIME_IPS_BLOCKING);
            }

        }

        if(self::isIP(self::$userip['v6'], true)) {
            //Is IPv6
            //TODO: Support for IPV6
        }
    }

    /**
     * Loscht und erstellt eine neue session
     */
    public static final function dzcp_session_destroy() {
        $_SESSION['id']        = '';
        $_SESSION['pwd']       = '';
        $_SESSION['ip']        = '';
        $_SESSION['lastvisit'] = '';
        $_SESSION['akl_id']    = 0;
        $_SESSION['admin_id']  = '';
        $_SESSION['admin_pwd'] = '';
        $_SESSION['admin_ip']  = '';
        session_unset();
        session_destroy();
        session_regenerate_id();
        cookie::clear();
    }

    /**
     * Passwort in md5 oder sha1 bis 512 codieren
     * @param $password
     * @param int $encoder (optional)
     * @return string
     * TODO: Use password_hash()
     */
    public static final function pwd_encoder(string $password,int $encoder=-1) {
        $encoder = ($encoder != -1 ? $encoder :
            settings::get('default_pwd_encoder'));
        switch ($encoder) {
            case 0: return md5($password);
            case 1: return sha1($password);
            default:
            case 3: return hash('sha256', $password);
            case 2: return hash('sha512', $password);
        }
    }

    /**
     * Funktion um notige Erweiterungen zu prufen
     * @return boolean
     **/
    public static function fsockopen_support() {
        return ((!config::$fsockopen_support_bypass && (self::disable_functions('fsockopen') || self::disable_functions('fopen'))) ? false : true);
    }

    /**
     * @param string $function
     * @return bool
     */
    public static function disable_functions(string $function='') {
        if (!function_exists($function)) { return true; }
        $disable_functions = ini_get('disable_functions');
        if (empty($disable_functions)) { return false; }
        $disabled_array = explode(',', $disable_functions);
        foreach ($disabled_array as $disabled) {
            if (strtolower(trim($function)) == strtolower(trim($disabled))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $address
     * @param int $port
     * @param int $timeout (optional)
     * @param bool $udp (optional)
     * @return bool
     */
    public static function ping_port(string $address,int $port,int $timeout=2,bool $udp=false) {
        if (!self::fsockopen_support()) {
            return false;
        }

        $errstr = NULL; $errno = NULL;
        if(!$ip = self::DNSToIp($address)) {
            return false;
        }

        if($fp = @fsockopen(($udp ? "udp://".$ip : $ip), $port, $errno, $errstr, $timeout)) {
            unset($ip,$port,$errno,$errstr,$timeout);
            fclose($fp);
            return true;
        }

        return false;
    }

    /**
     * Funktion um eine Datei im Web auf Existenz zu prufen und abzurufen
     * @param string $url
     * @param bool|array $post (optional)
     * @param bool|array $header (optional)
     * @param bool $nogzip (optional)
     * @param int $timeout (optional)
     * @return String|Boolean
     */
    public static function get_external_contents(string $url,$post=false,$header=false,bool $nogzip=false,int $timeout=0) {
        if((!(ini_get('allow_url_fopen') == 1) && !config::$use_curl || (config::$use_curl && !extension_loaded('curl'))))
            return false;

        if(!$timeout)
            $timeout = config::$file_get_contents_timeout;

        $url_p = @parse_url($url);
        $host = $url_p['host'];
        $port = isset($url_p['port']) ? $url_p['port'] : 80;
        $port = (($url_p['scheme'] == 'https' && $port == 80) ? 443 : $port);
        if(!self::ping_port($host,$port,$timeout))
            return false;

        unset($host);

        if(!$curl = curl_init())
            return false;

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        if($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_USERAGENT, "DZCP");
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , $timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout * 2); // x 2
        curl_setopt($curl, CURLOPT_COOKIEFILE, basePath.'/inc/_cache_/netapi.cookie');
        curl_setopt($curl, CURLOPT_COOKIEJAR, basePath.'/inc/_cache_/netapi.cookie');

        //For POST
        if(count($post) >= 1 && $post != false) {
            foreach ($post as $key => $var) {
                if(is_array($var)) {
                    $post[$key] = json_encode($var);
                }
            }

            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            curl_setopt($curl, CURLOPT_VERBOSE , 0 );
        }

        $gzip = false;
        if(function_exists('gzinflate') && !$nogzip) {
            $gzip = true;
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept-Encoding: gzip,deflate']);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        }

        if($url_p['scheme'] == 'https') { //SSL
            curl_setopt($curl, CURLOPT_PORT , $port);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        $content = curl_exec($curl);
        if (empty($content) || (is_bool($content) && !$content)) {
            return false;
        }

        if($gzip) {
            $curl_info = curl_getinfo($curl,CURLINFO_HEADER_OUT);
            if(stristr($curl_info, 'accept-encoding') && stristr($curl_info, 'gzip')) {
                $content = gzinflate( substr($content,10,-8) );
            }
        }

        @curl_close($curl);
        unset($curl);

        return ((string)(trim($content)));
    }

    /**
     * Verschlusselt eine E-Mail Adresse per Javascript
     * @param string $email
     * @param string $template (optional)
     * @param array $custom (optional)
     * @return string
     * @throws SmartyException
     */
    public static function CryptMailto(string $email='',string $template=_emailicon,array $custom= []) {
        $smarty = self::getSmarty(true); //Use Smarty
        if(empty($template) || empty($email) || !self::permission("editusers")) return '';
        $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
        $key = str_shuffle($character_set); $cipher_text = ''; $id = 'e'.rand(1,999999999);
        for ($i=0;$i<strlen($email);$i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])];
        $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";';
        $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
        if(!empty($custom) && count($custom) >= 1) {
            $smarty->caching = false;
            foreach ($custom as $key => $var) {
                $smarty->assign($key,$var);
            }

            $template = $smarty->fetch('string:'.$template);
            $smarty->clearAllAssign();
        }

        $script.= 'document.getElementById("'.$id.'").innerHTML="'.$template.'"';
        $script = "eval(\"".str_replace(["\\",'"'], ["\\\\",'\"'], $script)."\")";
        $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>';
        return '<span id="'.$id.'">[javascript protected email address]</span>'.$script;
    }

    /**
     * Schreibe in die IPCheck Tabelle
     * @param string $what
     * @param bool $time
     */
    public static function setIpcheck(string $what = '',bool $time = true) {
        self::$sql['default']->insert("INSERT INTO `{prefix_ip_action}` SET `ipv4` = ?, `user_id` = ?, `what` = ?, `time` = ?, `created` = ?;",
            [self::visitorIp()['v4'],(int)(self::userid()),$what,($time ? time() : 0),time()]);
    }

    /**
     * Preuft ob alle clicks nur einmal gezahlt werden *gast/user
     * @param string $side_tag (optional)
     * @param int $clickedID (optional)
     * @param bool $update (optional)
     * @return bool
     */
    public static function count_clicks(string $side_tag='',int $clickedID=0,bool $update=true) {
        if(!self::$CrawlerDetect->isCrawler()) {
            $qry = self::$sql['default']->select("SELECT `id`,`side` FROM `{prefix_clicks_ips}` WHERE `uid` = 0 AND `time` <= ?;", [time()]);
            if(self::$sql['default']->rowCount()) {
                foreach($qry as $get) {
                    if($get['side'] != 'vote') {
                        self::$sql['default']->delete("DELETE FROM `{prefix_clicks_ips}` WHERE `id` = ?;", [$get['id']]);
                    }
                }
            }

            if(self::$chkMe != 'unlogged') {
                if (self::$sql['default']->rows("SELECT `id` FROM `{prefix_clicks_ips}` WHERE `uid` = ? AND `ids` = ? AND `side` = ?;",
                    [(int)(self::$userid),(int)($clickedID),$side_tag])) {
                    return false;
                }

                if(self::$sql['default']->rows("SELECT `id` FROM `{prefix_clicks_ips}` WHERE (`ipv4` = ? OR (`ipv6` != ? AND `ipv6` = ?)) AND `ids` = ? AND `side` = ?;",
                    [self::$userip['v4'],self::IPV6_NULL_ADDR,self::$userip['v6'],(int)($clickedID),$side_tag])) {
                    if($update) {
                        self::$sql['default']->update("UPDATE `{prefix_clicks_ips}` SET `uid` = ?, `time` = ? WHERE (`ipv4` = ? OR (`ipv6` != ? AND `ipv6` = ?)) AND `ids` = ? AND `side` = ?;",
                            [(int)(self::$userid),(time()+count_clicks_expires),self::$userip['v4'],self::IPV6_NULL_ADDR,self::$userip['v6'],(int)($clickedID),$side_tag]);
                    }

                    return false;
                } else {
                    if($update) {
                        self::$sql['default']->insert("INSERT INTO `{prefix_clicks_ips}` SET `ipv4` = ?, `ipv6` = ?, `uid` = ?, `ids` = ?, `side` = ?, `time` = ?;",
                            [self::$userip['v4'], self::$userip['v6'], (int)(self::$userid), (int)($clickedID), $side_tag, (time() + count_clicks_expires)]);
                    }

                    return true;
                }
            } else {
                if(!self::$sql['default']->rows("SELECT id FROM `{prefix_clicks_ips}` WHERE (`ipv4` = ? OR (`ipv6` != ? AND `ipv6` = ?)) AND `ids` = ? AND `side` = ?;",
                    [self::$userip['v4'],self::IPV6_NULL_ADDR,self::$userip['v6'],(int)($clickedID),$side_tag])) {
                    if($update) {
                        self::$sql['default']->insert("INSERT INTO `{prefix_clicks_ips}` SET `ipv4` = ?, `ipv6` = ?, `uid` = 0, `ids` = ?, `side` = ?, `time` = ?;",
                            [self::$userip['v4'],self::$userip['v6'],(int)($clickedID),$side_tag,(time()+count_clicks_expires)]);
                    }

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Rechte abfragen
     * @param int $checkID (optional)
     * @param int $pos (optional)
     * @return string
     */
    public static function getPermissions(int $checkID = 0, int $pos = 0) {
        //Rechte des Users oder des Teams suchen
        if(!empty($checkID)) {
            $check = empty($pos) ? 'user' : 'pos'; $checked = [];
            $qry = self::$sql['default']->fetch("SELECT * FROM `{prefix_permissions}` WHERE `".$check."` = ?;", [(int)($checkID)]);
            if (self::$sql['default']->rowCount()) {
                foreach($qry as $k => $v) {
                    if($k != 'id' && $k != 'user' && $k != 'pos' && $k != 'intforum') {
                        $checked[$k] = $v;
                    }
                }
            }
        }

        //Liste der Rechte zusammenstellen
        $permission = [];
        $qry = self::$sql['default']->show("SHOW COLUMNS FROM `{prefix_permissions}`;");
        if(self::$sql['default']->rowCount()) {
            foreach($qry as $get) {
                if($get['Field'] != 'id' && $get['Field'] != 'user' && $get['Field'] != 'pos' && $get['Field'] != 'intforum') {
                    $lang = constant('_perm_'.$get['Field']);
                    $chk = empty($checked[$get['Field']]) ? '' : ' checked="checked"';
                    $permission[$lang] = '<input type="checkbox" class="checkbox" id="'.$get['Field'].'" name="perm[p_'.$get['Field'].']" value="1"'.$chk.' /><label for="'.$get['Field'].'"> '.$lang.'</label> ';
                }
            }
        }

        $permissions = '';
        if(count($permission)) {
            natcasesort($permission); $break = 1;
            foreach($permission AS $perm) {
                $br = ($break % 2) ? '<br />' : ''; $break++;
                $permissions .= $perm.$br;
            }
        }

        return $permissions;
    }

    /**
     * interne Foren-Rechte abfragen
     * @param int $checkID (optional)
     * @param int $pos (optional)
     * @return string
     */
    public static function getBoardPermissions(int $checkID = 0,int $pos = 0) {
        $break = 0; $i_forum = ''; $fkats = '';
        $qry = self::$sql['default']->select("SELECT `id`,`name` FROM `{prefix_forum_kats}` WHERE `intern` = 1 ORDER BY `kid` ASC;");
        if(self::$sql['default']->rowCount()) {
            foreach($qry as $get) {
                unset($kats, $fkats, $break);
                $kats = (empty($katbreak) ? '' : '<div style="clear:both">&nbsp;</div>').'<table class="hperc" cellspacing="1"><tr><td class="contentMainTop"><b>'.stringParser::decode($get["name"]).'</b></td></tr></table>';
                $katbreak = 1; $break = 0; $fkats = '';

                $qry2 = self::$sql['default']->select("SELECT `kattopic`,`id` FROM `{prefix_forum_sub_kats}` WHERE `sid` = ? ORDER BY `kattopic` ASC;", [$get['id'],]);
                if(self::$sql['default']->rowCount()) {
                    foreach($qry2 as $get2) {
                        $br = ($break % 2) ? '<br />' : ''; $break++;
                        $chk = (self::$sql['default']->rows("SELECT `id` FROM `{prefix_forum_access}` WHERE `".(empty($pos) ? 'user' : 'pos')."` = ? AND ".(empty($pos) ? 'user' : 'pos')." != 0 AND `forum` = ?;", [(int)($checkID),$get2['id']]) ? ' checked="checked"' : '');
                        $fkats .= '<input type="checkbox" class="checkbox" id="board_'.$get2['id'].'" name="board['.$get2['id'].']" value="'.$get2['id'].'"'.$chk.' /><label for="board_'.$get2['id'].'"> '.stringParser::decode($get2['kattopic']).'</label> '.$br;
                    }
                }

                $i_forum .= $kats.$fkats;
            }
        }

        return $i_forum;
    }

    /**
     * Generate a globally unique identifier (GUID)
     * @return string
     */
    public static function guid() {
        if (function_exists('com_create_guid')){
            return com_create_guid();
        } else {
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
            return $uuid;
        }
    }

    /**
     * Adminberechtigungen ueberpruefen
     * @param int $userid (optional)
     * @param bool $refresh (optional)
     * @return bool
     */
    public static function admin_perms(int $userid = 0, bool $refresh = false) {
        $userid = !$userid ? self::$userid : $userid;
        if (empty($userid) || !$userid) {
            return false;
        }

        if(self::rootAdmin($userid) || self::$chkMe == 4) {
            return true;
        }

        // no need for these admin areas & check user permission
        $e = ['editusers', 'votes', 'contact', 'intnews', 'forum', 'dlintern','intforum'];
        $cache_hash = md5('permissions_'.$userid);
        if(!self::$cache->AutoMemExists($cache_hash) || !config::$use_system_cache || $refresh) {
            $permissions = self::$sql['default']->fetch("SELECT * FROM `{prefix_permissions}` WHERE `user` = ?;",
                [(int)($userid)]);
            if(config::$use_system_cache) {
                self::$cache->AutoMemSet($cache_hash,$permissions,cache::TIME_USERPERM);
            }
        } else {
            $permissions = self::$cache->AutoMemGet($cache_hash);
        }

        foreach($permissions as $v => $k) {
            if($v != 'id' && $v != 'user' && $v != 'pos' && !in_array($v, $e)) {
                if($k == 1) {
                    return true;
                    break;
                }
            }
        }

        // check rank permission
        $qry = self::$sql['default']->select("SELECT s1.* FROM `{prefix_permissions}` AS `s1` LEFT JOIN `{prefix_user_posis}` AS `s2` ON s1.`pos` = s2.`posi` WHERE s2.`user` = ? AND s2.`posi` != 0;",
            [(int)($userid)]);
        foreach($qry as $get) {
            foreach($get AS $v => $k) {
                if($v != 'id' && $v != 'user' && $v != 'pos' && !in_array($v, $e)) {
                    if($k == 1) {
                        return true;
                        break;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Zugriffsberechtigung auf die Seite
     * @return bool
     */
    public static function check_internal_url() {
        if (self::$chkMe >= 1) {
            return false;
        }
        $install_pfad = explode("/",dirname(dirname(self::GetServerVars('SCRIPT_NAME'))."../"));
        $now_pfad = explode("/",self::GetServerVars('REQUEST_URI')); $pfad = '';
        foreach($now_pfad as $key => $value) {
            if(!empty($value)) {
                if(!isset($install_pfad[$key]) || $value != $install_pfad[$key]) {
                    $pfad .= "/".$value;
                }
            }
        }

        list($pfad) = explode('&',$pfad);
        $pfad = "..".$pfad;

        if (strpos($pfad, "?") === false && strpos($pfad, ".php") === false) {
            $pfad .= "/";
        }

        if (strpos($pfad, "index.php") !== false) {
            $pfad = str_replace('index.php', '', $pfad);
        }

        $url = $pfad.'index.php';
        $get_navi = self::$sql['default']->fetch("SELECT `internal` FROM `{prefix_navi}` WHERE `url` = ? OR `url` = ?;", [$pfad,$url]);
        if(self::$sql['default']->rowCount()) {
            if ($get_navi['internal']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checkt, ob neue Nachrichten vorhanden sind
     * @return bool|mixed|string
     * @throws SmartyException
     */
    public static function check_msg() {
        if(self::$sql['default']->rows("SELECT `id` FROM `{prefix_messages}` WHERE `an` = ? AND `page` = 0;", [(int)($_SESSION['id'])])) {
            self::$sql['default']->update("UPDATE `{prefix_messages}` SET `page` = 1 WHERE `an` = ?;", [(int)($_SESSION['id'])]);
            $smarty = self::getSmarty(true);
            $smarty->caching = false;
            $msg = $smarty->fetch('file:['.common::$tmpdir.']user/msg/new_msg.tpl');
            $smarty->clearAllAssign();
            return $msg;
        }

        return '';
    }

    /**
     * Flaggen ausgeben
     * @param string $code
     * @return string
     */
    public static function flag(string $code) {
        if (empty($code)) {
            return '<img src="../inc/images/flaggen/nocountry.gif" alt="" class="icon" />';
        }

        foreach(common::SUPPORTED_PICTURE as $end) {
            if (file_exists(basePath . "/inc/images/flaggen/" . $code . "." . $end)) {
                break;
            }
        }

        if (file_exists(basePath . "/inc/images/flaggen/" . $code . "." . $end)) {
            return'<img src="../inc/images/flaggen/' . $code . '.' . $end . '" alt="" class="icon" />';
        }

        return '<img src="../inc/images/flaggen/nocountry.gif" alt="" class="icon" />';
    }

    /**
     * @param $code
     * @return string
     */
    public static function rawflag($code) {
        if (empty($code)) {
            return '<img src=../inc/images/flaggen/nocountry.gif alt= class=icon />';
        }

        foreach(common::SUPPORTED_PICTURE as $end) {
            if (file_exists(basePath . "/inc/images/flaggen/" . $code . "." . $end)) {
                break;
            }
        }

        if (file_exists(basePath . "/inc/images/flaggen/" . $code . "." . $end)) {
            return '<img src=../inc/images/flaggen/' . $code . '.' . $end . ' alt= class=icon />';
        }

        return '<img src=../inc/images/flaggen/nocountry.gif alt= class=icon />';
    }

    /**
     * Aktualisierung des Online Status *preview
     */
    public static function update_user_status_preview() {
        if(self::HasDSGVO()) {
            ## User aus der Datenbank suchen ##
            $get = self::$sql['default']->fetch("SELECT `id`,`time` FROM `{prefix_users}` "
                . "WHERE `id` = ? AND `sessid` = ? AND `ipv4` = ? AND level != 0;",
                [(int)($_SESSION['id']), session_id(), stringParser::encode(self::$userip['v4'])]);

            if (self::$sql['default']->rowCount()) {
                ## Schreibe Werte in die Server Sessions ##
                $_SESSION['lastvisit'] = $get['time'];

                if (stringParser::decode(self::data("ipv4", $get['id'])) != $_SESSION['ip'])
                    $_SESSION['lastvisit'] = self::data($get['id'], "time");

                if (empty($_SESSION['lastvisit']))
                    $_SESSION['lastvisit'] = self::data($get['id'], "time");

                ## Aktualisiere Datenbank ##
                self::$sql['default']->update("UPDATE `{prefix_users}` SET `online` = 1 WHERE `id` = ?;", [$get['id']]);
            }
        }
    }

    /**
     * Prueft, ob der User gesperrt ist und meldet ihn ab
     * @param int $userid_set
     * @param bool $logout
     * @return bool
     */
    public static function isBanned(int $userid_set=0,bool $logout=true) {
        $userid_set = $userid_set ? $userid_set : self::$userid;
        if(self::checkme($userid_set) >= 1 || $userid_set) {
            $get = self::$sql['default']->fetch("SELECT `banned` FROM `{prefix_users}` WHERE `id` = ? LIMIT 1;", [(int)($userid_set)]);
            if($get['banned']) {
                if($logout) {
                    self::dzcp_session_destroy();
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Prueft, ob ein User diverse Rechte besitzt
     * @param string $check
     * @param int $uid (optional)
     * @param bool $refresh (optional)
     * @return bool
     */
    public static function permission(string $check,int $uid=0,bool $refresh = false): bool
    {
        if (!$uid) { $uid = self::$userid; }
        if(self::rootAdmin($uid) || empty($check)) {
            return true;
        }

        if(self::$chkMe == 4) {
            return true;
        } else {
            if ($uid) {
                //Check ROW
                if(!self::$sql['default']->rows("SELECT `id` FROM `{prefix_permissions}` WHERE `user` = ?;", [(int)($uid)]) && $uid >= 1) {
                    self::$sql['default']->insert("INSERT INTO `{prefix_permissions}` SET `user` = ?;", [(int)($uid)]);
                }

                $cache_hash = md5('permissions_'.$uid);
                if(!self::$cache->AutoMemExists($cache_hash) || !config::$use_system_cache || $refresh) {
                    $permissions = self::$sql['default']->fetch("SELECT * FROM `{prefix_permissions}` WHERE `user` = ?;", [(int)($uid)]);
                    if(!array_key_exists($check,$permissions) && $check != 'xxxxx') {
                        self::$sql['default']->query("ALTER TABLE `{prefix_permissions}` ADD `".$check."` INT(1) NOT NULL DEFAULT '0';");
                        $permissions = self::$sql['default']->fetch("SELECT * FROM `{prefix_permissions}` WHERE `user` = ?;", [(int)($uid)]);
                    }

                    if(config::$use_system_cache) {
                        self::$cache->AutoMemSet($cache_hash,$permissions,cache::TIME_USERPERM);
                    }

                    // check rank permission
                    if($check != 'xxxxx') {
                        if (self::$sql['default']->rows("SELECT s1.`" . $check . "` FROM `{prefix_permissions}` AS `s1` LEFT JOIN `{prefix_user_posis}` AS `s2` ON s1.`pos` = s2.`posi`"
                            . "WHERE s2.`user` = ? AND s1.`" . $check . "` = 1 AND s2.`posi` != 0;", [(int)($uid)])
                        ) {
                            return true;
                        }

                        return (bool)(array_key_exists($check, $permissions) && $permissions[$check]);
                    }
                } else {
                    $permissions = self::$cache->AutoMemGet($cache_hash);
                    if(!array_key_exists($check,$permissions) && $check != 'xxxxx' && !empty($check)) {
                        self::$sql['default']->query("ALTER TABLE `{prefix_permissions}` ADD `".$check."` INT(1) NOT NULL DEFAULT '0';");
                        $permissions = self::$sql['default']->fetch("SELECT * FROM `{prefix_permissions}` WHERE `user` = ?;", [(int)($uid)]);

                        if(config::$use_system_cache) {
                            self::$cache->AutoMemSet($cache_hash, $permissions, cache::TIME_USERPERM);
                        }
                    }

                    if($check != 'xxxxx') {
                        // check rank permission
                        if (self::$sql['default']->rows("SELECT s1.`" . $check . "` FROM `{prefix_permissions}` AS `s1` LEFT JOIN `{prefix_user_posis}` AS `s2` ON s1.`pos` = s2.`posi`"
                            . "WHERE s2.`user` = ? AND s1.`" . $check . "` = 1 AND s2.`posi` != 0;", [(int)($uid)])
                        ) {
                            return true;
                        }

                        return (bool)(array_key_exists($check, $permissions) && $permissions[$check]);
                    }
                }
            }
        }

        return false;
    }

    /**
     * Prueft, wieviele registrierte User gerade online sind
     * @param string $where (optional)
     * @param bool $like (optional)
     * @return int
     */
    public static function online_reg(string $where='',bool $like=false): int
    {
        if(!self::$CrawlerDetect->isCrawler()) {
            $whereami = (empty($where) ? '' :
                ($like ? " AND `whereami` LIKE '%".$where."%'" :
                    " AND `whereami` = ".self::$sql['default']->quote($where)));
            return self::cnt('{prefix_users}', " WHERE (time+1800)>".time()."".$whereami." AND `online` = 1");
        }

        return 0;
    }

    /**
     * Prueft ob der User eingeloggt ist und wenn welches Level er besitzt.
     * @param int $uid (optional)
     * @param bool $refresh (optional)
     * @return int
     */
    public static function checkme(int $uid=0, bool $refresh = false): int
    {
        if (empty($_SESSION['id']) || empty($_SESSION['pwd'])) { return 0; }
        if (!$uid = ($uid != 0 ? (int)($uid) : self::userid())) { return 0; }
        if (self::rootAdmin($uid)) { return 4; }
        $user = self::getUserIndex($uid,$refresh); //Load user from Mem/Netcache
        if(count($user) > 2) {
            return (int)$user['level'];
        }

        return 0;
    }

    /**
     * Infomeldung ausgeben
     * @param string $msg
     * @param string $url
     * @param int $timeout (optional)
     * @param bool $direct_refresh (optional)
     * @return mixed|string
     * @throws SmartyException
     */
    public static function info(string $msg,string $url,int $timeout = 5,bool $direct_refresh = true) {
        if (settings::get('direct_refresh') && $direct_refresh) {
            header('Location: ' . str_replace('&amp;', '&', $url));
            exit();
        }

        $u = parse_url($url); $parts = '';
        $u['query'] = array_key_exists('query', $u) ? $u['query'] : '';
        $u['query'] = str_replace('&amp;', '&', $u['query']);
        foreach(explode('&', $u['query']) as $p) {
            $p = explode('=', $p);
            if (count($p) == 2) {
                $parts .= '<input type="hidden" name="' . $p[0] . '" value="' . $p[1] . '" />' . "\r\n";
            }
        }

        if (!array_key_exists('path', $u)) {
            $u['path'] = '';
        }

        $smarty = self::getSmarty(true);
        $smarty->caching = false;
        $smarty->assign('msg',$msg);
        $smarty->assign('url',$u['path']);
        $smarty->assign('rawurl',html_entity_decode($url));
        $smarty->assign('parts',$parts);
        $smarty->assign('timeout',$timeout);
        $info = $smarty->fetch('file:['.common::$tmpdir.']errors/info.tpl');
        $smarty->clearAllAssign();
        return $info;
    }

    /**
     * Updatet die Maximalen User die gleichzeitig online sind
     */
    public static function update_maxonline() {
        $maxonline = self::$sql['default']->fetch("SELECT `maxonline` FROM `{prefix_counter}` WHERE `today` = ?;", [date("j.n.Y")],'maxonline');
        if ($maxonline < ($count = self::cnt('{prefix_counter_whoison}'))) {
            self::$sql['default']->update("UPDATE `{prefix_counter}` SET `maxonline` = ? WHERE `today` = ?;", [$count,date("j.n.Y")]);
        }
    }

    /**
     * Aktualisiert die Position der Gaste & User
     * @param string $where
     */
    public static function update_online(string $where='') {
        //Bug mit IPv6
        return;
        if(self::HasDSGVO()) {
            if (!self::$CrawlerDetect->isCrawler() && !empty($where) && !self::$sql['default']->rows("SELECT `id` FROM `{prefix_iptodns}` WHERE `sessid` = ? AND `bot` = 1;", [session_id()])) {
                if (self::$sql['default']->rows("SELECT `id` FROM `{prefix_counter_whoison}` WHERE `online` < ?;", [time()])) { //Cleanup
                    self::$sql['default']->delete("DELETE FROM `{prefix_counter_whoison}` WHERE `online` < ?;", [time()]);
                }

                $get = self::$sql['default']->fetch("SELECT `id` FROM `{prefix_counter_whoison}` WHERE (`ipv4` = ? OR `ipv6` = ?) AND `ssid` = ?;",
                    [self::$userip['v4'], self::$userip['v6'], session_id()]); //Update Move
                if (self::$sql['default']->rowCount()) {
                    self::$sql['default']->update("UPDATE `{prefix_counter_whoison}` SET `whereami` = ?, `online` = ?, `login` = ?  WHERE `id` = ?;",
                        [stringParser::encode($where), (time() + 1800), (!self::$chkMe ? 0 : 1), $get['id']]);
                } else {
                    self::$sql['default']->insert("INSERT INTO `{prefix_counter_whoison}` SET `ipv4` = ?, `ipv6` = ?, `ssid` = ?, `online` = ?, `whereami` = ?, `login` = ?;",
                        [self::$userip['v4'], self::$userip['v6'], session_id(), (time() + 1800), stringParser::encode($where), (!self::$chkMe ? 0 : 1)]);
                }

                if (self::$chkMe) {
                    self::$sql['default']->update("UPDATE `{prefix_users}` SET `time` = ?, `whereami` = ? WHERE `id` = ?;", [time(), stringParser::encode($where), (int)(self::$userid)]);
                }
            }
        }
    }

    /**
     * Prueft, wieviele Besucher gerade online sind
     * @param string $where
     * @param bool $like (optional)
     * @return int
     */
    public static function online_guests(string $where='',bool $like=false) {
        if(!self::$CrawlerDetect->isCrawler()) {
            $whereami = (empty($where) ? '' :
                ($like ? " AND `whereami` LIKE '%".$where."%'" :
                    " AND `whereami` = ".self::$sql['default']->quote($where)));
            return self::cnt('{prefix_counter_whoison}'," WHERE (online+1800)>".time()."".$whereami." AND `login` = 0");
        }

        return 0;
    }

    /**
     * Counter updaten
     */
    public static function updateCounter(): void {
        if(self::HasDSGVO()) {
            $datum = time();
            $get_agent = self::$sql['default']->fetch("SELECT `id`,`agent`,`bot` FROM `{prefix_iptodns}` WHERE ".
                "((`ipv4` = ? AND `ipv4` != '0.0.0.0') OR (`ipv6` = ? AND `ipv6` != 'xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx'));",
                [stringParser::encode(self::$userip['v4']),stringParser::encode(self::$userip['v6'])]);
            if (self::$sql['default']->rowCount()) {
                if (!$get_agent['bot'] && !self::$CrawlerDetect->isCrawler(stringParser::decode($get_agent['agent']))) {
                    if (self::$sql['default']->rows("SELECT id FROM `{prefix_counter_ips}` WHERE datum+? <= ? OR FROM_UNIXTIME(datum,'%d.%m.%Y') != ?;", [self::$reload, time(), date("d.m.Y")])) {
                        self::$sql['default']->delete("DELETE FROM `{prefix_counter_ips}` WHERE datum+? <= ? OR FROM_UNIXTIME(datum,'%d.%m.%Y') != ?;", [self::$reload, time(), date("d.m.Y")]);
                    }

                    $get = self::$sql['default']->fetch("SELECT `datum` FROM `{prefix_counter_ips}` WHERE `ipv4` = ? AND FROM_UNIXTIME(datum,'%d.%m.%Y') = ?;", [stringParser::encode(self::$userip['v4']), date("d.m.Y")]);
                    if (self::$sql['default']->rowCount()) {
                        $sperrzeit = $get['datum'] + self::$reload;
                        if ($sperrzeit <= time()) {
                            self::$sql['default']->delete("DELETE FROM `{prefix_counter_ips}` WHERE `ipv4` = ?;", [stringParser::encode(self::$userip['v4'])]);
                            if (self::$sql['default']->rows("SELECT `id` FROM `{prefix_counter}` WHERE `today` = '" . date("j.n.Y") . "';", [date("j.n.Y")])) {
                                self::$sql['default']->update("UPDATE `{prefix_counter}` SET `visitors` = (visitors+1) WHERE `today` = ?;", [date("j.n.Y")]);
                            } else {
                                self::$sql['default']->insert("INSERT INTO `{prefix_counter}` SET `visitors` = 1 WHERE `today` = ?;", [date("j.n.Y")]);
                            }

                            self::$sql['default']->insert("INSERT INTO `{prefix_counter_ips}` SET `ipv4` = ?, `datum` = ?;", [stringParser::encode(self::$userip['v4']), (int)($datum)]);
                        }
                    } else {
                        if (self::$sql['default']->rows("SELECT `id` FROM `{prefix_counter}` WHERE `today` = ?;", [date("j.n.Y")])) {
                            self::$sql['default']->update("UPDATE `{prefix_counter}` SET `visitors` = (visitors+1) WHERE `today` = ?;", [date("j.n.Y")]);
                        } else {
                            self::$sql['default']->insert("INSERT INTO `{prefix_counter}` SET `visitors` = 1, `today` = ?;", [date("j.n.Y")]);
                        }

                        self::$sql['default']->insert("INSERT INTO `{prefix_counter_ips}` SET `ipv4` = ?, `datum` = ?;", [stringParser::encode(self::$userip['v4']), (int)($datum)]);
                    }
                }
            }
        }
    }

    /**
     * @param string $template
     * @param bool $regen
     * @return bool|string
     * @throws Less_Exception_Parser
     */
    public static function less($template='template',$regen=false) {
        $cache_hash = md5(self::$tmpdir.$template);
        if(config::$use_less_cache && !$regen && config::$use_system_cache &&
            config::$use_network_cache && self::$cache->AutoMemExists($cache_hash)) {
            return self::$cache->AutoMemGet($cache_hash);
        }

        if(config::$use_less_cache && !$regen && config::$use_system_cache &&
            !config::$use_network_cache && self::$cache->FileExists($cache_hash)) {
            return self::$cache->FileGet($cache_hash);
        }

        $main_dir = basePath . "/inc/_templates_/" . self::$tmpdir . "/_less";
        $auto_imports = [];
        $auto_imports[basePath . '/inc/_templates_/' . self::$tmpdir . '/_less/imports/'] =
            '../inc/_templates_/' . self::$tmpdir . '/_less/imports';

        if (count($auto_imports) >= 1) {
            self::$less->SetImportDirs($auto_imports);
        }

        if (file_exists($main_dir . '/' . $template . '.less')) {
            self::$less->parseFile($main_dir . '/' . $template . '.less', "/inc/_templates_/" . self::$tmpdir . "/_less/");
        }

        $css = self::$less->getCss();
        if(config::$use_less_cache && config::$use_system_cache && config::$use_network_cache) {
            self::$cache->AutoMemSet($cache_hash, $css, Cache::TIME_LESS);
        }

        if(config::$use_less_cache && config::$use_system_cache && !config::$use_network_cache) {
            self::$cache->FileSet($cache_hash, $css, Cache::TIME_LESS);
        }

        return $css;
    }

    /**
     * Generiert die Select-Felder für ein Dropdown Menu
     * @param array $get
     * @return string
     * @throws SmartyException
     */
    public static function editor_is_reg(array $get = []) {
        $get['reg'] = (array_key_exists('reg',$get) ? $get['reg'] : (array_key_exists('t_reg',$get) ? $get['t_reg'] : self::$userid)); //Fix for thread/post
        $smarty = self::getSmarty(true);
        $smarty->caching = false;
        if($get['reg'] != 0) {
            $smarty->assign('nick',self::autor($get['reg']));
            $editor = $smarty->fetch('file:['.common::$tmpdir.']page/editor_regged.tpl');
        } else {
            if(!array_key_exists('email',$get)) { $get['email'] = array_key_exists('t_email',$get) ? $get['t_email'] : ''; } //Fix for thread/post
            if(!array_key_exists('hp',$get)) { $get['hp'] = array_key_exists('t_hp',$get) ? $get['t_hp'] : ''; } //Fix for thread/post
            if(!array_key_exists('nick',$get)) { $get['nick'] = array_key_exists('t_nick',$get) ? $get['t_nick'] : ''; } //Fix for thread/post
            $smarty->assign('postemail',stringParser::decode($get['email']));
            $smarty->assign('posthp',stringParser::decode($get['hp']));
            $smarty->assign('postnick',stringParser::decode($get['nick']));
            $editor = $smarty->fetch('file:['.common::$tmpdir.']page/editor_notregged.tpl');
        }

        $smarty->clearAllAssign();
        return $editor;
    }

    /**
     * Generiert die Select-Felder für ein Dropdown Menu
     * @param string $value
     * @param string $what
     * @param bool $selected
     * @param string|null $class
     * @return string
     * @throws SmartyException
     */
    public static function select_field(string $value,bool $selected=false,string $what,string $class = null) {
        return self::select_field_bootstrap($value,$what,$selected,['class' => $class]);
    }

    /**
     * @param string $value
     * @param string $what
     * @param bool $selected
     * @param array $options
     * @return string
     * @throws SmartyException
     */
    public static function select_field_bootstrap(string $value = '' ,string $what = '',bool $selected = false,array $options = []) {
        $class = ""; $thumbnail = ""; $icon = "";
        if(array_key_exists('class',$options)) {
            $class = !empty($options['class']) ? ' class="'.$options['class'].'"' : '';
        }

        if(array_key_exists('thumbnail',$options)) {
            $thumbnail = !empty($options['thumbnail']) ? ' data-content="<img src=\''.$options['thumbnail'].'\' /> '.$what.'"' : '';
        }

        if(array_key_exists('icon',$options)) {
            $icon = !empty($options['icon']) ? ' data-icon="'.$options['icon'].'"' : '';
        }

        $smarty = self::getSmarty(true);
        $smarty->caching = false;
        $smarty->assign('value',$value);
        $smarty->assign('sel',($selected ? ' selected="selected"' : ''));
        $smarty->assign('what',$what);
        $smarty->assign('class',$class);

        //bootstrap
        $smarty->assign('thumbnail',$thumbnail);
        $smarty->assign('icon',$icon);

        $select_field = $smarty->fetch('file:['.common::$tmpdir.']page/select_field.tpl');
        $smarty->clearAllAssign();
        return $select_field;
    }

    /**
     * Generiert die Select-Felder für ein Dropdown Menu
     * @param string $id
     * @param string $action
     * @param string $title (optional)
     * @param string $del (optional)
     * @return string
     * @internal param string $value
     * @internal param bool $is_selected
     * @internal param string $what
     * @throws SmartyException
     */
    public static function button_delete_single(string $id,string $action,string $title=_button_title_del,string $del=_confirm_del_entry) {
        $smarty = self::getSmarty(true);
        $smarty->caching = false;
        $smarty->assign('id',$id);
        $smarty->assign('action',$action);
        $smarty->assign('title',$title);
        $smarty->assign('del',$del);
        $delete = $smarty->fetch('file:['.common::$tmpdir.']page/buttons/button_delete_single.tpl');
        $smarty->clearAllAssign();
        return $delete;
    }

    /**
     * Generiert einen A link mit einem Bild als Inhalt
     * @param string $href
     * @param string $img
     * @param string $title
     * @param string $target
     * @param string $alt
     * @return string
     * @throws SmartyException
     */
    public static function a_img_link(string $href,string $img,string $title = '',string $target = '_blank', string $alt = '') {
        //Img Template detect
        $src = "../inc/images/languages/" . $_SESSION['language'] . "/" . $img. ".png";
        foreach (common::SUPPORTED_PICTURE as $endung) {
            if (file_exists(basePath . "/inc/_templates_/" . common::$tmpdir . "/images/languages/" . $_SESSION['language'] . "/" . $img. "." .$endung)) {
                $src = "../inc/_templates_/" . common::$tmpdir . "/images/languages/" . $_SESSION['language'] . "/" . $img. "." .$endung;
            }
        }

        $smarty = self::getSmarty(true);
        $smarty->caching = false;
        $smarty->assign('src',$src);
        $smarty->assign('target',$target);
        $smarty->assign('href',$href);
        $smarty->assign('alt',$alt);
        $smarty->assign('title',$title);
        $link = $smarty->fetch('file:['.common::$tmpdir.']page/links/a_img_link.tpl');
        $smarty->clearAllAssign();
        return $link;
    }

    /**
     * @param string $sort
     * @return string
     */
    public static function orderby(string $sort) {
        $split = explode("&",self::GetServerVars('QUERY_STRING'));
        $url = "?";

        foreach($split as $part) {
            if(strpos($part,"orderby") === false && strpos($part,"order") === false && !empty($part)) {
                $url .= $part;
                $url .= "&";
            }
        }

        if(isset($_GET['orderby']) && $_GET['order']) {
            if ($_GET['orderby'] == $sort && $_GET['order'] == "ASC") {
                return $url . "orderby=" . $sort . "&order=DESC";
            }
        }

        return $url."orderby=".$sort."&order=ASC";
    }

    /**
     * @param array $order_by
     * @param string $default_order
     * @param string $join (optional)
     * @param array $order (optional)
     * @return string
     */
    public static function orderby_sql(array $order_by= [], string $default_order='', string $join='', array $order = ['ASC','DESC']) {
        if (!isset($_GET['order']) || empty($_GET['order']) || !in_array($_GET['order'], $order) ||
            !isset($_GET['orderby']) || empty($_GET['orderby']) || !in_array($_GET['orderby'], $order_by) ||
            empty($_GET['orderby']) || empty($_GET['order'])) {
            return $default_order;
        }
        $key = array_search($_GET['orderby'], $order_by);   // $key = 1;
        $order_by = (in_array($_GET['orderby'], $order_by) ? '`'.$order_by[$key].'` ' : '`id` ');
        $order = (in_array(strtoupper($_GET['order']), $order) ? (strtoupper($_GET['order']) == 'DESC' ? 'DESC ' : 'ASC ') : 'DESC ');
        return 'ORDER BY '.(!empty($join) ? $join.'.' : '').$order_by.$order;
    }

    /**
     * @return string
     */
    public static function orderby_nav(): string {
        parse_str(parse_url($_SERVER['REQUEST_URI'],PHP_URL_QUERY), $output);
        $output['orderby'] = isset($_GET['orderby']) ? $_GET['orderby'] : "";
        $output['order'] = isset($_GET['order']) ? $_GET['order'] : "";
        return http_build_query($output);
    }

    /**
     * Funktion um diverse Dinge aus Tabellen auszaehlen zu lassen
     * @param string $db
     * @param string $where
     * @param string $what
     * @param array $sql_std
     * @return int
     */
    public static function cnt(string $db,string $where = "",string $what = "id",array $sql_std= []) {
        $cnt = self::$sql['default']->fetch("SELECT COUNT(`".$what."`) AS `cnt` FROM `".$db."` ".$where.";",$sql_std,'cnt');
        if(self::$sql['default']->rowCount() >= 1) {
            return $cnt;
        }

        return 0;
    }

    /**
     * Funktion um diverse Dinge aus Tabellen zusammenzaehlen zu lassen
     * @param string $db
     * @param string $where (optional)
     * @param string $what (optional)
     * @param array $sql_std (optional)
     * @return int
     */
    public static function sum(string $db,string $where = "",string $what = "id",array $sql_std=[]) {
        $sum = self::$sql['default']->fetch("SELECT SUM(`".$what."`) AS `sum` FROM `".$db."` ".$where.";",$sql_std,'sum');
        if(self::$sql['default']->rowCount() >= 1) {
            return $sum;
        }

        return 0;
    }

    /**
     * @param string $str
     * @param int $width (optional)
     * @param string $break (optional)
     * @param bool $cut (optional)
     * @return string
     */
    public static function wrap(string $str,int $width = 75,string $break = "\n",bool $cut = true): string
    {
        return strtr(str_replace(htmlentities($break), $break, htmlentities(wordwrap(html_entity_decode($str), $width, $break, $cut), ENT_QUOTES)),
            array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_COMPAT)));
    }

    /**
     * @param int $level
     * @return string
     * @throws SmartyException
     */
    public static function level_select(int $level = 1): string
    {
        $elevel = '';
        $levels = [1 => _status_user, 2 => _status_trial, 3 => _status_member, 4 => _status_admin];
        foreach ($levels as $id => $var) {
            $elevel .= self::select_field($id, ($level == $id),$var);
        }

        return $elevel;
    }

    /**
     * @param string $var
     * @param array $search
     * @return bool
     */
    public static function array_var_exists(string $var,array $search): bool {
        foreach($search as $key => $var_) {
            if($var_==$var) return true;
        }
        return false;
    }

    /**
     * Prueft ob der User ein Rootadmin ist
     * @param int $userid (optional)
     * @return bool
     */
    public static function rootAdmin(int $userid=0): bool
    {
        $userid = (!$userid ? self::userid() : $userid);
        if (!count(config::$rootAdmins)) { return false; }
        return in_array($userid, config::$rootAdmins);
    }

    /**
     * Languagefiles einlesen
     * @param string $lng (optional)
     * @param bool $refresh (optional)
     */
    public static function lang(string $lng = 'de', bool $refresh = false) {
        global $language_text;

        $language_text = array(); $charset = 'utf-8';
        $cache_hash = md5('system_lang_'.$lng);
        if(!self::$cache->AutoMemExists($cache_hash) || !config::$use_system_cache || $refresh) {
            require_once(basePath . "/inc/lang/global.php");
            require_once(basePath . "/inc/lang/uk.php"); //Load Base Language

            if ($lng != 'en' && file_exists(basePath . "/inc/lang/" . $lng . ".php")) {
                include(basePath . "/inc/lang/" . $lng . ".php");
                if (config::$use_system_cache) {
                   self::$cache->AutoMemSet($cache_hash, serialize(['language' => $language_text, 'charset' => $charset]), cache::TIME_LANGUAGE);
                }
            } else if ($lng == 'en') {
                self::$cache->AutoMemSet($cache_hash, serialize(['language' => $language_text, 'charset' => $charset]), cache::TIME_LANGUAGE);
            }
        } else {
            $language_cache_text = unserialize(self::$cache->AutoMemGet($cache_hash));
            $language_text = $language_cache_text['language'];
            $charset = $language_cache_text['charset'];
            unset($language_cache_text);
        }

        //Set Base-Content-type header
        header("Content-type: text/html; charset=".$charset);

        if(config::$use_additional_dir) {
            //-> Neue Languages einbinden, sofern vorhanden
            if ($language_files = self::get_files(basePath . '/inc/additional-languages/' . $lng . '/', false, true, array('php'))) {
                foreach ($language_files AS $languages) {
                    if (is_file(basePath . '/inc/additional-languages/' . $lng . '/' . $languages))
                        require_once(basePath . '/inc/additional-languages/' . $lng . '/' . $languages);
                }
                unset($language_files, $languages);
            }
        }

        foreach ($language_text as $key => $text) {
            if(!defined($key)) {
                define($key,$text);
            }
        } unset($key,$text);
    }

    /**
     * Auslesen der UserID
     * @return integer
     **/
    public static function userid(): int
    {
        if (empty($_SESSION['id']) || empty($_SESSION['pwd']) || !self::HasDSGVO()) { return 0; }
        $user = self::getUserIndex((int)$_SESSION['id']); //Load user from Mem/Netcache
        if(count($user) > 2) {
            return $user['id'];
        }

        return 0;
    }

    /**
     * Wandelt bytes in eine lesbare Größe.
     * @param int $bytes
     * @param int $decimals (optional)
     * @return string
     */
    public static function parser_filesize(int $bytes, int $decimals = 2) {
        $size = ['B','kB','MB','GB','TB','PB','EB','ZB','YB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . (array_key_exists((int)$factor,$size) ? $size[(int)$factor] : '');
    }

    /**
     * Gibt den Wartungsmodus aus.
     * @param string $title
     * @return mixed|string
     * @throws SmartyException
     */
    public static function wmodus(string $title) {
        $smarty = self::getSmarty(true);

        // JS-Dateine einbinden * json *
        $java_vars = '<script language="javascript" type="text/javascript">var json=\''.javascript::encode().'\';</script>'."\n";
        $java_vars .= '<script language="javascript" type="text/javascript" src="../vendor/ckeditor/ckeditor/ckeditor.js"></script>'."\n";
        $java_vars .= '<script language="javascript" type="text/javascript" src="../vendor/ckeditor/ckeditor/adapters/jquery.js"></script>'."\n";

        //Sicherheitsscode
        $secure='';
        if(settings::get('securelogin')) {
            $smarty->caching = false;
            $secure = $smarty->fetch('file:['.self::$tmpdir.']user/access/secure.tpl');
        }

        $smarty->caching = false;
        $smarty->assign('secure',$secure);
        $login = $smarty->fetch('file:['.self::$tmpdir.']errors/wmodus_login.tpl');
        $smarty->clearAllAssign();

        if(self::HasDSGVO()) {
            cookie::save(); //Save Cookie
        }

        $smarty->caching = false;
        $smarty->assign('java_vars',$java_vars);
        $smarty->assign('title',strip_tags(stringParser::decode($title)));
        $smarty->assign('login',$login);
        $wmodus = $smarty->fetch('file:['.common::$tmpdir.']errors/wmodus.tpl');
        $smarty->clearAllAssign();

        return $wmodus;
    }

    /**
     * @return APIClientMethods
     */
    public static function getServer(): ?APIClientMethods
    {
        return self::$server;
    }

    /**
     * @return string|string[]
     */
    public static function getAction()
    {
        return self::$action;
    }

    /**
     * @return dzcp_network_api|null
     */
    public static function getApi(): ?dzcp_network_api
    {
        return self::$api;
    }

    /**
     * @return Cache|null
     */
    public static function getCache(): ?Cache
    {
        return self::$cache;
    }

    /**
     * @return GUMP|null
     */
    public static function getGump(): ?GUMP
    {
        return self::$gump;
    }

    /**
     * @return int
     */
    public static function getPage(): int
    {
        return self::$page;
    }

    /**
     * Ausgabe des Indextemplates
     * @param string $index
     * @param string $title (optional)
     * @param string $where (optional)
     * @param string $template (optional)
     * @throws SmartyException
     */
    public static final function page(string $index,string $title='',string $where='',string $template='index') {
        //JS SetOptions & language for CKEditor
        switch ($_SESSION['language']) {
            case 'uk': javascript::set('lng','en'); break;
            default:
                if(file_exists(basePath.'/vendor/ckeditor/ckeditor/lang/'.
                    strtolower($_SESSION['language']).'.js')) {
                    javascript::set('lng',$_SESSION['language']);
                } else {
                    javascript::set('lng','en');
                }
            break;
        }

        javascript::set('dsgvo',!array_key_exists('do_show_dsgvo',$_SESSION) || !$_SESSION['do_show_dsgvo'] ? 1 : 0);
        javascript::set('maxW',settings::get('maxwidth'));
        javascript::set('autoRefresh',1);  // Enable Auto-Refresh for Ajax
        javascript::set('debug',config::$view_javascript_debug);  // Enable JS Debug
        javascript::set('dir',self::$designpath);  // Designpath
        javascript::set('dialog_button_00',_yes);
        javascript::set('dialog_button_01',_no);

        //Check Wartungsmodus
        if(settings::get("wmodus") && self::$chkMe != 4) {
            $_SESSION['DSGVO'] = true;
            $_SESSION['do_show_dsgvo'] = true;
            $index = self::wmodus($title);
        } else {
            $where = preg_replace_callback("#autor_(.*?)$#", function($id) {
                     return stringParser::decode(common::data("nick","$id[1]"));
                },
                $where);

            if(!self::$CrawlerDetect->isCrawler() && self::HasDSGVO()) {
                self::updateCounter();
                self::update_maxonline();
                self::update_online($where);
            }

            // JS-Dateine einbinden * json *
            $java_vars = '<script language="javascript" type="text/javascript">var json=\''.javascript::encode().'\';</script>'."\n";
            $java_vars .= '<script language="javascript" type="text/javascript" src="../vendor/ckeditor/ckeditor/ckeditor.js"></script>'."\n";
            $java_vars .= '<script language="javascript" type="text/javascript" src="../vendor/ckeditor/ckeditor/adapters/jquery.js"></script>'."\n";

            //check permissions
            $check_msg = '';
            if(self::$chkMe) {
                $check_msg = self::check_msg();
                self::set_lastvisit(self::$userid);
            }

            //Check internal_url
            if (self::check_internal_url()) {
                $index = self::error(_error_have_to_be_logged);
            }

            $smarty = self::getSmarty(true);
            $smarty->caching = false;
            $smarty->assign('check_msg',empty($check_msg) ? '' : $check_msg);
            $smarty->assign('index',$index);
            $smarty->assign('notification',notification::get());
            $smarty->assign('clanname',stringParser::decode(settings::get("clanname")));
            $smarty->assign('title',strip_tags($title));
            $smarty->assign('java_vars',$java_vars,true);
            $smarty->assign('regen',isset($_GET['less_regen']) ? '&refresh=1' : '');
            $smarty->assign('where',$where);
            $smarty->assign('lock',self::HasDSGVO(),true);
            $smarty->assign('templateswitch',self::permission('templateswitch'),true);
            if($template != 'index' && file_exists(self::$designpath.'/'.$template.'.tpl')) {
                $index = $smarty->fetch('file:['.common::$tmpdir.']'.$template.'.tpl');
            } else {
                $index = $smarty->fetch('file:['.common::$tmpdir.']index.tpl');
            }
            $smarty->clearAllAssign();
        }

        //Save Cookie
        if(self::HasDSGVO()) {
            cookie::save();
        }

        DebugConsole::insert_info('common::page()','Memory Usage: '.self::parser_filesize(memory_get_usage()));
        DebugConsole::insert_info('common::page()','Memory-Peak Usage: '.self::parser_filesize(memory_get_peak_usage()));
        DebugConsole::insert_info('common::page()',sprintf("Page generated in %.8f seconds", (getmicrotime() - start_time)));

        //index output
        $index = (!self::$chkMe ? preg_replace("|<logged_in>.*?</logged_in>|is", "", $index) :
            preg_replace("|<logged_out>.*?</logged_out>|is", "", $index));
        $index = str_ireplace(["<logged_in>","</logged_in>","<logged_out>","</logged_out>"], '', $index);

        if (config::$debug_save_to_file) {
            DebugConsole::save_log();
        } //Debug save to file
        $output = config::$view_error_reporting || DebugConsole::get_warning_enable() ? DebugConsole::show_logs().$index : $index; //Debug Console + Index Out

        if(!array_key_exists('do_show_dsgvo',$_SESSION)) {
           $_SESSION['do_show_dsgvo'] = true;
        }

        //DEV
        if(isset($_GET['reset']))
            self::dzcp_session_destroy();

        exit($output); //Exit
    }
}

/**
 * ###########################################################
 *                       API Loader
 * ###########################################################
 */

//-> Neue Funktionen einbinden, sofern vorhanden
if(config::$use_additional_dir) {
    if ($functions_files = common::get_files(basePath . '/inc/additional-functions/', false, true, ['php'])) {
        foreach ($functions_files AS $func) {
            include_once(basePath . '/inc/additional-functions/' . $func);
        }
        unset($functions_files, $func);
    }
}

/**
 * Class Smarty_CacheResource_FastCache
 */
class Smarty_CacheResource_FastCache extends Smarty_CacheResource_KeyValueStore {
    /**
     * Read values for a set of keys from cache
     *
     * @param array $keys list of keys to fetch
     * @return array list of values with the given keys used as indexes
     * @return boolean true on success, false on failure
     */
    protected function read(array $keys)
    {
        $_keys = $lookup = array();
        foreach ($keys as $k) {
            $_k = sha1($k);
            $_keys[] = $_k;
            $lookup[$_k] = $k;
        }
        $_res = array();

        $res = [];
        foreach ($_keys as $k) {
            if(common::$cache->AutoExists($k)) {
                $res[$k] = common::$cache->AutoGet($k);
            }
        }

        foreach ($res as $k => $v) {
            $_res[$lookup[$k]] = $v;
        }
        return $_res;
    }

    /**
     * Save values for a set of keys to cache
     *
     * @param array $keys list of values to save
     * @param int $expire expiration time
     * @return boolean true on success, false on failure
     */
    protected function write(array $keys, $expire=null)
    {
        foreach ($keys as $k => $v) {
            $k = sha1($k);
            common::$cache->AutoSet($k,$v,$expire);
        }
        return true;
    }

    /**
     * Remove values from cache
     *
     * @param array $keys list of keys to delete
     * @return boolean true on success, false on failure
     */
    protected function delete(array $keys)
    {
        foreach ($keys as $k) {
            $k = sha1($k);
            common::$cache->AutoDelete($k);
        }
        return true;
    }
}