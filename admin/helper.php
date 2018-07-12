<?php
/**
 * Prüft online ob DZCP aktuell ist.
 *
 * @return array
 */
function show_dzcp_version() {
    global $api;
    $dzcp_version_info = 'onmouseover="DZCP.showInfo(\'<tr><td colspan=2 align=center padding=3 class=infoTop>DZCP Versions Checker</td></tr><tr><td>' . _dzcp_vcheck . '</td></tr>\')" onmouseout="DZCP.hideInfo()"';
    $return = array();
    if (dzcp_version_checker) {
        $json = $api->get_dzcp_version();
        if (empty($json) || is_bool($json) || (!is_array($json) && !is_object($json))) {
            $return['version'] = '<b><a href="" [info]>' . _akt_version . ': <span style="color:#FFFF00">' . _version . '</span> / Release: <span style="color:#FFFF00">' . _release . '</span> / Build: <span style="color:#FFFF00">' . _build . '</span></a></b>';
            $return['version'] = show($return['version'], array('info' => $dzcp_version_info));
            $return['version_img'] = '<img src="../inc/images/admin/version.gif" align="absmiddle" width="111" height="14" />';
            return $return;
        }

        if ($json['error']) {
            $return['version'] = '<b><a href="" [info]>' . _akt_version . ': <span style="color:#7783ff">' . _version . '</span> / Release: <span style="color:#7783ff">' . _release . '</span> / Build: <span style="color:#7783ff">' . _build . '</span> / <span style="color:#FF0000">== API ERROR ==</span></a></b>';
            $return['version'] = show($return['version'], array('info' => $dzcp_version_info));
            $return['version_img'] = '<img src="../inc/images/admin/version.gif" align="absmiddle" width="111" height="14" />';
            return $return;
        }

        $_build = '<span style="color:#17D427">' . _build . '</span>';
        if ((int)(str_replace('.', '', $json['build'])) > (int)(str_replace('.', '', _build)))
            $_build = '<span style="color:#FF0000">' . _build . '</span> => <span style="color:#17D427">' . $json['build'] . '</span>';

        if ((int)(str_replace('.', '', $json['version'])) > (int)(str_replace('.', '', _version))) {
            $return['version'] = '<a href="https://www.dzcp.de/" target="_blank" title="external Link: www.dzcp.de"><b>' . _akt_version . ':</b> <span style="color:#FF0000">' . _version . '</span> / Update Version: <span style="color:#17D427">' . $json['version'] . '</span> / Release: <span style="color:#17D427">' . $json['release'] . '</span> / Build: <span style="color:#17D427">' . $json['build'] . '</span></a>';
            $return['version_img'] = '<img src="../inc/images/admin/version_old.gif" align="absmiddle" width="111" height="14" />';
        } else {
            $return['version'] = '<b><a href="" [info]>' . _akt_version . ': <span style="color:#17D427">' . _version . '</span> / Release: <span style="color:#17D427">' . _release . '</span> / Build: ' . $_build . '</b></a>';
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