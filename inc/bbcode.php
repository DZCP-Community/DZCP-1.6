<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

//Filter 404
$test = strtolower($_SERVER["REQUEST_URI"]);
if (strpos($test, 'index.php/') !== false ||
    strpos($test, 'ajax.php/') !== false) {
    header("HTTP/1.0 404 Not Found");
    exit();
}

## INCLUDES/REQUIRES ##
require_once(basePath.'/inc/_version.php');
require_once(basePath."/inc/cookie.php");
require_once(basePath.'/inc/server_query/_functions.php');
require_once(basePath."/inc/teamspeak_query.php");
require_once(basePath.'/inc/steamapi.php');
require_once(basePath.'/inc/api.php');

//Libs
use phpFastCache\CacheManager;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use PHPMailer\PHPMailer\PHPMailer;

## Is AjaxJob ##
$ajaxJob = (!isset($ajaxJob) ? false : $ajaxJob);

//Set DSGVO to false
if(!array_key_exists('DSGVO',$_SESSION)) {
    $_SESSION['DSGVO'] = false;
}

//Set DSGVO Lock
if(!array_key_exists('user_has_dsgvo_lock',$_SESSION)) {
    $_SESSION['user_has_dsgvo_lock'] = false;
}

//Check is DSGVO Set?
if(isset($_GET['dsgvo'])) {
    switch ((int)$_GET['dsgvo']) {
        case 1:
            $_SESSION['DSGVO'] = true;
            $_SESSION['do_show_dsgvo'] = true;
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
            break;
        default:
            $_SESSION['DSGVO'] = false;
            $_SESSION['do_show_dsgvo'] = true;
            $_SESSION['user_has_dsgvo_lock'] = false;
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
    }
}

// Cache
try {
    CacheManager::setDefaultConfig(array(
        "path" => basePath . "/inc/_cache_/",
        "defaultTtl" => 10,
        "storage" => $config_cache['storage'],
        "memcache" => $config_cache['server_mem'],
        "redis" => $config_cache['server_redis'],
        "ssdb" => $config_cache['server_ssdb'],
        "default_chmod" => 0775,
        "compress_data" => true,
        "cacheFileExtension" => 'pfc',
        "fallback" => "files"
    ));
} catch (\phpFastCache\Exceptions\phpFastCacheInvalidArgumentException $e) {
}

$cache = CacheManager::getInstance($config_cache['storage']); // return your setup storage

//-> Automatische Datenbank Optimierung
if(auto_db_optimize && settings('db_optimize',false) <= time() && !$installer && !$updater) {
    @ignore_user_abort(true);
    db("UPDATE `".$db['settings']."` SET `db_optimize` = '".(time()+auto_db_optimize_interval)."' WHERE `id` = 1;");
    db_optimize();
    @ignore_user_abort(false);
}

//-> Settingstabelle auslesen * Use function settings('xxxxxx');
if(!dbc_index::issetIndex('settings')) {
    $get_settings = db("SELECT * FROM `".$db['settings']."`;",false,true);
    dbc_index::setIndex('settings', $get_settings);
    unset($get_settings);
}

//-> Configtabelle auslesen * Use function config('xxxxxx');
if(!dbc_index::issetIndex('config')) {
    $get_config = db("SELECT * FROM `".$db['config']."`;",false,true);
    dbc_index::setIndex('config', $get_config);
    unset($get_config);
}

if(HasDSGVO()) {
//-> Cookie initialisierung
    cookie::init('dzcp_' . settings('prev'),false,"/",re(settings('i_domain')));
}

//-> SteamAPI
SteamAPI::set('apikey',re(settings('steam_api_key')));

//-> Language auslesen
if(array_key_exists('language',$_SESSION) && !empty($_SESSION['language'])) {
    if(!file_exists(basePath.'/inc/lang/languages/'.$_SESSION['language'].'.php')) {
        $_SESSION['language'] = re(settings('language'));
    }
} else {
    $_SESSION['language'] = re(settings('language'));
}

//-> einzelne Definitionen
$CrawlerDetect = new CrawlerDetect();
$isSpider = $CrawlerDetect->isCrawler();
$subfolder = basename(dirname(dirname($_SERVER['PHP_SELF']).'../'));
$httphost = $_SERVER['HTTP_HOST'].(empty($subfolder) ? '' : '/'.$subfolder);
$domain = str_replace('www.','',$httphost);
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
if(HasDSGVO() && (cookie::get('id') != false && cookie::get('pkey') != false && empty($_SESSION['id']) && !checkme())) {
    //-> User aus der Datenbank suchen
    $sql = db_stmt("SELECT `id`,`user`,`nick`,`pwd`,`email`,`level`,`time`,`pkey`,`dsgvo_lock`,`language` FROM `".
        $db['users']."` WHERE `id` = ? AND `pkey` = ? AND `level` != 0;",
        array('is', cookie::get('id'), cookie::get('pkey')));
    if(_rows($sql)) {
        $get = _fetch($sql);
        if($get['dsgvo_lock']) {
            $_SESSION['user_has_dsgvo_lock'] = true;
            $_SESSION['dsgvo_lock_permanent_login'] = true;
            $_SESSION['dsgvo_lock_login_id'] = $get['id'];
            if(!array_key_exists('dsgvo_lock_login_id',$_SESSION))
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

            if(!empty($get['language'])) {
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
        $_SESSION['id']        = '';
        $_SESSION['pwd']       = '';
        $_SESSION['ip']        = '';
        $_SESSION['lastvisit'] = '';
        $_SESSION['pkey']      = '';
    }

    unset($sql);
}

//Check UserID & Level
$userid = userid();
$chkMe = checkme();

//-> Sprache aendern
if(isset($_GET['set_language'])) {
    if(file_exists(basePath."/inc/lang/languages/".$_GET['set_language'].".php")) {
        $_SESSION['language'] = $_GET['set_language'];
    }

    if($chkMe && $userid) {
        db("UPDATE `" . $db['users'] . "` SET `language` = '".$_SESSION['language']."' WHERE `id` = " .$userid. ";");
    }

    header("Location: ".$_SERVER['HTTP_REFERER']);
}

lang($_SESSION['language']); //Lade Sprache

if(!$chkMe) {
    $_SESSION['id']        = '';
    $_SESSION['pwd']       = '';
    $_SESSION['ip']        = '';
    $_SESSION['lastvisit'] = '';
    $_SESSION['identy_id'] = '';
}

//-> Prueft ob der User gebannt ist, oder die IP des Clients warend einer offenen session verändert wurde.
if($chkMe && $userid && !empty($_SESSION['ip'])) {
    if($_SESSION['ip'] != visitorIp() || isBanned($userid,false) ) {
        $_SESSION['id']        = '';
        $_SESSION['pwd']       = '';
        $_SESSION['ip']        = '';
        $_SESSION['lastvisit'] = '';
        $_SESSION['identy_id'] = '';
        session_unset();
        session_destroy();
        session_regenerate_id();
        if(HasDSGVO()) {
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
    if(array_key_exists('DSGVO',$_SESSION) && $_SESSION['DSGVO'])
        return true;

    return false;
}

/**
* Gibt die IP des Besuchers / Users zurück
* Forwarded IP Support
*/
function visitorIp() {
    if(array_key_exists('identy_ip',$_SESSION)) {
        if(!empty($_SESSION['identy_ip']))
            return $_SESSION['identy_ip'];
    }

    $TheIp=$_SERVER['REMOTE_ADDR'];
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        $TheIp = $_SERVER['HTTP_X_FORWARDED_FOR'];

    if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
        $TheIp = $_SERVER['HTTP_CLIENT_IP'];

    if(isset($_SERVER['HTTP_FROM']) && !empty($_SERVER['HTTP_FROM']))
        $TheIp = $_SERVER['HTTP_FROM'];

    $TheIp_X = explode('.',$TheIp);
    if(count($TheIp_X) == 4 && $TheIp_X[0]<=255 && $TheIp_X[1]<=255 && $TheIp_X[2]<=255 && $TheIp_X[3]<=255 && preg_match("!^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$!",$TheIp))
        return trim($TheIp);

    return '0.0.0.0';
}

/**
 * Pruft eine IP gegen eine IP-Range
 * @param ipv4 $ip
 * @param ipv4 range $range
 * @return boolean
 */
function validateIpV4Range ($ip, $range) {
    if(!is_array($range)) {
        $counter = 0;
        $tip = explode ('.', $ip);
        $rip = explode ('.', $range);
        foreach ($tip as $targetsegment) {
            $rseg = $rip[$counter];
            $rseg = preg_replace ('=(\[|\])=', '', $rseg);
            $rseg = explode ('-', $rseg);
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
            $tip = explode ('.', $ip);
            $rip = explode ('.', $range_num);
            foreach ($tip as $targetsegment) {
                $rseg = $rip[$counter];
                $rseg = preg_replace ('=(\[|\])=', '', $rseg);
                $rseg = explode ('-', $rseg);
                if (!isset($rseg[1])) {
                    $rseg[1] = $rseg[0];
                }

                if ($targetsegment < $rseg[0] || $targetsegment > $rseg[1]) {
                    return false;
                } $counter++;
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
    if(fsockopen_support_bypass) return true;

    if(disable_functions('fsockopen') || disable_functions('fopen'))
        return false;

    return true;
}

function disable_functions($function='') {
    if(!function_exists($function)) return true;
    $disable_functions = ini_get('disable_functions');
    if(empty($disable_functions)) return false;
    $disabled_array = explode(',', $disable_functions);
    foreach ($disabled_array as $disabled) {
       if(strtolower(trim($function)) == strtolower(trim($disabled)))
            return true;
    }

    return false;
}

function allow_url_fopen_support() {
    if(ini_get('allow_url_fopen') == 1)
        return true;

    return false;
}

//-> Auslesen der UserID
function userid() {
    global $db;
    if(HasDSGVO()) {
        if (empty($_SESSION['id']) || empty($_SESSION['pwd'])) return 0;
        if (!dbc_index::issetIndex('user_' . $_SESSION['id'])) {
            $sql = db("SELECT * FROM `" . $db['users'] . "` WHERE `id` = ".$_SESSION['id']." AND `pwd` = '".$_SESSION['pwd']."';");
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

    if($var=='HTTP_REFERER') { //Fix for empty HTTP_REFERER
        return GetServerVars('REQUEST_SCHEME').'://'.GetServerVars('HTTP_HOST').
            GetServerVars('DOCUMENT_URI');
    }

    return false;
}

if (isset($_GET['tmpl_set'])) {
    sysTemplateswitch();
}

if(!empty($_SESSION['tmpdir'])) {
    if(!empty($_SESSION['tmpdir'])) {
        if(file_exists(basePath."/inc/_templates_/".$_SESSION['tmpdir']))
            $tmpdir = $_SESSION['tmpdir'];
        else
            $tmpdir = $files[0];
    } else
        $tmpdir = $files[0];
} else {
    if(file_exists(basePath."/inc/_templates_/".$sdir))
        $tmpdir = $sdir;
    else
        $tmpdir = $files[0];
} unset($files);

$designpath = '../inc/_templates_/'.$tmpdir;

//-> Languagefiles einlesen
/**
 * @param $lng
 */
function lang($lng) {
    if(!file_exists(basePath."/inc/lang/languages/".$lng.".php")) {
        $files = get_files(basePath.'/inc/lang/languages/',false,true,array('php'));
        $lng = str_replace('.php','',$files[0]);
    }

    $language_text = array(); $charset = 'utf-8';
    require_once(basePath."/inc/lang/global.php");
    require_once(basePath."/inc/lang/languages/english.php"); //Load Base Language
    require_once(basePath."/inc/lang/languages/dsgvo/english.php"); //Load Base DSGVO
    require_once(basePath."/inc/lang/languages/".$lng.".php");

    if(file_exists(basePath."/inc/lang/languages/dsgvo/".$lng.".php"))
        require_once(basePath."/inc/lang/languages/dsgvo/".$lng.".php");

    //Set bBase-Content-type header
    header("Content-type: text/html; charset=".$charset);

    //-> Neue Languages einbinden, sofern vorhanden
    if($language_files = get_files(basePath.'/inc/additional-languages/'.$lng.'/',false,true,array('php'))) {
        foreach($language_files AS $languages) {
            if(file_exists(basePath.'/inc/additional-languages/'.$lng.'/'.$languages))
                include_once(basePath.'/inc/additional-languages/'.$lng.'/'.$languages);
        }
        unset($language_files,$languages);
    }

    foreach ($language_text as $key => $text) {
        if(!defined($key)) {
            define($key,$text);
        }
    } unset($language_text,$key,$text);
}

//->Daten uber file_get_contents oder curl abrufen
function get_external_contents($url,$post=false,$nogzip=false,$timeout=file_get_contents_timeout) {
    if(!allow_url_fopen_support() && (!extension_loaded('curl') || !use_curl_support))
        return false;
    
    $url_p = @parse_url($url);
    $host = $url_p['host'];
    $port = isset($url_p['port']) ? $url_p['port'] : 80;
    $port = (($url_p['scheme'] == 'https' && $port == 80) ? 443 : $port);
    if(!ping_port($host,$port,$timeout)) return false;

    if(extension_loaded('curl') && use_curl_support) {
        if(!$curl = curl_init())
            return false;
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_USERAGENT, "DZCP");
		
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , $timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout * 2); // x 2
        
        //For POST
        /** @var TYPE_NAME $post */
        if($post != false && count($post) >= 1) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            curl_setopt($curl, CURLOPT_VERBOSE , 0 );
        }
        
        $gzip = false;

        if(function_exists('gzinflate') && !$nogzip) {
            $gzip = true;
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Encoding: gzip,deflate'));
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        }
        
        if($url_p['scheme'] == 'https') { //SSL
            curl_setopt($curl, CURLOPT_PORT , $port); 
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (!($content = curl_exec($curl)) || empty($content)) {
            return false;
        }

        if($gzip) {
			$org_content = $content;
            $curl_info = curl_getinfo($curl,CURLINFO_HEADER_OUT);
            if(stristr($curl_info, 'accept-encoding') && stristr($curl_info, 'gzip') && !$nogzip) {
                $content = @gzinflate( substr($content,10,-8) );
				if(!$content)
					$content = $org_content;
            }
        }

        @curl_close($curl);
        unset($curl);
    } else {
        if($url_p['scheme'] == 'https') //HTTPS not Supported!
            $url = str_replace('https', 'http', $url);
        
        $opts = array();
        $opts['http']['method'] = "GET";
        $opts['http']['timeout'] = $timeout * 2;
                
        $gzip = false;

        if(function_exists('gzinflate') && !$nogzip) {
            $gzip = true;
            $opts['http']['header'] = 'Accept-Encoding:gzip,deflate'."\r\n";
        }
        
        $context = stream_context_create($opts);
        $content = file_get_contents($url, false, $context);
        if (!$content || empty($content)) {
            return false;
        }
        $content = substr($content, -1, 40000);

        if($gzip) {
            foreach($http_response_header as $c => $h) {
                if(stristr($h, 'content-encoding') && stristr($h, 'gzip')) {
                    $content = gzinflate( substr($content,10,-8) );
                }
            }
        }
    }
    
    return $content;
}

//-> Sprachdateien auflisten
function languages() {
    $lang="";
    $files = get_files('../inc/lang/languages/',false,true,array('php'));
    for($i=0;$i<=count($files)-1;$i++) {
        $file = str_replace('.php','',$files[$i]);
        $upFile = strtoupper(substr($file,0,1)).substr($file,1);
        if(file_exists('../inc/lang/flaggen/'.$file.'.gif'))
            $lang .= '<a href="?set_language='.$file.'"><img src="../inc/lang/flaggen/'.$file.'.gif" alt="'.$upFile.'" title="'.$upFile.'" class="icon" /></a> ';
    }

    return $lang;
}

//-> Userspezifiesche Dinge
if($userid >= 1 && $ajaxJob != true && HasDSGVO()) {
    db("UPDATE `".$db['userstats']."` SET `hits` = (hits+1), `lastvisit` = '".((int)$_SESSION['lastvisit'])."' WHERE `user` = ".$userid.";");
}

//-> Settings auslesen
function settings($what,$use_dbc=true) {
    global $db;

    if(is_array($what)) {
        if($use_dbc)
            $dbd = dbc_index::getIndex('settings');
        else
            $dbd = db("SELECT * FROM `".$db['settings']."`;",false,true);

        $return = array();
        foreach ($dbd as $key => $var) {
            if(!in_array($key,$what))
                continue;

            $return[$key] = $var;
        }

        return $return;
    } else {
        if($use_dbc)
            return dbc_index::getIndexKey('settings', $what);

        $get = db("SELECT `".$what."` FROM `".$db['settings']."`;",false,true);
        return $get[$what];
    }
}

//-> Config auslesen
function config($what,$use_dbc=true) {
    global $db;

    if(is_array($what)) {
        if($use_dbc)
            $dbd = dbc_index::getIndex('config');
        else
            $dbd = db("SELECT * FROM `".$db['config']."`;",false,true);

        $return = array();
        foreach ($dbd as $key => $var) {
            if(!in_array($key,$what))
                continue;

            $return[$key] =  $var;
        }

        return $return;
    } else {
        if($use_dbc)
            return dbc_index::getIndexKey('config', $what);

        $query = db("SELECT `".$what."` FROM `".$db['config']."`;");
        if(_rows($query)) {
            $get = _fetch($query);
            return $get[$what];
        }
    }

    return 0;
}

//-> Prueft ob der User ein Rootadmin ist
function rootAdmin($userid=0) {
    global $rootAdmins;
    $userid = !$userid ? userid() : $userid;
    if(!count($rootAdmins)) return false;
    return in_array($userid, $rootAdmins);
}

//-> PHP-Code farbig anzeigen
function highlight_text($txt) {
    while(preg_match("=\[php\](.*)\[/php\]=Uis",$txt)!=FALSE) {
        preg_match("=\[php\](.*)\[/php\]=Uis",$txt,$matches);
        $src = $matches[1];
        $src = str_replace('<?php','',$src);
        $src = str_replace('<?php','',$src);
        $src = str_replace('?>','',$src);
        $src = str_replace("&#39;", "'", $src);
        $src = str_replace("&#34;", "\"", $src);
        $src = str_replace("&amp;","&",$src);
        $src = str_replace("&lt;","<",$src);
        $src = str_replace("&gt;",">",$src);
        $src = str_replace('<?php','&#60;?',$src);
        $src = str_replace('?>','?&#62;',$src);
        $src = str_replace("&quot;","\"",$src);
        $src = str_replace("&nbsp;"," ",$src);
        $src = str_replace("&nbsp;"," ",$src);
        $src = str_replace("<p>","\n",$src);
        $src = str_replace("</p>","",$src);
        $l = explode("<br />", $src);
        $src = preg_replace("#\<br(.*?)\>#is","\n",$src);
        $src = '<?php'.$src.' ?>';
        $colors = array('#111111' => 'string', '#222222' => 'comment', '#333333' => 'keyword', '#444444' => 'bg',     '#555555' => 'default', '#666666' => 'html');

        foreach ($colors as $color => $key)
            ini_set('highlight.'.$key, $color);

        // Farben ersetzen & highlighten
        $src = preg_replace('!style="color: (#\d{6})"!e','"class=\"".$prefix.$colors["\1"]."\""',highlight_string($src, TRUE));

        // PHP-Tags komplett entfernen
        $src = str_replace('&lt;?php','',$src);
        $src = str_replace('?&gt;','',$src);
        $src = str_replace('&amp;</span><span class="comment">#60;?','&lt;?',$src);
        $src = str_replace('?&amp;</span><span class="comment">#62;','?&gt;',$src);
        $src = str_replace('&amp;#60;?','&lt;?',$src);
        $src = str_replace('?&amp;#62;','?&gt;',$src);
        $src = str_replace(":", "&#58;", $src);
        $src = str_replace("(", "&#40;", $src);
        $src = str_replace(")", "&#41;", $src);
        $src = str_replace("^", "&#94;", $src);

        // Zeilen zaehlen
        $lines = "";
        for($i=1;$i<=count($l)+1;$i++)
            $lines .= $i.".<br />";

        // Ausgabe
        $code = '<div class="codeHead">&nbsp;&nbsp;&nbsp;Code:</div><div class="code"><table style="width:100%;padding:0px" cellspacing="0"><tr><td class="codeLines">'.$lines.'</td><td class="codeContent">'.$src.'</td></table></div>';
        $txt = preg_replace("=\[php\](.*)\[/php\]=Uis",$code,$txt,1);
    }

    return $txt;
}

function regexChars($txt) {
    $txt = strip_tags($txt);
    $txt = str_replace('"','&quot;',$txt);
    $txt = str_replace('\\','\\\\',$txt);
    $txt = str_replace('<','\<',$txt);
    $txt = str_replace('>','\>',$txt);
    $txt = str_replace('/','\/',$txt);
    $txt = str_replace('.','\.',$txt);
    $txt = str_replace(':','\:',$txt);
    $txt = str_replace('^','\^',$txt);
    $txt = str_replace('$','\$',$txt);
    $txt = str_replace('|','\|',$txt);
    $txt = str_replace('?','\?',$txt);
    $txt = str_replace('*','\*',$txt);
    $txt = str_replace('+','\+',$txt);
    $txt = str_replace('-','\-',$txt);
    $txt = str_replace('(','\(',$txt);
    $txt = str_replace(')','\)',$txt);
    $txt = str_replace('[','\[',$txt);
    $txt = str_replace(']','\]',$txt);
    $txt = str_replace('}','\}',$txt);
    $txt = str_replace('{','\{',$txt);
    $txt = str_replace("\r",'',$txt);
    return str_replace("\n",'',$txt);
}

//-> Glossarfunktion
$use_glossar = true; //Global
function glossar_load_index() {
    global $db,$use_glossar;
    if(!$use_glossar) return false;

    $gl_words = array(); $gl_desc = array();
    $qryglossar = db("SELECT `word`,`glossar` FROM `".$db['glossar']."`;");
    while($getglossar = _fetch($qryglossar)) {
        $gl_words[] = re($getglossar['word']);
        $gl_desc[]  = $getglossar['glossar'];
    }

    dbc_index::setIndex('glossar', array('gl_words' => $gl_words, 'gl_desc' => $gl_desc));
    return true;
}

/**
 * @param $txt
 * @return mixed
 */
function glossar($txt) {
    global $gl_words,$gl_desc,$use_glossar,$ajaxJob;

    if(!$use_glossar || $ajaxJob)
        return $txt;

    if(!dbc_index::issetIndex('glossar'))
        glossar_load_index();

    $gl_words = dbc_index::getIndexKey('glossar', 'gl_words');
    $gl_desc = dbc_index::getIndexKey('glossar', 'gl_desc');

    $txt = str_replace('&#93;',']',$txt);
    $txt = str_replace('&#91;','[',$txt);

    // mark words
    if(is_array($gl_words)) {
        foreach ($gl_words as $gl_word) {
            $w = addslashes(regexChars(html_entity_decode($gl_word)));
            $txt = str_ireplace(' ' . $w . ' ', ' <tmp|' . $w . '|tmp> ', $txt);
            $txt = str_ireplace('>' . $w . '<', '> <tmp|' . $w . '|tmp> <', $txt);
            $txt = str_ireplace('>' . $w . ' ', '> <tmp|' . $w . '|tmp> ', $txt);
            $txt = str_ireplace(' ' . $w . '<', ' <tmp|' . $w . '|tmp> <', $txt);
        }

        // replace words
        for($g=0;$g<=count($gl_words)-1;$g++) {
            $desc = regexChars($gl_desc[$g]);
            $info = 'onmouseover="DZCP.showInfo(\''.jsconvert($desc).'\')" onmouseout="DZCP.hideInfo()"';
            $w = regexChars(html_entity_decode($gl_words[$g]));
            $r = "<a class=\"glossar\" href=\"../glossar/?word=".$gl_words[$g]."\" ".$info.">".$gl_words[$g]."</a>";
            $txt = str_ireplace('<tmp|'.$w.'|tmp>', $r, $txt);
        }
    }

    $txt = str_replace(']','&#93;',$txt);
    return str_replace('[','&#91;',$txt);
}

function bbcodetolow($founds) {
    return "[".strtolower($founds[1])."]".trim($founds[2])."[/".strtolower($founds[3])."]";
}

//-> Replaces
function replace($txt,$type=false,$no_vid_tag=false) {
    $txt = str_replace("&#34;","\"",$txt);

    if($type)
        $txt = preg_replace("#<img src=\"(.*?)\" mce_src=\"(.*?)\"(.*?)\>#i","<img src=\"$2\" alt=\"\">",$txt);

    $txt = preg_replace_callback("/\[(.*?)\](.*?)\[\/(.*?)\]/","bbcodetolow",$txt);
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

    $txt = preg_replace($var,$repl,$txt);
    $txt = preg_replace_callback("#\<img(.*?)\>#", function($img) {
        if(preg_match("#class#i",$img[1])) {
            return "<img".$img[1].">";
        } else {
            return "<img class=\"content\"".$img[1].">";
        }
    }, $txt);

    if(!$no_vid_tag) {
            $txt = preg_replace_callback("/\[youtube\](?:http?s?:\/\/)?(?:www\.)?youtu(?:\.be\/|be\.com\/watch\?v=)([A-Z0-9\-_]+)(?:&(.*?))?\[\/youtube\]/i",
                function($match) {
                    return '<object width="425" height="344"><param name="movie" value="//www.youtube.com/v/'.trim($match[1]).'?hl=de_DE&amp;version=3&amp;rel=0"></param>
            <param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
            <embed src="//www.youtube.com/v/'.trim($match[1]).'?hl=de_DE&amp;version=3&amp;rel=0" type="application/x-shockwave-flash" width="425" height="344" allowscriptaccess="always" allowfullscreen="true">
            </embed></object>';
                }, $txt);
    }

    $txt = str_replace("\"","&#34;",$txt);
    return preg_replace("#(\w){1,1}(&nbsp;)#Uis","$1 ",$txt);
}

//-> Badword Filter
function BadwordFilter($txt) {
    $words = explode(",",trim(settings('badwords')));
    foreach($words as $word)
    { $txt = preg_replace("#".$word."#i", str_repeat("*", strlen($word)), $txt); }
    return $txt;
}

//-> Funktion um Bestimmte Textstellen zu markieren
function hl($text, $word) {
    $ret = array();
    if(!empty($_GET['hl']) && $_SESSION['search_type'] == 'text') {
        if($_SESSION['search_con'] == 'or') {
            $words = explode(" ",$word);
            for($x=0;$x<count($words);$x++)
                $ret['text'] = preg_replace("#".$words[$x]."#i",'<span class="fontRed" title="'.$words[$x].'">'.$words[$x].'</span>',$text);
        }
        else
            $ret['text'] = preg_replace("#".$word."#i",'<span class="fontRed" title="'.$word.'">'.$word.'</span>',$text);

        if(!preg_match("#<span class=\"fontRed\" title=\"(.*?)\">#", $ret['text']))
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
function eMailAddr($email) {
    $output = "";

    for($i=0;$i<strlen($email);$i++)
    { $output.=str_replace(substr($email,$i,1),"&#".ord(substr($email,$i,1)).";",substr($email,$i,1)); }

    return $output;
}

//-> Leerzeichen mit + ersetzen (w3c)
function convSpace($string) {
    $string = spChars($string);
    return str_replace(" ","+",$string);
}

//-> BBCode
function re_bbcode($txt) {
    $txt = spChars($txt);
    $txt = str_replace("'", "&#39;", $txt);
    $txt = str_replace("[","&#91;",$txt);
    $txt = str_replace("]","&#93;",$txt);
    $txt = str_replace("&lt;","&#60;",$txt);
    $txt = str_replace("&gt;","&#62;",$txt);
    return stripslashes($txt);
}

/* START # from wordpress under GBU GPL license
   URL autolink function */
function _make_url_clickable_cb($matches)
{
    $ret = '';
    $url = $matches[2];

    if ( empty($url) )
        return $matches[0];
    // removed trailing [.,;:] from URL
    if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true ) {
        $ret = substr($url, -1);
        $url = substr($url, 0, strlen($url)-1);
    }

    return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $ret;
}

function _make_web_ftp_clickable_cb($matches) {
    $ret = '';
    $dest = $matches[2];
    $dest = 'http://' . $dest;

    if ( empty($dest) )
        return $matches[0];

    // removed trailing [,;:] from URL
    if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === true ) {
        $ret = substr($dest, -1);
        $dest = substr($dest, 0, strlen($dest)-1);
    }

    return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>" . $ret;
}

function _make_email_clickable_cb($matches) {
    $email = $matches[2] . '@' . $matches[3];
    return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}

function make_clickable($ret) {
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
function bbcode($txt, $tinymce=false, $no_vid=false, $ts=false, $nolink=false) {
    global $charset;

    $txt = html_entity_decode($txt,ENT_COMPAT,$charset);
    if(!$no_vid && settings('urls_linked') && !$nolink)
        $txt = make_clickable($txt);

    $txt = str_replace("\\","\\\\",$txt);
    $txt = str_replace("\\n","<br />",$txt);
    $txt = BadwordFilter($txt);
    $txt = replace($txt,$tinymce,$no_vid);
    $txt = highlight_text($txt);
    $txt = re_bbcode($txt);

    if(!$ts)
        $txt = strip_tags($txt,"<br><object><em><param><embed><strong><iframe><hr><table><tr><td><div><span><a><b><i><u><p><ul><ol><li><br /><img>");

    $txt = smileys($txt);

    if(!$no_vid)
         $txt = glossar($txt);

    $txt = str_replace("&#34;","\"",$txt);
    return str_replace('<p></p>', '<p>&nbsp;</p>', $txt);
}

function bbcode_nletter($txt) {
    $txt = stripslashes($txt);
    $txt = nl2br(trim($txt));
    return '<style type="text/css">p { margin: 0px; padding: 0px; }</style>'.$txt;
}

function bbcode_nletter_plain($txt) {
    $txt = preg_replace("#\<\/p\>#Uis","\r\n",$txt);
    $txt = preg_replace("#\<br(.*?)\>#Uis","\r\n",$txt);
    $txt = str_replace("p { margin: 0px; padding: 0px; }","",$txt);
    $txt = convert_feed($txt);
    $txt = str_replace("&amp;#91;","[",$txt);
    $txt = str_replace("&amp;#93;","]",$txt);
    return strip_tags($txt);
}

function bbcode_html($txt,$tinymce=0) {
    $txt = str_replace("&lt;","<",$txt);
    $txt = str_replace("&gt;",">",$txt);
    $txt = str_replace("&quot;","\"",$txt);
    $txt = BadwordFilter($txt);
    $txt = replace($txt,$tinymce);
    $txt = highlight_text($txt);
    $txt = re_bbcode($txt);
    $txt = smileys($txt);
    $txt = glossar($txt);
    return str_replace("&#34;","\"",$txt);
}

function bbcode_email($txt) {
    $txt = bbcode($txt);
    $txt = str_replace("&#91;","[",$txt);
    return str_replace("&#93;","]",$txt);
}

//-> Textteil in Zitat-Tags setzen
function zitat($nick,$zitat) {
    $zitat = str_replace(chr(145), chr(39), $zitat);
    $zitat = str_replace(chr(146), chr(39), $zitat);
    $zitat = str_replace("'", "&#39;", $zitat);
    $zitat = str_replace(chr(147), chr(34), $zitat);
    $zitat = str_replace(chr(148), chr(34), $zitat);
    $zitat = str_replace(chr(10), " ", $zitat);
    $zitat = str_replace(chr(13), " ", $zitat);
    $zitat = preg_replace("#[\n\r]+#", "<br />", $zitat);
    return '<div class="quote"><b>'.$nick.' '._wrote.':</b><br />'.re_bbcode($zitat).'</div><br /><br /><br />';
}

//-> convert string for output
function re($txt,$tinymce=false) {
    global $charset;
    if($tinymce)
        return stripslashes($txt);

    return trim(stripslashes(spChars(html_entity_decode(utf8_decode($txt), ENT_COMPAT, $charset),true)));
}

//-> Smileys ausgeben
function smileys($txt) {
    $files = get_files('../inc/images/smileys',false,true);
    for($i=0; $i<count($files); $i++) {
        $smileys = $files[$i];
        $bbc = preg_replace("=.gif=Uis","",$smileys);

        if(preg_match("=:".$bbc.":=Uis",$txt)!=FALSE)
            $txt = preg_replace("=:".$bbc.":=Uis","<img src=\"../inc/images/smileys/".$bbc.".gif\" alt=\"\" />", $txt);
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

  $txt = preg_replace($var,$repl, $txt);
  return str_replace(" ^^"," <img src=\"../inc/images/smileys/^^.gif\" alt=\"\" />", $txt);
}

function cut($text,$length='',$dots = true,$html = true,$ending = '',$exact = false,$considerHtml = true) {
    if($length === 0)
        return '';

    if(empty($length))
        return $text;

    $ending = $dots || !empty($ending) ? (!empty($ending) ? $ending : '...') : '';

    if(!$html) {
        if(strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length).$ending;
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

function wrap($str, $width = 75, $break = "\n", $cut = true) {
    return strtr(str_replace(htmlentities($break), $break, htmlentities(wordwrap(html_entity_decode($str), $width, $break, $cut), ENT_QUOTES)), array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_COMPAT)));
}

//-> Funktion um Dateien aus einem Verzeichnis auszulesen
function get_files($dir=null,$only_dir=false,$only_files=false,$file_ext=array(),$preg_match=false,$blacklist=array(),$blacklist_word=false) {
    $cache_hash = md5($dir.$only_dir.$only_files.print_r($file_ext,true).$preg_match.print_r($blacklist,true).$blacklist_word);
    if(!dbc_index::issetIndex('files') || !dbc_index::getIndexKey('files',$cache_hash) || !dbc_index::MemSetIndex()) {
        $files = array();
        if(!file_exists($dir) && !is_dir($dir)) return $files;
        if($handle = @opendir($dir)) {
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
            if(dbc_index::MemSetIndex() && dbc_index::issetIndex('files')) {
                $cache = dbc_index::getIndex('files');
            }

            $cache[$cache_hash] = $files;

            if(dbc_index::MemSetIndex())
                dbc_index::setIndex('files',$cache);

            return $files;
        }
        else
            return false;
    } else {
        return dbc_index::getIndexKey('files',$cache_hash);
    }
}

//-> Gibt einen Teil eines nummerischen Arrays wieder
function limited_array($array=array(),$begin,$max) {
    $array_exp = array();
    $range=range($begin=($begin-1), ($begin+$max-1));
    foreach($array as $key => $wert) {
        if(array_var_exists($key, $range))
            $array_exp[$key] = $wert;
    }

    return $array_exp;
}

function array_var_exists($var,$search)
{ foreach($search as $key => $var_) { if($var_==$var) return true; } return false; }

//-> Funktion um eine Datei im Web auf Existenz zu prfen
function fileExists($url) {
    $url_p = @parse_url($url);
    $port = isset($url_p['port']) ? $url_p['port'] : 80;

    if(!allow_url_fopen_support()) return false;
    $fp = @fsockopen($url_p['host'], $port, $errno, $errstr, 5);
    if(!$fp) return false;

    @fputs($fp, 'GET '.$url_p['path'].' HTTP/1.1'.chr(10));
    @fputs($fp, 'HOST: '.$url_p['host'].chr(10));
    @fputs($fp, 'Connection: close'.chr(10).chr(10));

    $response = @fgets($fp, 1024);
    $content = @fread($fp,1024);
    $ex = explode("\n",$content);
    $content = $ex[count($ex)-1];
    @fclose ($fp);

    if(preg_match("#404#",$response)) return false;
    else return trim($content);
}

//-> Funktion um Sonderzeichen zu konvertieren
function spChars($txt) {
  $txt = str_replace("Ä","&Auml;",$txt);
  $txt = str_replace("ä","&auml;",$txt);
  $txt = str_replace("Ü","&Uuml;",$txt);
  $txt = str_replace("ü","&uuml;",$txt);
  $txt = str_replace("Ö","&Ouml;",$txt);
  $txt = str_replace("ö","&ouml;",$txt);
  $txt = str_replace("ß","&szlig;",$txt);
  return str_replace("€","&euro;",$txt);
}

//-> Funktion um sauber in die DB einzutragen
function up($txt,$escape=true) {
    global $charset;
    $return = utf8_encode(stripcslashes(spChars(htmlentities($txt, ENT_COMPAT, $charset))));
    return $escape ? _real_escape_string($return) : $return;
}

//-> Funktion um diverse Dinge aus Tabellen auszaehlen zu lassen
function cnt($count, $where = "", $what = "id") {
    $cnt_sql = db("SELECT COUNT(".$what.") AS `num` FROM ".$count." ".$where.";");
    if(_rows($cnt_sql)) {
        $cnt = _fetch($cnt_sql);
        return $cnt['num'];
    }

    return 0;
}

//-> Funktion um diverse Dinge aus Tabellen zusammenzaehlen zu lassen
function sum($db, $where = "", $what) {
    $cnt_sql = db("SELECT SUM(".$what.") AS `num` FROM ".$db.$where.";");
    if(_rows($cnt_sql)) {
        $cnt = _fetch($cnt_sql);
        return $cnt['num'];
    }

    return 0;
}

function orderby($sort) {
    $split = explode("&",$_SERVER['QUERY_STRING']);
    $url = "?";

    foreach($split as $part) {
        if(strpos($part,"orderby") === false && strpos($part,"order") === false && !empty($part)) {
            $url .= $part;
            $url .= "&";
        }
    }

    if(isset($_GET['orderby']) && $_GET['order']) {
        if($_GET['orderby'] == $sort && $_GET['order'] == "ASC")
            return $url."orderby=".$sort."&order=DESC";
    }

    return $url."orderby=".$sort."&order=ASC";
}

function orderby_sql($sort_by=array(), $default_order='',$join='', $order_by = array('ASC','DESC')) {
    if(!isset($_GET['order']) || empty($_GET['order']) || !in_array($_GET['order'],$order_by)) return $default_order;
    if(!isset($_GET['orderby']) || empty($_GET['orderby']) || !in_array($_GET['orderby'],$sort_by)) return $default_order;
    $orderby_real = _real_escape_string($_GET['orderby']);
    $order_real = _real_escape_string($_GET['order']);
    if(empty($orderby_real) || empty($order_real)) return $default_order;
    $join = !empty($join) ? $join.'.' : '';
    return 'ORDER BY '.$join.$orderby_real." ".$order_real;
}

function orderby_nav() {
    $orderby = isset($_GET['orderby']) ? "&orderby".$_GET['orderby'] : "";
    $orderby .= isset($_GET['order']) ? "&order=".$_GET['order'] : "";
    return $orderby;
}

//-> Funktion um ein Datenbankinhalt zu highlighten
function highlight($word) {
    if(substr(phpversion(),0,1) == 5)
        return str_ireplace($word,'<span class="fontRed">'.$word.'</span>',$word);
    else
        return str_replace($word,'<span class="fontRed">'.$word.'</span>',$word);
}

//-> Counter updaten
function updateCounter() {
    global $db,$reload,$today,$datum,$userip,$CrawlerDetect;
    $ipcheck = db("SELECT `id`,`ip`,`datum` FROM `".$db['c_ips']."` WHERE `ip` = '".$userip."' AND FROM_UNIXTIME(datum,'%d.%m.%Y') = '".date("d.m.Y")."'");
    db("DELETE FROM ".$db['c_ips']." WHERE datum+".$reload." <= ".time()." OR FROM_UNIXTIME(datum,'%d.%m.%Y') != '".date("d.m.Y")."'");
    $count = db("SELECT id,visitors,today FROM ".$db['counter']." WHERE today = '".$today."'");
    if(_rows($ipcheck)>=1) {
        $get = _fetch($ipcheck);
        $sperrzeit = $get['datum']+$reload;
        if($sperrzeit <= time()) {
            if(_rows($count))
                db("UPDATE `".$db['counter']."` SET `visitors` = (visitors+1) WHERE `today` = '".$today."';");
            else
                db("INSERT INTO `".$db['counter']."` SET `visitors` = '1', `today` = '".$today."'");

            if(db("SELECT `id` FROM `".$db['c_ips']."` WHERE `ip` = '".$userip."';",true)) {
                db("UPDATE ".$db['c_ips']." SET `datum` = '".((int)$datum)."', `agent` = '".$CrawlerDetect->userAgent."' WHERE `ip` = '".$userip."';");
            } else {
                db("INSERT INTO `".$db['c_ips']."` SET `ip` = '".$userip."', `datum` = '".((int)$datum)."', `agent` = '".$CrawlerDetect->userAgent."';");
            }
        }
    } else {
        if(_rows($count))
            db("UPDATE `".$db['counter']."` SET `visitors` = (visitors+1) WHERE `today` = '".$today."';");
       else
            db("INSERT INTO `".$db['counter']."` SET `visitors` = '1', `today` = '".$today."'");

        if(db("SELECT `id` FROM `".$db['c_ips']."` WHERE `ip` = '".$userip."';",true)) {
            db("UPDATE `".$db['c_ips']."` SET `datum` = '".((int)$datum)."', `agent` = '".$CrawlerDetect->userAgent."' WHERE `ip` = '".$userip."';");
        } else {
            db("INSERT INTO `".$db['c_ips']."` SET `ip` = '".$userip."', `datum` = '".((int)$datum)."', `agent` = '".$CrawlerDetect->userAgent."';");
        }
    }
}

//-> Updatet die Maximalen User die gleichzeitig online sind
function update_maxonline() {
    global $db,$today;

    $get = db("SELECT `maxonline` FROM `".$db['counter']."` WHERE `today` = '".$today."';",false,true);
    $count = cnt($db['c_who']);

    if($get['maxonline'] <= (int)$count)
        db("UPDATE `".$db['counter']."` SET `maxonline` = ".((int)$count)." WHERE `today` = '".$today."';");
}

//-> Prueft, wieviele Besucher gerade online sind
function online_guests($where='') {
    global $db,$useronline,$userip,$chkMe,$isSpider;

    if(!$isSpider) {
        $logged = !$chkMe ? 0 : 1;
        db("DELETE FROM `".$db['c_who']."` WHERE `online` < ".time().";");
        db("REPLACE INTO `".$db['c_who']."`
               SET `ip`       = '".$userip."',
                   `online`   = ".((int)(time()+$useronline)).",
                   `whereami` = '".up($where)."',
                   `login`    = ".((int)$logged).";");
        return cnt($db['c_who']);
    }
    return true;
}
//-> Prueft, wieviele registrierte User gerade online sind
function online_reg() {
    global $db,$useronline;
    return cnt($db['users'], " WHERE (time+".$useronline.") > ".time()." AND `online` = 1;");
}

//-> Prueft, ob der User eingeloggt ist und wenn ja welches Level besitzt er
function checkme($userid_set=0) {
    global $db;
    if(HasDSGVO() || $userid_set!=0) {
        if (!$userid = ($userid_set != 0 ? (int)($userid_set) : userid())) return 0;
        if (rootAdmin($userid)) return 4;
        if (empty($_SESSION['id']) || empty($_SESSION['pwd'])) return 0;
        if (!dbc_index::issetIndex('user_' . $userid)) {
            $qry = db("SELECT * FROM `" . $db['users'] . "` WHERE `id` = ".$userid." AND `pwd` = '".$_SESSION['pwd']."' AND `ip` = '".up($_SESSION['ip'])."';");
            if (!_rows($qry)) return 0;
            $get = _fetch($qry);
            dbc_index::setIndex('user_' . $get['id'], $get);
            return $get['level'];
        }

        return dbc_index::getIndexKey('user_' . $userid, 'level');
    }

    return 0;
}

//-> Prueft, ob der User gesperrt ist und meldet ihn ab
function isBanned($userid_set=0,$logout=true) {
    global $db,$userid;
    $userid_set = $userid_set ? $userid_set : $userid;
    if(checkme($userid_set) >= 1 || $userid_set) {
        $get = db("SELECT `banned` FROM `".$db['users']."` WHERE `id` = ".(int)($userid_set)." LIMIT 1;",false,true);
        if($get['banned']) {
            if($logout) {
                $_SESSION['id']        = '';
                $_SESSION['pwd']       = '';
                $_SESSION['ip']        = '';
                $_SESSION['lastvisit'] = '';
                session_unset();
                session_destroy();
                session_regenerate_id();
                if(HasDSGVO()) {
                    cookie::clear();
                }
                $userid = 0;
            }

            return true;
        }
    }

    return false;
}

//-> Prueft, ob ein User diverse Rechte besitzt
function permission($check,$uid=0) {
    global $db,$userid,$chkMe;
    if(!$uid) $uid = $userid;
    if(rootAdmin($uid)) return true;
    if($chkMe == 4)
        return true;
    else {
        if($uid) {
            // check rank permission
            if(db("SELECT s1.`".$check."` FROM ".$db['permissions']." AS s1
                   LEFT JOIN ".$db['userpos']." AS s2 ON s1.`pos` = s2.`posi`
                   WHERE s2.`user` = '".(int)($uid)."' AND s1.`".$check."` = '1' AND s2.`posi` != '0'",true))
                return true;

            // check user permission
            if(!dbc_index::issetIndex('user_permission_'.$uid)) {
                $permissions = db("SELECT * FROM ".$db['permissions']." WHERE user = '".(int)($uid)."'",false,true);
                dbc_index::setIndex('user_permission_'.$uid, $permissions);
            }

            return dbc_index::getIndexKey('user_permission_'.$uid, $check) ? true : false;
        }
        else
            return false;
    }
}

//-> Checkt, ob neue Nachrichten vorhanden sind
function check_msg() {
    global $db;
    if(db("SELECT page FROM ".$db['msg']." WHERE an = '".$_SESSION['id']."' AND page = 0",true)) {
        db("UPDATE ".$db['msg']." SET `page` = '1' WHERE an = '".$_SESSION['id']."'");
        return show("user/new_msg", array("new" => _site_msg_new));
    }

    return '';
}

//-> Prueft sicherheitsrelevante Gegebenheiten im Forum
function forumcheck($tid, $what) {
    global $db;
    return db("SELECT ".$what." FROM ".$db['f_threads']." WHERE id = '".(int)($tid)."' AND ".$what." = '1'",true) ? true : false;
}

//-> Prueft ob ein User schon in der Buddyliste vorhanden ist
function check_buddy($buddy) {
    global $db,$userid;
    return !db("SELECT buddy FROM ".$db['buddys']." WHERE user = '".(int)($userid)."' AND buddy = '".(int)($buddy)."'",true) ? true : false;
}

//-> Funktion um bei Clanwars Endergebnisse auszuwerten
function cw_result($punkte, $gpunkte) {
    if($punkte > $gpunkte)
        return '<span class="CwWon">'.$punkte.':'.$gpunkte.'</span> <img src="../inc/images/won.gif" alt="" class="icon" />';
    else if($punkte < $gpunkte)
        return '<span class="CwLost">'.$punkte.':'.$gpunkte.'</span> <img src="../inc/images/lost.gif" alt="" class="icon" />';
    else
        return '<span class="CwDraw">'.$punkte.':'.$gpunkte.'</span> <img src="../inc/images/draw.gif" alt="" class="icon" />';
}

function cw_result_pic($punkte, $gpunkte) {
    if($punkte > $gpunkte)
        return '<img src="../inc/images/won.gif" alt="" class="icon" />';
    else if($punkte < $gpunkte)
        return '<img src="../inc/images/lost.gif" alt="" class="icon" />';
    else
        return '<img src="../inc/images/draw.gif" alt="" class="icon" />';
}

//-> Funktion um bei Clanwars Endergebnisse auszuwerten ohne bild
function cw_result_nopic($punkte, $gpunkte) {
    if($punkte > $gpunkte)
        return '<span class="CwWon">'.$punkte.':'.$gpunkte.'</span>';
    else if($punkte < $gpunkte)
        return '<span class="CwLost">'.$punkte.':'.$gpunkte.'</span>';
    else
        return '<span class="CwDraw">'.$punkte.':'.$gpunkte.'</span>';
}

//-> Funktion um bei Clanwars Endergebnisse auszuwerten ohne bild und ohne farbe
function cw_result_nopic_nocolor($punkte, $gpunkte) {
    if($punkte > $gpunkte)
        return $punkte.':'.$gpunkte;
    else if($punkte < $gpunkte)
        return $punkte.':'.$gpunkte;
    else
        return $punkte.':'.$gpunkte;
}

//-> Funktion um bei Clanwars Details Endergebnisse auszuwerten ohne bild
function cw_result_details($punkte, $gpunkte) {
    if($punkte > $gpunkte)
        return '<td class="contentMainFirst" align="center"><span class="CwWon">'.$punkte.'</span></td><td class="contentMainFirst" align="center"><span class="CwLost">'.$gpunkte.'</span></td>';
    else if($punkte < $gpunkte)
        return '<td class="contentMainFirst" align="center"><span class="CwLost">'.$punkte.'</span></td><td class="contentMainFirst" align="center"><span class="CwWon">'.$gpunkte.'</span></td>';
    else
        return '<td class="contentMainFirst" align="center"><span class="CwDraw">'.$punkte.'</span></td><td class="contentMainFirst" align="center"><span class="CwDraw">'.$gpunkte.'</span></td>';
}

//-> Flaggen ausgeben
function flag($code) {
    global $picformat;
    if(empty($code))
        return '<img src="../inc/images/flaggen/nocountry.gif" alt="" class="icon" />';

    foreach($picformat as $end) {
        if(file_exists(basePath."/inc/images/flaggen/".$code.".".$end)) break;
    }

    if(file_exists(basePath."/inc/images/flaggen/".$code.".".$end))
        return'<img src="../inc/images/flaggen/'.$code.'.'.$end.'" alt="" class="icon" />';

    return '<img src="../inc/images/flaggen/nocountry.gif" alt="" class="icon" />';
}

function rawflag($code) {
    global $picformat;
    if(empty($code))
        return '<img src=../inc/images/flaggen/nocountry.gif alt= class=icon />';

    foreach($picformat as $end) {
        if(file_exists(basePath."/inc/images/flaggen/".$code.".".$end)) break;
    }

    if(file_exists(basePath."/inc/images/flaggen/".$code.".".$end))
        return '<img src=../inc/images/flaggen/'.$code.'.'.$end.' alt= class=icon />';

    return '<img src=../inc/images/flaggen/nocountry.gif alt= class=icon />';
}

//-> Liste der Laender ausgeben
function show_countrys($i="") {
    if($i != "")
        $options = preg_replace('#<option value="'.$i.'">(.*?)</option>#', '<option value="'.$i.'" selected="selected"> \\1</option>', _country_list);
    else
        $options = preg_replace('#<option value="de"> Deutschland</option>#', '<option value="de" selected="selected"> Deutschland</option>', _country_list);

    return '<select id="land" name="land" class="dropdown">'.$options.'</select>';
}

//-> Gameicon ausgeben
function squad($code) {
    global $picformat;
    if(empty($code))
        return '<img src="../inc/images/gameicons/nogame.gif" alt="" class="icon" />';

    $code = str_replace(array('.png','.gif','.jpg'),'',$code);
    foreach($picformat as $end) {
        if(file_exists(basePath."/inc/images/gameicons/".$code.".".$end)) break;
    }

    if(file_exists(basePath."/inc/images/gameicons/".$code.".".$end))
        return'<img src="../inc/images/gameicons/'.$code.'.'.$end.'" alt="" class="icon" />';

    return '<img src="../inc/images/gameicons/nogame.gif" alt="" class="icon" />';
}

//-> Funktion um bei DB-Eintraegen URLs einem http:// oder https:// zuzuweisen
function links($hp) {
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
    }

    return $hp;
}

//-> Funktion um Passwoerter generieren zu lassen
function mkpwd($length = 8, $add_dashes = false, $available_sets = 'luds') {
    $sets = array();
    if(strpos($available_sets, 'l') !== false)
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
    if(strpos($available_sets, 'u') !== false)
        $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    if(strpos($available_sets, 'd') !== false)
        $sets[] = '23456789';
    if(strpos($available_sets, 's') !== false)
        $sets[] = '!@#$%&*?';

    $all = '';
    $password = '';
    foreach($sets as $set) {
        $password .= $set[array_rand(str_split($set))];
        $all .= $set;
    }
    $all = str_split($all);
    for($i = 0; $i < $length - count($sets); $i++)
        $password .= $all[array_rand($all)];

    $password = str_shuffle($password);

    if(!$add_dashes)
        return $password;

    $dash_len = floor(sqrt($length));
    $dash_str = '';
    while(strlen($password) > $dash_len) {
        $dash_str .= substr($password, 0, $dash_len) . '-';
        $password = substr($password, $dash_len);
    }

    $dash_str .= $password;
    return $dash_str;
}

//-> Passwortabfrage und rückgabe des users
function checkpwd($user, $pwd) {
    global $db;
    $sql = db("SELECT * FROM `".$db['users']."` WHERE `user` = '".up($user).
        "' AND (`pwd` = '".hash('sha256',$pwd)."' OR (`pwd` = '".md5($pwd)."' AND `pwd_md5` = 1)) AND `level` != 0;");
    if(_rows($sql)) {
        $get = _fetch($sql);
        if($get['pwd_md5']) {
            //Update Password to SHA256
            db("UPDATE `".$db['users']."` SET `pwd` = '".hash('sha256',$pwd)."', `pwd_md5` = 0 WHERE `id` = ".$get['id'].";");
            $get['pwd'] = hash('sha256',$pwd);
            $get['pwd_md5'] = 0;
        }

        return $get;
    }

    return false;
}

//-> Infomeldung ausgeben
function info($msg, $url, $timeout = 5) {
    if(config('direct_refresh')) {
        header('Location: '.str_replace('&amp;', '&', $url));
        exit();
    }

    $u = parse_url($url); $parts = '';
    $u['query'] = array_key_exists('query', $u) ? $u['query'] : '';
    $u['query'] = str_replace('&amp;', '&', $u['query']);
    foreach(explode('&', $u['query']) as $p) {
        $p = explode('=', $p);
        if(count($p) == 2)
            $parts .= '<input type="hidden" name="'.$p[0].'" value="'.$p[1].'" />'."\r\n";
    }

    if(!array_key_exists('path',$u)) $u['path'] = '';
    return show("errors/info", array("msg" => $msg,
                                     "url" => $u['path'],
                                     "rawurl" => html_entity_decode($url),
                                     "parts" => $parts,
                                     "timeout" => $timeout,
                                     "info" => _info,
                                     "weiter" => _weiter,
                                     "backtopage" => _error_fwd));
}

//-> Errormmeldung ausgeben
function error($error, $back=1) {
    return show("errors/error", array("error" => $error, "back" => $back, "fehler" => _error, "backtopage" => _error_back));
}

//-> Errormmeldung ohne "zurueck" ausgeben
function error2($error) {
    return show("errors/error2", array("error" => $error, "fehler" => _error));
}

//-> Email wird auf korrekten Syntax & Erreichbarkeit ueberprueft
function check_email($email) {
    return (!preg_match("#^([a-zA-Z0-9\.\_\-]+)@([a-zA-Z0-9\.\-]+\.[A-Za-z][A-Za-z]+)$#", $email) ? false : true);
}

//-> Bilder verkleinern
function img_size($img) {
    return "<a href=\"../".$img."\" rel=\"lightbox[l_".(int)($img)."]\"><img src=\"../thumbgen.php?img=".$img."\" alt=\"\" /></a>";
}

function img_cw($folder="", $img="") {
    return "<a href=\"../".$folder.$img."\" rel=\"lightbox[cw_".(int)($folder)."]\"><img src=\"../thumbgen.php?img=".$folder.$img."\" alt=\"\" /></a>";
}

function gallery_size($img="") {
    return "<a href=\"../gallery/images/".$img."\" rel=\"lightbox[gallery_".(int)($img)."]\"><img src=\"../thumbgen.php?img=gallery/images/".$img."\" alt=\"\" /></a>";
}

//-> URL wird auf Richtigkeit ueberprueft
function check_url($url) {
    if($url && $fp = @fopen($url, "r")) {
        return true;
        @fclose($fp);
    }

    return false;
}

//-> Blaetterfunktion
function nav($entrys, $perpage, $urlpart='', $icon=true) {
    global $page;
    if($perpage == 0)
        return "&#xAB; <span class=\"fontSites\">0</span> &#xBB;";

    if($icon == true)
        $icon = '<img src="../inc/images/multipage.gif" alt="" class="icon" /> '._seiten;

    if($entrys <= $perpage)
        return $icon.' &#xAB; <span class="fontSites">1</span> &#xBB;';

    if(!$page || $page < 1)
        $page = 2;

    $pages = ceil($entrys/$perpage);
    $urlpart_ext = empty($urlpart) ? '?' : '&amp;';

    if(($page-5) <= 2 && $page != 1)
        $first = '<a class="sites" href="'.$urlpart.$urlpart_ext.'page='.($page-1).'">&#xAB;</a><span class="fontSitesMisc">&#xA0;</span> <a  class="sites" href="'.$urlpart.$urlpart_ext.'page=1">1</a> ';
    else if($page > 1)
        $first = '<a class="sites" href="'.$urlpart.$urlpart_ext.'page='.($page-1).'">&#xAB;</a><span class="fontSitesMisc">&#xA0;</span> <a class="sites" href="'.$urlpart.$urlpart_ext.'page=1">1</a>...';
    else
        $first = '<span class="fontSitesMisc">&#xAB;&#xA0;</span>';

    if($page == $pages)
        $last = '<span class="fontSites">'.$pages.'</span><span class="fontSitesMisc">&#xA0;&#xBB;<span>';
    else if(($page+5) >= $pages)
        $last = '<a class="sites" href="'.$urlpart.$urlpart_ext.'page='.($pages).'">'.$pages.'</a>&#xA0;<a class="sites" href="'.$urlpart.$urlpart_ext.'page='.($page+1).'">&#xBB;</a>';
    else
        $last = '...<a class="sites" href="'.$urlpart.$urlpart_ext.'page='.($pages).'">'.$pages.'</a>&#xA0;<a class="sites" href="'.$urlpart.$urlpart_ext.'page='.($page+1).'">&#xBB;</a>';

    $result = ''; $resultm = '';
    for($i = $page;$i<=($page+5) && $i<=($pages-1);$i++) {
        if($i == $page)
            $result .= '<span class="fontSites">'.$i.'</span><span class="fontSitesMisc">&#xA0;</span>';
        else
            $result .= '<a class="sites" href="'.$urlpart.$urlpart_ext.'page='.$i.'">'.$i.'</a><span class="fontSitesMisc">&#xA0;</span>';
    }

    for($i=($page-5);$i<=($page-1);$i++) {
        if($i >= 2)
            $resultm .= '<a class="sites" href="'.$urlpart.$urlpart_ext.'page='.$i.'">'.$i.'</a> ';
    }

    return $icon.' '.$first.$resultm.$result.$last;
}

//-> Funktion um Seiten-Anzahl der Artikel zu erhalten
function artikelSites($sites, $id) {
    global $part;
    $seiten = '';
    for($i=0;$i<$sites;$i++) {
        if ($i == $part)
            $seiten .= show(_page, array("num" => ($i+1)));
        else
            $seiten .= show(_artike_sites, array("part" => $i,"id" => $id,"num" => ($i+1)));
    }

    return $seiten;
}

//-> Nickausgabe mit Profillink oder Emaillink (reg/nicht reg)
function autor($uid, $class="", $nick="", $email="", $cut="",$add="") {
    global $db;
    if(!dbc_index::issetIndex('user_'.(int)($uid))) {
        $qry = db("SELECT * FROM `".$db['users']."` WHERE `id` = ".(int)($uid).";");
        if(_rows($qry)) {
            $get = _fetch($qry);
            dbc_index::setIndex('user_'.$get['id'], $get);
        } else {
            $nickname = (!empty($cut)) ? cut($nick, $cut,true,false) : $nick;
            return show(_user_link_noreg, array("nick" => re($nickname), "class" => $class, "email" => eMailAddr($email)));
        }
    }

    $nickname = (!empty($cut)) ? cut(re(dbc_index::getIndexKey('user_'.(int)($uid), 'nick')), $cut,true,false) :
        re(dbc_index::getIndexKey('user_'.(int)($uid), 'nick'));
    return show(_user_link, array("id" => $uid,
                                  "country" => flag(dbc_index::getIndexKey('user_'.(int)($uid), 'country')),
                                  "class" => $class,
                                  "get" => $add,
                                  "nick" => $nickname));
}

function cleanautor($uid, $class="", $nick="", $email="", $cut="") {
    global $db;
    if(!dbc_index::issetIndex('user_'.(int)($uid))) {
        $qry = db("SELECT * FROM `".$db['users']."` WHERE `id` = ".(int)($uid).";");
        if(_rows($qry)) {
            $get = _fetch($qry);
            dbc_index::setIndex('user_'.$get['id'], $get);
        }
        else
            return show(_user_link_noreg, array("nick" => re(cut($nick,$cut,false,false)), "class" => $class, "email" => eMailAddr($email)));
    }

    return show(_user_link_preview, array("id" => $uid, "country" => flag(dbc_index::getIndexKey('user_'.(int)($uid), 'country')),
                                          "class" => $class, "nick" => re(cut(dbc_index::getIndexKey('user_'.(int)($uid),'nick'),$cut,false,false))));
}

function rawautor($uid) {
    global $db;
    if(!dbc_index::issetIndex('user_'.(int)($uid))) {
        $qry = db("SELECT * FROM `".$db['users']."` WHERE `id` = ".(int)($uid).";");
        if(_rows($qry)) {
            $get = _fetch($qry);
            dbc_index::setIndex('user_'.$get['id'], $get);
        }
        else
            return rawflag('')." ".jsconvert(re($uid));
    }

    return rawflag(dbc_index::getIndexKey('user_'.(int)($uid), 'country'))." ".
    jsconvert(re(dbc_index::getIndexKey('user_'.(int)($uid), 'nick')));
}

//-> Nickausgabe ohne Profillink oder Emaillink fr das ForenAbo
function fabo_autor($uid) {
    global $db;
    $qry = db("SELECT `nick` FROM `".$db['users']."` WHERE `id` = ".$uid.";");
    if(_rows($qry)) {
        $get = _fetch($qry);
        return show(_user_link_fabo, array("id" => $uid, "nick" => re($get['nick'])));
    }

    return '';
}

function blank_autor($uid) {
    global $db;
    $qry = db("SELECT `nick` FROM `".$db['users']."` WHERE `id` = ".$uid.";");
    if(_rows($qry)) {
        $get = _fetch($qry);
        return show(_user_link_blank, array("id" => $uid, "nick" => re($get['nick'])));
    }

    return '';
}

//-> Rechte abfragen
function jsconvert($txt) {
    return str_replace(array("'","&#039;","\"","\r","\n"),array("\'","\'","&quot;","",""),$txt);
}

//-> interner Forencheck
function fintern($id) {
    global $db,$userid,$chkMe;
    $sql = db("SELECT s1.`intern`,s2.`id` FROM `".$db['f_kats']."` AS `s1` LEFT JOIN `".$db['f_skats']."` AS `s2` ON s2.`sid` = s1.`id` WHERE s2.`id` = ".(int)($id).";");
    if(_rows($sql)) {
        $fget = _fetch($sql);
        if (!$chkMe)
            return empty($fget['intern']) ? true : false;
        else {
            $team = db("SELECT s1.`id` FROM `".$db['f_access']."` AS `s1` LEFT JOIN `".$db['userpos']."` AS `s2` ON s1.`pos` = s2.`posi` WHERE s2.`user` = ".(int)($userid)." AND s2.`posi` != 0 AND s1.`forum` = ".(int)($id).";",true);
            $user = db("SELECT `id` FROM `".$db['f_access']."` WHERE `user` = ".(int)($userid)." AND `forum` = ".(int)($id).";",true);
            if ($user || $team || $chkMe == 4 || !$fget['intern'])
                return true;
        }
    }

    return false;
}

//-> Einzelne Userdaten ermitteln
function data($what,$tid=0) {
    global $db,$userid;
    if(!$tid) $tid = $userid;
    if(!dbc_index::issetIndex('user_'.$tid)) {
        $sql = db("SELECT * FROM `".$db['users']."` WHERE `id` = ".(int)($tid).";");
        if(_rows($sql)) {
            $get = _fetch($sql);
            dbc_index::setIndex('user_' . $tid, $get);
        } else {
            return null;
        }
    }

    return dbc_index::getIndexKey('user_'.$tid, $what);
}

//-> Einzelne Userstatistiken ermitteln
function userstats($what,$tid=0) {
    global $db,$userid;
    if(!$tid) $tid = $userid;
    if(!dbc_index::issetIndex('userstats_'.$tid)) {
        $sql = db("SELECT * FROM `".$db['userstats']."` WHERE `user` = ".(int)($tid).";");
        if(_rows($sql)) {
            $get = _fetch($sql);
            dbc_index::setIndex('userstats_' . $tid, $get);
        } else {
            return null;
        }
    }

    return dbc_index::getIndexKey('userstats_'.$tid, $what);
}

//- Funktion zum versenden von Emails
function sendMail($mailto,$subject,$content) {
    $mail = new PHPMailer(false);
    if(phpmailer_use_smtp) {
        $mail->isSMTP();
        $mail->Host = phpmailer_smtp_host;
        $mail->SMTPAuth = phpmailer_use_auth;
        $mail->Username = phpmailer_smtp_user;
        $mail->Password = phpmailer_smtp_password;
        $mail->SMTPSecure = phpmailer_smtp_secure;
        $mail->Port = phpmailer_smtp_port;
    }

    $mail->setFrom(($mailfrom=re(settings('mailfrom'))), $mailfrom);
    $mail->AddAddress(preg_replace('/(\\n+|\\r+|%0A|%0D)/i', '',$mailto));
    $mail->isHTML(true);
    $mail->Subject = re($subject);
    $mail->Body    = $content;
    $mail->AltBody = bbcode_nletter_plain($content);

    $mail->setLanguage(language_short_tag(), basePath.'/vendor/phpmailer/phpmailer/language');
    return $mail->send();
}

function language_short_tag() {
    switch ($_SESSION['language']) {
        case "spanish": return 'es';
        case "deutsch": return 'de';
        case "russian": return 'ru';
        default: return 'en';
    }
}

function check_msg_emal() {
    global $db,$httphost;
    $qry = db("SELECT s1.`an`,s1.`page`,s1.`titel`,s1.`sendmail`,s1.`id` AS `mid`,s2.`id`,s2.`nick`,s2.`email`,s2.`pnmail` FROM `"
        .$db['msg']."` AS `s1` LEFT JOIN `".$db['users'].
        "` AS `s2` ON s2.`id` = s1.`an` WHERE `page` = 0 AND `sendmail` = 0;");
    if(_rows($qry)) {
        while ($get = _fetch($qry)) {
            if ($get['pnmail']) {
                db("UPDATE ".$db['msg']." SET `sendmail` = 1 WHERE `id` = ".(int)$get['mid'].";");
                $subj = show(settings('eml_pn_subj'), array("domain" => $httphost));
                $message = show(bbcode_email(settings('eml_pn')), array("nick" => re($get['nick']), "domain" => $httphost, "titel" => $get['titel'], "clan" => settings('clanname')));
                sendMail(re($get['email']), $subj, $message);
            }
        }
    }
}

if(!$ajaxJob && HasDSGVO())
    check_msg_emal();

//-> Checkt ob ein Ereignis neu ist
function check_new($datum,$new = "",$datum2 = "") {
    global $userid;
    if($userid) {
        if($datum >= userstats('lastvisit') || $datum2 >= userstats('lastvisit'))
            return (empty($new) ? _newicon : $new);
    }

    return empty($new) ? false : '';
}

//-> DropDown Mens Date/Time
function dropdown($what, $wert, $age = 0) {
    $return = '';
    if($what == "day") {
        $return = ($age == 1 ? '<option value="" class="dropdownKat">'._day.'</option>'."\n" : '');
        for($i=1; $i<32; $i++) {
            if($i==$wert)
                $return .= "<option value=\"".$i."\" selected=\"selected\">".$i."</option>\n";
            else
                $return .= "<option value=\"".$i."\">".$i."</option>\n";
        }
    } else if($what == "month") {
        $return = ($age == 1 ? '<option value="" class="dropdownKat">'._month.'</option>'."\n" : '');
        for($i=1; $i<13; $i++) {
            if($i==$wert)
                $return .= "<option value=\"".$i."\" selected=\"selected\">".$i."</option>\n";
            else
                $return .= "<option value=\"".$i."\">".$i."</option>\n";
        }
    } else if($what == "year") {
        if($age == 1) {
            $return ='<option value="" class="dropdownKat">'._year.'</option>'."\n";
            for($i=date("Y",time())-80; $i<date("Y",time())-10; $i++)
            {
                if($i==$wert)
                    $return .= "<option value=\"".$i."\" selected=\"selected\">".$i."</option>\n";
                else
                    $return .= "<option value=\"".$i."\">".$i."</option>\n";
            }
        } else {
            $return = '';
            for($i=date("Y",time())-3; $i<date("Y",time())+3; $i++) {
                if($i==$wert)
                    $return .= "<option value=\"".$i."\" selected=\"selected\">".$i."</option>\n";
                else
                    $return .= "<option value=\"".$i."\">".$i."</option>\n";
            }
        }
    } else if($what == "hour") {
        $return = '';
        for($i=0; $i<24; $i++) {
            if($i==$wert)
                $return .= "<option value=\"".$i."\" selected=\"selected\">".$i."</option>\n";
            else
                $return .= "<option value=\"".$i."\">".$i."</option>\n";
        }
    } else if($what == "minute") {
        $return = '';
        for($i="00"; $i<60; $i++) {
            if($i == 0 || $i == 15 || $i == 30 || $i == 45) {
                if($i==$wert)
                    $return .= "<option value=\"".$i."\" selected=\"selected\">".$i."</option>\n";
                else
                    $return .= "<option value=\"".$i."\">".$i."</option>\n";
            }
        }
    }

    return $return;
}

function getgames() {
    $protocols_path = basePath . "/vendor/austinb/gameq/src/GameQ/Protocols/";
    $protocols = [];

    $files = get_files($protocols_path);
    foreach ($files as $entry) {
        if (!is_file($protocols_path . $entry)) {
            continue;
        }

        // Lets get some info on the class
        $reflection = new ReflectionClass('\\GameQ\\Protocols\\' . pathinfo($entry, PATHINFO_FILENAME));

        // Check to make sure we can actually load the class
        if (!$reflection->IsInstantiable()) {
            continue;
        }

        // Load up the class so we can get info
        $class = $reflection->newInstance();

        if(in_array($class->name(),['ventrilo','teamspeak2','teamspeak3',
            'gamespy','gamespy3','won']))
            continue;

        // Add it to the list
        $protocols[ $class->name() ] = [
            'name'  => $class->nameLong(),
            'state' => $class->state(),
        ];

        unset($class);
    }

    unset($files,$protocols_path);
    ksort($protocols);
    return $protocols;
}

/**
 * Sucht nach Game Icons
 *
 * @param string $icon
 * @return array
 */
function search_game_icon($icon='') {
    global $picformat;
    $image = '../inc/images/gameicons/unknown.gif'; $found = false;
    foreach($picformat AS $end) {
        if(file_exists(basePath.'/inc/images/gameicons/'.$icon.'.'.$end)) {
            $found = true;
            $image = '../inc/images/gameicons/'.$icon.'.'.$end;
            break;
        }
    }
    return array('image'=> $image, 'found'=> $found);
}

function listgame($games,$game) {
    $content = '';
    foreach ($games AS $sname => $info) {
        $selected = (!empty($game) && $game != false && $game == $sname ? 'selected="selected" ' : '');
        $content .= '<option '.$selected.'value="'.$sname.'">'.htmlentities($info['name']).'</option>';
    }

    return $content;
}

//Umfrageantworten selektieren
function voteanswer($what, $vid) {
    global $db;
    $get = db("SELECT `sel` FROM `".$db['vote_results']."` WHERE `what` = '".up($what)."' AND `vid` = ".(int)$vid.";",false,true);
    return $get['sel'];
}

//Profilfelder konvertieren
function conv($txt) {
    return str_replace(array("ä","ü","ö","","Ä","Ö",""), array("ae","ue","oe","Ae","Ue","Oe","ss"), $txt);
}

//-> Geburtstag errechnen
function getAge($bday) {
    if(!empty($bday) && $bday) {
        $bday = date('d.m.Y',$bday);
        list($tiday,$iMonth,$iYear) = explode(".",$bday);
        $iCurrentDay = date('j');
        $iCurrentMonth = date('n');
        $iCurrentYear = date('Y');

        if(($iCurrentMonth>$iMonth) || (($iCurrentMonth==$iMonth) && ($iCurrentDay>=$tiday)))
            return $iCurrentYear - $iYear;
        else
            return $iCurrentYear - ($iYear + 1);
    }
    else
        return '-';
}

//-> Ausgabe der Position des einzelnen Members
function getrank($tid, $squad="", $profil=false) {
    global $db;
    if($squad) {
        if($profil)
            $qry = db("SELECT * FROM `".$db['userpos']."` AS `s1` LEFT JOIN `".$db['squads']."` AS `s2` ON s1.`squad` = s2.`id` WHERE s1.`user` = ".(int)($tid)." AND s1.`squad` = ".(int)($squad)." AND s1.`posi` != 0;");
        else
            $qry = db("SELECT * FROM `".$db['userpos']."` WHERE `user` = ".(int)($tid)." AND `squad` = ".(int)($squad)." AND `posi` != 0;");

        if(_rows($qry)) {
            while($get = _fetch($qry)) {
                $getp = db("SELECT * FROM `".$db['pos']."` WHERE `id` = ".(int)($get['posi']).";",false,true);
                if(!empty($get['name'])) $squadname = '<b>'.$get['name'].':</b> ';
                else $squadname = '';
                return $squadname.$getp['position'];
            }
        } else {
            $get = db("SELECT `level`,`banned` FROM `".$db['users']."` WHERE `id` = ".(int)($tid).";",false,true);
            if(!$get['level'] && !$get['banned'])
                return _status_unregged;
            else if($get['level'] == 1)
                return _status_user;
            else if($get['level'] == 2)
                return _status_trial;
            else if($get['level'] == 3)
                return _status_member;
            else if($get['level'] == 4)
                return _status_admin;
            else if(!$get['level'] && $get['banned'])
                return _status_banned;
            else
                return _gast;
        }
    } else {
        $qry = db("SELECT s1.*,s2.`position` FROM `".$db['userpos']."` AS `s1` LEFT JOIN `".$db['pos']."` AS `s2` ON s1.`posi` = s2.`id` WHERE s1.`user` = ".(int)($tid)." AND s1.`posi` != 0 ORDER BY s2.`pid` ASC;");
        if(_rows($qry)) {
            $get = _fetch($qry);
            return $get['position'];
        } else {
            $get = db("SELECT `level`,`banned` FROM `".$db['users']."` WHERE `id` = ".(int)($tid).";",false,true);
            if(!$get['level'] && !$get['banned'])
                return _status_unregged;
            elseif($get['level'] == 1)
                return _status_user;
            elseif($get['level'] == 2)
                return _status_trial;
            elseif($get['level'] == 3)
                return _status_member;
            elseif($get['level'] == 4)
                return _status_admin;
            elseif(!$get['level'] && $get['banned'])
                return _status_banned;
            else
                return _gast;
        }
    }
    return '';
}

//-> Session fuer den letzten Besuch setzen
function set_lastvisit() {
    global $db,$useronline,$userid;
    if($userid) {
        if(!db("SELECT `id` FROM `".$db['users']."` WHERE `id` = ".(int)($userid)." AND (time+".$useronline.") > ".time().";",true)) {
            $_SESSION['lastvisit'] = data("time");
        }
    }
}

//-> Checkt welcher User gerade noch online ist
function onlinecheck($tid) {
    global $db,$useronline;
    $row = db("SELECT `id` FROM `".$db['users']."` WHERE `id` = ".(int)($tid)." AND (time+".$useronline.") > ".time()." AND `online` = 1;",true);
    return $row ? "<img src=\"../inc/images/online.gif\" alt=\"\" class=\"icon\" />" : "<img src=\"../inc/images/offline.gif\" alt=\"\" class=\"icon\" />";
}

//Funktion fuer die Sprachdefinierung der Profilfelder
function pfields_name($name) {
    return preg_replace_callback("=_(.*?)_=Uis",
        function($match) {
        if(defined("_profil".substr(trim($match[0]), 0, -1)))
            return constant("_profil".substr(trim($match[0]), 0, -1));

        return $match[0];
        }, $name);
}

//-> Checkt versch. Dinge anhand der Hostmaske eines Users
function ipcheck($what,$time = "") {
    global $db,$userip;
    $get = db("SELECT `time`,`what` FROM `".$db['ipcheck']."` WHERE `what` = '".$what."' AND `ip` = '".$userip."' ORDER BY `time` DESC;",false,true);
    if(count($get) >= 1) {
        if (preg_match("#vid#", $get['what']))
            return true;
        else {
            if ($get['time'] + (int)($time) < time())
                db("DELETE FROM `" . $db['ipcheck'] . "` WHERE `what` = '" . $what . "' AND `ip` = '" . $userip . "' AND (`time`+" . $time . ") < " . time() . ";");

            if (($get['time'] + $time) > time())
                return true;
        }
    }

    return false;
}

//-> Gibt die Tageszahl eines Monats aus
function days_in_month($month, $year)
{ return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31); }

//-> Setzt bei einem Tag >10 eine 0 vorran (Kalender)
function cal($i) {
    if(preg_match("=10|20|30=Uis",$i) == FALSE) $i = preg_replace("=0=", "", $i);
    if($i < 10) $tag_nr = "0".$i;
    else $tag_nr = $i;
    return $tag_nr;
}

//-> Entfernt fuehrende Nullen bei Monatsangaben
function nonum($i) {
    if(preg_match("=10=Uis",$i) == false)
        return preg_replace("=0=", "", $i);

    return $i;
}

//-> maskiert Zeilenumbrueche fuer <textarea>
function txtArea($txt)
{ return $txt; }

//-> Konvertiert Platzhalter in die jeweiligen bersetzungen
function navi_name($name) {
    $name = trim($name);
    if(preg_match("#^_(.*?)_$#Uis",$name)) {
        $name = preg_replace("#_(.*?)_#Uis", "$1", $name);

        if(defined("_".$name))
            return constant("_".$name);
    }

    return $name;
}

//RSS News Feed erzeugen
function convert_feed($txt) {
    global $charset;
    $txt = stripslashes($txt);
    $txt = str_replace("&Auml;","Ae",$txt);
    $txt = str_replace("&auml;","ae",$txt);
    $txt = str_replace("&Uuml;","Ue",$txt);
    $txt = str_replace("&uuml;","ue",$txt);
    $txt = str_replace("&Ouml;","Oe",$txt);
    $txt = str_replace("&ouml;","oe",$txt);
    $txt = htmlentities($txt, ENT_QUOTES, $charset);
    $txt = str_replace("&amp;","&",$txt);
    $txt = str_replace("&lt;","<",$txt);
    $txt = str_replace("&gt;",">",$txt);
    $txt = str_replace("&#60;","<",$txt);
    $txt = str_replace("&#62;",">",$txt);
    $txt = str_replace("&#34;","\"",$txt);
    $txt = str_replace("&nbsp;"," ",$txt);
    $txt = str_replace("&szlig;","ss",$txt);
    $txt = preg_replace("#&(.*?);#is","",$txt);
    $txt = str_replace("&","&amp;",$txt);
    $txt = str_replace("", "\"",$txt);
    $txt = str_replace("", "\"",$txt);
    return strip_tags($txt);
}

// Userpic ausgeben
function userpic($userid, $width=170,$height=210) {
    global $picformat; $pic = '';
    foreach($picformat as $endung) {
        if(file_exists(basePath."/inc/images/uploads/userpics/".$userid.".".$endung)) {
            $pic = show(_userpic_link, array("id" => $userid, "endung" => $endung, "width" => $width, "height" => $height));
            break;
        }
        else
            $pic = show(_no_userpic, array("width" => $width, "height" => $height));
    }

    return $pic;
}

// Useravatar ausgeben
function useravatar($uid=0, $width=100,$height=100) {
    global $picformat,$userid; $pic = '';
    $uid = $uid == 0 ? $userid : $uid;
    foreach($picformat as $endung) {
        if(file_exists(basePath."/inc/images/uploads/useravatare/".$uid.".".$endung))
        {
            $pic = show(_userava_link, array("id" => $uid, "endung" => $endung, "width" => $width, "height" => $height));
            break;
        }
        else
            $pic = show(_no_userava, array("width" => $width, "height" => $height));
    }

    return $pic;
}

// Userpic fuer Hoverinformationen ausgeben
function hoveruserpic($userid, $width=170,$height=210) {
    global $picformat;
    $pic = "../inc/images/nopic.gif', '".$width."', '".$height;
    foreach($picformat as $endung) {
        if(file_exists(basePath."/inc/images/uploads/userpics/".$userid.".".$endung)) {
            $pic = "../inc/images/uploads/userpics/".$userid.".".$endung."', '".$width."', '".$height."";
            break;
        }
    }

    return $pic;
}

// Adminberechtigungen ueberpruefen
function admin_perms($userid) {
    global $db,$chkMe;
    if(empty($userid))
        return false;
    
    if(rootAdmin($userid)) 
        return true;

   // no need for these admin areas
    $e = array('gb', 'shoutbox', 'editusers', 'votes', 'contact', 'joinus', 'intnews', 'forum', 
	'gs_showpw','dlintern','intforum','galleryintern');

   // check user permission
    $c = db("SELECT * FROM `".$db['permissions']."` WHERE `user` = ".(int)($userid).";",false,true);
    if(!empty($c)) {
        foreach($c AS $v => $k) {
            if($v != 'id' && $v != 'user' && $v != 'pos' && !in_array($v, $e)) {
                if($k == 1) {
                    return true;
                    break;
                }
            }
        }
    }

   // check rank permission
    $qry = db("SELECT s1.* FROM `".$db['permissions']."` AS `s1` LEFT JOIN `".$db['userpos']."` AS `s2` ON s1.`pos` = s2.`posi` WHERE s2.`user` = ".(int)($userid)." AND s2.`posi` != 0;");
    while($r = _fetch($qry)) {
        foreach($r AS $v => $k) {
            if($v != 'id' && $v != 'user' && $v != 'pos' && !in_array($v, $e)) {
                if($k == 1) {
                    return true;
                    break;
                }
            }
        }
    }

    return ($chkMe == 4) ? true : false;
}

//-> filter placeholders
function pholderreplace($pholder) {
    /** @noinspection CssInvalidAtRule */
    $search = array('@<script[^>]*?>.*?</script>@si',
                    '@<style[^>]*?>.*?</style>@siU',
                    '@<[\/\!]*?[^<>]*?>@si',
                    '@<![\s\S]*?--[ \t\n\r]*>@');
    //Replace
    $pholder = preg_replace("#<script(.*?)</script>#is","",$pholder);
    $pholder = preg_replace("#<style(.*?)</style>#is","",$pholder);
    $pholder = preg_replace($search, '', $pholder);
    $pholder = str_replace(" ","",$pholder);
    $pholder = preg_replace("#[0-9]#is","",$pholder);
    $pholder = preg_replace("#&(.*?);#s","",$pholder);
    $pholder = str_replace("\r","",$pholder);
    $pholder = str_replace("\n","",$pholder);
    $pholder = preg_replace("#\](.*?)\[#is","][",$pholder);
    $pholder = str_replace("][","^",$pholder);
    $pholder = preg_replace("#^(.*?)\[#s","",$pholder);
    $pholder = preg_replace("#\](.*?)$#s","",$pholder);
    $pholder = str_replace("[","",$pholder);
    return str_replace("]","",$pholder);
}

//-> Zugriffsberechtigung auf die Seite
function check_internal_url() {
    global $db,$chkMe;
    if($chkMe >= 1) return false;
    $install_pfad = explode("/",dirname(dirname($_SERVER['SCRIPT_NAME'])."../"));
    $now_pfad = explode("/",$_SERVER['REQUEST_URI']); $pfad = '';
    foreach($now_pfad as $key => $value) {
        if(!empty($value)) {
            if(!isset($install_pfad[$key]) || $value != $install_pfad[$key]) {
                $pfad .= "/".$value;
            }
        }
    }

    list($pfad) = explode('&',$pfad);
    $pfad = "..".$pfad;

    if(strpos($pfad, "?") === false && strpos($pfad, ".php") === false)
        $pfad .= "/";

    if(strpos($pfad, "index.php") !== false)
        $pfad = str_replace('index.php','',$pfad);

    $qry_navi = db("SELECT `internal` FROM ".$db['navi']." WHERE `url` = '".$pfad."' OR `url` = '".$pfad.'index.php'."'");
    if(_rows($qry_navi)) {
        $get_navi = _fetch($qry_navi);
        if($get_navi['internal'])
            return true;
    }

    return false;
}

//-> Ladezeit
function generatetime() {
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

//-> Rechte abfragen
function getPermissions($checkID = 0, $pos = 0) {
    global $db,$lang;

    if(!empty($checkID)) {
        $check = empty($pos) ? 'user' : 'pos'; $checked = array();
        $qry = db("SELECT * FROM ".$db['permissions']." WHERE `".$check."` = '".(int)($checkID)."'");
        if(_rows($qry)) foreach(_fetch($qry) AS $k => $v) $checked[$k] = $v;
    }

    $permission = array();
    $qry = db("SHOW COLUMNS FROM ".$db['permissions']."");
    while($get = _fetch($qry)) {
        if($get['Field'] != 'id' && $get['Field'] != 'user' && $get['Field'] != 'pos' && $get['Field'] != 'intforum') {
            @eval("\$lang = _perm_".$get['Field'].";");
            $chk = empty($checked[$get['Field']]) ? '' : ' checked="checked"';
            $permission[$lang] = '<input type="checkbox" class="checkbox" id="'.$get['Field'].'" name="perm[p_'.$get['Field'].']" value="1"'.$chk.' /><label for="'.$get['Field'].'"> '.$lang.'</label> ';
        }
    }

    natcasesort($permission); $break = 1; $p = '';
    foreach($permission AS $perm) {
        $br = ($break % 2) ? '<br />' : ''; $break++;
        $p .= $perm.$br;
    }

    return $p;
}

//-> interne Foren-Rechte abfragen
function getBoardPermissions($checkID = 0, $pos = 0) {
    global $db;

    $break = 0; $i_forum = ''; $fkats = '';
    $qry = db("SELECT `id`,`name` FROM `".$db['f_kats']."` WHERE `intern` = 1 ORDER BY `kid` ASC;");
    while($get = _fetch($qry)) {
        unset($kats, $fkats, $break);
        $kats = (empty($katbreak) ? '' : '<div style="clear:both">&nbsp;</div>').'<table class="hperc" cellspacing="1"><tr><td class="contentMainTop"><b>'.re($get["name"]).'</b></td></tr></table>';
        $katbreak = 1;

        $qry2 = db("SELECT `kattopic`,`id` FROM `".$db['f_skats']."` WHERE `sid` = ".$get['id']." ORDER BY `kattopic` ASC;"); $break = 0; $fkats = '';
        while($get2 = _fetch($qry2)) {
            $br = ($break % 2) ? '<br />' : ''; $break++;
            $check =  db("SELECT * FROM ".$db['f_access']." WHERE `".(empty($pos) ? 'user' : 'pos')."` = '".(int)($checkID)."' AND ".(empty($pos) ? 'user' : 'pos')." != '0' AND `forum` = '".$get2['id']."'");
            $chk = _rows($check) ? ' checked="checked"' : '';
            $fkats .= '<input type="checkbox" class="checkbox" id="board_'.$get2['id'].'" name="board['.$get2['id'].']" value="'.$get2['id'].'"'.$chk.' /><label for="board_'.$get2['id'].'"> '.re($get2['kattopic']).'</label> '.$br;
        }

        $i_forum .= $kats.$fkats;
    }

    return $i_forum;
}

//-> schreibe in die IPCheck Tabelle
function setIpcheck($what = '',$time=true) {
    global $db, $userip;
    db("INSERT INTO `".$db['ipcheck']."` SET `ip` = '".$userip."', "
            . "`user_id` = ".userid().", `what` = '".$what."', "
            . "`time` = ".($time ? time() : 0).", `created` = ".time().";");
}

function is_php($version='5.3.0')
{ return (floatval(phpversion()) >= $version); }

function hextobin($hexstr) {
    if(is_php('5.4.0'))
        return hex2bin($hexstr);
    // < PHP 5.4
    $n = strlen($hexstr);
    $sbin="";
    $i=0;
    while($i<$n) {
        $a =substr($hexstr,$i,2);
        $c = pack("H*",$a);
        if ($i==0){$sbin=$c;}
        else {$sbin.=$c;}
        $i+=2;
    }

    return $sbin;
}

//-> Speichert Rückgaben der MySQL Datenbank zwischen um SQL-Queries einzusparen
final class dbc_index {
    private static $index = array();
    public static final function setIndex($index_key,$data) {
        global $cache,$config_cache;

        if(self::MemSetIndex()) {
            if(show_dbc_debug)
                DebugConsole::insert_info('dbc_index::setIndex()', 'Set index: "'.$index_key.'" to cache');

            if($config_cache['dbc']) {
                $data_cache = null;
                try {
                    $data_cache = $cache->getItem('dbc_' . $index_key);
                } catch (\phpFastCache\Exceptions\phpFastCacheInvalidArgumentException $e) {
                }
                $data_cache->set(serialize($data))->expiresAfter(1.5);
                $cache->save($data_cache);
            }
        }

        if(show_dbc_debug)
            DebugConsole::insert_info('dbc_index::setIndex()', 'Set index: "'.$index_key.'"');

        self::$index[$index_key] = $data;
    }

    public static final function getIndex($index_key) {
        if(!self::issetIndex($index_key))
            return false;

        if(show_dbc_debug)
            DebugConsole::insert_info('dbc_index::getIndex()', 'Get full index: "'.$index_key.'"');

        return self::$index[$index_key];
    }

    public static final function getIndexKey($index_key,$key) {
        if(!self::issetIndex($index_key))
            return false;

        $data = self::$index[$index_key];
        if(empty($data) || !array_key_exists($key,$data))
            return false;

        return $data[$key];
    }

    public static final function issetIndex($index_key) {
        global $cache;
        if(isset(self::$index[$index_key])) return true;
        if(self::MemSetIndex()) {
            $data = null;
            try {
                $data = $cache->getItem('dbc_' . $index_key);
            } catch (\phpFastCache\Exceptions\phpFastCacheInvalidArgumentException $e) {
            }
            if(!is_null($data->get())) {
                if(show_dbc_debug)
                    DebugConsole::insert_loaded('dbc_index::issetIndex()', 'Load index: "'.$index_key.'" from cache');

                self::$index[$index_key] = unserialize($data->get());
                return true;
            }
        }

        return false;
    }

    public static final function MemSetIndex() {
        global $config_cache,$cache;
        if (!$config_cache['dbc'] || CacheManager::$fallback) {
            return false;
        }

        switch($cache->getDriverName()) {
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
 * @param boolean $zeige_leere_einheiten * sollen einheiten, die den wert 0 haben, angezeigt werden?
 * @param array $zeige_einheiten * zeige nur angegebene einheiten. jahre werden zb in sekunden umgerechnet
 * @param string $standard * falls der timestamp 0 oder ungueltig ist, gebe diesen string zurueck
 * @return string
 */
function get_elapsed_time( $timestamp, $aktuell = null, $anzahl_einheiten = null, $zeige_leere_einheiten = null, $zeige_einheiten = null, $standard = null ) {
    if ( $aktuell === null ) $aktuell = time();
    if ( $anzahl_einheiten === null ) $anzahl_einheiten = 1;
    if ( $zeige_leere_einheiten === null ) $zeige_leere_einheiten = true;
    if ( !is_array( $zeige_einheiten ) ) $zeige_einheiten = array();
    if ( $standard === null ) $standard = "nie";
    if ( $timestamp == 0 ) return $standard;
    if ( $timestamp > $aktuell ) $timestamp = $aktuell;
    if ( $anzahl_einheiten < 1 ) $anzahl_einheiten = 10;
    $zeit = bcsub( $aktuell, $timestamp );
    if ( $zeit < 1 ) $zeit = 1; $arr = array();
    $werte = array( 63115200 => _years, 31557600 => _year.' ', 4838400 => _months, 2419200 => _month.' ',
            1209600 => _weeks, 604800 => _week.' ', 172800 => _days.' ', 86400 => _day.' ', 7200 => _hours,
            3600 => _hour.' ', 120 => _minutes, 60 => _minute.' ',  1 => _seconds );

    if ( ( is_array( $zeige_einheiten ) ) and ( count( $zeige_einheiten ) > 0 ) ) {
        $neu = array();
        foreach ( $werte as $key => $val ) {
            if ( in_array( $val, $zeige_einheiten ) )
                $neu[$key] = $val;
        }

        $werte = $neu;
    }

    foreach ( $werte as $div => $einheit ) {
        if ( $zeit < $div ) {
            if ( count( $arr ) != 0 )
                $arr[$einheit] = 0;

            continue;
        }

        $anzahl = bcdiv( $zeit, $div );
        $zeit -= bcmul( $anzahl, $div );
        $arr[$einheit] = $anzahl;
    }

    reset( $arr ); $output = 0; $ret = "";
    while ( ( count( $arr ) > 0 ) and ( $output < $anzahl_einheiten ) ) {
        $key = key( $arr );
        $cur = current( $arr );
        $einheit = ( $cur == 1 ) ? substr( $key, 0, bcsub( strlen( $key ), 1 ) ) : $key;
        if ( ( $cur != 0 ) or ( $zeige_leere_einheiten == true ) )
            $ret .= ( empty( $ret ) )
            ? ($anzahl_einheiten == 1 ? round($cur, 0, PHP_ROUND_HALF_DOWN) : $cur) . " " . $einheit
            : ", " . ($anzahl_einheiten == 1 ? round($cur, 0, PHP_ROUND_HALF_DOWN) : $cur) . " " . $einheit;
        $output++;
        unset( $arr[$key] );
    }
    return $ret;
}

//-> Neue Funktionen einbinden, sofern vorhanden
if($functions_files = get_files(basePath.'/inc/additional-functions/',false,true,array('php'))) {
    foreach($functions_files AS $func) {
        include_once(basePath.'/inc/additional-functions/'.$func);
    }
    unset($functions_files,$func);
}

//-> Navigation einbinden
include_once(basePath.'/inc/menu-functions/navi.php');

//-> Ausgabe des Indextemplates
function page($index='',$title='',$where='',$wysiwyg='',$index_templ='index')
{
    global $db,$userid,$userip,$tmpdir,$chkMe,$charset,$mysql,$isSpider;
    global $designpath,$cp_color,$time_start;

    // Timer Stop
    $time = round(generatetime() - $time_start,4);

    // JS-Dateine einbinden
    $lng = language_short_tag(); $login = '';
    $edr = ($wysiwyg=='_word')?'advanced':'normal';
    $lcolor = ($cp_color==1)?'lcolor=true;':'';
    $dsgvo = (!array_key_exists('do_show_dsgvo',$_SESSION) || !$_SESSION['do_show_dsgvo'] ? 1 : 0);
    $dsgvo_lock = (!array_key_exists('user_has_dsgvo_lock',$_SESSION) || !$_SESSION['user_has_dsgvo_lock'] ? 0 : 1);
    $java_vars = '<script language="javascript" type="text/javascript">var maxW = '.config('maxwidth').',lng = \''.$lng.'\',dsgvo = \''.$dsgvo.'\',
    dsgvo_lock = \''.$dsgvo_lock.'\',dzcp_editor = \''.$edr.'\';'.$lcolor.'</script>'."\n";
    $min = (use_min_css_js_files ? '.min' : '');

    if(!strstr($_SERVER['HTTP_USER_AGENT'],'Android') && !strstr($_SERVER['HTTP_USER_AGENT'],'webOS'))
        $java_vars .= '<script language="javascript" type="text/javascript" src="'.$designpath.'/_js/wysiwyg'.$min.'.js"></script>'."\n";

    if(settings("wmodus") && $chkMe != 4) {
        if(HasDSGVO()) {
            $secure = '';
            if (config('securelogin'))
                $secure = show("menu/secure", array("help" => _login_secure_help, "security" => _register_confirm));

            $login = show("errors/wmodus_login", array("what" => _login_login, "secure" => $secure, "signup" => _login_signup,
                "permanent" => _login_permanent, "lostpwd" => _login_lostpwd));
            cookie::save(); //Save Cookie
        }

        include_once(basePath.'/inc/menu-functions/dsgvo.php');
        echo show("errors/wmodus", array("wmodus" => _wartungsmodus,
                                              "head" => _wartungsmodus_head,
                                              "tmpdir" => $tmpdir,
                                              "dsgvo" => dsgvo(),
                                              "java_vars" => $java_vars,
                                              "dir" => $designpath,
                                              "title" => re(strip_tags($title)),
                                              "login" => $login));
    } else {
        if(!$isSpider && HasDSGVO()) {
            updateCounter();
            update_maxonline();
        }

        //check permissions
        $check_msg = '';
        if($chkMe) {
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

        if(check_internal_url())
            $index = error(_error_have_to_be_logged, 1);

        $where = preg_replace_callback("#autor_(.*?)$#",function($id) { return re(data("nick","$id[1]")); },$where);
        $index = empty($index) ? '' : (empty($check_msg) ? '' : $check_msg).'<table class="mainContent" cellspacing="1">'.$index.'</table>';

        //-> Sort & filter placeholders
        //default placeholders
        $arr = array("idir" => '../inc/images/admin', "dir" => $designpath);

        //check if placeholders are given
        $pholder = file_get_contents($designpath."/index.html");

        //filter placeholders
        $blArr = array("[clanname]","[title]","[copyright]","[java_vars]","[min]",
            "[headtitle]","[index]", "[time]","[rss]","[dir]","[charset]","[where]","[lang]");
        $pholdervars = '';
        for($i=0;$i<=count($blArr)-1;$i++) {
            if(preg_match("#".$blArr[$i]."#",$pholder))
                $pholdervars .= $blArr[$i];
        }

        for($i=0;$i<=count($blArr)-1;$i++)
            $pholder = str_replace($blArr[$i],"",$pholder);

        $pholder = pholderreplace($pholder);
        $pholdervars = pholderreplace($pholdervars);

        //put placeholders in array
        $pholder = explode("^",$pholder);
        for($i=0;$i<=count($pholder)-1;$i++) {
            if(strstr($pholder[$i], 'nav_'))
                $arr[$pholder[$i]] = navi($pholder[$i]);
            else {
                if(@file_exists(basePath.'/inc/menu-functions/'.$pholder[$i].'.php'))
                    include_once(basePath.'/inc/menu-functions/'.$pholder[$i].'.php');

                if(function_exists($pholder[$i]))
                    $arr[$pholder[$i]] = $pholder[$i]();
            }
        }

        $pholdervars = explode("^",$pholdervars);
        foreach ($pholdervars as $pholdervar) {
			if(isset($$pholdervar)) {
				$arr[$pholdervar] = $$pholdervar;
			}
        }

        //index output
        $index = (file_exists("../inc/_templates_/".$tmpdir."/".$index_templ.".html") ? show($index_templ, $arr) : show("index", $arr));
        if(!mysqli_persistconns)
            $mysql->close(); //MySQL

        if(HasDSGVO())
            cookie::save(); //Save Cookie

        if(debug_save_to_file) DebugConsole::save_log(); //Debug save to file
        $output = view_error_reporting ? DebugConsole::show_logs().$index : $index; //Debug Console + Index Out

        if(!array_key_exists('do_show_dsgvo',$_SESSION)) {
            $_SESSION['do_show_dsgvo'] = true;
        }

        gz_output($output); // OUTPUT BUFFER END
    }
}