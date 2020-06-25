<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if (_adminMenu != 'true') exit;

$where = $where.': '._site_addoncheck;

//load all xml files for addons
$addons_installed = array(); $addons_not_installed = array();
$addons_xml = get_files(basePath . "/inc/_versions_"); $addons_local = array();
foreach ($addons_xml as $addon_xml) {
    $array = json_decode(json_encode(simplexml_load_file(basePath . '/inc/_versions_/' . $addon_xml)),true);
    if(!$array['Version'] || !$array['AID'] ||   empty($array['AID'])) {
        $addons_not_installed[] = $array;
        continue;
    }

    $addons_installed[] = $array;
} unset($array,$addon_xml);

if(api_enabled) {
    $api_data = $api->getAddonVersions($addons_installed, true, 600);
    $addons_installed['error'] = true;
    if(!empty($api_data) && !$api_data['error']) {
        $addons_installed = $api_data['results'];
        $addons_installed['error'] = false;
        $addons_installed['error_msg'] = $api_data['status'];
    }
} else {
    $addons_installed['data'] = [];
    $addons_installed['error'] = true;
    $addons_installed['error_msg'] = 'inc/config.php => "api_enabled" is false';
}

$addons = array_merge($addons_installed,$addons_not_installed);
$addons['error'] = $addons_installed['error'];
$addons['error_msg'] = $addons_installed['error_msg'];
unset($addons_installed,$addons_not_installed);

$show_not_installed = $show_installed = '';
if(count($addons_xml)) {
    foreach ($addons as $addon) {
        if (!is_array($addon) || !array_key_exists('AID', $addon))
            continue;

        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst";
        if (!$addon['Version']) {
            $show_not_installed .= show($dir . '/addon_check_show', [
                'class' => $class,
                'name' => $addon['Name'],
                'autor' => $addon['Autor'],
                'version' => '<span class="fontBold" style="color:#999999">' . _addoncheck_notinstalled . '</span>',
                'url' => $addon['Link']['URL'],
                'title' => $addon['Link']['Title']]);
        } else {
            if (array_key_exists('Server', $addon) && array_key_exists('Version', $addon['Server']) && !array_key_exists('error', $addon['Server'])) {
                if (!$addons['error'] && array_key_exists('Server', $addon) && $addon['Server']['Version']) {
                    $version = '<span class="fontBold">' . _addoncheck_yourversion . ':</span> <span style="color:#17D427">' . $addon['Version'] . '</span><br />' .
                        '<span class="fontBold" style="color:#17D427">' . _addoncheck_VersionOK . '</span>';
                    if (api::versionCompare($addon['Version'], '<', $addon['Server']['Version'])) {
                        $version = '<span class="fontBold">' . _addoncheck_yourversion . ':</span> <span class="fontBold" style="color:#FF0000">' . $addon['Version'] . '</span><br />' .
                            '<span class="fontBold" style="color:#FF0000">' . _addoncheck_currVersion . ':</span> <span class="fontBold" style="color:#FF0000">' . $addon['Server']['Version'] . '</span>';
                    }
                } else if ($addons['error']) {
                    $version = '<span class="fontBold" style="color:#7783ff">' . _addoncheck_checkDisabled . '</span><br /><span class="fontBold">' . _addoncheck_yourversion . ': </span>' . $addon['Version'];
                } else {
                    $version = '<span class="fontBold" style="color:#999999">' . _addoncheck_checkDisabled . '</span><br />' .
                        '<span class="fontBold">' . _addoncheck_yourversion . ': </span><span style="color:#17D427" class="fontBold">' . $addon['Version'] . '</span>';
                }

                $show_installed .= show($dir . '/addon_check_show', [
                    'class' => $class,
                    'name' => $addon['Name'],
                    'autor' => $addon['Autor'],
                    'version' => $version,
                    'url' => (api::versionCompare($addon['Version'], '<', $addon['Server']['Version']) ?
                        $addon['Server']['URL'] : $addon['Link']['URL']),
                    'title' => (api::versionCompare($addon['Version'], '<', $addon['Server']['Version']) ?
                        $addon['Server']['Title'] : $addon['Link']['Title'])]);
            } else {
                $msg = _addoncheck_checkDisabled;
                if (array_key_exists('error', $addon['Server'])) {
                    $msg = $addon['Server']['msg'] != 'no_id' ? $msg :
                        show(_addoncheck_id_error, ['id' => $addon['AID']]);
                }

                $show_installed .= show($dir . '/addon_check_show', [
                    'class' => $class,
                    'name' => $addon['Name'],
                    'autor' => $addon['Autor'],
                    'version' => '<span class="fontBold" style="color:#999999">'
                        . $msg . '</span>',
                    'url' => $addon['Link']['URL'],
                    'title' => $addon['Link']['Title']]);
            }
        }

        $color++;
    }
} unset($addons_xml);

if(empty($show_not_installed))
    $show_not_installed = '<tr><td class="contentMainSecond" colspan="4" style="text-align: center;"><span class="fontBold">'._no_entry.'</span></td></tr>';

if(empty($show_installed))
    $show_installed = '<tr><td class="contentMainSecond" colspan="4" style="text-align: center;"><span class="fontBold">'._no_entry.'</span></td></tr>';

$show = show($dir . '/addon_check', [
    'show_installed' => $show_installed,
    'show_not_installed' => $show_not_installed,
    'lock_show_installed_0' => empty($show_installed) ? '<!--' : '',
    'lock_show_installed_1' => empty($show_installed) ? '-->' : '',
    'lock_show_not_installed_0' => empty($show_not_installed) ? '<!--' : '',
    'lock_show_not_installed_1' => empty($show_not_installed) ? '-->' : '',
    'show_cmf_table_0' => !empty($show_installed) && !empty($show_not_installed) ? '' : '<!--',
    'show_cmf_table_1' => !empty($show_installed) && !empty($show_not_installed) ? '' : '-->'
]);

if($addons['error']) {
    DebugConsole::insert_warning('index::admin::addoncheck',$addons['error_msg']);
}