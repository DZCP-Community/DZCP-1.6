<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START ##
include("../inc/buffer.php");

## INCLUDES ##
include(basePath . "/inc/debugger.php");
include(basePath . "/inc/config.php");
include(basePath . "/inc/bbcode.php");

## SETTINGS ##
$where = _side_membermap;
$dir = "membermap";

## SECTIONS ##
if (!$chkMe)
    $index = error(_error_wrong_permissions, 1);
else {
    $level = settings('gmaps_who');
    if (!($level == 0 || $level == 1)) {
        $level = 0;
    }

    $mm_qry = db('SELECT u.`id`, u.`nick`, u.`city` FROM ' . $db['users'] .
        ' u WHERE u.`level` > ' . $level . ' ORDER BY u.id');

    $test = ['type'=>'FeatureCollection','features'=>[
        'type' => 'Feature',
        'geometry' => ['type'=>'Point','coordinates'=>[1332700, 7906300]],
        'properties' => ['Name'=>'Igor Tihonov','Country'=>'Sweden','City'=>'Gothenburg'],
    ]];
    $test = json_encode($test);
    //file_put_contents('test.json',$test);

    $mm_coords = '';
    $mm_infos = "'<tr>";
    $mm_markerIcon = '';
    $mm_lastCoord = '';
    $i = 0;
    $mm_users = '';
    $realCount = 0;
    $markerCount = 0;
    $userListPic = '';
    $userListName = '';
    $userListRank = '';
    $userListCity = '';
    $entrys = _rows($mm_qry);

    $test = [
        0=>["lat"=>51.2213125,"lng"=>6.9073473,"text"=>"<b>Thomas Heiles<br>Stra&szlig;e 123<br>54290 Trier</b><p>","img"=>["src"=>"dberror.png","width"=>"180","height"=>"113"]],
        1=>["lat"=>50.2213125,"lng"=>6.7073473,"text"=>"<b>Thomas Heiles<br>Stra&szlig;e 123<br>54290 Trier</b><p>","img"=>["src"=>"dberror.png","width"=>"180","height"=>"113"]],
        2=>["lat"=>49.1000000,"lng"=>7.0000000,"text"=>"<b>Thomas Heiles<br>Stra&szlig;e 123<br>54290 Trier</b><p>","img"=>["src"=>"dberror.png","width"=>"180","height"=>"113"]]
    ];
    javascript::set('membermap',$test);

    /*
    while ($mm_get = _fetch($mm_qry)) {
        if ($mm_lastCoord != $mm_get['gmaps_koord']) {
            if ($i > 0) {
                $mm_coords .= ',';
                $mm_infos .= "</tr>','<tr>";
            }

            $mm_infos .= '<td><b style="font-size:13px">&nbsp;' . re($mm_get['city']) . '</td></tr><tr>';
            $mm_coords .= 'new google.maps.LatLng' . $mm_get['gmaps_koord'];
            $realCount++;
        } else {
            if ($markerCount > 0) {
                $mm_markerIcon .= ',';
            }

            $mm_markerIcon .= ($realCount - 1) . ':true';
            $markerCount++;
        }

        $userInfos = '<b>' . rawautor($mm_get['id']) . '</b><br /><b>' . _position .
            ':</b> ' . getrank($mm_get['id']) . '<br />' . userpic($mm_get['id']);
        $mm_infos .= '<td><div id="memberMapInner">' . $userInfos . '</div></td>';
        $mm_lastCoord = $mm_get['gmaps_koord'];
        $i++;
    }
    */

    $mm_qry = db('SELECT `id`, `nick`, `city` FROM `' . $db['users'] . '` WHERE `level` > ' . $level .
        ' AND `geolocation` != "" AND `dsgvo_lock` = 0 ORDER BY id LIMIT ' . ($page - 1) *
        config('m_membermap') . ',' . config('m_membermap').';');
    while ($mm_user_get = _fetch($mm_qry)) {
        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst";
        $color++;
        $mm_users .= show($dir . '/membermap_users', array('id' => $mm_user_get['id'],
            'userListPic' => userpic($mm_user_get['id'], 40, 50),
            'userListName' => autor($mm_user_get['id']),
            'userListRank' => getrank($mm_user_get['id']),
            'userListCity' => re($mm_user_get['city']),
            'class' => $class));
    }

    $mm_infos .= "</tr>'";
    $seiten = nav($entrys, config('m_membermap'));
    $index = show($dir . "/membermap", array('mm_coords' => $mm_coords,
        'mm_infos' => $mm_infos,
        'membermapusers' => $mm_users,
        'mm_markerIcon' => $mm_markerIcon,
        'nav' => $seiten));
}

## INDEX OUTPUT ##
$title = $pagetitle . " - " . $where . "";
page($index, $title, $where);