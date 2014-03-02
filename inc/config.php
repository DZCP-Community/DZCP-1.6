<?php
#########################################
//-> DZCP Settings Start
#########################################

define('view_error_reporting', true); // Zeigt alle Fehler und Notices etc.
define('use_default_timezone', true); // Verwendende die Zeitzone vom Server
define('default_timezone', 'Europe/Berlin'); // Die zu verwendende Zeitzone selbst einstellen * 'use_default_timezone' auf false stellen *

#########################################
//-> DZCP Settings End
#########################################

if(view_error_reporting) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
else
    error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

## REQUIRES ##
//DZCP-Install default variable
if(!isset($sql_host) || !isset($sql_user) || !isset($sql_pass) || !isset($sql_db)) {
$sql_prefix = ''; $sql_host = ''; $sql_user =  ''; $sql_pass = ''; $sql_db = '';
}

if(file_exists(basePath."/inc/mysql.php"))
    require_once(basePath."/inc/mysql.php");

//DZCP-Install default variable
if(!isset($installation))
  $installation = false;

if(function_exists("date_default_timezone_set") && function_exists("date_default_timezone_get") && use_default_timezone)
    date_default_timezone_set(date_default_timezone_get());
else if(!use_default_timezone) date_default_timezone_set(default_timezone);
else date_default_timezone_set("Europe/Berlin");

function show($tpl="", $array=array(), $array_lang_constant=array(), $array_block=array()) {
    global $tmpdir;
    if(!empty($tpl) && $tpl != null) {
        $template = basePath."/inc/_templates_/".$tmpdir."/".$tpl;
        $array['dir'] = '../inc/_templates_/'.$tmpdir;

        if(file_exists($template.".html"))
            $tpl = file_get_contents($template.".html");

        //put placeholders in array
        $pholder = explode("^",pholderreplace($tpl));
        for($i=0;$i<=count($pholder)-1;$i++) {
            if(in_array($pholder[$i],$array_block))
                continue;

            if(array_key_exists($pholder[$i],$array))
                continue;

            if(!strstr($pholder[$i], 'lang_'))
                continue;

            if(defined(substr($pholder[$i], 4)))
                $array[$pholder[$i]] = (count($array_lang_constant) >= 1 ? show(constant(substr($pholder[$i], 4)),$array_lang_constant) : constant(substr($pholder[$i], 4)));
        }

        unset($pholder);

        if(count($array) >= 1) {
            foreach($array as $value => $code)
            { $tpl = str_replace('['.$value.']', $code, $tpl); }
        }
    }

    return $tpl;
}

//-> MySQL-Datenbankangaben
$prefix = $sql_prefix;
$db = array("host" =>           $sql_host,
            "user" =>           $sql_user,
            "pass" =>           $sql_pass,
            "db" =>             $sql_db,
            "prefix" =>         $prefix,
            "artikel" =>        $prefix."artikel",
            "acomments" =>      $prefix."acomments",
            "awards" =>         $prefix."awards",
            "away" =>           $prefix."away",
            "banned" =>         $prefix."banned",
            "buddys" =>         $prefix."userbuddys",
            "ipcheck" =>        $prefix."ipcheck",
            "clankasse" =>      $prefix."clankasse",
            "c_kats" =>         $prefix."clankasse_kats",
            "c_payed" =>        $prefix."clankasse_payed",
            "config" =>         $prefix."config",
            "counter" =>        $prefix."counter",
            "c_ips" =>          $prefix."counter_ips",
            "c_who" =>          $prefix."counter_whoison",
            "cw" =>             $prefix."clanwars",
            "cw_comments" =>    $prefix."cw_comments",
            "cw_player" =>      $prefix."clanwar_players",
            "downloads" =>      $prefix."downloads",
            "dl_kat" =>         $prefix."download_kat",
            "events" =>         $prefix."events",
            "f_access" =>       $prefix."f_access",
            "f_abo" =>          $prefix."f_abo",
            "f_kats" =>         $prefix."forumkats",
            "f_posts" =>        $prefix."forumposts",
            "f_skats" =>        $prefix."forumsubkats",
            "f_threads" =>      $prefix."forumthreads",
            "gallery" =>        $prefix."gallery",
            "gb" =>             $prefix."gb",
            "glossar" =>        $prefix."glossar",
            "links" =>          $prefix."links",
            "linkus" =>         $prefix."linkus",
            "msg" =>            $prefix."messages",
            "news" =>           $prefix."news",
            "navi" =>           $prefix."navi",
            "navi_kats" =>      $prefix."navi_kats",
            "newscomments" =>   $prefix."newscomments",
            "newskat" =>        $prefix."newskat",
            "partners" =>       $prefix."partners",
            "permissions" =>    $prefix."permissions",
            "pos" =>            $prefix."positions",
            "profile" =>        $prefix."profile",
            "rankings" =>       $prefix."rankings",
            "reg" =>            $prefix."reg",
            "server" =>         $prefix."server",
            "serverliste" =>    $prefix."serverliste",
            "settings" =>       $prefix."settings",
            "shout" =>          $prefix."shoutbox",
            "sites" =>          $prefix."sites",
            "squads" =>         $prefix."squads",
            "squaduser" =>      $prefix."squaduser",
            "sponsoren" =>      $prefix."sponsoren",
            "slideshow" =>      $prefix."slideshow",
            "taktik" =>         $prefix."taktiken",
            "users" =>          $prefix."users",
            "usergallery" =>    $prefix."usergallery",
            "usergb" =>         $prefix."usergb",
            "userpos" =>        $prefix."userposis",
            "userstats" =>      $prefix."userstats",
            "votes" =>          $prefix."votes",
            "vote_results" =>   $prefix."vote_results");
unset($prefix,$sql_host,$sql_user,$sql_pass,$sql_db);

if($db['host'] != '' && $db['user'] != '' && $db['pass'] != '' && $db['db'] != '')
    if(!$mysql = mysqli_connect($db['host'],$db['user'],$db['pass'],$db['db'])) die("<b>Fehler beim Zugriff auf die Datenbank!");

//MySQL-Funktionen
function _rows($rows)
{ return mysqli_num_rows($rows); }

function _fetch($fetch)
{ return mysqli_fetch_assoc($fetch); }

function db($db='',$rows=false,$fetch=false) {
    global $prefix,$mysql;
    if(!$qry = mysqli_query($mysql,$db)) die('<b>MySQL-Query failed:</b><br /><br /><ul>'.
                                     '<li><b>ErrorNo</b> = '.!empty($prefix) ? str_replace($prefix,'',mysqli_errno()) : mysqli_errno().
                                     '<li><b>Error</b>   = '.!empty($prefix) ? str_replace($prefix,'',mysqli_error()) : mysqli_error().
                                     '<li><b>Query</b>   = '.!empty($prefix) ? str_replace($prefix,'',$db).'</ul>' : $db);
    if ($rows && !$fetch)
        return mysqli_num_rows($qry);
    else if($fetch && $rows)
        return mysqli_fetch_array($qry);
    else if($fetch && !$rows)
        return mysqli_fetch_assoc($qry);

    return $qry;
}

function sql_backup() {
    global $mysql,$db;
    $backup_table_data = array();

    //Table Drop
    $sqlqry = db('SHOW TABLE STATUS');
    while($table = _fetch($sqlqry))
    { $backup_table_data[$table['Name']]['drop'] = 'DROP TABLE IF EXISTS `'.$table['Name'].'`;'; }
    unset($table);

    //Table Create
    foreach($backup_table_data as $table => $null) {
        unset($null);
        $sqlqry = db('SHOW CREATE TABLE '.$table.';');
        while($table = _fetch($sqlqry))
        { $backup_table_data[$table['Table']]['create'] = $table['Create Table'].';'; }
    }
    unset($table);

    //Insert Create
    foreach($backup_table_data as $table => $null) {
        unset($null); $backup = '';
        $sqlqry = db('SELECT * FROM '.$table.' ;');
        while($dt = _fetch($sqlqry)) {
            if(!empty($dt)) {
                $backup_data = '';
                foreach ($dt as $key => $var)
                { $backup_data .= "`".$key."` = '".((string)(str_replace("'", "`", $var)))."',"; }

                $backup .= "INSERT INTO `".$table."` SET ".substr($backup_data, 0, -1).";\r\n";
                unset($backup_data);
            }
        }

        $backup_table_data[$table]['insert'] = $backup;
        unset($backup);
    }
    unset($table);

    $sql_backup =  "-- -------------------------------------------------------------------\r\n";
    $sql_backup .= "-- Datenbank Backup von deV!L`z Clanportal v."._version."\r\n";
    $sql_backup .= "-- Build: "._release." * "._build."\r\n";
    $sql_backup .= "-- Host: ".$db['host']."\r\n";
    $sql_backup .= "-- Erstellt am: ".date("d.m.Y")." um ".date("H:i")."\r\n";
    $sql_backup .= "-- MySQL-Version: ".mysqli_get_server_info($mysql)."\r\n";
    $sql_backup .= "-- PHP Version: ".phpversion()."\r\n";
    $sql_backup .= "-- -------------------------------------------------------------------\r\n\r\n";
    $sql_backup .= "--\r\n-- Datenbank: `".$db['db']."`\r\n--\n\n";
    $sql_backup .= "-- -------------------------------------------------------------------\r\n";
    foreach($backup_table_data as $table => $data) {
        $sql_backup .= "\r\n--\r\n-- Tabellenstruktur: `".$table."`\r\n--\r\n\r\n";
        $sql_backup .= $data['drop']."\r\n";
        $sql_backup .= $data['create']."\r\n";

        if(!empty($data['insert'])) {
            $sql_backup .= "\r\n--\r\n-- Datenstruktur: `".$table."`\r\n--\r\n\r\n";
            $sql_backup .= $data['insert']."\r\n";
        }
    }

    unset($data);
    return $sql_backup;
}