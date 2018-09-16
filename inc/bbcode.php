<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

//Filter 404
$filter404 = strtolower(GetServerVars("REQUEST_URI"));
if (strpos($filter404, 'index.php/') !== false ||
    strpos($filter404, 'ajax.php/') !== false) {
    header("HTTP/1.0 404 Not Found");
    exit();
} unset($filter404);

## INCLUDES/REQUIRES ##
require_once(basePath . '/inc/_version.php');
require_once(basePath . "/inc/cookie.php");
require_once(basePath . '/inc/server_query/_functions.php');
require_once(basePath . "/inc/teamspeak_query.php");
require_once(basePath . '/inc/steamapi.php');
require_once(basePath . '/inc/api.php');

//Libs
use Phpfastcache\CacheManager;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use PHPMailer\PHPMailer\PHPMailer;

//Set Build
$_build = str_replace('.','',_version);
$_release = substr(_release, 0, -5);
define('_build', $_build.'.'.$_release);

## Is AjaxJob ##
$ajaxJob = (!isset($ajaxJob) ? false : $ajaxJob);

//Set DSGVO to false
if (!$ajaxJob && !array_key_exists('DSGVO', $_SESSION)) {
    $_SESSION['DSGVO'] = false;
}

//Set DSGVO Lock
if (!$ajaxJob && !array_key_exists('user_has_dsgvo_lock', $_SESSION)) {
    $_SESSION['user_has_dsgvo_lock'] = false;
}

//Check is DSGVO Set?
if (isset($_GET['dsgvo'])) {
    switch ((int)$_GET['dsgvo']) {
        case 1:
            $_SESSION['DSGVO'] = true;
            $_SESSION['do_show_dsgvo'] = true;
            header("Location: " . GetServerVars('HTTP_REFERER'));
            exit();
            break;
        default:
            $_SESSION['DSGVO'] = false;
            $_SESSION['do_show_dsgvo'] = true;
            $_SESSION['user_has_dsgvo_lock'] = false;
            header("Location: " . GetServerVars('HTTP_REFERER'));
            exit();
    }
}

// Cache
$cache = CacheManager::getInstance($config_cache['storage'], $config_cache['config'],'default');

//-> Settingstabelle auslesen * Use function settings('xxxxxx');
if (!dbc_index::issetIndex('settings')) {
    $get_settings = db("SELECT * FROM `" . $db['settings'] . "`;", false, true);
    dbc_index::setIndex('settings', $get_settings);
    unset($get_settings);
}

//-> Configtabelle auslesen * Use function config('xxxxxx');
if (!dbc_index::issetIndex('config')) {
    $get_config = db("SELECT * FROM `" . $db['config'] . "`;", false, true);
    dbc_index::setIndex('config', $get_config);
    unset($get_config);
}

$isSecure_cheked = ['check' => false, 'isSecure' => false];
if (HasDSGVO()) {
//-> Cookie initialisierung
    cookie::init('dzcp_' . settings('prev'), false, "/", re(settings('i_domain')));
    $isSecure_cheked['isSecure'] = cookie::$secure;
    $isSecure_cheked['check'] = true;
}

//-> SteamAPI
SteamAPI::set('apikey', re(settings('steam_api_key')));

//-> Language auslesen
if (array_key_exists('language', $_SESSION) && !empty($_SESSION['language'])) {
    if (!file_exists(basePath . '/inc/lang/languages/' . $_SESSION['language'] . '.php')) {
        $_SESSION['language'] = re(settings('language'));
    }
} else {
    $_SESSION['language'] = re(settings('language'));
}

//-> einzelne Definitionen
$CrawlerDetect = new CrawlerDetect();
$isSpider = $CrawlerDetect->isCrawler();
$subfolder = basename(dirname(dirname(GetServerVars('PHP_SELF')) . '../'));
$httphost = GetServerVars('HTTP_HOST') . (empty($subfolder) ? '' : '/' . $subfolder);
$domain = str_replace('www.', '', $httphost);
$pagetitle = settings('pagetitel');
$sdir = settings('tmpdir');
$useronline = 1800;
$reload = 3600 * 24;
$datum = time();
$today = date("j.n.Y");
$picformat = array("jpg", "gif", "png");
$userip = HasDSGVO() ? visitorIp() : '0.0.0.0';
$maxpicwidth = 90;
$maxadmincw = 10;
$maxfilesize = @ini_get('upload_max_filesize');
$search_forum = false;
$api = new api('api.dzcp.de');

//-> Global
$action = isset($_GET['action']) ? strtolower($_GET['action']) : '';
$page = (isset($_GET['page']) && (int)($_GET['page']) >= 1) ? (int)($_GET['page']) : 1;
$do = isset($_GET['do']) ? strtolower($_GET['do']) : '';
$index = ''; $show = ''; $color = 0;

//-> Auslesen der Cookies und automatisch anmelden
if (HasDSGVO() && (cookie::get('id') != false && cookie::get('pkey') != false && empty($_SESSION['id']) && !checkme())) {
    //-> User aus der Datenbank suchen
    $sql = db_stmt("SELECT `id`,`user`,`nick`,`pwd`,`email`,`level`,`time`,`pkey`,`dsgvo_lock`,`language` FROM `" .
        $db['users'] . "` WHERE `id` = ? AND `pkey` = ? AND `level` != 0;",
        array('is', cookie::get('id'), cookie::get('pkey')));
    if (_rows($sql)) {
        $get = _fetch($sql);
        if ($get['dsgvo_lock']) {
            $_SESSION['user_has_dsgvo_lock'] = true;
            $_SESSION['dsgvo_lock_permanent_login'] = true;
            $_SESSION['dsgvo_lock_login_id'] = $get['id'];
            if (!array_key_exists('dsgvo_lock_login_id', $_SESSION))
                header("Location: ?action=userlock");
        } else {
            //-> Generiere neuen permanent-key - sha256
            $permanent_key = hash('sha256', mkpwd(12));
            cookie::put('pkey', $permanent_key);
            cookie::save();

            //-> Schreibe Werte in die Server Sessions
            $_SESSION['id'] = $get['id'];
            $_SESSION['pwd'] = $get['pwd'];
            $_SESSION['lastvisit'] = $get['time'];
            $_SESSION['ip'] = $userip;

            if (!empty($get['language'])) {
                $_SESSION['language'] = re($get['language']);
            }

            if (data("ip", $get['id']) != $_SESSION['ip'])
                $_SESSION['lastvisit'] = data("time", $get['id']);

            if (empty($_SESSION['lastvisit']))
                $_SESSION['lastvisit'] = data("time", $get['id']);

            //-> Aktualisiere Datenbank
            db("UPDATE `" . $db['users'] . "` SET `online` = 1, `sessid` = '" . session_id() . "', `ip` = '" . $userip . "', `pkey` = '" . $permanent_key . "' WHERE `id` = " . $get['id'] . ";");

            //-> Aktualisiere die User-Statistik
            db("UPDATE `" . $db['userstats'] . "` SET `logins` = (logins+1) WHERE `user` = " . $get['id'] . ";");
            unset($get, $permanent_key);
        }
    } else {
        $_SESSION['id'] = '';
        $_SESSION['pwd'] = '';
        $_SESSION['ip'] = '';
        $_SESSION['lastvisit'] = '';
        $_SESSION['pkey'] = '';
    }

    unset($sql);
}

//Check UserID & Level
$userid = userid();
$chkMe = checkme();

//-> Sprache aendern
if (isset($_GET['set_language'])) {
    if (file_exists(basePath . "/inc/lang/languages/" . $_GET['set_language'] . ".php")) {
        $_SESSION['language'] = $_GET['set_language'];
    }

    if ($chkMe && $userid) {
        db("UPDATE `" . $db['users'] . "` SET `language` = '" . $_SESSION['language'] . "' WHERE `id` = " . $userid . ";");
    }

    header("Location: " . GetServerVars('HTTP_REFERER'));
}

lang(strval($_SESSION['language'])); //Lade Sprache

if (!$chkMe) {
    $_SESSION['id'] = '';
    $_SESSION['pwd'] = '';
    $_SESSION['ip'] = '';
    $_SESSION['lastvisit'] = '';
    $_SESSION['identy_id'] = '';
}

//-> Prueft ob der User gebannt ist, oder die IP des Clients warend einer offenen session verändert wurde.
if ($chkMe && $userid && !empty($_SESSION['ip'])) {
    if ($_SESSION['ip'] != visitorIp() || isBanned($userid, false)) {
        $_SESSION['id'] = '';
        $_SESSION['pwd'] = '';
        $_SESSION['ip'] = '';
        $_SESSION['lastvisit'] = '';
        $_SESSION['identy_id'] = '';
        session_reset();
        if (HasDSGVO()) {
            cookie::clear();
        }
        header("Location: ../news/");
    }
}

/**
 * Prüft ob die DSGVO akzeptiert wurde
 * @return bool
 */
function HasDSGVO() {
    if (array_key_exists('DSGVO', $_SESSION) && $_SESSION['DSGVO'])
        return true;

    return false;
}

function isSecure() {
    if (GetServerVars('HTTPS') && GetServerVars('HTTPS') == 'on') {
        return true;
    } elseif ((GetServerVars('HTTP_X_FORWARDED_PROTO') && GetServerVars('HTTP_X_FORWARDED_PROTO') == 'https') ||
        (GetServerVars('HTTP_X_FORWARDED_SSL') && GetServerVars('HTTP_X_FORWARDED_SSL') == 'on')) {
        return true;
    }

    return false;
}

function hasSecure() {
    if(use_ssl_auto_redirect) {
        if (ping_port(GetServerVars('HTTP_HOST'), 443, 0.1)) {
            return true;
        }
    }

    return false;
}

//Weiterleitung zu einer SSL Verbindung
if(!isSecure() && use_ssl_auto_redirect && !$ajaxJob && !$installation && !$updater ) {
    if(!$isSecure_cheked['check']) {
        if(hasSecure()) {
            header("Location: https://" . GetServerVars('HTTP_HOST') .
                GetServerVars('REQUEST_URI'));
            exit();
        }
    }

    if($isSecure_cheked['check'] && $isSecure_cheked['isSecure']) {
        header("Location: https://" . GetServerVars('HTTP_HOST') .
            GetServerVars('REQUEST_URI'));
        exit();
    }
}

/**
 * Gibt die IP des Besuchers / Users zurück
 * Forwarded IP Support
 */
function visitorIp() {
    if (array_key_exists('identy_ip', $_SESSION)) {
        if (!empty($_SESSION['identy_ip']))
            return $_SESSION['identy_ip'];
    }

    $TheIp = GetServerVars('REMOTE_ADDR');
    if (GetServerVars('HTTP_X_FORWARDED_FOR') &&
        !empty(GetServerVars('HTTP_X_FORWARDED_FOR')))
        $TheIp = GetServerVars('HTTP_X_FORWARDED_FOR');

    if (GetServerVars('HTTP_CLIENT_IP') &&
        !empty(GetServerVars('HTTP_CLIENT_IP')))
        $TheIp = GetServerVars('HTTP_CLIENT_IP');

    if (GetServerVars('HTTP_FROM') &&
        !empty(GetServerVars('HTTP_FROM')))
        $TheIp = GetServerVars('HTTP_FROM');

    $TheIp_X = explode('.', $TheIp);
    if (count($TheIp_X) == 4 && $TheIp_X[0] <= 255 && $TheIp_X[1] <= 255 &&
        $TheIp_X[2] <= 255 && $TheIp_X[3] <= 255 &&
        preg_match("!^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$!", $TheIp))
        return trim($TheIp);

    return '0.0.0.0';
}

/**
 * Pruft eine IP gegen eine IP-Range
 * @param string $ip
 * @param string|array $range
 * @return boolean
 */
function validateIpV4Range(string $ip, $range) {
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
 * Funktion um notige Erweiterungen zu prufen
 * @return boolean
 **/
function fsockopen_support() {
    if (fsockopen_support_bypass) return true;

    if (disable_functions('fsockopen') || disable_functions('fopen'))
        return false;

    return true;
}

function disable_functions(string $function = '')
{
    if (!function_exists($function)) return true;
    $disable_functions = ini_get('disable_functions');
    if (empty($disable_functions)) return false;
    $disabled_array = explode(',', $disable_functions);
    foreach ($disabled_array as $disabled) {
        if (strtolower(trim($function)) == strtolower(trim($disabled)))
            return true;
    }

    return false;
}

function allow_url_fopen_support() {
    if (ini_get('allow_url_fopen') == 1)
        return true;

    return false;
}

//-> Auslesen der UserID
function userid() {
    global $db;
    if (HasDSGVO()) {
        if (empty($_SESSION['id']) || empty($_SESSION['pwd'])) return 0;
        if (!dbc_index::issetIndex('user_' . $_SESSION['id'])) {
            $sql = db("SELECT * FROM `" . $db['users'] . "` WHERE `id` = " . $_SESSION['id'] . " AND `pwd` = '" . $_SESSION['pwd'] . "';");
            if (!_rows($sql)) return 0;
            $get = _fetch($sql);
            dbc_index::setIndex('user_' . $get['id'], $get);
            return $get['id'];
        }

        return dbc_index::getIndexKey('user_' . $_SESSION['id'], 'id');
    }

    return 0;
}

function sysTemplateswitch() {
    global $chkMe;
    $files = get_files(basePath . '/inc/_templates_/', true);
    foreach ($files as $templ) {
        $xml = simplexml_load_file(basePath . '/inc/_templates_/' . $templ . '/template.xml');
        if (!empty((string)$xml->permissions) && (string)$xml->permissions != 'null') {
            if (permission((string)$xml->permissions) || ((int)$xml->level >= 1 && $chkMe >= (int)$xml->level)) {
                if ($templ == $_GET['tmpl_set']) {
                    $_SESSION['tmpdir'] = $templ;
                    if (HasDSGVO()) {
                        cookie::put('tmpdir', $templ);
                        cookie::save();
                    }
                    header("Location: " . GetServerVars('HTTP_REFERER'));
                }
            }
        } else if ((int)$xml->level >= 1 && $chkMe >= (int)$xml->level) {
            if ($templ == $_GET['tmpl_set']) {
                $_SESSION['tmpdir'] = $templ;
                if (HasDSGVO()) {
                    cookie::put('tmpdir', $templ);
                    cookie::save();
                }
                header("Location: " . GetServerVars('HTTP_REFERER'));
            }
        } else if (!(int)$xml->level) {
            if ($templ == $_GET['tmpl_set']) {
                $_SESSION['tmpdir'] = $templ;
                if (HasDSGVO()) {
                    cookie::put('tmpdir', $templ);
                    cookie::save();
                }
                header("Location: " . GetServerVars('HTTP_REFERER'));
            }
        }

    }

    unset($xml, $templ);
}

function GetServerVars(string $var) {
    if (array_key_exists($var, $_SERVER) && !empty($_SERVER[$var])) {
        return utf8_encode($_SERVER[$var]);
    } else if (array_key_exists($var, $_ENV) && !empty($_ENV[$var])) {
        return utf8_encode($_ENV[$var]);
    }

    if ($var == 'HTTP_REFERER') { //Fix for empty HTTP_REFERER
        return GetServerVars('REQUEST_SCHEME') . '://' . GetServerVars('HTTP_HOST') .
            GetServerVars('DOCUMENT_URI');
    }

    return false;
}

if (isset($_GET['tmpl_set'])) {
    sysTemplateswitch();
}

if (!empty($_SESSION['tmpdir'])) {
    if (!empty($_SESSION['tmpdir'])) {
        if (file_exists(basePath . "/inc/_templates_/" . $_SESSION['tmpdir']))
            $tmpdir = $_SESSION['tmpdir'];
        else
            $tmpdir = $files[0];
    } else
        $tmpdir = $files[0];
} else {
    if (file_exists(basePath . "/inc/_templates_/" . $sdir))
        $tmpdir = $sdir;
    else
        $tmpdir = $files[0];

    if (!array_key_exists('tmpdir', $_SESSION)) {
        $_SESSION['tmpdir'] = $tmpdir;
    }
}
unset($files);

$designpath = '../inc/_templates_/' . $tmpdir;

//-> Languagefiles einlesen
function lang(string $lng) {
    global $gump;
    if (!file_exists(basePath . "/inc/lang/languages/" . $lng . ".php")) {
        $files = get_files(basePath . '/inc/lang/languages/', false, true, array('php'));
        $lng = str_replace('.php', '', $files[0]);
    }

    $language_text = array();
    $charset = 'utf-8';
    require_once(basePath . "/inc/lang/global.php");
    require_once(basePath . "/inc/lang/languages/english.php"); //Load Base Language
    require_once(basePath . "/inc/lang/languages/dsgvo/english.php"); //Load Base DSGVO
    require_once(basePath . "/inc/lang/languages/" . $lng . ".php");

    if (file_exists(basePath . "/inc/lang/languages/dsgvo/" . $lng . ".php"))
        require_once(basePath . "/inc/lang/languages/dsgvo/" . $lng . ".php");

    //Set bBase-Content-type header
    header("Content-type: text/html; charset=" . $charset);

    //Set language for GUMP
    $gump->language(language_short_tag());

    //-> Neue Languages einbinden, sofern vorhanden
    if ($language_files = get_files(basePath . '/inc/additional-languages/' . $lng . '/', false, true, array('php'))) {
        foreach ($language_files AS $languages) {
            if (file_exists(basePath . '/inc/additional-languages/' . $lng . '/' . $languages))
                include_once(basePath . '/inc/additional-languages/' . $lng . '/' . $languages);
        }
        unset($language_files, $languages);
    }

    foreach ($language_text as $key => $text) {
        if (!defined($key)) {
            define($key, $text);
        }
    }
    unset($language_text, $key, $text);
}

//->Daten uber file_get_contents oder curl abrufen
function get_external_contents(string $url, $post = false, bool $nogzip = false, int $timeout = file_get_contents_timeout) {
    if (!allow_url_fopen_support() && (!extension_loaded('curl') || !use_curl_support))
        return false;

    $url_p = @parse_url($url);
    $host = $url_p['host'];
    $port = isset($url_p['port']) ? $url_p['port'] : 80;
    $port = (($url_p['scheme'] == 'https' && $port == 80) ? 443 : $port);
    if (!ping_port($host, $port, $timeout)) return false;

    if (extension_loaded('curl') && use_curl_support) {
        if (!$curl = curl_init())
            return false;

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_USERAGENT, "DZCP");

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout * 2); // x 2

        //For POST
        /** @var TYPE_NAME $post */
        if ($post != false && count($post) >= 1) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            curl_setopt($curl, CURLOPT_VERBOSE, 0);
        }

        $gzip = false;

        if (function_exists('gzinflate') && !$nogzip) {
            $gzip = true;
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Encoding: gzip,deflate'));
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        }

        if ($url_p['scheme'] == 'https') { //SSL
            curl_setopt($curl, CURLOPT_PORT, $port);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (!($content = curl_exec($curl)) || empty($content)) {
            return false;
        }

        if ($gzip) {
            $org_content = $content;
            $curl_info = curl_getinfo($curl, CURLINFO_HEADER_OUT);
            if (stristr($curl_info, 'accept-encoding') && stristr($curl_info, 'gzip') && !$nogzip) {
                $content = @gzinflate(substr($content, 10, -8));
                if (!$content)
                    $content = $org_content;
            }
        }

        @curl_close($curl);
        unset($curl);
    } else {
        if ($url_p['scheme'] == 'https') //HTTPS not Supported!
            $url = str_replace('https', 'http', $url);

        $opts = array();
        $opts['http']['method'] = "GET";
        $opts['http']['timeout'] = $timeout * 2;

        $gzip = false;

        if (function_exists('gzinflate') && !$nogzip) {
            $gzip = true;
            $opts['http']['header'] = 'Accept-Encoding:gzip,deflate' . "\r\n";
        }

        $context = stream_context_create($opts);
        $content = file_get_contents($url, false, $context);
        if (!$content || empty($content)) {
            return false;
        }
        $content = substr($content, -1, 40000);

        if ($gzip) {
            foreach ($http_response_header as $c => $h) {
                if (stristr($h, 'content-encoding') && stristr($h, 'gzip')) {
                    $content = gzinflate(substr($content, 10, -8));
                }
            }
        }
    }

    return $content;
}

//-> Sprachdateien auflisten
function languages() {
    $lang = "";
    $files = get_files('../inc/lang/languages/', false, true, array('php'));
    for ($i = 0; $i <= count($files) - 1; $i++) {
        $file = str_replace('.php', '', $files[$i]);
        $upFile = strtoupper(substr($file, 0, 1)) . substr($file, 1);
        if (file_exists('../inc/lang/flaggen/' . $file . '.gif'))
            $lang .= '<a href="?set_language=' . $file . '"><img src="../inc/lang/flaggen/' . $file . '.gif" alt="' . $upFile . '" title="' . $upFile . '" class="icon" /></a> ';
    }

    return $lang;
}

//-> Userspezifiesche Dinge
if ($userid >= 1 && $ajaxJob != true && HasDSGVO()) {
    db("UPDATE `" . $db['userstats'] . "` SET `hits` = (hits+1), `lastvisit` = '" . ((int)$_SESSION['lastvisit']) . "' WHERE `user` = " . $userid . ";");
}

//-> Settings auslesen
function settings($what, bool $use_dbc = true) {
    global $db;

    if (is_array($what)) {
        if ($use_dbc)
            $dbd = dbc_index::getIndex('settings');
        else
            $dbd = db("SELECT * FROM `" . $db['settings'] . "`;", false, true);

        $return = array();
        foreach ($dbd as $key => $var) {
            if (!in_array($key, $what))
                continue;

            $return[$key] = $var;
        }

        return $return;
    } else {
        if ($use_dbc)
            return dbc_index::getIndexKey('settings', $what);

        $get = db("SELECT `" . $what . "` FROM `" . $db['settings'] . "`;", false, true);
        return $get[$what];
    }
}

//-> Config auslesen
function config($what, bool $use_dbc = true) {
    global $db;

    if (is_array($what)) {
        if ($use_dbc)
            $dbd = dbc_index::getIndex('config');
        else
            $dbd = db("SELECT * FROM `" . $db['config'] . "`;", false, true);

        $return = array();
        foreach ($dbd as $key => $var) {
            if (!in_array($key, $what))
                continue;

            $return[$key] = $var;
        }

        return $return;
    } else {
        if ($use_dbc)
            return dbc_index::getIndexKey('config', $what);

        $query = db("SELECT `" . $what . "` FROM `" . $db['config'] . "`;");
        if (_rows($query)) {
            $get = _fetch($query);
            return $get[$what];
        }
    }

    return 0;
}

//-> Prueft ob der User ein Rootadmin ist
function rootAdmin(int $userid = 0) {
    global $rootAdmins;
    $userid = !$userid ? userid() : $userid;
    if (!count($rootAdmins)) return false;
    return in_array($userid, $rootAdmins);
}

//-> PHP-Code farbig anzeigen
function highlight_text(string $txt) {
    while (preg_match("=\[php\](.*)\[/php\]=Uis", $txt) != FALSE) {
        preg_match("=\[php\](.*)\[/php\]=Uis", $txt, $matches);
        $src = $matches[1];
        $src = str_replace('<?php', '', $src);
        $src = str_replace('<?php', '', $src);
        $src = str_replace('?>', '', $src);
        $src = str_replace("&#39;", "'", $src);
        $src = str_replace("&#34;", "\"", $src);
        $src = str_replace("&amp;", "&", $src);
        $src = str_replace("&lt;", "<", $src);
        $src = str_replace("&gt;", ">", $src);
        $src = str_replace('<?php', '&#60;?', $src);
        $src = str_replace('?>', '?&#62;', $src);
        $src = str_replace("&quot;", "\"", $src);
        $src = str_replace("&nbsp;", " ", $src);
        $src = str_replace("&nbsp;", " ", $src);
        $src = str_replace("<p>", "\n", $src);
        $src = str_replace("</p>", "", $src);
        $l = explode("<br />", $src);
        $src = preg_replace("#\<br(.*?)\>#is", "\n", $src);
        $src = '<?php' . $src . ' ?>';
        $colors = array('#111111' => 'string', '#222222' => 'comment', '#333333' => 'keyword', '#444444' => 'bg', '#555555' => 'default', '#666666' => 'html');

        foreach ($colors as $color => $key)
            ini_set('highlight.' . $key, $color);

        // Farben ersetzen & highlighten
        $src = preg_replace('!style="color: (#\d{6})"!',
            '"class=\"".$prefix.$colors["\1"]."\""',
            highlight_string($src, TRUE));

        // PHP-Tags komplett entfernen
        $src = str_replace('&lt;?php', '', $src);
        $src = str_replace('?&gt;', '', $src);
        $src = str_replace('&amp;</span><span class="comment">#60;?', '&lt;?', $src);
        $src = str_replace('?&amp;</span><span class="comment">#62;', '?&gt;', $src);
        $src = str_replace('&amp;#60;?', '&lt;?', $src);
        $src = str_replace('?&amp;#62;', '?&gt;', $src);
        $src = str_replace(":", "&#58;", $src);
        $src = str_replace("(", "&#40;", $src);
        $src = str_replace(")", "&#41;", $src);
        $src = str_replace("^", "&#94;", $src);

        // Zeilen zaehlen
        $lines = "";
        for ($i = 1; $i <= count($l) + 1; $i++)
            $lines .= $i . ".<br />";

        // Ausgabe
        $code = '<div class="codeHead">&nbsp;&nbsp;&nbsp;Code:</div><div class="code"><table style="width:100%;padding:0px" cellspacing="0"><tr><td class="codeLines">' . $lines . '</td><td class="codeContent">' . $src . '</td></table></div>';
        $txt = preg_replace("=\[php\](.*)\[/php\]=Uis", $code, $txt, 1);
    }

    return $txt;
}

function regexChars(string $txt) {
    $txt = strip_tags($txt);
    $txt = str_replace('"', '&quot;', $txt);
    $txt = str_replace('\\', '\\\\', $txt);
    $txt = str_replace('<', '\<', $txt);
    $txt = str_replace('>', '\>', $txt);
    $txt = str_replace('/', '\/', $txt);
    $txt = str_replace('.', '\.', $txt);
    $txt = str_replace(':', '\:', $txt);
    $txt = str_replace('^', '\^', $txt);
    $txt = str_replace('$', '\$', $txt);
    $txt = str_replace('|', '\|', $txt);
    $txt = str_replace('?', '\?', $txt);
    $txt = str_replace('*', '\*', $txt);
    $txt = str_replace('+', '\+', $txt);
    $txt = str_replace('-', '\-', $txt);
    $txt = str_replace('(', '\(', $txt);
    $txt = str_replace(')', '\)', $txt);
    $txt = str_replace('[', '\[', $txt);
    $txt = str_replace(']', '\]', $txt);
    $txt = str_replace('}', '\}', $txt);
    $txt = str_replace('{', '\{', $txt);
    $txt = str_replace("\r", '', $txt);
    return str_replace("\n", '', $txt);
}

//-> Glossarfunktion
$use_glossar = true; //Global
function glossar_load_index() {
    global $db, $use_glossar;
    if (!$use_glossar) return false;

    $gl_words = array();
    $gl_desc = array();
    $qryglossar = db("SELECT `word`,`glossar` FROM `" . $db['glossar'] . "`;");
    while ($getglossar = _fetch($qryglossar)) {
        $gl_words[] = re($getglossar['word']);
        $gl_desc[] = $getglossar['glossar'];
    }

    dbc_index::setIndex('glossar', array('gl_words' => $gl_words, 'gl_desc' => $gl_desc));
    return true;
}

/**
 * @param $txt
 * @return mixed
 */
function glossar(string $txt) {
    global $gl_words, $gl_desc, $use_glossar, $ajaxJob;

    if (!$use_glossar || $ajaxJob)
        return $txt;

    if (!dbc_index::issetIndex('glossar'))
        glossar_load_index();

    $gl_words = dbc_index::getIndexKey('glossar', 'gl_words');
    $gl_desc = dbc_index::getIndexKey('glossar', 'gl_desc');

    $txt = str_replace('&#93;', ']', $txt);
    $txt = str_replace('&#91;', '[', $txt);

    // mark words
    if (is_array($gl_words)) {
        foreach ($gl_words as $gl_word) {
            $w = addslashes(regexChars(html_entity_decode($gl_word)));
            $txt = str_ireplace(' ' . $w . ' ', ' <tmp|' . $w . '|tmp> ', $txt);
            $txt = str_ireplace('>' . $w . '<', '> <tmp|' . $w . '|tmp> <', $txt);
            $txt = str_ireplace('>' . $w . ' ', '> <tmp|' . $w . '|tmp> ', $txt);
            $txt = str_ireplace(' ' . $w . '<', ' <tmp|' . $w . '|tmp> <', $txt);
        }

        // replace words
        for ($g = 0; $g <= count($gl_words) - 1; $g++) {
            $desc = regexChars($gl_desc[$g]);
            $info = 'onmouseover="DZCP.showInfo(\'' . up($desc) . '\')" onmouseout="DZCP.hideInfo()"';
            $w = regexChars(html_entity_decode($gl_words[$g]));
            $r = "<a class=\"glossar\" href=\"../glossar/?word=" . $gl_words[$g] . "\" " . $info . ">" . $gl_words[$g] . "</a>";
            $txt = str_ireplace('<tmp|' . $w . '|tmp>', $r, $txt);
        }
    }

    $txt = str_replace(']', '&#93;', $txt);
    return str_replace('[', '&#91;', $txt);
}

function bbcodetolow(array $founds) {
    return "[" . strtolower($founds[1]) . "]" . trim($founds[2]) . "[/" . strtolower($founds[3]) . "]";
}

//-> Replaces
function replace(string $txt, bool $type = false, bool $no_vid_tag = false) {
    $txt = str_replace("&#34;", "\"", $txt);

    if ($type)
        $txt = preg_replace("#<img src=\"(.*?)\" mce_src=\"(.*?)\"(.*?)\>#i", "<img src=\"$2\" alt=\"\">", $txt);

    $txt = preg_replace_callback("/\[(.*?)\](.*?)\[\/(.*?)\]/", "bbcodetolow", $txt);
    $var = array("/\[url\](.*?)\[\/url\]/",
        "/\[img\](.*?)\[\/img\]/",
        "/\[url\=(http\:\/\/)?(.*?)\](.*?)\[\/url\]/",
        "/\[b\](.*?)\[\/b\]/",
        "/\[i\](.*?)\[\/i\]/",
        "/\[u\](.*?)\[\/u\]/",
        "/\[color=(.*?)\](.*?)\[\/color\]/");

    $repl = array("<a href=\"$1\" target=\"_blank\">$1</a>",
        "<img src=\"$1\" class=\"content\" alt=\"\" />",
        "<a href=\"http://$2\" target=\"_blank\">$3</a>",
        "<b>$1</b>",
        "<i>$1</i>",
        "<u>$1</u>",
        "<span style=\"color:$1\">$2</span>");

    $txt = preg_replace($var, $repl, $txt);
    $txt = preg_replace_callback("#\<img(.*?)\>#", function ($img) {
        if (preg_match("#class#i", $img[1])) {
            return "<img" . $img[1] . ">";
        } else {
            return "<img class=\"content\"" . $img[1] . ">";
        }
    }, $txt);

    if (!$no_vid_tag) {
        $txt = preg_replace_callback("/\[youtube\](?:http?s?:\/\/)?(?:www\.)?youtu(?:\.be\/|be\.com\/watch\?v=)([A-Z0-9\-_]+)(?:&(.*?))?\[\/youtube\]/i",
            function ($match) {
                return '<object width="425" height="344"><param name="movie" value="//www.youtube.com/v/' . trim($match[1]) . '?hl=de_DE&amp;version=3&amp;rel=0"></param>
            < name="allowFullScreen" value="true">< name="allowscriptaccess" value="always">
            < src="//www.youtube.com/v/' . trim($match[1]) . '?hl=de_DE&amp;version=3&amp;rel=0" type="application/x-shockwave-flash" width="425" height="344" allowscriptaccess="always" allowfullscreen="true">
            </param></object>';
            }, $txt);
    }

    $txt = str_replace("\"", "&#34;", $txt);
    return preg_replace("#(\w){1,1}(&nbsp;)#Uis", "$1 ", $txt);
}

//-> Badword Filter
function BadwordFilter($txt) {
    $words = explode(",", trim(settings('badwords')));
    foreach ($words as $word) {
        $txt = preg_replace("#" . $word . "#i", str_repeat("*", strlen($word)), $txt);
    }
    return $txt;
}

//-> Funktion um Bestimmte Textstellen zu markieren
function hl($text, $word) {
    $ret = array();
    if (!empty($_GET['hl']) && $_SESSION['search_type'] == 'text') {
        if ($_SESSION['search_con'] == 'or') {
            $words = explode(" ", $word);
            for ($x = 0; $x < count($words); $x++)
                $ret['text'] = preg_replace("#" . $words[$x] . "#i", '<span class="fontRed" title="' . $words[$x] . '">' . $words[$x] . '</span>', $text);
        } else
            $ret['text'] = preg_replace("#" . $word . "#i", '<span class="fontRed" title="' . $word . '">' . $word . '</span>', $text);

        if (!preg_match("#<span class=\"fontRed\" title=\"(.*?)\">#", $ret['text']))
            $ret['class'] = 'class="commentsRight"';
        else
            $ret['class'] = 'class="highlightSearchTarget"';
    } else {
        $ret['text'] = $text;
        $ret['class'] = 'class="commentsRight"';
    }

    return $ret;
}

//-> Emailadressen in Unicode umwandeln
function eMailAddr(string $email)
{
    $output = "";

    for ($i = 0; $i < strlen($email); $i++) {
        $output .= str_replace(substr($email, $i, 1), "&#" . ord(substr($email, $i, 1)) . ";", substr($email, $i, 1));
    }

    return $output;
}

//-> Leerzeichen mit + ersetzen (w3c)
function convSpace(string $string)
{
    $string = spChars($string);
    return str_replace(" ", "+", $string);
}

//-> BBCode
function re_bbcode(string $txt)
{
    $txt = spChars($txt);
    $txt = str_replace("'", "&#39;", $txt);
    $txt = str_replace("[", "&#91;", $txt);
    $txt = str_replace("]", "&#93;", $txt);
    $txt = str_replace("&lt;", "&#60;", $txt);
    $txt = str_replace("&gt;", "&#62;", $txt);
    return stripslashes($txt);
}

/* START # from wordpress under GBU GPL license
   URL autolink function */
function _make_url_clickable_cb(array $matches)
{
    $ret = '';
    $url = $matches[2];

    if (empty($url))
        return $matches[0];
    // removed trailing [.,;:] from URL
    if (in_array(substr($url, -1), array('.', ',', ';', ':')) === true) {
        $ret = substr($url, -1);
        $url = substr($url, 0, strlen($url) - 1);
    }

    return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $ret;
}

function _make_web_ftp_clickable_cb(array $matches)
{
    $ret = '';
    $dest = $matches[2];
    $dest = 'http://' . $dest;

    if (empty($dest))
        return $matches[0];

    // removed trailing [,;:] from URL
    if (in_array(substr($dest, -1), array('.', ',', ';', ':')) === true) {
        $ret = substr($dest, -1);
        $dest = substr($dest, 0, strlen($dest) - 1);
    }

    return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>" . $ret;
}

function _make_email_clickable_cb(array $matches)
{
    $email = $matches[2] . '@' . $matches[3];
    return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}

function make_clickable(string $ret)
{
    $ret = ' ' . $ret;
    // in testing, using arrays here was found to be faster
    $ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
    $ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
    $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);

    // this one is not in an array because we need it to run last, for cleanup of accidental links within links
    $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
    return trim($ret);
}

/* END # from wordpress under GBU GPL license */

//Diverse BB-Codefunktionen
function bbcode(string $txt, bool $tinymce = false, bool $no_vid = false, bool $ts = false, bool $nolink = false)
{
    global $charset;

    $txt = html_entity_decode($txt, ENT_COMPAT, $charset);
    if (!$no_vid && settings('urls_linked') && !$nolink)
        $txt = make_clickable($txt);

    $txt = str_replace("\\", "\\\\", $txt);
    $txt = str_replace("\\n", "<br />", $txt);
    $txt = BadwordFilter($txt);
    $txt = replace($txt, $tinymce, $no_vid);
    $txt = highlight_text($txt);
    $txt = re_bbcode(strval($txt));

    if (!$ts)
        $txt = strip_tags($txt, "<br><object><em><param><embed><strong><iframe><hr><table><tr><td><div><span><a><b><i><u><p><ul><ol><li><br /><img>");

    $txt = smileys($txt);

    if (!$no_vid)
        $txt = glossar($txt);

    $txt = str_replace("&#34;", "\"", $txt);
    return str_replace('<p></p>', '<p>&nbsp;</p>', $txt);
}

function bbcode_nletter(string $txt)
{
    $txt = stripslashes($txt);
    $txt = nl2br(trim($txt));
    return '<style type="text/css">p { margin: 0px; padding: 0px; }</style>' . $txt;
}

function bbcode_nletter_plain(string $txt)
{
    $txt = preg_replace("#\<\/p\>#Uis", "\r\n", $txt);
    $txt = preg_replace("#\<br(.*?)\>#Uis", "\r\n", $txt);
    $txt = str_replace("p { margin: 0px; padding: 0px; }", "", $txt);
    $txt = convert_feed($txt);
    $txt = str_replace("&amp;#91;", "[", $txt);
    $txt = str_replace("&amp;#93;", "]", $txt);
    return strip_tags($txt);
}

function bbcode_html(string $txt, bool $tinymce = false)
{
    $txt = str_replace("&lt;", "<", $txt);
    $txt = str_replace("&gt;", ">", $txt);
    $txt = str_replace("&quot;", "\"", $txt);
    $txt = BadwordFilter($txt);
    $txt = replace($txt, $tinymce);
    $txt = highlight_text($txt);
    $txt = re_bbcode(strval($txt));
    $txt = smileys($txt);
    $txt = glossar($txt);
    return str_replace("&#34;", "\"", $txt);
}

function bbcode_email(string $txt)
{
    $txt = bbcode($txt);
    $txt = str_replace("&#91;", "[", $txt);
    $txt = str_ireplace("../",
        GetServerVars('REQUEST_SCHEME').'://'.GetServerVars('HTTP_HOST').'/', $txt);
    return str_replace("&#93;", "]", $txt);
}

//-> Textteil in Zitat-Tags setzen
function zitat(string $nick, string $zitat)
{
    $zitat = str_replace(chr(145), chr(39), $zitat);
    $zitat = str_replace(chr(146), chr(39), $zitat);
    $zitat = str_replace("'", "&#39;", $zitat);
    $zitat = str_replace(chr(147), chr(34), $zitat);
    $zitat = str_replace(chr(148), chr(34), $zitat);
    $zitat = str_replace(chr(10), " ", $zitat);
    $zitat = str_replace(chr(13), " ", $zitat);
    $zitat = preg_replace("#[\n\r]+#", "<br />", $zitat);
    return '<div class="quote"><b>' . $nick . ' ' . _wrote . ':</b><br />' . re_bbcode($zitat) . '</div><br /><br /><br />';
}

//-> convert string for output
function re($txt, bool $only_stripslashes = false)
{
    global $charset;
    if ($only_stripslashes)
        return strval(stripslashes($txt));

    return strval(trim(stripslashes(spChars(html_entity_decode(utf8_decode($txt), ENT_COMPAT, $charset), true))));
}

//-> Smileys ausgeben
function smileys(string $txt)
{
    $files = get_files('../inc/images/smileys', false, true);
    for ($i = 0; $i < count($files); $i++) {
        $smileys = $files[$i];
        $bbc = preg_replace("=.gif=Uis", "", $smileys);

        if (preg_match("=:" . $bbc . ":=Uis", $txt) != FALSE)
            $txt = preg_replace("=:" . $bbc . ":=Uis", "<img src=\"../inc/images/smileys/" . $bbc . ".gif\" alt=\"\" />", $txt);
    }

    $var = array("/\ :D/",
        "/\ :P/",
        "/\ ;\)/",
        "/\ :\)/",
        "/\ :-\)/",
        "/\ :\(/",
        "/\ :-\(/",
        "/\ ;-\)/");

    $repl = array(" <img src=\"../inc/images/smileys/grin.gif\" alt=\"\" />",
        " <img src=\"../inc/images/smileys/zunge.gif\" alt=\"\" />",
        " <img src=\"../inc/images/smileys/zwinker.gif\" alt=\"\" />",
        " <img src=\"../inc/images/smileys/smile.gif\" alt=\"\" />",
        " <img src=\"../inc/images/smileys/smile.gif\" alt=\"\" />",
        " <img src=\"../inc/images/smileys/traurig.gif\" alt=\"\" />",
        " <img src=\"../inc/images/smileys/traurig.gif\" alt=\"\" />",
        " <img src=\"../inc/images/smileys/zwinker.gif\" alt=\"\" />");

    $txt = preg_replace($var, $repl, $txt);
    return str_replace(" ^^", " <img src=\"../inc/images/smileys/^^.gif\" alt=\"\" />", $txt);
}

function cut(string $text, int $length = 0, bool $dots = true, bool $html = true, string $ending = '', bool $exact = false, bool $considerHtml = true) {
    if ($length === 0)
        return '';

    $ending = $dots || !empty($ending) ? (!empty($ending) ? $ending : '...') : '';

    if (!$html) {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . $ending;
    }

    $open_tags = array();
    if ($considerHtml) {
        // if the plain text is shorter than the maximum length, return the whole text
        if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
        }

        // splits all html-tags to scanable lines
        preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
        $total_length = strlen($ending);
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
            if ($total_length + $content_length > $length) {
                // the number of characters which are left
                $left = $length - $total_length;
                $entities_length = 0;
                // search for html entities
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                    // calculate the real length of all entities in the legal range
                    foreach ($entities[0] as $entity) {
                        if ($entity[1] + 1 - $entities_length <= $left) {
                            $left--;
                            $entities_length += strlen($entity[0]);
                        } else {
                            // no more characters left
                            break;
                        }
                    }
                }
                $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
                // maximum lenght is reached, so get off the loop
                break;
            } else {
                $truncate .= $line_matchings[2];
                $total_length += $content_length;
            }
            // if the maximum length is reached, get off the loop
            if ($total_length >= $length) {
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
    if ($considerHtml) {
        // close all unclosed html-tags
        foreach ($open_tags as $tag) {
            $truncate .= '</' . $tag . '>';
        }
    }
    return $truncate;
}

function wrap(string $str, int $width = 75, string $break = "\n", bool $cut = true) {
    return strtr(str_replace(htmlentities($break), $break, htmlentities(wordwrap(html_entity_decode($str), $width, $break, $cut), ENT_QUOTES)), array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_COMPAT)));
}

//-> Funktion um Dateien aus einem Verzeichnis auszulesen
function get_files(string $dir = null, bool $only_dir = false, bool $only_files = false, array $file_ext = array(), $preg_match = false, array $blacklist = array(), $blacklist_word = false) {
    $cache_hash = md5($dir . $only_dir . $only_files . print_r($file_ext, true) . $preg_match . print_r($blacklist, true) . $blacklist_word);
    if (!dbc_index::issetIndex('files') || !dbc_index::getIndexKey('files', $cache_hash) || !dbc_index::MemSetIndex()) {
        $files = array();
        if (!file_exists($dir) && !is_dir($dir)) return $files;
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


            $cache = array();
            if (dbc_index::MemSetIndex() && dbc_index::issetIndex('files')) {
                $cache = dbc_index::getIndex('files');
            }

            $cache[$cache_hash] = $files;

            if (dbc_index::MemSetIndex())
                dbc_index::setIndex('files', $cache);

            return $files;
        } else
            return false;
    } else {
        return dbc_index::getIndexKey('files', $cache_hash);
    }
}

//-> Gibt einen Teil eines nummerischen Arrays wieder
function limited_array(array $array = array(), int $begin, int $max) {
    $array_exp = array();
    $range = range($begin = ($begin - 1), ($begin + $max - 1));
    foreach ($array as $key => $wert) {
        if (array_var_exists($key, $range))
            $array_exp[$key] = $wert;
    }

    return $array_exp;
}

function array_var_exists($var, $search) {
    foreach ($search as $key => $var_) {
        if ($var_ == $var) return true;
    }
    return false;
}

//-> Funktion um Sonderzeichen zu konvertieren
function spChars(string $txt) {
    $txt = str_replace("Ä", "&Auml;", $txt);
    $txt = str_replace("ä", "&auml;", $txt);
    $txt = str_replace("Ü", "&Uuml;", $txt);
    $txt = str_replace("ü", "&uuml;", $txt);
    $txt = str_replace("Ö", "&Ouml;", $txt);
    $txt = str_replace("ö", "&ouml;", $txt);
    $txt = str_replace("ß", "&szlig;", $txt);
    return str_replace("€", "&euro;", $txt);
}

//-> Funktion um sauber in die DB einzutragen
function up($txt, bool $escape = true) {
    global $charset;
    $txt = strval($txt);
    $txt = htmlentities($txt, ENT_COMPAT, $charset);
    $txt = spChars($txt);
    $txt = stripcslashes($txt);
    $txt = utf8_encode($txt);
    return $escape ? _real_escape_string($txt) : $txt;
}

//-> Funktion um diverse Dinge aus Tabellen auszaehlen zu lassen
function cnt($count, $where = "", $what = "id") {
    $cnt_sql = db("SELECT COUNT(" . $what . ") AS `num` FROM " . $count . " " . $where . ";");
    if (_rows($cnt_sql)) {
        $cnt = _fetch($cnt_sql);
        return $cnt['num'];
    }

    return 0;
}

//-> Funktion um diverse Dinge aus Tabellen zusammenzaehlen zu lassen
function sum($db, $where = "", $what) {
    $cnt_sql = db("SELECT SUM(" . $what . ") AS `num` FROM " . $db . $where . ";");
    if (_rows($cnt_sql)) {
        $cnt = _fetch($cnt_sql);
        return $cnt['num'];
    }

    return 0;
}

function orderby($sort) {
    $split = explode("&", GetServerVars('QUERY_STRING'));
    $url = "?";

    foreach ($split as $part) {
        if (strpos($part, "orderby") === false && strpos($part, "order") === false && !empty($part)) {
            $url .= $part;
            $url .= "&";
        }
    }

    if (isset($_GET['orderby']) && $_GET['order']) {
        if ($_GET['orderby'] == $sort && $_GET['order'] == "ASC")
            return $url . "orderby=" . $sort . "&order=DESC";
    }

    return $url . "orderby=" . $sort . "&order=ASC";
}

function orderby_sql(array $sort_by = array(), $default_order = '', $join = '', array $order_by = array('ASC', 'DESC')) {
    if (!isset($_GET['order']) || empty($_GET['order']) || !in_array($_GET['order'], $order_by)) return $default_order;
    if (!isset($_GET['orderby']) || empty($_GET['orderby']) || !in_array($_GET['orderby'], $sort_by)) return $default_order;
    $orderby_real = _real_escape_string($_GET['orderby']);
    $order_real = _real_escape_string($_GET['order']);
    if (empty($orderby_real) || empty($order_real)) return $default_order;
    $join = !empty($join) ? $join . '.' : '';
    return 'ORDER BY ' . $join . $orderby_real . " " . $order_real;
}

function orderby_nav() {
    $orderby = isset($_GET['orderby']) ? "&orderby" . $_GET['orderby'] : "";
    $orderby .= isset($_GET['order']) ? "&order=" . $_GET['order'] : "";
    return $orderby;
}

//-> Funktion um ein Datenbankinhalt zu highlighten
function highlight(string $word) {
    if (substr(phpversion(), 0, 1) == 5)
        return str_ireplace($word, '<span class="fontRed">' . $word . '</span>', $word);
    else
        return str_replace($word, '<span class="fontRed">' . $word . '</span>', $word);
}

//-> Counter updaten
function updateCounter() {
    global $db, $reload, $today, $datum, $userip, $CrawlerDetect;
    $ipcheck = db("SELECT `id`,`ip`,`datum` FROM `" . $db['c_ips'] . "` WHERE `ip` = '" . $userip . "' AND FROM_UNIXTIME(datum,'%d.%m.%Y') = '" . date("d.m.Y") . "'");
    db("DELETE FROM `" . $db['c_ips'] . "` WHERE `datum`+" . $reload . " <= " . time() . " OR FROM_UNIXTIME(datum,'%d.%m.%Y') != '" . date("d.m.Y") . "'");
    $count = db("SELECT id,visitors,today FROM " . $db['counter'] . " WHERE today = '" . $today . "'");
    if (_rows($ipcheck) >= 1) {
        $get = _fetch($ipcheck);
        $sperrzeit = $get['datum'] + $reload;
        if ($sperrzeit <= time()) {
            if (_rows($count))
                db("UPDATE `" . $db['counter'] . "` SET `visitors` = (visitors+1) WHERE `today` = '" . $today . "';");
            else
                db("INSERT INTO `" . $db['counter'] . "` SET `visitors` = '1', `today` = '" . $today . "'");

            if (db("SELECT `id` FROM `" . $db['c_ips'] . "` WHERE `ip` = '" . $userip . "';", true)) {
                db("UPDATE " . $db['c_ips'] . " SET `datum` = " . ((int)$datum) . ", `agent` = '" . $CrawlerDetect->userAgent . "' WHERE `ip` = '" . $userip . "';");
            } else {
                db("INSERT INTO `" . $db['c_ips'] . "` SET `ip` = '" . $userip . "', `datum` = '" . ((int)$datum) . "', `agent` = '" . $CrawlerDetect->userAgent . "';");
            }
        }
    } else {
        if (_rows($count))
            db("UPDATE `" . $db['counter'] . "` SET `visitors` = (visitors+1) WHERE `today` = '" . $today . "';");
        else
            db("INSERT INTO `" . $db['counter'] . "` SET `visitors` = '1', `today` = '" . $today . "'");

        if (db("SELECT `id` FROM `" . $db['c_ips'] . "` WHERE `ip` = '" . $userip . "';", true)) {
            db("UPDATE `" . $db['c_ips'] . "` SET `datum` = '" . ((int)$datum) . "', `agent` = '" . $CrawlerDetect->userAgent . "' WHERE `ip` = '" . $userip . "';");
        } else {
            db("INSERT INTO `" . $db['c_ips'] . "` SET `ip` = '" . $userip . "', `datum` = '" . ((int)$datum) . "', `agent` = '" . $CrawlerDetect->userAgent . "';");
        }
    }
}

//-> Updatet die Maximalen User die gleichzeitig online sind
function update_maxonline() {
    global $db, $today;

    $get = db("SELECT `maxonline` FROM `" . $db['counter'] . "` WHERE `today` = '" . $today . "';", false, true);
    $count = cnt($db['c_who']);

    if ($get['maxonline'] <= (int)$count)
        db("UPDATE `" . $db['counter'] . "` SET `maxonline` = " . ((int)$count) . " WHERE `today` = '" . $today . "';");
}

//-> Prueft, wieviele Besucher gerade online sind
function online_guests(string $where = '') {
    global $db, $useronline, $userip, $chkMe, $isSpider;

    if (!$isSpider) {
        $logged = !$chkMe ? 0 : 1;
        db("DELETE FROM `" . $db['c_who'] . "` WHERE `online` < " . time() . ";");
        db("REPLACE INTO `" . $db['c_who'] . "`
               SET `ip`       = '" . $userip . "',
                   `online`   = " . ((int)(time() + $useronline)) . ",
                   `whereami` = '" . up($where) . "',
                   `login`    = " . ((int)$logged) . ";");
        return cnt($db['c_who']);
    }
    return true;
}

//-> Prueft, wieviele registrierte User gerade online sind
function online_reg() {
    global $db, $useronline;
    return cnt($db['users'], " WHERE (time+" . $useronline . ") > " . time() . " AND `online` = 1;");
}

/**
 * Prueft, ob der User eingeloggt ist und wenn ja welches Level besitzt er
 * @param int $userid_set
 * @return bool|int
 */
function checkme(int $userid_set = 0) {
    global $db;
    if (HasDSGVO() || $userid_set != 0) {
        if (!$userid = ($userid_set != 0 ? (int)($userid_set) : userid())) return 0;
        if (rootAdmin($userid)) return 4;
        if (empty($_SESSION['id']) || empty($_SESSION['pwd'])) return 0;
        if (!dbc_index::issetIndex('user_' . $userid)) {
            $qry = db("SELECT * FROM `" . $db['users'] . "` WHERE `id` = " . $userid . " AND `pwd` = '" . $_SESSION['pwd'] . "' AND `ip` = '" . up($_SESSION['ip']) . "';");
            if (!_rows($qry)) return 0;
            $get = _fetch($qry);
            dbc_index::setIndex('user_' . $get['id'], $get);
            return $get['level'];
        }

        return dbc_index::getIndexKey('user_' . $userid, 'level');
    }

    return 0;
}

/**
 * Prueft, ob der User gesperrt ist und meldet ihn ab
 * @param int $userid_set
 * @param bool $logout
 * @return bool
 */
function isBanned(int $userid_set = 0, bool $logout = true) {
    global $db, $userid;
    $userid_set = $userid_set ? $userid_set : $userid;
    if (checkme($userid_set) >= 1 || $userid_set) {
        $get = db("SELECT `banned` FROM `" . $db['users'] . "` WHERE `id` = " . (int)($userid_set) . " LIMIT 1;", false, true);
        if ($get['banned']) {
            if ($logout) {
                $_SESSION['id'] = '';
                $_SESSION['pwd'] = '';
                $_SESSION['ip'] = '';
                $_SESSION['lastvisit'] = '';
                session_unset();
                session_destroy();
                session_regenerate_id();
                if (HasDSGVO()) {
                    cookie::clear();
                }
                $userid = 0;
            }

            return true;
        }
    }

    return false;
}

/**
 * Prueft, ob ein User diverse Rechte besitzt
 * @param string $check
 * @param int $uid
 * @return bool
 */
function permission(string $check, int $uid = 0) {
    global $db, $userid, $chkMe;
    if (!$uid) $uid = $userid;
    if (rootAdmin($uid)) return true;
    if ($chkMe == 4)
        return true;
    else {
        if ($uid) {
            $qry = db("SHOW columns FROM `" . $db['permissions'] . "`;");
            while ($get = _fetch($qry)) {
                if ($get['Field'] == $check) {
                    // check rank permission
                    if (db("SELECT s1.`" . $check . "` FROM `" . $db['permissions'] . "` AS `s1`
                   LEFT JOIN `" . $db['userpos'] . "` AS `s2` ON s1.`pos` = s2.`posi`
                   WHERE s2.`user` = " . (int)($uid) . " AND s1.`" . $check . "` = 1 AND s2.`posi` != 0;", true))
                        return true;

                    // check user permission
                    if (!dbc_index::issetIndex('user_permission_' . $uid)) {
                        $permissions = db("SELECT * FROM " . $db['permissions'] . " WHERE `user` = " . (int)($uid) . ";", false, true);
                        dbc_index::setIndex('user_permission_' . $uid, $permissions);
                    }

                    return dbc_index::getIndexKey('user_permission_' . $uid, $check) ? true : false;
                }
            }
        }
    }

    return false;
}

/**
 * Checkt, ob neue Nachrichten vorhanden sind
 * @return string
 */
function check_msg() {
    global $db;
    if (db("SELECT `page` FROM `" . $db['msg'] . "` WHERE `an` = " . ((int)$_SESSION['id']) . " AND `page` = 0;", true)) {
        db("UPDATE `" . $db['msg'] . "` SET `page` = 1 WHERE `an` = " . ((int)$_SESSION['id']) . ";");
        return show("user/new_msg", array("new" => _site_msg_new));
    }

    return '';
}

/**
 * Prueft ob ein User schon in der Buddyliste vorhanden ist
 * @param int $buddy
 * @return bool
 */
function check_buddy(int $buddy) {
    global $db, $userid;
    return db("SELECT `id` FROM `" . $db['buddys'] . "` WHERE `user` = " . (int)($userid) . " AND `buddy` = " . (int)($buddy) . ";", true) >= 1;
}

/**
 * Flaggen ausgeben
 * @param $code
 * @return string
 */
function flag($code) {
    global $picformat;
    if (empty($code))
        return '<img src="../inc/images/flaggen/nocountry.gif" alt="" class="icon" />';

    foreach ($picformat as $end) {
        if (file_exists(basePath . "/inc/images/flaggen/" . $code . "." . $end)) break;
    }

    if (file_exists(basePath . "/inc/images/flaggen/" . $code . "." . $end))
        return '<img src="../inc/images/flaggen/' . $code . '.' . $end . '" alt="" class="icon" />';

    return '<img src="../inc/images/flaggen/nocountry.gif" alt="" class="icon" />';
}

/**
 * Flaggen ausgeben
 * @param $code
 * @return string
 */
function rawflag($code) {
    global $picformat;
    if (empty($code))
        return '<img src=../inc/images/flaggen/nocountry.gif alt= class=icon />';

    foreach ($picformat as $end) {
        if (file_exists(basePath . "/inc/images/flaggen/" . $code . "." . $end)) break;
    }

    if (file_exists(basePath . "/inc/images/flaggen/" . $code . "." . $end))
        return '<img src=../inc/images/flaggen/' . $code . '.' . $end . ' alt= class=icon />';

    return '<img src=../inc/images/flaggen/nocountry.gif alt= class=icon />';
}

/**
 * Liste der Laender ausgeben
 * @param string $i
 * @return string
 */
function show_countrys(string $i = "") {
    if ($i != "")
        $options = preg_replace('#<option value="' . $i . '">(.*?)</option>#', '<option value="' . $i . '" selected="selected"> \\1</option>', _country_list);
    else
        $options = preg_replace('#<option value="de"> Deutschland</option>#', '<option value="de" selected="selected"> Deutschland</option>', _country_list);

    return '<select id="land" name="land" class="dropdown">' . $options . '</select>';
}

/**
 * Gameicon ausgeben
 * @param string $code
 * @return string
 */
function squad(string $code) {
    global $picformat;
    if (empty($code))
        return '<img src="../inc/images/gameicons/nogame.gif" alt="" class="icon" />';

    $code = str_replace(array('.png', '.gif', '.jpg'), '', $code);
    foreach ($picformat as $end) {
        if (file_exists(basePath . "/inc/images/gameicons/" . $code . "." . $end)) break;
    }

    if (file_exists(basePath . "/inc/images/gameicons/" . $code . "." . $end))
        return '<img src="../inc/images/gameicons/' . $code . '.' . $end . '" alt="" class="icon" />';

    return '<img src="../inc/images/gameicons/nogame.gif" alt="" class="icon" />';
}

/**
 * Funktion um bei DB-Eintraegen URLs einem http:// oder https:// zuzuweisen
 * @param string $hp
 * @return mixed|string
 */
function links(string $hp) {
    if (!empty($hp)) {
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
    }

    return $hp;
}

/**
 * Funktion um Passwoerter generieren zu lassen
 * @param int $length
 * @param bool $add_dashes
 * @param string $available_sets
 * @return bool|string
 */
function mkpwd(int $length = 8, bool $add_dashes = false, string $available_sets = 'luds') {
    $sets = array();
    if (strpos($available_sets, 'l') !== false)
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
    if (strpos($available_sets, 'u') !== false)
        $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    if (strpos($available_sets, 'd') !== false)
        $sets[] = '23456789';
    if (strpos($available_sets, 's') !== false)
        $sets[] = '!@#$%&*?';

    $all = '';
    $password = '';
    foreach ($sets as $set) {
        $password .= $set[array_rand(str_split($set))];
        $all .= $set;
    }
    $all = str_split($all);
    for ($i = 0; $i < $length - count($sets); $i++)
        $password .= $all[array_rand($all)];

    $password = str_shuffle($password);

    if (!$add_dashes)
        return $password;

    $dash_len = floor(sqrt($length));
    $dash_str = '';
    while (strlen($password) > $dash_len) {
        $dash_str .= substr($password, 0, $dash_len) . '-';
        $password = substr($password, $dash_len);
    }

    $dash_str .= $password;
    return $dash_str;
}

/**
 * Passwortabfrage und rückgabe des users
 * @param string $user
 * @param string $pwd
 * @return bool|mixed
 */
function checkpwd(string $user, string $pwd) {
    global $db;
    $sql = db("SELECT * FROM `" . $db['users'] . "` WHERE `user` = '" . up($user) .
        "' AND (`pwd` = '" . hash('sha256', $pwd) . "' OR (`pwd` = '" . md5($pwd) . "' AND `pwd_md5` = 1)) AND `level` != 0;");
    if (_rows($sql)) {
        $get = _fetch($sql);
        if ($get['pwd_md5']) {
            //Update Password to SHA256
            db("UPDATE `" . $db['users'] . "` SET `pwd` = '" . hash('sha256', $pwd) . "', `pwd_md5` = 0 WHERE `id` = " . $get['id'] . ";");
            $get['pwd'] = hash('sha256', $pwd);
            $get['pwd_md5'] = 0;
        }

        return $get;
    }

    return false;
}

/**
 * Infomeldung ausgeben
 * @param string $msg
 * @param string $url
 * @param int $timeout
 * @return bool|mixed|null|string|string[]
 */
function info(string $msg, string $url, int $timeout = 5) {
    if (config('direct_refresh')) {
        header('Location: ' . str_replace('&amp;', '&', $url));
        exit();
    }

    $u = parse_url($url);
    $parts = '';
    $u['query'] = array_key_exists('query', $u) ? $u['query'] : '';
    $u['query'] = str_replace('&amp;', '&', $u['query']);
    foreach (explode('&', $u['query']) as $p) {
        $p = explode('=', $p);
        if (count($p) == 2)
            $parts .= '<input type="hidden" name="' . $p[0] . '" value="' . $p[1] . '" />' . "\r\n";
    }

    if (!array_key_exists('path', $u)) $u['path'] = '';
    return show("errors/info", array("msg" => $msg,
        "url" => $u['path'],
        "rawurl" => html_entity_decode($url),
        "parts" => $parts,
        "timeout" => $timeout,
        "info" => _info,
        "weiter" => _weiter,
        "backtopage" => _error_fwd));
}

/**
 * Errormmeldung ausgeben
 * @param string $error
 * @param int $back
 * @return bool|mixed|null|string|string[]
 */
function error(string $error, int $back = 1) {
    return show("errors/error", array("error" => $error, "back" => $back, "fehler" => _error, "backtopage" => _error_back));
}

/**
 * Errormmeldung ohne "zurueck" ausgeben
 * @param string $error
 * @return bool|mixed|null|string|string[]
 */
function error2(string $error) {
    return show("errors/error2", array("error" => $error, "fehler" => _error));
}

/**
 * Email wird auf korrekten Syntax & Erreichbarkeit ueberprueft
 * @param string $email
 * @return bool
 * @throws Exception
 */
function check_email(string $email) {
    global $gump;
    $email = $gump->filter(array('email' => $email), array('email' => 'trim|sanitize_email'));
    return ($gump->validate($email, array('email' => 'valid_email')) === true);
}

/**
 * Bilder verkleinern
 * @param string $img
 * @return string
 */
function img_size(string $img) {
    return "<a href=\"../" . $img . "\" rel=\"lightbox[l_" . (int)($img) . "]\"><img src=\"../thumbgen.php?img=" . $img . "\" alt=\"\" /></a>";
}

/**
 * Bilder verkleinern
 * @param string $folder
 * @param string $img
 * @return string
 */
function img_cw(string $folder, string $img) {
    return "<a href=\"../" . $folder . $img . "\" rel=\"lightbox[cw_" . (int)($folder) . "]\"><img src=\"../thumbgen.php?img=" . $folder . $img . "\" alt=\"\" /></a>";
}

/**
 * Bilder verkleinern
 * @param string $img
 * @return string
 */
function gallery_size(string $img = "") {
    return "<a href=\"../gallery/images/" . $img . "\" rel=\"lightbox[gallery_" . (int)($img) . "]\"><img src=\"../thumbgen.php?img=gallery/images/" . $img . "\" alt=\"\" /></a>";
}

/**
 * Blaetterfunktion
 * @param int $entrys
 * @param int $perpage
 * @param string $urlpart
 * @param bool $icon
 * @return string
 */
function nav(int $entrys, int $perpage, string $urlpart = '', bool $icon = true) {
    global $page;
    if ($perpage == 0)
        return "&#xAB; <span class=\"fontSites\">0</span> &#xBB;";

    if ($icon == true)
        $icon = '<img src="../inc/images/multipage.gif" alt="" class="icon" /> ' . _seiten;

    if ($entrys <= $perpage)
        return $icon . ' &#xAB; <span class="fontSites">1</span> &#xBB;';

    if (!$page || $page < 1)
        $page = 2;

    $pages = ceil($entrys / $perpage);
    $urlpart_ext = empty($urlpart) ? '?' : '&amp;';

    if (($page - 5) <= 2 && $page != 1)
        $first = '<a class="sites" href="' . $urlpart . $urlpart_ext . 'page=' . ($page - 1) . '">&#xAB;</a><span class="fontSitesMisc">&#xA0;</span> <a  class="sites" href="' . $urlpart . $urlpart_ext . 'page=1">1</a> ';
    else if ($page > 1)
        $first = '<a class="sites" href="' . $urlpart . $urlpart_ext . 'page=' . ($page - 1) . '">&#xAB;</a><span class="fontSitesMisc">&#xA0;</span> <a class="sites" href="' . $urlpart . $urlpart_ext . 'page=1">1</a>...';
    else
        $first = '<span class="fontSitesMisc">&#xAB;&#xA0;</span>';

    if ($page == $pages)
        $last = '<span class="fontSites">' . $pages . '</span><span class="fontSitesMisc">&#xA0;&#xBB;<span>';
    else if (($page + 5) >= $pages)
        $last = '<a class="sites" href="' . $urlpart . $urlpart_ext . 'page=' . ($pages) . '">' . $pages . '</a>&#xA0;<a class="sites" href="' . $urlpart . $urlpart_ext . 'page=' . ($page + 1) . '">&#xBB;</a>';
    else
        $last = '...<a class="sites" href="' . $urlpart . $urlpart_ext . 'page=' . ($pages) . '">' . $pages . '</a>&#xA0;<a class="sites" href="' . $urlpart . $urlpart_ext . 'page=' . ($page + 1) . '">&#xBB;</a>';

    $result = '';
    $resultm = '';
    for ($i = $page; $i <= ($page + 5) && $i <= ($pages - 1); $i++) {
        if ($i == $page)
            $result .= '<span class="fontSites">' . $i . '</span><span class="fontSitesMisc">&#xA0;</span>';
        else
            $result .= '<a class="sites" href="' . $urlpart . $urlpart_ext . 'page=' . $i . '">' . $i . '</a><span class="fontSitesMisc">&#xA0;</span>';
    }

    for ($i = ($page - 5); $i <= ($page - 1); $i++) {
        if ($i >= 2)
            $resultm .= '<a class="sites" href="' . $urlpart . $urlpart_ext . 'page=' . $i . '">' . $i . '</a> ';
    }

    return $icon . ' ' . $first . $resultm . $result . $last;
}

/**
 * Nickausgabe mit Profillink oder Emaillink (reg/nicht reg)
 * @param int $uid
 * @param string $class
 * @param string $nick
 * @param string $email
 * @param int $cut
 * @param string $add
 * @return bool|mixed|null|string|string[]
 */
function autor(int $uid, string $class = "", string $nick = "", string $email = "", int $cut = 20, string $add = "") {
    global $db;
    if (!dbc_index::issetIndex('user_' . (int)($uid))) {
        $qry = db("SELECT * FROM `" . $db['users'] . "` WHERE `id` = " . (int)($uid) . ";");
        if (_rows($qry)) {
            $get = _fetch($qry);
            dbc_index::setIndex('user_' . $get['id'], $get);
        } else {
            $nickname = (!empty($cut)) ? cut($nick, $cut, true, false) : $nick;
            return show(_user_link_noreg, array("nick" => re($nickname), "class" => $class, "email" => eMailAddr($email)));
        }
    }

    $nickname = (!empty($cut)) ? cut(re(dbc_index::getIndexKey('user_' . (int)($uid), 'nick')), $cut, true, false) :
        re(dbc_index::getIndexKey('user_' . (int)($uid), 'nick'));
    return show(_user_link, array("id" => $uid,
        "country" => flag(dbc_index::getIndexKey('user_' . (int)($uid), 'country')),
        "class" => $class,
        "get" => $add,
        "nick" => $nickname));
}

/**
 * @param int $uid
 * @param string $class
 * @param string $nick
 * @param string $email
 * @param int $cut
 * @return bool|mixed|null|string|string[]
 */
function cleanautor(int $uid, string $class = "", string $nick = "", string $email = "", int $cut = 20) {
    global $db;
    if (!dbc_index::issetIndex('user_' . (int)($uid))) {
        $qry = db("SELECT * FROM `" . $db['users'] . "` WHERE `id` = " . (int)($uid) . ";");
        if (_rows($qry)) {
            $get = _fetch($qry);
            dbc_index::setIndex('user_' . $get['id'], $get);
        } else
            return show(_user_link_noreg, array("nick" => re(cut($nick, $cut, false, false)), "class" => $class, "email" => eMailAddr($email)));
    }

    return show(_user_link_preview, array("id" => $uid, "country" => flag(dbc_index::getIndexKey('user_' . (int)($uid), 'country')),
        "class" => $class, "nick" => re(cut(dbc_index::getIndexKey('user_' . (int)($uid), 'nick'), $cut, false, false))));
}

function rawautor(int $uid) {
    global $db;
    if (!dbc_index::issetIndex('user_' . (int)($uid))) {
        $qry = db("SELECT * FROM `" . $db['users'] . "` WHERE `id` = " . (int)($uid) . ";");
        if (_rows($qry)) {
            $get = _fetch($qry);
            dbc_index::setIndex('user_' . $get['id'], $get);
        } else
            return rawflag('') . " " . up(re($uid));
    }

    return rawflag(dbc_index::getIndexKey('user_' . (int)($uid), 'country')) . " " .
        up(re(dbc_index::getIndexKey('user_' . (int)($uid), 'nick')));
}

/**
 * Nickausgabe ohne Profillink oder Emaillink fr das ForenAbo
 * @param int $uid
 * @return bool|mixed|null|string|string[]
 */
function fabo_autor(int $uid) {
    global $db;
    $qry = db("SELECT `nick` FROM `" . $db['users'] . "` WHERE `id` = " . $uid . ";");
    if (_rows($qry)) {
        $get = _fetch($qry);
        return show(_user_link_fabo, array("id" => $uid, "nick" => re($get['nick'])));
    }

    return '';
}

/**
 * @param int $uid
 * @return bool|mixed|null|string|string[]
 */
function blank_autor(int $uid) {
    global $db;
    $qry = db("SELECT `nick` FROM `" . $db['users'] . "` WHERE `id` = " . $uid . ";");
    if (_rows($qry)) {
        $get = _fetch($qry);
        return show(_user_link_blank, array("id" => $uid, "nick" => re($get['nick'])));
    }

    return '';
}

/**
 * interner Forencheck
 * @param int $id
 * @return bool
 */
function fintern(int $id) {
    global $db, $userid, $chkMe;
    $sql = db("SELECT s1.`intern`,s2.`id` FROM `" . $db['f_kats'] . "` AS `s1` LEFT JOIN `" . $db['f_skats'] . "` AS `s2` ON s2.`sid` = s1.`id` WHERE s2.`id` = " . (int)($id) . ";");
    if (_rows($sql)) {
        $fget = _fetch($sql);
        if (!$chkMe)
            return empty($fget['intern']) ? true : false;
        else {
            $team = db("SELECT s1.`id` FROM `" . $db['f_access'] . "` AS `s1` LEFT JOIN `" . $db['userpos'] . "` AS `s2` ON s1.`pos` = s2.`posi` WHERE s2.`user` = " . (int)($userid) . " AND s2.`posi` != 0 AND s1.`forum` = " . (int)($id) . ";", true);
            $user = db("SELECT `id` FROM `" . $db['f_access'] . "` WHERE `user` = " . (int)($userid) . " AND `forum` = " . (int)($id) . ";", true);
            if ($user || $team || $chkMe == 4 || !$fget['intern'])
                return true;
        }
    }

    return false;
}

/**
 * Einzelne Userdaten ermitteln
 * @param string $what
 * @param int $tid
 * @return bool|null
 */
function data(string $what, int $tid = 0) {
    global $db, $userid;
    if (!$tid) $tid = $userid;
    if (!dbc_index::issetIndex('user_' . $tid)) {
        $sql = db("SELECT * FROM `" . $db['users'] . "` WHERE `id` = " . (int)($tid) . ";");
        if (_rows($sql)) {
            $get = _fetch($sql);
            dbc_index::setIndex('user_' . $tid, $get);
        } else {
            return null;
        }
    }

    return dbc_index::getIndexKey('user_' . $tid, $what);
}

/**
 * Einzelne Userstatistiken ermitteln
 * @param string $what
 * @param int $tid
 * @return bool|null
 */
function userstats(string $what, int $tid = 0)
{
    global $db, $userid;
    if (!$tid) $tid = $userid;
    if (!dbc_index::issetIndex('userstats_' . $tid)) {
        $sql = db("SELECT * FROM `" . $db['userstats'] . "` WHERE `user` = " . (int)($tid) . ";");
        if (_rows($sql)) {
            $get = _fetch($sql);
            dbc_index::setIndex('userstats_' . $tid, $get);
        } else {
            return null;
        }
    }

    return dbc_index::getIndexKey('userstats_' . $tid, $what);
}

/**
 * Funktion zum versenden von Emails
 * @param string $mailto
 * @param string $subject
 * @param string $content
 * @return bool
 * @throws \PHPMailer\PHPMailer\Exception
 */
function sendMail(string $mailto, string $subject, string $content) {
    $mail = new PHPMailer(false);
    if (phpmailer_use_smtp) {
        $mail->isSMTP();
        $mail->Host = phpmailer_smtp_host;
        $mail->SMTPAuth = phpmailer_use_auth;
        $mail->Username = phpmailer_smtp_user;
        $mail->Password = phpmailer_smtp_password;
        $mail->SMTPSecure = phpmailer_smtp_secure;
        $mail->Port = phpmailer_smtp_port;
    }

    $mail->setFrom(($mailfrom = re(settings('mailfrom'))), $mailfrom);
    $mail->AddAddress(preg_replace('/(\\n+|\\r+|%0A|%0D)/i', '', $mailto));
    $mail->isHTML(true);
    $mail->Subject = re($subject);
    $mail->Body = $content;
    $mail->AltBody = bbcode_nletter_plain($content);

    $mail->setLanguage(language_short_tag(), basePath . '/vendor/phpmailer/phpmailer/language');
    return $mail->send();
}

function language_short_tag() {
    switch ($_SESSION['language']) {
        case "spanish":
            return 'es';
        case "deutsch":
            return 'de';
        case "russian":
            return 'ru';
        default:
            return 'en';
    }
}

function check_msg_emal() {
    global $db, $httphost;
    $qry = db("SELECT s1.`an`,s1.`page`,s1.`titel`,s1.`sendmail`,s1.`id` AS `mid`,s2.`id`,s2.`nick`,s2.`email`,s2.`pnmail` FROM `"
        . $db['msg'] . "` AS `s1` LEFT JOIN `" . $db['users'] .
        "` AS `s2` ON s2.`id` = s1.`an` WHERE `page` = 0 AND `sendmail` = 0;");
    if (_rows($qry)) {
        while ($get = _fetch($qry)) {
            if ($get['pnmail']) {
                db("UPDATE " . $db['msg'] . " SET `sendmail` = 1 WHERE `id` = " . (int)$get['mid'] . ";");
                $subj = show(settings('eml_pn_subj'), array("domain" => $httphost));
                $message = show(bbcode_email(settings('eml_pn')), array("nick" => re($get['nick']), "domain" => $httphost, "titel" => $get['titel'], "clan" => settings('clanname')));
                sendMail(re($get['email']), $subj, $message);
            }
        }
    }
}

if (!$ajaxJob && HasDSGVO())
    check_msg_emal();

/**
 * Checkt ob ein Ereignis neu ist
 * @param int $datum
 * @return bool
 */
function check_new(int $datum) {
    global $userid;
    if ($userid) {
        if ($datum >= userstats('lastvisit') ||
            !userstats('lastvisit')) {
            return true;
        }
    }

    return false;
}

/**
 * DropDown Mens Date/Time
 * @param string $what
 * @param int $wert
 * @param int $age
 * @return string
 */
function dropdown(string $what, int $wert, int $age = 0) {
    $return = '';
    if ($what == "day") {
        $return = ($age == 1 ? '<option value="" class="dropdownKat">' . _day . '</option>' . "\n" : '');
        for ($i = 1; $i < 32; $i++) {
            if ($i == $wert)
                $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
            else
                $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
        }
    } else if ($what == "month") {
        $return = ($age == 1 ? '<option value="" class="dropdownKat">' . _month . '</option>' . "\n" : '');
        for ($i = 1; $i < 13; $i++) {
            if ($i == $wert)
                $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
            else
                $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
        }
    } else if ($what == "year") {
        if ($age == 1) {
            $return = '<option value="" class="dropdownKat">' . _year . '</option>' . "\n";
            for ($i = date("Y", time()) - 80; $i < date("Y", time()) - 10; $i++) {
                if ($i == $wert)
                    $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
                else
                    $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
            }
        } else {
            $return = '';
            for ($i = date("Y", time()) - 3; $i < date("Y", time()) + 3; $i++) {
                if ($i == $wert)
                    $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
                else
                    $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
            }
        }
    } else if ($what == "hour") {
        $return = '';
        for ($i = 0; $i < 24; $i++) {
            if ($i == $wert)
                $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
            else
                $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
        }
    } else if ($what == "minute") {
        $return = '';
        for ($i = "00"; $i < 60; $i++) {
            if ($i == 0 || $i == 15 || $i == 30 || $i == 45) {
                if ($i == $wert)
                    $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
                else
                    $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
            }
        }
    }

    return $return;
}

/**
 * Games fuer den Livestatus
 * @param string $game
 * @return mixed|string
 */
function sgames($game = '') {
    $protocols = get_files(basePath.'/inc/server_query/');
    $games = '';
    foreach($protocols AS $protocol)
    {
        unset($gamemods, $server_name_config);
        $protocol = str_replace('.php', '', $protocol);
        if(substr($protocol, 0, 1) != '_')
        {
            $explode = '##############################################################################################################################';
            $protocol_config = explode($explode, file_get_contents(basePath.'/inc/server_query/'.$protocol.'.php'));
            eval(str_replace('<?php', '', $protocol_config[0]));
            if(!empty($server_name_config) && count($server_name_config) > 2) {
                $gamemods = '';
                foreach($server_name_config AS $slabel => $sconfig) {
                    $gamemods .= $sconfig[1].', ';
                }
            }
            $gamemods = empty($gamemods) ? '' : ' ('.substr($gamemods, 0, strlen($gamemods) - 2).')';

            $games .= '<option value="'.$protocol.'">';
            switch($protocol):
                case 'bf1942'; case 'bf2142'; case 'bf2'; case 'bfvietnam'; case 'bfbc2';
                $protocol = strtr($protocol, array('bfbc2' => 'Battlefield Bad Company 2', 'bfv' => 'Battlefield V', 'bf' => 'Battlefield '));
                break;
                case 'swat4'; $protocol = strtoupper($protocol); break;
                case 'aarmy'; $protocol = 'Americas Army'; break;
                case 'arma'; $protocol = 'Armed Assault'; break;
                case 'wet'; $protocol = 'Wolfenstein: Enemy Territory'; break;
                case 'mta'; $protocol = 'Multi-Theft-Auto'; break;
                case 'cnc'; $protocol = 'Command &amp; Conquer'; break;
                case 'sof2'; $protocol = 'Soldiers of Fortune 2'; break;
                case 'ut'; $protocol = 'Unreal Tournament'; break;
                default;
                    $protocol = ucfirst(str_replace('_', ' ', $protocol));
                    $protocol = (strlen($protocol) < 4) ? strtoupper($protocol) : $protocol;
                    break;
            endswitch;
            $games .= $protocol.$gamemods;
            $games .= '</option>';
        }
    }
    $games = str_replace("value=\"".$game."\"","value=\"".$game."\" selected=\"selected\"",$games);
    return $games;
}

/**
 * Sucht nach Game Icons
 * @param string $icon
 * @return array
 */
function search_game_icon(string $icon = '')
{
    global $picformat;
    $image = '../inc/images/gameicons/unknown.gif';
    $found = false;
    foreach ($picformat AS $end) {
        if (file_exists(basePath . '/inc/images/gameicons/' . $icon . '.' . $end)) {
            $found = true;
            $image = '../inc/images/gameicons/' . $icon . '.' . $end;
            break;
        }
    }
    return array('image' => $image, 'found' => $found);
}

/**
 * @param string $games
 * @param string $game
 * @return string
 */
function listgame(string $games, string $game) {
    $content = '';
    foreach ($games AS $sname => $info) {
        $selected = (!empty($game) && $game != false && $game == $sname ? 'selected="selected" ' : '');
        $content .= '<option ' . $selected . 'value="' . $sname . '">' . htmlentities($info['name']) . '</option>';
    }

    return $content;
}

/**
 * Umfrageantworten selektieren
 * @param string $what
 * @param int $vid
 * @return string
 */
function voteanswer(string $what, int $vid) {
    global $db;
    $get = db("SELECT `sel` FROM `" . $db['vote_results'] . "` WHERE `what` = '" . up($what) . "' AND `vid` = " . (int)$vid . ";", false, true);
    return re($get['sel']);
}

/**
 * Profilfelder konvertieren
 * @param $txt
 * @return mixed
 */
function conv($txt) {
    return str_replace(array("ä", "ü", "ö", "", "Ä", "Ö", ""), array("ae", "ue", "oe", "Ae", "Ue", "Oe", "ss"), $txt);
}

/**
 * Geburtstag errechnen
 * @param $bday
 * @return false|int|string
 */
function getAge($bday) {
    if (!empty($bday) && $bday) {
        $bday = date('d.m.Y', $bday);
        list($tiday, $iMonth, $iYear) = explode(".", $bday);
        $iCurrentDay = date('j');
        $iCurrentMonth = date('n');
        $iCurrentYear = date('Y');

        if (($iCurrentMonth > $iMonth) || (($iCurrentMonth == $iMonth) && ($iCurrentDay >= $tiday)))
            return $iCurrentYear - $iYear;
        else
            return $iCurrentYear - ($iYear + 1);
    }

    return '-';
}

/**
 * Ausgabe der Position des einzelnen Members
 * @param int $tid
 * @param int $squad
 * @param bool $profil
 * @return string
 */
function getrank(int $tid, int $squad = 0, bool $profil = false) {
    global $db;
    if ($squad) {
        if ($profil)
            $qry = db("SELECT * FROM `" . $db['userpos'] . "` AS `s1` LEFT JOIN `" . $db['squads'] . "` AS `s2` ON s1.`squad` = s2.`id` WHERE s1.`user` = " . (int)($tid) . " AND s1.`squad` = " . (int)($squad) . " AND s1.`posi` != 0;");
        else
            $qry = db("SELECT * FROM `" . $db['userpos'] . "` WHERE `user` = " . (int)($tid) . " AND `squad` = " . (int)($squad) . " AND `posi` != 0;");

        if (_rows($qry)) {
            while ($get = _fetch($qry)) {
                $getp = db("SELECT * FROM `" . $db['pos'] . "` WHERE `id` = " . (int)($get['posi']) . ";", false, true);
                if (!empty($get['name'])) $squadname = '<b>' . $get['name'] . ':</b> ';
                else $squadname = '';
                return $squadname . $getp['position'];
            }
        } else {
            $get = db("SELECT `level`,`banned` FROM `" . $db['users'] . "` WHERE `id` = " . (int)($tid) . ";", false, true);
            if (!$get['level'] && !$get['banned'])
                return _status_unregged;
            else if ($get['level'] == 1)
                return _status_user;
            else if ($get['level'] == 2)
                return _status_trial;
            else if ($get['level'] == 3)
                return _status_member;
            else if ($get['level'] == 4)
                return _status_admin;
            else if (!$get['level'] && $get['banned'])
                return _status_banned;
            else
                return _gast;
        }
    } else {
        $qry = db("SELECT s1.*,s2.`position` FROM `" . $db['userpos'] . "` AS `s1` LEFT JOIN `" . $db['pos'] . "` AS `s2` ON s1.`posi` = s2.`id` WHERE s1.`user` = " . (int)($tid) . " AND s1.`posi` != 0 ORDER BY s2.`pid` ASC;");
        if (_rows($qry)) {
            $get = _fetch($qry);
            return $get['position'];
        } else {
            $get = db("SELECT `level`,`banned` FROM `" . $db['users'] . "` WHERE `id` = " . (int)($tid) . ";", false, true);
            if (!$get['level'] && !$get['banned'])
                return _status_unregged;
            elseif ($get['level'] == 1)
                return _status_user;
            elseif ($get['level'] == 2)
                return _status_trial;
            elseif ($get['level'] == 3)
                return _status_member;
            elseif ($get['level'] == 4)
                return _status_admin;
            elseif (!$get['level'] && $get['banned'])
                return _status_banned;
            else
                return _gast;
        }
    }
    return '';
}

/**
 * Session fuer den letzten Besuch setzen
 */
function set_lastvisit() {
    global $db, $useronline, $userid;
    if ($userid) {
        if (!db("SELECT `id` FROM `" . $db['users'] . "` WHERE `id` = " . (int)($userid) . " AND (time+" . $useronline . ") > " . time() . ";", true)) {
            $_SESSION['lastvisit'] = data("time");
        }
    }
}

/**
 * Checkt welcher User gerade noch online ist
 * @param int $tid
 * @return string
 */
function onlinecheck(int $tid) {
    global $db, $useronline;
    $row = db("SELECT `id` FROM `" . $db['users'] . "` WHERE `id` = " . (int)($tid) . " AND (time+" . $useronline . ") > " . time() . " AND `online` = 1;", true);
    return $row ? "<img src=\"../inc/images/online.gif\" alt=\"\" class=\"icon\" />" : "<img src=\"../inc/images/offline.gif\" alt=\"\" class=\"icon\" />";
}

/**
 * Funktion fuer die Sprachdefinierung der Profilfelder
 * @param string $name
 * @return null|string|string[]
 */
function pfields_name(string $name) {
    return preg_replace_callback("=_(.*?)_=Uis",
        function ($match) {
            if (defined("_profil" . substr(trim($match[0]), 0, -1)))
                return constant("_profil" . substr(trim($match[0]), 0, -1));

            return $match[0];
        }, $name);
}

/**
 * Checkt versch. Dinge anhand der Hostmaske eines Users
 * @param string $what
 * @param int $time
 * @return bool
 */
function ipcheck(string $what, int $time = 0) {
    global $db, $userip;
    $get = db("SELECT `time`,`what` FROM `" . $db['ipcheck'] . "` WHERE `what` = '" . $what . "' AND `ip` = '" . $userip . "' ORDER BY `time` DESC;", false, true);
    if (count($get) >= 1) {
        if (preg_match("#vid#", $get['what']))
            return true;
        else {
            if ($get['time'] + (int)($time) < time())
                db("DELETE FROM `" . $db['ipcheck'] . "` WHERE `what` = '" .
                    $what . "' AND `ip` = '" . $userip . "' AND (`time`+" . $time . ") < " . time() . ";");

            if (($get['time'] + $time) > time())
                return true;
        }
    }

    return false;
}

/**
 * Gibt die Tageszahl eines Monats aus
 * @param int $month
 * @param int $year
 * @return int
 */
function days_in_month(int $month, int $year) {
    return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}

/**
 * Setzt bei einem Tag >10 eine 0 vorran (Kalender)
 * @param int $i
 * @return int|null|string|string[]
 */
function cal(int $i) {
    if (preg_match("=10|20|30=Uis", $i) == FALSE) $i = preg_replace("=0=", "", $i);
    if ($i < 10) $tag_nr = "0" . $i;
    else $tag_nr = $i;
    return $tag_nr;
}

//-> Entfernt fuehrende Nullen bei Monatsangaben
function nonum(int $i) {
    if (preg_match("=10=Uis", $i) == false)
        return preg_replace("=0=", "", $i);

    return $i;
}

/**
 * Konvertiert Platzhalter in die jeweiligen bersetzungen
 * @param string $name
 * @return mixed|null|string|string[]
 */
function navi_name(string $name) {
    $name = trim($name);
    if (preg_match("#^_(.*?)_$#Uis", $name)) {
        $name = preg_replace("#_(.*?)_#Uis", "$1", $name);

        if (defined("_" . $name))
            return constant("_" . $name);
    }

    return $name;
}

/**
 * RSS News Feed erzeugen
 * @param string $txt
 * @return string
 */
function convert_feed(string $txt) {
    global $charset;
    $txt = stripslashes($txt);
    $txt = str_replace("&Auml;", "Ae", $txt);
    $txt = str_replace("&auml;", "ae", $txt);
    $txt = str_replace("&Uuml;", "Ue", $txt);
    $txt = str_replace("&uuml;", "ue", $txt);
    $txt = str_replace("&Ouml;", "Oe", $txt);
    $txt = str_replace("&ouml;", "oe", $txt);
    $txt = htmlentities($txt, ENT_QUOTES, $charset);
    $txt = str_replace("&amp;", "&", $txt);
    $txt = str_replace("&lt;", "<", $txt);
    $txt = str_replace("&gt;", ">", $txt);
    $txt = str_replace("&#60;", "<", $txt);
    $txt = str_replace("&#62;", ">", $txt);
    $txt = str_replace("&#34;", "\"", $txt);
    $txt = str_replace("&nbsp;", " ", $txt);
    $txt = str_replace("&szlig;", "ss", $txt);
    $txt = preg_replace("#&(.*?);#is", "", $txt);
    $txt = str_replace("&", "&amp;", $txt);
    $txt = str_replace("", "\"", $txt);
    $txt = str_replace("", "\"", $txt);
    return strip_tags($txt);
}

/**
 * Userpic ausgeben
 * @param int $userid
 * @param int $width
 * @param int $height
 * @return bool|mixed|null|string|string[]
 */
function userpic(int $userid, int $width = 170, int $height = 210) {
    global $picformat;
    $pic = '';
    foreach ($picformat as $endung) {
        if (file_exists(basePath . "/inc/images/uploads/userpics/" . $userid . "." . $endung)) {
            $pic = show(_userpic_link, array("id" => $userid, "endung" => $endung, "width" => $width, "height" => $height));
            break;
        } else
            $pic = show(_no_userpic, array("width" => $width, "height" => $height));
    }

    return $pic;
}

/**
 * Useravatar ausgeben
 * @param int $uid
 * @param int $width
 * @param int $height
 * @return bool|mixed|null|string|string[]
 */
function useravatar(int $uid = 0, int $width = 100, int $height = 100) {
    global $picformat, $userid;
    $pic = '';
    $uid = $uid == 0 ? $userid : $uid;
    foreach ($picformat as $endung) {
        if (file_exists(basePath . "/inc/images/uploads/useravatare/" . $uid . "." . $endung)) {
            $pic = show(_userava_link, array("id" => $uid, "endung" => $endung, "width" => $width, "height" => $height));
            break;
        } else
            $pic = show(_no_userava, array("width" => $width, "height" => $height));
    }

    return $pic;
}

/**
 * Userpic fuer Hoverinformationen ausgeben
 * @param int $userid
 * @param int $width
 * @param int $height
 * @return string
 */
function hoveruserpic(int $userid, int $width = 170, int $height = 210) {
    global $picformat;
    $pic = "../inc/images/nopic.gif', '" . $width . "', '" . $height;
    foreach ($picformat as $endung) {
        if (file_exists(basePath . "/inc/images/uploads/userpics/" . $userid . "." . $endung)) {
            $pic = "../inc/images/uploads/userpics/" . $userid . "." . $endung . "', '" . $width . "', '" . $height . "";
            break;
        }
    }

    return $pic;
}

/**
 * Adminberechtigungen ueberpruefen
 * @param int $userid
 * @return bool
 */
function admin_perms(int $userid) {
    global $db, $chkMe;
    if (empty($userid))
        return false;

    if (rootAdmin($userid))
        return true;

    // no need for these admin areas
    $e = array('gb', 'shoutbox', 'editusers', 'votes', 'contact', 'joinus', 'intnews', 'forum',
        'gs_showpw', 'dlintern', 'intforum', 'galleryintern');

    // check user permission
    $c = db("SELECT * FROM `" . $db['permissions'] . "` WHERE `user` = " . (int)($userid) . ";", false, true);
    if (!empty($c)) {
        foreach ($c AS $v => $k) {
            if ($v != 'id' && $v != 'user' && $v != 'pos' && !in_array($v, $e)) {
                if ($k == 1) {
                    return true;
                    break;
                }
            }
        }
    }

    // check rank permission
    $qry = db("SELECT s1.* FROM `" . $db['permissions'] . "` AS `s1` LEFT JOIN `" . $db['userpos'] . "` AS `s2` ON s1.`pos` = s2.`posi` WHERE s2.`user` = " . (int)($userid) . " AND s2.`posi` != 0;");
    while ($r = _fetch($qry)) {
        foreach ($r AS $v => $k) {
            if ($v != 'id' && $v != 'user' && $v != 'pos' && !in_array($v, $e)) {
                if ($k == 1) {
                    return true;
                    break;
                }
            }
        }
    }

    return ($chkMe == 4) ? true : false;
}

/**
 * filter placeholders
 * @param string $pholder
 * @return mixed
 */
function pholderreplace(string $pholder) {
    /** @noinspection CssInvalidAtRule */
    $search = array('@<script[^>]*?>.*?</script>@si',
        '@<style[^>]*?>.*?</style>@siU',
        '@<[\/\!]*?[^<>]*?>@si',
        '@<![\s\S]*?--[ \t\n\r]*>@');
    //Replace
    $pholder = preg_replace("#<script(.*?)</script>#is", "", $pholder);
    $pholder = preg_replace("#<style(.*?)</style>#is", "", $pholder);
    $pholder = preg_replace($search, '', $pholder);
    $pholder = str_replace(" ", "", $pholder);
    $pholder = preg_replace("#[0-9]#is", "", $pholder);
    $pholder = preg_replace("#&(.*?);#s", "", $pholder);
    $pholder = str_replace("\r", "", $pholder);
    $pholder = str_replace("\n", "", $pholder);
    $pholder = preg_replace("#\](.*?)\[#is", "][", $pholder);
    $pholder = str_replace("][", "^", $pholder);
    $pholder = preg_replace("#^(.*?)\[#s", "", $pholder);
    $pholder = preg_replace("#\](.*?)$#s", "", $pholder);
    $pholder = str_replace("[", "", $pholder);
    return str_replace("]", "", $pholder);
}

/**
 * Zugriffsberechtigung auf die Seite
 * @return bool
 */
function check_internal_url() {
    global $db, $chkMe;
    if ($chkMe >= 1) return false;
    $install_pfad = explode("/", dirname(dirname(GetServerVars('SCRIPT_NAME')) . "../"));
    $now_pfad = explode("/", GetServerVars('REQUEST_URI'));
    $pfad = '';
    foreach ($now_pfad as $key => $value) {
        if (!empty($value)) {
            if (!isset($install_pfad[$key]) || $value != $install_pfad[$key]) {
                $pfad .= "/" . $value;
            }
        }
    }

    list($pfad) = explode('&', $pfad);
    $pfad = ".." . $pfad;

    if (strpos($pfad, "?") === false && strpos($pfad, ".php") === false)
        $pfad .= "/";

    if (strpos($pfad, "index.php") !== false)
        $pfad = str_replace('index.php', '', $pfad);

    $qry_navi = db_stmt("SELECT `internal` FROM `" . $db['navi'] . "` WHERE `url` = ? OR `url` = ?;",
        array('ss', $pfad, $pfad.'index.php'));
    if (_rows($qry_navi)) {
        $get_navi = _fetch($qry_navi);
        if ($get_navi['internal'])
            return true;
    }

    return false;
}

/**
 * Ladezeit
 * @return float
 */
function generatetime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/**
 * Rechte abfragen
 * @param int $checkID
 * @param int $pos
 * @return string
 */
function getPermissions(int $checkID = 0, int $pos = 0) {
    global $db, $lang;

    if (!empty($checkID)) {
        $check = empty($pos) ? 'user' : 'pos';
        $checked = array();
        $qry = db("SELECT * FROM " . $db['permissions'] . " WHERE `" . $check . "` = '" . (int)($checkID) . "'");
        if (_rows($qry)) foreach (_fetch($qry) AS $k => $v) $checked[$k] = $v;
    }

    $permission = array();
    $qry = db("SHOW COLUMNS FROM " . $db['permissions'] . "");
    while ($get = _fetch($qry)) {
        if ($get['Field'] != 'id' && $get['Field'] != 'user' && $get['Field'] != 'pos' && $get['Field'] != 'intforum') {
            @eval("\$lang = _perm_" . $get['Field'] . ";");
            $chk = empty($checked[$get['Field']]) ? '' : ' checked="checked"';
            $permission[$lang] = '<input type="checkbox" class="checkbox" id="' . $get['Field'] . '" name="perm[p_' . $get['Field'] . ']" value="1"' . $chk . ' /><label for="' . $get['Field'] . '"> ' . $lang . '</label> ';
        }
    }

    natcasesort($permission);
    $break = 1;
    $p = '';
    foreach ($permission AS $perm) {
        $br = ($break % 2) ? '<br />' : '';
        $break++;
        $p .= $perm . $br;
    }

    return $p;
}

/**
 * interne Foren-Rechte abfragen
 * @param int $checkID
 * @param int $pos
 * @return string
 */
function getBoardPermissions(int $checkID = 0, int $pos = 0) {
    global $db;

    $break = 0;
    $i_forum = '';
    $fkats = '';
    $qry = db("SELECT `id`,`name` FROM `" . $db['f_kats'] . "` WHERE `intern` = 1 ORDER BY `kid` ASC;");
    while ($get = _fetch($qry)) {
        unset($kats, $fkats, $break);
        $kats = (empty($katbreak) ? '' : '<div style="clear:both">&nbsp;</div>') . '<table class="hperc" cellspacing="1"><tr><td class="contentMainTop"><b>' . re($get["name"]) . '</b></td></tr></table>';
        $katbreak = 1;

        $qry2 = db("SELECT `kattopic`,`id` FROM `" . $db['f_skats'] . "` WHERE `sid` = " . $get['id'] . " ORDER BY `kattopic` ASC;");
        $break = 0;
        $fkats = '';
        while ($get2 = _fetch($qry2)) {
            $br = ($break % 2) ? '<br />' : '';
            $break++;
            $check = db("SELECT * FROM " . $db['f_access'] . " WHERE `" . (empty($pos) ? 'user' : 'pos') . "` = '" . (int)($checkID) . "' AND " . (empty($pos) ? 'user' : 'pos') . " != '0' AND `forum` = '" . $get2['id'] . "'");
            $chk = _rows($check) ? ' checked="checked"' : '';
            $fkats .= '<input type="checkbox" class="checkbox" id="board_' . $get2['id'] . '" name="board[' . $get2['id'] . ']" value="' . $get2['id'] . '"' . $chk . ' /><label for="board_' . $get2['id'] . '"> ' . re($get2['kattopic']) . '</label> ' . $br;
        }

        $i_forum .= $kats . $fkats;
    }

    return $i_forum;
}

/**
 * schreibe in die IPCheck Tabelle
 * @param string $what
 * @param bool $time
 */
function setIpcheck(string $what = '', bool $time = true) {
    global $db, $userip;
    db("INSERT INTO `" . $db['ipcheck'] . "` SET `ip` = '" . $userip . "', "
        . "`user_id` = " . userid() . ", `what` = '" . $what . "', "
        . "`time` = " . ($time ? time() : 0) . ", `created` = " . time() . ";");
}

//-> Speichert Rückgaben der MySQL Datenbank zwischen um SQL-Queries einzusparen
final class dbc_index {
    private static $index = array();

    /**
     * @param $index_key
     * @param $data
     */
    public static final function setIndex($index_key, $data) {
        global $cache, $config_cache;
        if (self::MemSetIndex()) {
            if (show_dbc_debug)
                DebugConsole::insert_info('dbc_index::setIndex()', 'Set index: "' . $index_key . '" to cache');

            if ($config_cache['dbc']) {
                $data_cache = null;
                try {
                    $data_cache = $cache->getItem('dbc_' . $index_key);
                } catch (\Phpfastcache\Exceptions\phpFastCacheInvalidArgumentException $e) {
                }
                $data_cache->set(serialize($data))->expiresAfter(1.5);
                $cache->save($data_cache);
            }
        }

        if (show_dbc_debug)
            DebugConsole::insert_info('dbc_index::setIndex()', 'Set index: "' . $index_key . '"');

        self::$index[$index_key] = $data;
    }

    /**
     * @param string $index_key
     * @return bool|mixed
     */
    public static final function getIndex(string $index_key){
        if (!self::issetIndex($index_key))
            return false;

        if (show_dbc_debug)
            DebugConsole::insert_info('dbc_index::getIndex()', 'Get full index: "' . $index_key . '"');

        return self::$index[$index_key];
    }

    /**
     * @param string $index_key
     * @param string $key
     * @return bool
     */
    public static final function getIndexKey(string $index_key, string $key){
        if (!self::issetIndex($index_key))
            return false;

        $data = self::$index[$index_key];
        if (empty($data) || !array_key_exists($key, $data))
            return false;

        return $data[$key];
    }

    /**
     * @param string $index_key
     * @return bool
     */
    public static final function issetIndex(string $index_key){
        global $cache;
        if (isset(self::$index[$index_key])) return true;
        if (self::MemSetIndex()) {
            $data = null;
            try {
                $data = $cache->getItem('dbc_' . $index_key);
            } catch (\Phpfastcache\Exceptions\phpFastCacheInvalidArgumentException $e) {}

            if (!is_null($data->get())) {
                if (show_dbc_debug)
                    DebugConsole::insert_loaded('dbc_index::issetIndex()', 'Load index: "' . $index_key . '" from cache');

                self::$index[$index_key] = unserialize($data->get());
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public static final function MemSetIndex() {
        global $config_cache, $cache;
        if (!$config_cache['dbc'] || CacheManager::$fallback) {
            return false;
        }

        switch ($cache->getDriverName()) {
            case 'Files':
            case 'Zenddisk':
            case 'Sqlite':
                return false;
        }

        return true;
    }
}

/**
 * Gibt die vergangene zeit zwischen $timestamp und $aktuell als lesbaren string zurueck.
 * bsp: 3 Wochen, 4 Tage, 5 Sekunden
 * @param int $timestamp * der timestamp der ersten zeit-marke.
 * @param int $aktuell * der timestamp der zweiten zeit-marke. * aktuelle zeit *
 * @param int $anzahl_einheiten * wie viele einheiten sollen maximal angezeigt werden
 * @param int $zeige_leere_einheiten * sollen einheiten, die den wert 0 haben, angezeigt werden?
 * @param int $zeige_einheiten * zeige nur angegebene einheiten. jahre werden zb in sekunden umgerechnet
 * @param string $standard * falls der timestamp 0 oder ungueltig ist, gebe diesen string zurueck
 * @return string
 */
function get_elapsed_time(int $timestamp, int $aktuell = 0, int $anzahl_einheiten = 0, int $zeige_leere_einheiten = 0, int $zeige_einheiten = 0, $standard = null) {
    if ($aktuell === null) $aktuell = time();
    if ($anzahl_einheiten === null) $anzahl_einheiten = 1;
    if ($zeige_leere_einheiten === null) $zeige_leere_einheiten = true;
    if (!is_array($zeige_einheiten)) $zeige_einheiten = array();
    if ($standard === null) $standard = "nie";
    if ($timestamp == 0) return $standard;
    if ($timestamp > $aktuell) $timestamp = $aktuell;
    if ($anzahl_einheiten < 1) $anzahl_einheiten = 10;
    $zeit = bcsub($aktuell, $timestamp);
    if ($zeit < 1) $zeit = 1;
    $arr = array();
    $werte = array(63115200 => _years, 31557600 => _year . ' ', 4838400 => _months, 2419200 => _month . ' ',
        1209600 => _weeks, 604800 => _week . ' ', 172800 => _days . ' ', 86400 => _day . ' ', 7200 => _hours,
        3600 => _hour . ' ', 120 => _minutes, 60 => _minute . ' ', 1 => _seconds);

    if ((is_array($zeige_einheiten)) and (count($zeige_einheiten) > 0)) {
        $neu = array();
        foreach ($werte as $key => $val) {
            if (in_array($val, $zeige_einheiten))
                $neu[$key] = $val;
        }

        $werte = $neu;
    }

    foreach ($werte as $div => $einheit) {
        if ($zeit < $div) {
            if (count($arr) != 0)
                $arr[$einheit] = 0;

            continue;
        }

        $anzahl = bcdiv($zeit, $div);
        $zeit -= bcmul($anzahl, $div);
        $arr[$einheit] = $anzahl;
    }

    reset($arr);
    $output = 0;
    $ret = "";
    while ((count($arr) > 0) and ($output < $anzahl_einheiten)) {
        $key = key($arr);
        $cur = current($arr);
        $einheit = ($cur == 1) ? substr($key, 0, bcsub(strlen($key), 1)) : $key;
        if (($cur != 0) or ($zeige_leere_einheiten == true))
            $ret .= (empty($ret))
                ? ($anzahl_einheiten == 1 ? round($cur, 0, PHP_ROUND_HALF_DOWN) : $cur) . " " . $einheit
                : ", " . ($anzahl_einheiten == 1 ? round($cur, 0, PHP_ROUND_HALF_DOWN) : $cur) . " " . $einheit;
        $output++;
        unset($arr[$key]);
    }
    return $ret;
}

//-> Neue Funktionen einbinden, sofern vorhanden
if ($functions_files = get_files(basePath . '/inc/additional-functions/', false, true, array('php'))) {
    foreach ($functions_files AS $func) {
        include_once(basePath . '/inc/additional-functions/' . $func);
    }
    unset($functions_files, $func);
}

class javascript {
    private static $data_array = [];

    public static function set($key='',$var='') {
        self::$data_array[$key] = $var;
    }

    public static function remove($key='') {
        unset(self::$data_array[$key]);
    }

    public static function get($key='') {
        return utf8_decode(self::$data_array[$key]);
    }

    public static function encode() {
        return json_encode(self::$data_array);
    }
}

//-> Navigation einbinden
include_once(basePath . '/inc/menu-functions/navi.php');

/**
 * Ausgabe des Indextemplates
 * @param string $index
 * @param string $title
 * @param string $where
 * @param string $wysiwyg
 * @param string $index_templ
 */
function page(string $index = '', string $title = '', string $where = '', string $wysiwyg = '', string $index_templ = 'index') {
    global $db, $userid, $userip, $tmpdir, $chkMe, $charset, $mysql, $isSpider;
    global $designpath, $time_start;

    // Timer Stop
    $time = round(generatetime() - $time_start, 4);

    // JS-Dateine einbinden
    $lng = language_short_tag(); $login = '';
    $dsgvo = (!array_key_exists('do_show_dsgvo', $_SESSION) || !$_SESSION['do_show_dsgvo'] ? 1 : 0);
    $dsgvo_lock = (!array_key_exists('user_has_dsgvo_lock', $_SESSION) || !$_SESSION['user_has_dsgvo_lock'] ? 0 : 1);

    javascript::set('maxW',config('maxwidth'));
    javascript::set('lng',$lng);
    javascript::set('dsgvo',$dsgvo);
    javascript::set('dsgvo_lock',$dsgvo_lock);
    javascript::set('dzcp_editor',($wysiwyg == '_word') ? 'advanced' : 'normal');
    javascript::set('tempdir',$_SESSION['tmpdir']);

    $java_vars = '<script language="javascript" type="text/javascript">DZCP.setConfig(\''.javascript::encode().'\');</script>'."\n";
    $min = (use_min_css_js_files ? '.min' : '');
    if (!strstr(GetServerVars('HTTP_USER_AGENT'), 'Android') && !strstr(GetServerVars('HTTP_USER_AGENT'), 'webOS'))
        $java_vars .= '<script language="javascript" type="text/javascript" src="' . $designpath . '/_js/wysiwyg' . $min . '.js"></script>' . "\n";

    if (settings("wmodus") && $chkMe != 4) {
        if (HasDSGVO()) {
            $secure = '';
            if (config('securelogin'))
                $secure = show("menu/secure", array("help" => _login_secure_help, "security" => _register_confirm));

            $login = show("errors/wmodus_login", array("what" => _login_login, "secure" => $secure, "signup" => _login_signup,
                "permanent" => _login_permanent, "lostpwd" => _login_lostpwd));
            cookie::save(); //Save Cookie
        }

        include_once(basePath . '/inc/menu-functions/dsgvo.php');
        echo show("errors/wmodus", array("wmodus" => _wartungsmodus,
            "head" => _wartungsmodus_head,
            "tmpdir" => $tmpdir,
            "dsgvo" => dsgvo(),
            "java_vars" => $java_vars,
            "dir" => $designpath,
            "index" => '',
            "title" => re(strip_tags($title)),
            "login" => $login));
    } else {
        if (!$isSpider && HasDSGVO()) {
            updateCounter();
            update_maxonline();
        }

        //check permissions
        $check_msg = '';
        if ($chkMe) {
            $check_msg = check_msg();
            set_lastvisit();
            db("UPDATE `" . $db['users'] . "` SET `time` = " . time() . ", `whereami` = '" . up($where) . "' WHERE `id` = " . (int)($userid) . ";");
        }

        //misc vars
        $language = $_SESSION['language'];
        $lang = $language;
        $clanname = re(settings("clanname"));
        $time = show(_generated_time, array("time" => $time));
        $headtitle = show(_index_headtitle, array("clanname" => $clanname));
        $rss = $clanname;
        $dir = $designpath;
        $title = re(strip_tags($title));

        if (check_internal_url())
            $index = error(_error_have_to_be_logged, 1);

        $where = preg_replace_callback("#autor_(.*?)$#", function ($id) {
            return re(data("nick", "$id[1]"));
        }, $where);
        $index = empty($index) ? '' : (empty($check_msg) ? '' : $check_msg) . '<table class="mainContent" cellspacing="1">' . $index . '</table>';

        //-> Sort & filter placeholders
        //default placeholders
        $arr = array("idir" => '../inc/images/admin', "dir" => $designpath);

        //check if placeholders are given
        $pholder = file_get_contents($designpath . "/index.html");

        //filter placeholders
        $blArr = array("[clanname]", "[title]", "[copyright]", "[java_vars]", "[min]",
            "[headtitle]", "[index]", "[time]", "[rss]", "[dir]", "[charset]", "[where]", "[lang]");
        $pholdervars = '';
        for ($i = 0; $i <= count($blArr) - 1; $i++) {
            if (preg_match("#" . $blArr[$i] . "#", $pholder))
                $pholdervars .= $blArr[$i];
        }

        for ($i = 0; $i <= count($blArr) - 1; $i++)
            $pholder = str_replace($blArr[$i], "", $pholder);

        $pholder = pholderreplace($pholder);
        $pholdervars = pholderreplace($pholdervars);

        //put placeholders in array
        $pholder = explode("^", $pholder);
        for ($i = 0; $i <= count($pholder) - 1; $i++) {
            if (strstr($pholder[$i], 'nav_'))
                $arr[$pholder[$i]] = navi($pholder[$i]);
            else {
                if (@file_exists(basePath . '/inc/menu-functions/' . $pholder[$i] . '.php'))
                    include_once(basePath . '/inc/menu-functions/' . $pholder[$i] . '.php');

                if (function_exists($pholder[$i]))
                    $arr[$pholder[$i]] = $pholder[$i]();
            }
        }

        $pholdervars = explode("^", $pholdervars);
        foreach ($pholdervars as $pholdervar) {
            if (isset(${$pholdervar})) {
                $arr[$pholdervar] = ${$pholdervar};
            }
        }

        //index output
        $index = (file_exists("../inc/_templates_/" . $tmpdir . "/" . $index_templ . ".html") ? show($index_templ, $arr) : show("index", $arr));
        if (!mysqli_persistconns)
            $mysql->close(); //MySQL

        if (HasDSGVO())
            cookie::save(); //Save Cookie

        if (debug_save_to_file) DebugConsole::save_log(); //Debug save to file
        $output = view_error_reporting ? DebugConsole::show_logs() . $index : $index; //Debug Console + Index Out

        if (!array_key_exists('do_show_dsgvo', $_SESSION)) {
            $_SESSION['do_show_dsgvo'] = true;
        }

        gz_output($output); // OUTPUT BUFFER END
    }
}
