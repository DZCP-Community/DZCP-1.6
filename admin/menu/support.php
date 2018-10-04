<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if (_adminMenu != 'true') exit;

$PhpInfo = parsePHPInfo();
$support = "#####################\r\n";
$support .= "Support Informationen\r\n";
$support .= "#####################\r\n";
$support .= "\r\n";

$support .= "#####################\r\n";
$support .= "DZCP Allgemein \r\n";
$support .= "#####################\r\n";
$support .= "DZCP Version: " . _version . "\r\n";
$support .= "DZCP Release: " . _release . "\r\n";
$support .= "DZCP Build: " . _build . "\r\n";
$support .= "DZCP Template: " . $tmpdir . "\r\n";
$support .= "DZCP API-Version: " . $api->getApiVersion() . "\r\n";
$support .= "Domain: " . str_replace('/admin', '', GetServerVars('HTTP_HOST')) . "\r\n";
$support .= "\r\n";

$support .= "#####################\r\n";
$support .= "Server Versionen\r\n";
$support .= "#####################\r\n";
$support .= "Server OS: " . @php_uname() . "\r\n";
$support .= "Webserver: " . (array_key_exists('apache2handler', $PhpInfo) ? (array_key_exists('Apache Version', $PhpInfo['apache2handler']) ? $PhpInfo['apache2handler']['Apache Version'] : 'PHP l&auml;uft als CGI <Keine Info>') : 'PHP l&auml;uft als CGI <Keine Info>') . "\r\n";
$support .= "PHP-Version: " . phpversion() . " (" . php_sapi_type() . ")" . "\r\n";
$support .= "MySQL-Server Version: " . mysqli_get_server_info($mysql) . "\r\n";
$support .= "MySQLi-Persistente Datenbankverbindung: " . (mysqli_persistconns ? 'On' : 'Off') . "\r\n";
$support .= "\r\n";

$support .= "#####################\r\n";
$support .= "Server Cache\r\n";
$support .= "#####################\r\n";
$support .= "PhpFastCache Version: " . Phpfastcache\Api::getVersion() . "\r\n";
$support .= "Cache Storage: " . str_replace('\\Phpfastcache\\Drivers\\', '', $cache->getDriverName()) . "\r\n";
$support .= "Cache Fallback Storage: " . $cache->getConfig()['fallback'] . "\r\n";
$support .= "Cache Fallback Enabled: " . (\Phpfastcache\CacheManager::$fallback ? 'On' : 'Off') . "\r\n";
$support .= "\r\n";

$support .= "#####################\r\n";
$support .= "Socket-Verbindungen \r\n";
$support .= "#####################\r\n";
$support .= "PHP fsockopen: " . (fsockopen_support() ? 'On' : 'Off') . "\r\n";
$support .= "PHP Sockets: " . (function_exists("socket_create") && $PhpInfo['sockets']['Sockets Support'] == "enabled" ? 'On' : 'Off') . "\r\n";
$support .= "\r\n";

$support .= "#####################\r\n";
$support .= "Servereinstellungen\r\n";
$support .= "#####################\r\n";
$support .= "open_basedir: " . $PhpInfo['Core']['open_basedir'][0] . "\r\n";
$support .= "PHP-Memory Limit: " . $PhpInfo['Core']['memory_limit'][0] . "\r\n";
$support .= "imagettftext(): " . (function_exists('imagettftext') == true ? 'existiert' : 'existiert nicht') . "\r\n";
$support .= "file_uploads: " . $PhpInfo['Core']['file_uploads'][0] . "\r\n";
$support .= "upload_max_filesize: " . $PhpInfo['Core']['upload_max_filesize'][0] . "\r\n";
$support .= "sendmail_from: " . $PhpInfo['Core']['sendmail_from'][0] . "\r\n";
$support .= "sendmail_path: " . $PhpInfo['Core']['sendmail_path'][0] . "\r\n";
$support .= "\r\n";

$support .= "#####################\r\n";
$support .= "Debug\r\n";
$support .= "#####################\r\n";
$support .= "SQL-Error Datei: " . (file_exists(basePath . "/inc/_logs/sql_error_log.log") ? 'vorhanden' : 'keine') . "\r\n";
$support .= "\r\n";

$show = show($dir . "/support", array("info" => _admin_support_info, "head" => _admin_support_head, "support" => $support));