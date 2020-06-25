<?php
/**
 * Prüft online ob DZCP aktuell ist.
 *
 * @param bool $reload
 * @return array
 */
function show_dzcp_version(bool $reload=false) {
    global $api;
    $dzcp_version_info = 'onmouseover="DZCP.showInfo(\'<tr><td colspan=2 align=center padding=3 class=infoTop>DZCP Versions Checker</td></tr><tr><td>' . _dzcp_vcheck . '</td></tr>\')" onmouseout="DZCP.hideInfo()"';
    $return = array();
    if (dzcp_version_checker && api_enabled) {
        $json = $api->getDzcpVersion(true, 60 , $reload);
        if($reload) {
            header("Location: " . GetServerVars('HTTP_REFERER'));
        }

        if(strpos(GetServerVars('HTTP_REFERER'), '?') === false) {
            $href = '../admin/?version_reload=true';
        } else {
            $href = '?'.GetServerVars('QUERY_STRING').'&version_reload=true';
        }
        if (empty($json) || is_bool($json) || (!is_array($json) && !is_object($json))) {
            $return['version'] = '<b><a href="'.$href.'" [info]>' . _akt_version . ': <span style="color:#FFFF00">' . _version . '</span> / Release: <span style="color:#FFFF00">' . _release . '</span> / Build: <span style="color:#FFFF00">' . _build . '</span></a></b>';
            $return['version'] = show($return['version'], array('info' => $dzcp_version_info));
            $return['version_img'] = '<img src="../inc/images/admin/version.gif" align="absmiddle" width="111" height="14" />';
            return $return;
        }

        if ($json['error']) {
            $return['version'] = '<b><a href="'.$href.'" [info]>' . _akt_version . ': <span style="color:#7783ff">' . _version . '</span> / Release: <span style="color:#7783ff">' . _release . '</span> / Build: <span style="color:#7783ff">' . _build . '</span> / <span style="color:#FF0000">== API ERROR ==</span></a></b>';
            $return['version'] = show($return['version'], array('info' => $dzcp_version_info));
            $return['version_img'] = '<img src="../inc/images/admin/version.gif" align="absmiddle" width="111" height="14" />';
            return $return;
        }

        $_build = '<span style="color:#17D427">' . _build . '</span>';
        if ((int)(str_replace('.', '', $json['results']['build'])) > (int)(str_replace('.', '', _build)))
            $_build = '<span style="color:#FF0000">' . _build . '</span> => <span style="color:#17D427">' . $json['results']['build'] . '</span>';

        if ((int)(str_replace('.', '', $json['results']['version'])) > (int)(str_replace('.', '', _version))) {
            $return['version'] = '<a href="https://www.dzcp.de/" target="_blank" title="external Link: www.dzcp.de"><b>' . _akt_version . ':</b> <span style="color:#FF0000">' . _version . '</span> / Update Version: <span style="color:#17D427">' . $json['results']['version'] . '</span> / Release: <span style="color:#17D427">' . $json['results']['release'] . '</span> / Build: <span style="color:#17D427">' . $json['results']['build'] . '</span></a>';
            $return['version_img'] = '<img src="../inc/images/admin/version_old.gif" align="absmiddle" width="111" height="14" />';
        } else {
            $return['version'] = '<b><a href="'.$href.'" [info]>' . _akt_version . ': <span style="color:#17D427">' . _version . '</span> / Release: <span style="color:#17D427">' . _release . '</span> / Build: ' . $_build . '</b></a>';
            $return['version'] = show($return['version'], array('info' => $dzcp_version_info));
            $return['version_img'] = '<img src="../inc/images/admin/version.gif" align="absmiddle" width="111" height="14" />';
        }
    } else {
        //check disabled
        $return['version'] = '<b><span style="color:#999999">' . _akt_version . ': ' . _version . '</span> / Release: <span style="color:#999999">' . _release . '</span> / Build: <span style="color:#999999">' . _build . '</span></b>';
        $return['version_img'] = '<img src="../inc/images/admin/version.gif" align="absmiddle" width="111" height="14" />';
    }

    return $return;
}

//PHPInfo in array lesen
function parsePHPInfo()
{
    ob_start();
    phpinfo();
    $s = ob_get_contents();
    ob_end_clean();

    $s = strip_tags($s, '<h2><th><td>');
    $s = preg_replace('/<th[^>]*>([^<]+)<\/th>/', "<info>\\1</info>", $s);
    $s = preg_replace('/<td[^>]*>([^<]+)<\/td>/', "<info>\\1</info>", $s);
    $vTmp = preg_split('/(<h2[^>]*>[^<]+<\/h2>)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
    $vModules = array();
    for ($i = 1; $i < count($vTmp); $i++) {
        if (preg_match('/<h2[^>]*>([^<]+)<\/h2>/', $vTmp[$i], $vMat)) {
            $vName = trim($vMat[1]);
            $vTmp2 = explode("\n", $vTmp[$i + 1]);
            foreach ($vTmp2 AS $vOne) {
                $vPat = '<info>([^<]+)<\/info>';
                $vPat3 = "/$vPat\s*$vPat\s*$vPat/";
                $vPat2 = "/$vPat\s*$vPat/";

                if (preg_match($vPat3, $vOne, $vMat))
                    $vModules[$vName][trim($vMat[1])] = array(trim($vMat[2]), trim($vMat[3]));
                else if (preg_match($vPat2, $vOne, $vMat))
                    $vModules[$vName][trim($vMat[1])] = trim($vMat[2]);
            }
        }
    }

    return $vModules;
}

function php_sapi_type()
{
    $sapi_type = php_sapi_name();
    $sapi_types = array("apache" => 'Apache HTTP Server', "apache2filter" => 'Apache 2: Filter',
        "apache2handler" => 'Apache 2: Handler', "cgi" => 'CGI', "cgi-fcgi" => 'Fast-CGI', "cli" => 'CLI', "isapi" => 'ISAPI', "nsapi" => 'NSAPI');
    return (empty($sapi_types[substr($sapi_type, 0, 3)]) ? substr($sapi_type, 0, 3) : $sapi_types[substr($sapi_type, 0, 3)]);
}

function sql_backup()
{
    global $mysql, $db;
    $backup_table_data = array();

    //Table Drop
    $sqlqry = db('SHOW TABLE STATUS');
    while ($table = _fetch($sqlqry)) {
        $backup_table_data[$table['Name']]['drop'] = 'DROP TABLE IF EXISTS `' . $table['Name'] . '`;';
    }
    unset($table);

    //Table Create
    foreach ($backup_table_data as $table => $null) {
        unset($null);
        $sqlqry = db('SHOW CREATE TABLE ' . $table . ';');
        while ($table = _fetch($sqlqry)) {
            $backup_table_data[$table['Table']]['create'] = $table['Create Table'] . ';';
        }
    }
    unset($table);

    //Insert Create
    foreach ($backup_table_data as $table => $null) {
        unset($null);
        $backup = '';
        $sqlqry = db('SELECT * FROM ' . $table . ' ;');
        while ($dt = _fetch($sqlqry)) {
            if (!empty($dt)) {
                $backup_data = '';
                foreach ($dt as $key => $var) {
                    $backup_data .= "`" . $key . "` = '" . ((string)(str_replace("'", "`", $var))) . "',";
                }

                $backup .= "INSERT INTO `" . $table . "` SET " . substr($backup_data, 0, -1) . ";\r\n";
                unset($backup_data);
            }
        }

        $backup_table_data[$table]['insert'] = $backup;
        unset($backup);
    }
    unset($table);

    $sql_backup = "-- -------------------------------------------------------------------\r\n";
    $sql_backup .= "-- Datenbank Backup von deV!L`z Clanportal v." . _version . "\r\n";
    $sql_backup .= "-- Build: " . _release . " * " . _build . "\r\n";
    $sql_backup .= "-- Host: " . $db['host'] . "\r\n";
    $sql_backup .= "-- Erstellt am: " . date("d.m.Y") . " um " . date("H:i") . "\r\n";
    $sql_backup .= "-- MySQL-Version: " . mysqli_get_server_info($mysql) . "\r\n";
    $sql_backup .= "-- PHP Version: " . phpversion() . "\r\n";
    $sql_backup .= "-- -------------------------------------------------------------------\r\n\r\n";
    $sql_backup .= "--\r\n-- Datenbank: `" . $db['db'] . "`\r\n--\n\n";
    $sql_backup .= "-- -------------------------------------------------------------------\r\n";
    foreach ($backup_table_data as $table => $data) {
        $sql_backup .= "\r\n--\r\n-- Tabellenstruktur: `" . $table . "`\r\n--\r\n\r\n";
        $sql_backup .= $data['drop'] . "\r\n";
        $sql_backup .= $data['create'] . "\r\n";

        if (!empty($data['insert'])) {
            $sql_backup .= "\r\n--\r\n-- Datenstruktur: `" . $table . "`\r\n--\r\n\r\n";
            $sql_backup .= $data['insert'] . "\r\n";
        }
    }

    unset($data);
    return $sql_backup;
}
