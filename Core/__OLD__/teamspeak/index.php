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
include(basePath . "/teamspeak/helper.php");

## SETTINGS ##
$where = _site_teamspeak;
$title = $pagetitle . " - " . $where . "";
$dir = "teamspeak";

## SECTIONS ##
if (fsockopen_support()) {
    $CachedString = $cache->getItem('page_teamspeak_' . $_SESSION['language']);
    if (is_null($CachedString->get()) || isset($_GET['cID'])) {
        $tsstatus = new TSStatus(settings('ts_ip'), settings('ts_port'), settings('ts_sport'), settings('ts_customicon'), settings('ts_showchannel'));
        $tstree = $tsstatus->render(true);

        $users = 0;
        foreach ($tsstatus->_userDatas AS $user) {
            if ($user["client_type"] == 0) {
                $users++;
                $icon = "16x16_player_off.png";
                if ($user["client_away"] == 1) $icon = "16x16_away.png";
                else if ($user["client_flag_talking"] == 1) $icon = "16x16_player_on.png";
                else if ($user["client_output_hardware"] == 0) $icon = "16x16_hardware_output_muted.png";
                else if ($user["client_output_muted"] == 1) $icon = "16x16_output_muted.png";
                else if ($user["client_input_hardware"] == 0) $icon = "16x16_hardware_input_muted.png";
                else if ($user["client_input_muted"] == 1) $icon = "16x16_input_muted.png";

                $flags = array();
                if (isset($tsstatus->_channelGroupFlags[$user['client_channel_group_id']])) $flags[] = $tsstatus->_channelGroupFlags[$user['client_channel_group_id']];
                $serverGroups = explode(",", $user['client_servergroups']);
                foreach ($serverGroups as $serverGroup) if (isset($tsstatus->_serverGroupFlags[$serverGroup])) $flags[] = $tsstatus->_serverGroupFlags[$serverGroup];

                $p = '<img src="../inc/images/tsicons/' . $icon . '" alt="" class="tsicon" />' . rep2($user['client_nickname']) . '&nbsp;' . $tsstatus->renderFlags($flags);

                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst";
                $color++;
                $userstats .= show($dir . "/userstats", array("player" => $p,
                    "channel" => rep2($tsstatus->getChannelInfos($user['cid'])),
                    "misc1" => '',
                    "class" => $class,
                    "misc2" => '',
                    "misc3" => time_convert(time() - $user['client_lastconnected']),
                    "misc4" => time_convert($user['client_idle_time'], true)));
            }
        }

        $index = show($dir . "/teamspeak", array("name" => $tsstatus->_serverDatas['virtualserver_name'],
            "os" => $tsstatus->_serverDatas['virtualserver_platform'],
            "uptime" => time_convert($tsstatus->_serverDatas['virtualserver_uptime']),
            "user" => $users,
            "t_name" => _ts_name,
            "t_os" => _ts_os,
            "uchannels" => $tstree,
            "info" => bbcode(re($tsstatus->welcome((int)($_GET['cID']), $_GET['cName'])), false, false, true),
            "t_uptime" => _ts_uptime,
            "t_channels" => _ts_channels,
            "t_user" => _ts_user,
            "head" => _ts_head,
            "users_head" => _ts_users_head,
            "player" => _ts_player,
            "channel" => _ts_channel,
            "channel_head" => _ts_channel_head,
            "max" => $max,
            "channels" => $tsstatus->_serverDatas['virtualserver_channelsonline'],
            "logintime" => _ts_logintime,
            "idletime" => _ts_idletime,
            "channelstats" => $channelstats,
            "userstats" => $userstats));
        $CachedString->set($index)->expiresAfter(config('cache_teamspeak'));
        $cache->save($CachedString);
    } else {
        $CachedString->get();
    }
} else {
    $index = error(_fopen, 1);
}

## INDEX OUTPUT ##
page($index, $title, $where);