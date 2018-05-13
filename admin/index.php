<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START ##
include("../inc/buffer.php");

## INCLUDES ##
include(basePath."/inc/debugger.php");
include(basePath."/inc/config.php");
include(basePath."/inc/bbcode.php");
include(basePath."/admin/helper.php");

## SETTINGS ##
$where = _site_config;
$dir = "admin";
$rootmenu = null;
$settingsmenu = null;
$contentmenu = null;
$amenu = array();
$wysiwyg = false;
$use_glossar = false;
define('_Admin', true);

## SECTIONS ##
$check = db("SELECT s1.user FROM ".$db['permissions']." s1, ".$db['users']." s2
             WHERE s1.user = '".$userid."'
             AND s2.id = '".(int)($userid)."'
             AND s2.pwd = '".$_SESSION['pwd']."'");

if(!admin_perms($userid))
    $index = error(_error_wrong_permissions, 1);
else {
    if(isset($_GET['admin']) && file_exists(basePath.'/admin/menu/'.strtolower($_GET['admin']).'.php') &&
                                file_exists(basePath.'/admin/menu/'.strtolower($_GET['admin']).'.xml')) {
        $permission = false; define('_adminMenu', true);
        $xml = simplexml_load_file(basePath.'/admin/menu/'.strtolower($_GET['admin']).'.xml');
        $rights = (string)$xml->Rights; $oa = (int)$xml->Only_Admin; $ora = (int)$xml->Only_Root;
        if(permission($rights) && !$oa && !$ora) $permission = true;
        if($oa && !$ora && $chkMe == 4) $permission = true;
        if($ora && $chkMe == 4 && rootAdmin()) $permission = true;

        if($permission)
            include(basePath.'/admin/menu/'.strtolower($_GET['admin']).'.php');
        else
            $show = error(_error_wrong_permissions, 1);
    }

    //Site Permissions
    $files = get_files(basePath.'/admin/menu/',false,true,array('xml'));
    if(count($files)) {
        foreach($files AS $file_xml) {
            if(file_exists(basePath.'/admin/menu/'.str_replace('.xml','.php',$file_xml))) {
                $permission = false;
                $xml = simplexml_load_file(basePath.'/admin/menu/'.$file_xml);
                $rights = (string)$xml->Rights; $oa = (int)$xml->Only_Admin; $ora = (int)$xml->Only_Root;
                if(permission($rights) && !$oa && !$ora) $permission = true;
                if($oa && !$ora && $chkMe == 4) $permission = true;
                if($ora && $chkMe == 4 && rootAdmin()) $permission = true;

                foreach($picformat AS $end) {
                    if(file_exists(basePath.'/admin/menu/'.str_replace('.xml','',$file_xml).'.'.$end))
                        break;
                }

                $link = constant("_config_".str_replace('.xml','',$file_xml));
                $menu = (string)$xml->Menu; $type = str_replace('.xml','',$file_xml);
                if(!empty($menu) && !empty($rights) && $permission)
                    $amenu[$menu][$type] = show("['[link]','?admin=[name]','background-image:url(menu/[name].".$end.");'],\n", array("link" => $link, 'name' => $type));
            }
        }
    }

    foreach($amenu AS $m => $k) {
        natcasesort($k);
        foreach($k AS $l) $$m .= $l;
    }

    $radmin1 = ''; $radmin2 = '';
    if(empty($rootmenu)) {
        $radmin1 = '/*'; $radmin2 = '*/';
    }

    $adminc1 = ''; $adminc2 = '';
    if(empty($settingsmenu)) {
        $adminc1 = '/*'; $adminc2 = '*/';
    }

    $cdminc1 = ''; $cdminc2 = '';
    if(empty($contentmenu)) {
        $cdminc1 = '/*'; $cdminc2 = '*/';
    }

    $dzcp_news = '';
    if(allow_url_fopen_support()) {
        if(admin_view_dzcp_news) {
            $CachedString = $cache->getItem('admin_news');
            if(is_null($CachedString->get())) {
                if($dzcp_news = get_external_contents("http://www.dzcp.de/dzcp_news.php",false,true)) {
                    $CachedString->set(base64_encode($dzcp_news))->expiresAfter(1200);
                    $cache->save($CachedString);
                }
            } else
                $dzcp_news = base64_decode($CachedString->get());
        }
    }

    if(@file_exists(basePath."/_installer") && $chkMe == 4 && !view_error_reporting)
        $index = _installdir;
    else {
        $dzcp_version = show_dzcp_version();
        $index = show($dir."/admin", array("head" => _config_head,
                                           "version" => $dzcp_version['version'],
                                           "version_img" => $dzcp_version['version_img'],
                                           "dbase" => _stats_mysql,
                                           "einst" => _config_einst,
                                           "content" => _content,
                                           "newsticker" => '<div style="padding:3px">'.(empty($dzcp_news) ? '' : '<b>DZCP News:</b><br />').'<div id="dzcpticker">'.$dzcp_news.'</div></div>',
                                           "rootadmin" => _rootadmin,
                                           "rootmenu" => $rootmenu,
                                           "settingsmenu" => $settingsmenu,
                                           "contentmenu" => $contentmenu,
                                           "radmin1" => $radmin1,
                                           "radmin2" => $radmin2,
                                           "adminc1" => $adminc1,
                                           "adminc2" => $adminc2,
                                           "cdminc1" => $cdminc1,
                                           "cdminc2" => $cdminc2,
                                           "show" => $show));
    }
}

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where."";
page($index, $title, $where, $wysiwyg);