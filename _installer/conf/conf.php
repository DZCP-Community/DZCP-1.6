<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

###############
## Variablen ##
###############
define('_disabled_fopen', 'Dein Webserver unterst&uuml;tzt die Funktion <i>fopen</i> nicht!');
define('_do_config', 'Du musst die Konfiguration erfolgreich abschlie&szlig;en, um die Datenbank installieren zu k&ouml;nnen!');
define('_true', '<img src="img/true.gif" border="0" alt="" vspace="0" align="center"> ');
define('_false', '<img src="img/false.gif" border="0" alt="" vspace="0" align="center"> ');
define('_link_start', '<span class="enabled">&raquo; Lizenz</span>');
define('_link_start_1', '<span class="disabled">1. Lizenz</span>');
define('_link_require', '<span class="enabled">&raquo; Erweiterungen</span>');
define('_link_require_1', '<span class="disabled">2. Erweiterungen</span>');
define('_link_prepare', '<span class="enabled">&raquo; Vorbereitung</span>');
define('_link_prepare_1', '<span class="disabled">3. Vorbereitung</span>');
define('_link_install', '<span class="enabled">&raquo; MySQL</span>');
define('_link_install_1', '<span class="disabled">4. MySQL</span>');
define('_link_db', '<span class="enabled">&raquo; Installation</span>');
define('_link_db_1', '<span class="disabled">5. Installation</span>');
define('_link_dbu', '<span class="enabled">&raquo; Update</span>');
define('_link_dbu_1', '<span class="disabled">4. Update</span>');
define('_link_done', '<span class="enabled">&raquo; Done</span>');
define('_link_done_1', '<span class="disabled">6. Done</span>');

define('_link_update_done', '<span class="enabled">&raquo; Done</span>');
define('_link_update_done_1', '<span class="disabled">5. Done</span>');

################
## Funktionen ##
################
function check_file_dir($file, $is_file=false) {
    if($is_file == 1) $what = "Dir:&nbsp;";
    else $what = "File:";

    $_file = preg_replace("#\.\.#Uis", "", $file);
    if(is_writable($file))
        return _true."<span style='color:green'><b>".$what."</b>&nbsp;&nbsp;&nbsp; ".$_file."</span><br />";
    else
        return _false."<span style='color:red'><b>".$what."</b>&nbsp;&nbsp;&nbsp; ".$_file."</span><br />";
}

function set_ftp_chmod($file,$pfad,$host,$user,$pwd) {
    $conn = @ftp_connect($host);
    @ftp_login($conn, $user, $pwd);
    ftp_site($conn, 'CHMOD 0777 '.$pfad.'/'.$file);
}

function _m ($prefix, $host, $user, $pwd, $db) {
    $fp = @fopen("../inc/mysql.php","w");
    @fwrite($fp,"<?php
    \$sql_prefix = '".$prefix."';
    \$sql_host = '".$host."';
    \$sql_user =  '".addslashes($user)."';
    \$sql_pass = '".addslashes($pwd)."';
    \$sql_db = '".$db."';");
    @fclose($fp);
}

function get_files($dir) {
    $dp = @opendir($dir);
    $files = array();
    while($file = @readdir($dp))
      {
        if($file != '.' && $file != '..')
              array_push($files, $file);
    }
      @closedir($dp);
      sort($files);
      return($files);
}

function makePrev() {
    $arr = array(0,1,2,3,4,5,6,7,8,9);
    return $arr[rand(0,9)].$arr[rand(0,9)].$arr[rand(0,9)];
}

function up($txt,$bbcode=0) {
    $txt = str_replace("& ","&amp; ",$txt);
    $txt = str_replace("\"","&#34;",$txt);
    $txt = trim($txt);
    if(empty($bbcode)) $txt = nl2br($txt);
    return spChars($txt);
}

function spChars($txt) {
    $txt = str_replace("�","&Auml;",$txt);
    $txt = str_replace("�","&auml;",$txt);
    $txt = str_replace("�","&Uuml;",$txt);
    $txt = str_replace("�","&uuml;",$txt);
    $txt = str_replace("�","&Ouml;",$txt);
    $txt = str_replace("�","&ouml;",$txt);
    $txt = str_replace("�","&szlig;",$txt);
    return str_replace("?","&euro;",$txt);
}

function visitorIp() {
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

function mkpwd() {
    $chars = '1234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $len = strlen($chars) - 1; $pw = '';
    for($i = 0; $i < 10; $i++)
    { $pw .= $chars{rand(0, $len)}; }
    return $pw;
}