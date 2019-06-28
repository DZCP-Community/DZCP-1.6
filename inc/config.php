<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

#########################################
//-> DZCP Settings Start
#########################################

define('view_error_reporting', false); // Zeigt alle Fehler und Notices etc.
define('debug_all_sql_querys', false);
define('debug_save_to_file', false);
define('debug_dzcp_handler', true);
define('fsockopen_support_bypass', false); //Umgeht die fsockopen pruefung
define('use_curl_support', true); //Soll CURL verwendet werden
define('use_min_css_js_files', false); //Sollen die Komprimierten versionen von css und js verwendet werden?

define('use_default_timezone', true); // Verwendende die Zeitzone vom Server
define('default_timezone', 'Europe/Berlin'); // Die zu verwendende Zeitzone selbst einstellen * 'use_default_timezone' auf false stellen. List of Supported Timezones: http://php.net/manual/en/timezones.php *

define('thumbgen_cache', true); // Sollen die verkleinerten Bilder der Thumbgen gespeichert werden
define('thumbgen_cache_time', 60 * 60); // Wie lange soll das Bild aus dem Cache verwendet werden

define('feed_update_time', 10 * 60); // Wann soll der Newsfeed aktualisiert werden
define('cookie_expires', (60 * 60 * 24 * 30 * 12)); // Wie Lange die Cookies des CMS ihre Gueltigkeit behalten.
define('file_get_contents_timeout', 10);

define('auto_db_optimize', true); // Soll in der Datenbank regelmaessig ein OPTIMIZE TABLE ausgefuehrt werden?
define('auto_db_optimize_interval', (3 * 24 * 60 * 60)); // Wann soll der OPTIMIZE TABLE ausgefuehrt werden, alle 3 Tage.

define('dzcp_version_checker', true); // Version auf DZCP.de abgleichen und benachrichtigen ob eine neue Version zur Verfuegung steht
define('dzcp_version_checker_refresh', (30 * 60)); // Wie lange soll gewartet werden um einen Versionsabgleich auszufuehren

define('admin_view_dzcp_news', true); // Entscheidet ob der Newstricker in der Administration angezeigt wird

define('buffer_gzip_compress_level', 4); // Level der GZIP Kompression 1 - 9
define('buffer_show_licence_bar', true); // Schaltet die "Powered by DZCP - deV!L`z Clanportal V1.6" am ende der Seite an oder aus

define('steam_enable', true); // Steam Status anzeigen
define('steam_avatar_cache', true); // Steam Useravatare fuer schnellen Zugriff speichern
define('steam_avatar_refresh', (60 * 60)); // Wann soll das Avatarbild aktualisiert werden
define('steam_refresh', (8 * 60 * 60)); // Wann soll der Steam Status in der Userliste aktualisiert werden
define('steam_api_refresh', 30); // Wann sollen die Daten der Steam API aktualisiert werden * Online / Offline / In-Game Status
define('steam_infos_cache', true); //Sollen die Profil Daten zwischen gespeichert werden, * Cache Use
define('steam_only_proxy', false); //Sollen soll nur der Steam Proxy Server verwendet werden

// DZCP.de API Autoupdates
define('api_enabled', true); //Sollem die funktionen der DZCP.de API verwendet werden? ( Keine Versionsabfrage, Keine Geolocation abfragen für die Memebermap usw. )
define('api_autoupdate', false); //Soll die DZCP.de API automatisch aktualisiert werden ( Nur in der Administration )
define('api_autoupdate_interval', (24 * 60 * 60)); //Wann soll die DZCP.de API automatisch aktualisiert werden ( alle 24 Std. )
define('api_autoupdate_dsgvo', false); //Soll die EU-DSGVO automatisch aktualisiert werden ( Nur in der Administration )
define('api_autoupdate_dsgvo_interval', (24 * 60 * 60)); //Wann soll die EU-DSGVO automatisch aktualisiert werden ( alle 24 Std. )

define('use_ssl_auto_redirect', false); //Wenn eine SSL-Verbindung möglich ist, dann wird der Besucher automatisch umgeleitet

/*
* Bitte vor der Aktivierung der Persistent Connections lesen:
* http://php.net/manual/de/features.persistent-connections.php
*/
define('mysqli_persistconns', false);

/*
 * Use SMTP connection with authentication for Mailing
 */
define('phpmailer_use_smtp', false); //Use SMTP for Mailing
define('phpmailer_use_auth', true); //Use SMTP authentication
define('phpmailer_smtp_host', 'localhost'); //Hostname of the mail server
define('phpmailer_smtp_port', 25); //SMTP port number
define('phpmailer_smtp_user', ''); //Username to use for SMTP authentication
define('phpmailer_smtp_password', '');//Password to use for SMTP authentication
define('phpmailer_smtp_secure', 'tls');//Enable TLS encryption, `ssl` also accepted

/*
 * Cache Configuration
 */
use Phpfastcache\Config\Config;
$config_cache = array(
    //auto ,apc, apcu, cassandra, cookie, couchbase, couchdb, files, leveldb, memcache, memcached, memstatic, mongodb, predis
    //redis, riak, sqlite, ssdb, wincache, xcache, zenddisk, zendshm
    "storage" => "files",
    "config" => new Config([
        "autoTmpFallback" => true,
        "defaultTtl" => 10,
        "defaultChmod" => 0775,
        "fallback" => 'files',
        "compressData" => true,
        "cacheFileExtension" => 'pfc',
        "path" => basePath . "/inc/_cache_/"
    ]),
    "dbc" => true,  //use database query caching * only use with memory cache
    "tpl" => false  //use template caching * only use with memory cache
);

//-> Legt die UserID des Rootadmins fest
//-> (dieser darf bestimmte Dinge, den normale Admins nicht duerfen, z.B. andere Admins editieren)
$rootAdmins = array(1); // Die ID/s der User die Rootadmins sein sollen, bei mehreren mit "," trennen '1,4,2,6' usw.

#########################################
//-> DZCP Settings End
#########################################

if (function_exists("date_default_timezone_set") && function_exists("date_default_timezone_get") && use_default_timezone)
    @date_default_timezone_set(@date_default_timezone_get());
else if (!use_default_timezone) date_default_timezone_set(default_timezone);
else date_default_timezone_set("Europe/Berlin");
if (!isset($thumbgen)) $thumbgen = false;

if (!$thumbgen) {
    if (view_error_reporting) {
        error_reporting(E_ALL);

        if (function_exists('ini_set'))
            ini_set('display_errors', 1);

        DebugConsole::initCon();

        if (debug_dzcp_handler)
            set_error_handler('dzcp_error_handler');
    } else {
        if (function_exists('ini_set'))
            ini_set('display_errors', 0);

        error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

        if (debug_dzcp_handler)
            set_error_handler('dzcp_error_handler');
    }
}

## REQUIRES ##
//DZCP-Install default variable
if (!isset($installer)) $installer = false;
if (!isset($sql_host) || !isset($sql_user) || !isset($sql_pass) || !isset($sql_db)) {
    $sql_prefix = '';
    $sql_host = '';
    $sql_user = '';
    $sql_pass = '';
    $sql_db = '';
}

if (file_exists(basePath . "/inc/mysql.php"))
    require_once(basePath . "/inc/mysql.php");

if (!isset($installation)) $installation = false;
if (!isset($updater)) $updater = false;
if (!isset($global_index)) $global_index = false;

function show($tpl = "", $array = array(), $array_lang_constant = array(), $array_block = array()) {
    global $tmpdir, $chkMe, $cache, $config_cache;
    if (!empty($tpl) && $tpl != null) {
        $template = basePath . "/inc/_templates_/" . $tmpdir . "/" . $tpl;
        $array['dir'] = '../inc/_templates_/' . $tmpdir;

        $CachedString = $cache->getItem(md5('tpl_' . $tmpdir . $template));
        if (is_null($CachedString->get())) {
            if(strlen($template . ".html") <= 128) {
                if (file_exists($template . ".html")) {
                    $tpl = file_get_contents($template . ".html");
                    if (!view_error_reporting && $config_cache['tpl'] && dbc_index::MemSetIndex()) {
                        $CachedString->set(base64_encode($tpl))->expiresAfter(60);
                        $cache->save($CachedString);
                    }
                }
            }
        } else {
            $tpl = base64_decode($CachedString->get());
        }

        //put placeholders in array
        $pholder = explode("^", pholderreplace($tpl));
        for ($i = 0; $i <= count($pholder) - 1; $i++) {
            if (in_array($pholder[$i], $array_block))
                continue;

            if (array_key_exists($pholder[$i], $array))
                continue;

            if (!strstr($pholder[$i], 'lang_'))
                continue;

            if (defined(substr($pholder[$i], 4)))
                $array[$pholder[$i]] = (count($array_lang_constant) >= 1 ? show(constant(substr($pholder[$i], 4)), $array_lang_constant) : constant(substr($pholder[$i], 4)));
        }

        unset($pholder);
        $tpl = (!defined('_Admin') || _Admin != 'true' ? preg_replace("|<is_admin>.*?</is_admin>|is", "", $tpl) : preg_replace("|<not_admin_menu>.*?</not_admin_menu>|is", "", $tpl));
        $tpl = (!$chkMe ? preg_replace("|<logged_in>.*?</logged_in>|is", "", $tpl) : preg_replace("|<logged_out>.*?</logged_out>|is", "", $tpl));
        $tpl = (!HasDSGVO() ? preg_replace("|<dsgvo_lock>.*?</dsgvo_lock>|is", "", $tpl) : $tpl);
        $tpl = (rootAdmin() ? preg_replace("|<is_root>.*?</is_root>|is", "", $tpl) : $tpl);
        $tpl = str_ireplace(array("<logged_in>", "</logged_in>", "<logged_out>", "</logged_out>"), '', $tpl);

        if (count($array) >= 1) {
            foreach ($array as $value => $code) {
                $tpl = str_replace('[' . $value . ']', $code, $tpl);
            }
        }
    }

    return $tpl;
}

//-> MySQL-Datenbankangaben
$prefix = $sql_prefix;
$db = array("host" => $sql_host,
    "user" => stripslashes($sql_user),
    "pass" => stripslashes($sql_pass),
    "db" => $sql_db,
    "prefix" => $prefix,
    "artikel" => $prefix . "artikel",
    "acomments" => $prefix . "acomments",
    "awards" => $prefix . "awards",
    "away" => $prefix . "away",
    "banned" => $prefix . "banned",
    "buddys" => $prefix . "userbuddys",
    "ipcheck" => $prefix . "ipcheck",
    "clankasse" => $prefix . "clankasse",
    "c_kats" => $prefix . "clankasse_kats",
    "c_payed" => $prefix . "clankasse_payed",
    "config" => $prefix . "config",
    "counter" => $prefix . "counter",
    "c_ips" => $prefix . "counter_ips",
    "c_who" => $prefix . "counter_whoison",
    "cw" => $prefix . "clanwars",
    "cw_comments" => $prefix . "cw_comments",
    "cw_player" => $prefix . "clanwar_players",
    "dsgvo" => $prefix . "dsgvo",
    "dsgvo_pers" => $prefix . "dsgvo_pers",
    "dsgvo_log" => $prefix . "dsgvo_log",
    "downloads" => $prefix . "downloads",
    "dl_kat" => $prefix . "download_kat",
    "events" => $prefix . "events",
    "f_access" => $prefix . "f_access",
    "f_abo" => $prefix . "f_abo",
    "f_kats" => $prefix . "forumkats",
    "f_posts" => $prefix . "forumposts",
    "f_skats" => $prefix . "forumsubkats",
    "f_threads" => $prefix . "forumthreads",
    "gallery" => $prefix . "gallery",
    "gb" => $prefix . "gb",
    "glossar" => $prefix . "glossar",
    "links" => $prefix . "links",
    "linkus" => $prefix . "linkus",
    "msg" => $prefix . "messages",
    "news" => $prefix . "news",
    "navi" => $prefix . "navi",
    "navi_kats" => $prefix . "navi_kats",
    "newscomments" => $prefix . "newscomments",
    "newskat" => $prefix . "newskat",
    "partners" => $prefix . "partners",
    "permissions" => $prefix . "permissions",
    "pos" => $prefix . "positions",
    "profile" => $prefix . "profile",
    "rankings" => $prefix . "rankings",
    "reg" => $prefix . "reg",
    "server" => $prefix . "server",
    "serverliste" => $prefix . "serverliste",
    "settings" => $prefix . "settings",
    "shout" => $prefix . "shoutbox",
    "sites" => $prefix . "sites",
    "squads" => $prefix . "squads",
    "squaduser" => $prefix . "squaduser",
    "sponsoren" => $prefix . "sponsoren",
    "slideshow" => $prefix . "slideshow",
    "sessions" => $prefix . "sessions",
    "taktik" => $prefix . "taktiken",
    "teamspeak" => $prefix . "teamspeak",
    "users" => $prefix . "users",
    "usergallery" => $prefix . "usergallery",
    "usergb" => $prefix . "usergb",
    "userpos" => $prefix . "userposis",
    "userstats" => $prefix . "userstats",
    "votes" => $prefix . "votes",
    "vote_results" => $prefix . "vote_results");
unset($prefix, $sql_host, $sql_user, $sql_pass, $sql_db);

if ($db['host'] != '' && $db['user'] != '' && $db['pass'] != '' && $db['db'] != '' && !$thumbgen) {
    $db_host = (mysqli_persistconns ? 'p:' : '') . $db['host'];
    $mysql = new mysqli($db_host, $db['user'], $db['pass'], $db['db']);
    if ($mysql->connect_error) {
        die("<b>Fehler beim Zugriff auf die Datenbank!");
    }
}

// Start session if no headers were sent
if (!headers_sent()) {
    session_start();

    if (!isset($_SESSION['PHPSESSID'])) {
        @session_destroy();
        @session_start();
        $_SESSION['PHPSESSID'] = true;
    }
} else {
    exit("Die Session konnte nicht gestartet werden! ( headers has already sent )<p> STOP!");
}

//MySQLi-Funktionen
function _rows($rows)
{
    return array_key_exists('_stmt_rows_', $rows) ? $rows['_stmt_rows_'] : $rows->num_rows;
}

function _fetch($fetch)
{
    return array_key_exists('_stmt_rows_', $fetch) ? $fetch[0] : $fetch->fetch_assoc();
}

function _real_escape_string($string = '')
{
    global $mysql;
    return !empty($string) ? $mysql->real_escape_string($string) : '';
}

function db($query = '', $rows = false, $fetch = false)
{
    global $mysql, $updater, $db;

    if (debug_all_sql_querys) DebugConsole::wire_log('debug', 9, 'SQL_Query', $query);
    if ($updater) {
        $qry = $mysql->query($query);
    } else {
        if (!$qry = $mysql->query($query)) {
            DebugConsole::sql_error_handler($query);
            $language_text = [];
            include_once(basePath.'/inc/lang/languages/english.php');
            $get = _fetch($mysql->query("SELECT `clanname` FROM `" . $db['settings'] . "`;"));
            die('<img src="../inc/images/dberror.png" align="absmiddle"/>&nbsp;&nbsp;<b>Upps...</b><br /><br />Entschuldige bitte! Das h&auml;tte nicht passieren d&uuml;rfen.<p>'.
                'Wir k&uuml;mmern uns so schnell wie m&ouml;glich darum.<br><br>' . utf8_decode($get['clanname']) . '<br><br>' . $language_text['_back']);
        }
    }

    if ($rows && !$fetch)
        return _rows($qry);
    else if ($fetch && $rows)
        return $qry->fetch_array(MYSQLI_NUM);
    else if ($fetch && !$rows)
        return _fetch($qry);

    return $qry;
}

/**
 *  i     corresponding variable has type integer
 *  d     corresponding variable has type double
 *  s     corresponding variable has type string
 *  b     corresponding variable is a blob and will be sent in packets
 * @param $query
 * @param array $params
 * @param bool $rows
 * @param bool $fetch
 * @return array|mixed|void
 */
function db_stmt($query, $params = array('si', 'hallo', '4'), $rows = false, $fetch = false)
{
    global $prefix, $mysql;
    if (!$statement = $mysql->prepare($query)) die('<b>MySQL-Query failed:</b><br /><br /><ul>' .
    '<li><b>ErrorNo</b> = ' . !empty($prefix) ? str_replace($prefix, '', $mysql->connect_errno) : $mysql->connect_errno .
    '<li><b>Error</b>   = ' . !empty($prefix) ? str_replace($prefix, '', $mysql->connect_error) : $mysql->connect_error .
    '<li><b>Query</b>   = ' . !empty($prefix) ? str_replace($prefix, '', $query) . '</ul>' : $query);

    call_user_func_array(array($statement, 'bind_param'), refValues($params));
    if (!$statement->execute()) die('<b>MySQL-Query failed:</b><br /><br /><ul>' .
    '<li><b>ErrorNo</b> = ' . !empty($prefix) ? str_replace($prefix, '', $mysql->connect_errno) : $mysql->connect_errno .
    '<li><b>Error</b>   = ' . !empty($prefix) ? str_replace($prefix, '', $mysql->connect_error) : $mysql->connect_error .
    '<li><b>Query</b>   = ' . !empty($prefix) ? str_replace($prefix, '', $query) . '</ul>' : $query);

    $meta = mysqli_stmt_result_metadata($statement);
    if (!$meta || empty($meta)) {
        mysqli_stmt_close($statement);
        return;
    }
    $row = array();
    $parameters = array();
    $results = array();
    while ($field = mysqli_fetch_field($meta)) {
        $parameters[] = &$row[$field->name];
    }

    mysqli_stmt_store_result($statement);
    $results['_stmt_rows_'] = mysqli_stmt_num_rows($statement);
    call_user_func_array(array($statement, 'bind_result'), refValues($parameters));

    while (mysqli_stmt_fetch($statement)) {
        $x = array();
        foreach ($row as $key => $val) {
            $x[$key] = $val;
        }

        $results[] = $x;
    }

    if ($rows && !$fetch)
        return _rows($results);
    else if ($fetch && !$rows)
        return _fetch($results);

    return $results;
}

function db_optimize()
{
    global $db;
    //Garbage Collection for ipcheck
    $qry = db("SELECT `id` FROM `" . $db['ipcheck'] . "` " .
        "WHERE `created` <= " . (time() - (14 * 24 * 60 * 60)) . " " . //14 Tage
        "AND `time` <= " . (time() - (14 * 24 * 60 * 60)) . " AND `time` >= 1;");
    while ($get = _fetch($qry)) {
        db("DELETE FROM `" . $db['ipcheck'] . "` WHERE `id` = " . $get['id'] . ";");
    }

    //Garbage Collection for counter ips
    $qry = db("SELECT `id` FROM `" . $db['c_ips'] . "` " .
        "WHERE `datum` <= " . (time() - (30 * 24 * 60 * 60)) . ";"); //30 Tage
    while ($get = _fetch($qry)) {
        db("DELETE FROM `" . $db['c_ips'] . "` WHERE `id` = " . $get['id'] . ";");
    }

    //Garbage Collection for counter whoison
    $qry = db("SELECT `id` FROM `" . $db['c_who'] . "` " .
        "WHERE `online` <= " . (time() - (3 * 24 * 60 * 60)) . ";"); //3 Tage
    while ($get = _fetch($qry)) {
        db("DELETE FROM `" . $db['c_who'] . "` WHERE `id` = " . $get['id'] . ";");
    }

    $sql = '';
    $blacklist = array('host', 'user', 'pass', 'db', 'prefix');
    foreach ($db as $key => $tb) {
        if (!in_array($key, $blacklist))
            $sql .= '`' . $tb . '`, ';
    }

    $sql = substr($sql, 0, -2);
    db('OPTIMIZE TABLE ' . $sql . ';');
}

function refValues($arr)
{
    if (strnatcmp(phpversion(), '5.3') >= 0) {
        $refs = array();
        foreach ($arr as $key => $value)
            $refs[$key] = &$arr[$key];

        return $refs;
    }

    return $arr;
}

//Auto Update Detect
if (file_exists(basePath . "/_installer/index.php") &&
    file_exists(basePath . "/inc/mysql.php") && !$installation && !$thumbgen) {
    $user_check = db("SELECT * FROM `" . $db['users'] . "` WHERE `id` = 1;", false, true);
    if (!array_key_exists('pwd_md5', $user_check) && !$installer)
        $global_index ? header('Location: _installer/update.php') :
            header('Location: ../_installer/update.php');
    unset($user_check);
}
