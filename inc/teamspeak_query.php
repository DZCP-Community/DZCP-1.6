<?php
/*
  TS2 Class  by iklas Hkansson <niklas.hk@telia.com>
  TS3 Class  by Sebastien Gerard <sebeuu@gmail.com>

  modified by CodeKing for DZCP 08-01-2010 (mm-dd-yyyy)
*/

######################################
### TS 3 Viewer###
######################################

function teamspeak3()
{
    $tsstatus = new TSStatus(settings('ts_ip'), settings('ts_port'), settings('ts_sport'), settings('ts_customicon'), settings('ts_showchannel'));
    return show("menu/teamspeak", array("hostname" => '', "channels" => $tsstatus->render()));
}

/**
 * @property array _serverGroup
 * @property array _channelGroup
 * @property string serverError
 */
class TSStatus
{
    var $_host;
    var $_qport;
    var $_port;
    var $_sid;
    var $_socket;
    var $_updated;
    var $_serverDatas;
    var $_channelDatas;
    var $_joinchannel;
    var $_userDatas;
    var $_serverGroupFlags;
    var $_channelGroupFlags;

    var $error;
    var $decodeUTF8;
    var $_showCountry;
    var $_showIcons;
    var $_showOnly;

    function __construct($host, $port, $queryPort, $customicon, $showchannel)
    {
        $this->_host = $host;
        $this->_port = $port;
        $this->_qport = $queryPort;
        $this->_sid = 1;

        $this->_socket = null;
        $this->_updated = false;
        $this->_serverDatas = array();
        $this->_channelDatas = array();
        $this->_joinchannel = array();
        $this->_userDatas = array();
        $this->_serverGroup = array();
        $this->_channelGroup = array();
        $this->_serverGroupFlags = array();
        $this->_channelGroupFlags = array();

        $this->error = '';
        $this->serverError = '';
        $this->decodeUTF8 = false;
        $this->_showCountry = true; //true = Country show || false = Country dont show
        $this->_showIcons = $customicon;
        $this->_showOnly = $showchannel;
    }

    function update()
    {
        $response = $this->queryServer();
        if ($response !== false && empty($this->error)) {
            $lines = explode("\n\rerror id=0 msg=ok\n\r", $response);
            if (count($lines) == 6) {
                $this->_serverDatas = $this->parseLine($lines[0]);
                $this->_serverDatas = $this->_serverDatas[0];
                $this->_channelDatas = $this->parseLine($lines[1]);
                $this->_userDatas = $this->parseLine($lines[2]);
                $this->_serverGroup = $this->parseLine($lines[3]);
                $this->_channelGroup = $this->parseLine($lines[4]);
                usort($this->_userDatas, array($this, "sortUsers"));

                $this->_updated = true;
            } else $this->error = rep2($response);
        }
    }

    function sendCommand($fp, $cmd)
    {
        if (empty($this->error)) {
            @fputs($fp, "$cmd\n");
            $response = "";
            while (strpos($response, 'msg=') === false) {
                $response .= @fread($fp, 8096);
            }
        }
        if (!empty($response) && !strstr($response, 'error id=0')) {
            $this->error = strtr(rep2($response), array(' msg=' => '<br />msg=', ' extra_msg=' => '<br />extra_msg='));
        }

        return $response;
    }

    function tsvars($str)
    {
        $str = explode("\n", $str);
        $vars = array();
        for ($i = 0; $i < sizeof($str); $i++) $vars[trim(array_shift(explode('=', $str[$i], 2)))] = trim(array_pop(explode('=', $str[$i], 2)));
        return $vars;
    }

    function queryServer()
    {
        @set_time_limit(10);
        $fp = @fsockopen($this->_host, $this->_qport, $errno, $errstr, 2);
        $this->_socket = $fp;
        @stream_set_timeout($fp, 2, 0);
        @stream_set_blocking($fp, true);
        if ($fp) {

            $response = $this->sendCommand($fp, "use port=" . $this->_port);

            if (strstr($response, 'error id=0 msg=ok')) {
                $response = "error id=0 msg=ok";
                $response .= $this->sendCommand($fp, "serverinfo");
                $response .= $this->sendCommand($fp, "channellist -topic -flags -voice -limits -icon");
                $response .= $this->sendCommand($fp, "clientlist -uid -times -away -voice -groups -info -icon -country");
                $response .= $this->sendCommand($fp, "servergrouplist");
                $response .= $this->sendCommand($fp, "channelgrouplist");
            }

            if ($this->decodeUTF8) $response = utf8_decode($response);

            return $response;
        } else {
            $this->error = '<br /><div style="text-align:center;">' . _error_no_teamspeak . '</div><br />';
        }
        return false;
    }

    function unescape($str)
    {
        $find = array('\\\\', "\/", "\s", "\p", "\a", "\b", "\f", "\n", "\r", "\t", "\v");
        $rplc = array(chr(92), chr(47), chr(32), chr(124), chr(7), chr(8), chr(12), chr(10), chr(3), chr(9), chr(11));

        return str_replace($find, $rplc, $str);
    }

    function parseLine($rawLine)
    {
        $datas = array();
        $rawItems = explode("|", $rawLine);
        foreach ($rawItems as $rawItem) {
            $rawDatas = explode(" ", $rawItem);
            $tempDatas = array();
            foreach ($rawDatas as $rawData) {
                $ar = explode("=", $rawData, 2);
                $tempDatas[$ar[0]] = isset($ar[1]) ? $this->unescape($ar[1]) : "";
            }
            $datas[] = $tempDatas;
        }
        return $datas;
    }

    function sortUsers($a, $b)
    {
        return strcasecmp($a["client_nickname"], $b["client_nickname"]);
    }

    function renderFlags($channel)
    {
        $flags = array();
        if (array_key_exists('channel_flag_default', $channel) && $channel["channel_flag_default"] == 1) $flags[] = '16x16_default.png';
        if (array_key_exists('channel_needed_talk_power', $channel) && $channel["channel_needed_talk_power"] > 0) $flags[] = '16x16_moderated.png';
        if (array_key_exists('channel_flag_password', $channel) && $channel["channel_flag_password"] == 1) $flags[] = '16x16_register.png';
        $out = "";
        foreach ($flags as $flag) $out .= '<img src="../inc/images/tsicons/' . $flag . '" alt="" class="icon" />';
        return $out;
    }

    function user_groups($user)
    {
        $server = array();
        $server = explode(",", $user['client_servergroups']);
        $channel = array();
        $channel = explode(",", $user['client_channel_group_id']);
        $out = "";
        foreach ($this->_channelGroup as $cgroup) {
            if (in_array($cgroup['cgid'], $channel)) {
                $out .= $this->icon($cgroup['iconid'], $cgroup['name']);
            }
        }
        foreach ($this->_serverGroup as $sgroup) {
            if (in_array($sgroup['sgid'], $server)) {
                $out .= $this->icon($sgroup['iconid'], $sgroup['name']);
            }
        }
        $out .= $this->icon($user['client_icon_id']);
        if ($this->_showCountry) {
            if (!file_exists($country = "../inc/images/flaggen/" . strtolower($user['client_country']) . ".gif")) {
                $country = "../inc/images/flaggen/nocountry.gif";
            }
            $out .= "<img src=\"" . $country . "\" alt=\"\" class=\"tsicon\" />";
        }
        return $out;
    }

    function icon($id, $title = "")
    {
        if ($id != 0) {
            if ($id < 0) $id = $id + 4294967296;
            if ($id == "100" || $id == "200") {
                $pfad = "../inc/images/tsicons/changroup_" . $id . ".png";
            } elseif ($id == "300" || $id == "500" || $id == "600") {
                $pfad = "../inc/images/tsicons/servergroup_" . $id . ".png";
            } elseif ($this->_showIcons) {
                $pfad = "../inc/images/tsicons/server/" . $id . ".png";
            }
            if (!file_exists($pfad) && $this->_showIcons) {
                $dl = $this->parseLine($this->sendCommand($this->_socket, "ftinitdownload clientftfid=" . rand(1, 99) . " name=\/icon_" . $id . " cid=0 cpw= seekpos=0"));
                $ft = @fsockopen($this->_host, $dl[0]['port'], $errnum, $errstr, 2);
                if ($ft) {
                    fputs($ft, $dl[0]['ftkey']);
                    $img = '';
                    while (!feof($ft)) {
                        $img .= fgets($ft, 4096);
                    }
                    $file = fopen($pfad, "w");
                    fwrite($file, $img);
                    fclose($file);
                }
            }
            $title = empty($title) ? "" : " title=\"" . $title . "\"";
            return empty($pfad) ? "" : "<img src=\"" . $pfad . "\" alt=\"\" class=\"tsicon\"" . $title . " />";
        }
    }

    function renderUsers($parentId, $i, $tpl)
    {
        $out = "";
        foreach ($this->_userDatas as $user) {
            if ($user["client_type"] == 0 && $user["cid"] == $parentId) {
                $icon = "16x16_player_off.png";
                if ($user["client_away"] == 1) $icon = "16x16_away.png";
                else if ($user["client_flag_talking"] == 1) $icon = "16x16_player_on.png";
                else if ($user["client_output_hardware"] == 0) $icon = "16x16_hardware_output_muted.png";
                else if ($user["client_output_muted"] == 1) $icon = "16x16_output_muted.png";
                else if ($user["client_input_hardware"] == 0) $icon = "16x16_hardware_input_muted.png";
                else if ($user["client_input_muted"] == 1) $icon = "16x16_input_muted.png";
                $left = $i * 20 + 12;
                $out .= "<div style=\"text-indent:" . $left . "px;float:left; width:80%;\"><img src=\"../inc/images/tsicons/trenner.gif\" alt=\"\" class=\"tsicon\" /><img src=\"../inc/images/tsicons/" . $icon . "\" alt=\"\" class=\"tsicon\" />" . rep2($user["client_nickname"]) . "</div>\n";
                $out .= "<div style=\"float:right; width:20%; text-align:right;\">" . $this->user_groups($user) . "</div>\n";
                $out .= "<div style=\"clear:both;\"></div>\n";
            }
        }
        return $out;
    }

    function getChannelInfos($cid, $full = false)
    {
        foreach ($this->_channelDatas as $channel) {
            if ($channel['cid'] == $cid) return ($full) ? $channel : $channel['channel_name'];
        }
    }

    function channel_icon($channel)
    {
        $icon = "16x16_channel_green.png";
        if ($channel["channel_maxclients"] > -1 && ($channel["total_clients"] >= $channel["channel_maxclients"])) $icon = "16x16_channel_red.png";
        else if ($channel["channel_maxfamilyclients"] > -1 && ($channel["total_clients_family"] >= $channel["channel_maxfamilyclients"])) $icon = "16x16_channel_red.png";
        else if ($channel["channel_flag_password"] == 1) $icon = "16x16_channel_yellow.png";
        return "../inc/images/tsicons/" . $icon;
    }

    function channel_name($channel, $tpl = false, $joints)
    {
        return '<a href="' . ($tpl ? '?cID=' . $channel['cid'] . '&amp;cName=' . rawurlencode($joints) : 'javascript:DZCP.popup(\'../teamspeak/login.php?ts3&amp;cName=' . rawurlencode($joints) . '\', \'600\', \'100\')') . '"
        class="navTeamspeak" style="font-weight:bold;white-space:nowrap" title="' . rep2($channel['channel_name']) . '">' . rep2($channel['channel_name']) . '</a>' . "\n";
    }

    function sub_channel($channels, $channel, $i, $tpl, $joints)
    {
        $out = "";
        $join_ts = "";
        foreach ($channels as $sub_channel) {
            if ($channel == $sub_channel['pid']) {
                if (($this->_showOnly && (($sub_channel['total_clients_family'] > 0 && $sub_channel['channel_flag_default'] == 0) || ($sub_channel['total_clients_family'] > 1 && $sub_channel['channel_flag_default']))) || !$this->_showOnly) {
                    $users = $this->renderUsers($sub_channel['cid'], $i + 1, $tpl);
                    $subs = $this->sub_channel($channels, $sub_channel['cid'], $i + 1, $tpl, $join_ts);
                    if ($tpl) {
                        $box = "";
                    } else {
                        $box = "box_";
                    }
                    if (!empty($users) || !empty($subs)) {
                        $moreshow = "<img id=\"img_" . $box . "cid" . $sub_channel['cid'] . "\" src=\"../inc/images/toggle_normal.png\" alt=\"\" class=\"tsicons\" onclick=\"DZCP.fadetoggle('" . $box . "cid" . $sub_channel['cid'] . "')\" />";
                        $style = "0";
                        $div_first = "<div id=\"more_" . $box . "cid" . $sub_channel['cid'] . "\">\n";
                        $div_sec = "</div>";
                    } else {
                        $moreshow = "";
                        $style = "12";
                        $div_first = "";
                        $div_sec = "";
                    }
                    $left = ($i * 20) + $style;
                    $join_ts = $joints . "/" . $sub_channel['channel_name'];
                    $out .= "<div class=\"tstree_left\" style=\"text-indent:" . $left . "px;\">" . $moreshow . "<img src=\"../inc/images/tsicons/trenner.gif\" alt=\"\" class=\"tsicon\" />
                <img src=\"" . $this->channel_icon($sub_channel) . "\" alt=\"\" class=\"tsicon\" />" . $this->channel_name($sub_channel, $tpl, $join_ts) . "</div>\n";
                    $out .= "<div class=\"tstree_right\">" . $this->renderFlags($sub_channel) . $this->icon($sub_channel['channel_icon_id']) . "</div>\n";
                    $out .= "<div class=\"tstree_clear\"></div>\n";
                    $out .= $div_first;
                    $out .= $users;
                    $out .= $subs;
                    $out .= $div_sec;
                }
            }
        }
        return $out;
    }

    function render($tpl = false)
    {
        if (!$this->_updated) $this->update();
        if ($this->error == '') {
            $channels = $this->_channelDatas;
            $style = " style=\"text-indent:12px;\"";
            $out = "<div class=\"tstree_left\"" . $style . "><img src=\"../inc/images/tsicons/16x16_server_green.png\" alt=\"\" class=\"tsicon\" /> <span class=\"fontBold\">" . $this->_serverDatas["virtualserver_name"] . "</span></div>\n";
            $out .= "<div class=\"tstree_right\">" . $this->icon($this->_serverDatas["virtualserver_icon_id"]) . "</div>\n";
            $out .= "<div class=\"tstree_clear\"></div>\n";
            foreach ($channels as $channel) {
                if ($channel['pid'] == 0) {
                    if (($this->_showOnly && (($channel['total_clients_family'] > 0 && $channel['channel_flag_default'] == 0) || ($channel['total_clients_family'] > 1 && $channel['channel_flag_default']))) || !$this->_showOnly) {
                        $users = $this->renderUsers($channel['cid'], 0, $tpl);
                        $subs = $this->sub_channel($channels, $channel['cid'], 0, $tpl, $channel['channel_name']);
                        if ($tpl) {
                            $box = "";
                        } else {
                            $box = "box_";
                        }
                        if (!empty($users) || !empty($subs)) {
                            $moreshow = "<img id=\"img_" . $box . "cid" . $channel['cid'] . "\" src=\"../inc/images/toggle_normal.png\" alt=\"\" class=\"tsicons\" onclick=\"DZCP.fadetoggle('" . $box . "cid" . $channel['cid'] . "')\" />";
                            $style = "";
                            $div_first = "<div id=\"more_" . $box . "cid" . $channel['cid'] . "\">\n";
                            $div_sec = "</div>";
                        } else {
                            $moreshow = "";
                            $style = " style=\"text-indent:12px;\"";
                            $div_first = "";
                            $div_sec = "";
                        }
                        if (preg_match("/\[(.*?)spacer(.*?)\]/", $channel['channel_name'])) {
                            $out .= "<div class=\"tstree_left\"" . $style . ">" . $moreshow . "" . $this->channel_name($channel, $tpl, rep2($channel['channel_name'])) . "</div>\n";
                        } else {
                            $out .= "<div class=\"tstree_left\"" . $style . ">" . $moreshow . "<img src=\"" . $this->channel_icon($channel) . "\" alt=\"\" class=\"tsicon\" />" . $this->channel_name($channel, $tpl, $channel['channel_name']) . "</div>\n";
                        }
                        $out .= "<div class=\"tstree_right\">" . $this->renderFlags($channel) . $this->icon($channel['channel_icon_id']) . "</div>\n";
                        $out .= "<div class=\"tstree_clear\"></div>\n";
                        $out .= $div_first;
                        $out .= $users;
                        $out .= $subs;
                        $out .= $div_sec;
                    }
                }
            }
            return $out;
        } else return $this->error;
    }

    function welcome($cid, $cname = "")
    {
        $out = "";
        if (!$this->_updated) $this->update();

        if ($this->error == "") {
            if (empty($cid)) {
                $out = "<tr><td class=\"contentMainFirst\"><span class=\"fontBold\">Server:</span></td></tr>\n";
                $out .= "<tr><td class=\"contentMainFirst\">" . $this->_serverDatas['virtualserver_name'] . "<br /><br /></td></tr>\n";
                $out .= "<tr><td class=\"contentMainFirst\"><span class=\"fontBold\">Server IP:</span></td></tr>\n";
                $out .= "<tr><td class=\"contentMainFirst\">" . settings('ts_ip') . ":" . settings('ts_port') . "<br /><br /></td></tr>\n";
                $out .= "<tr><td class=\"contentMainFirst\"><span class=\"fontBold\">Version:</span></td></tr>\n";
                $out .= "<tr><td class=\"contentMainFirst\">" . $this->_serverDatas['virtualserver_version'] . "<br /><br /></td></tr>\n";
                $out .= "<tr><td class=\"contentMainFirst\"><span class=\"fontBold\">Welcome Message:</span></td></tr>\n";
                $out .= "<tr><td class=\"contentMainFirst\">" . rep2($this->_serverDatas['virtualserver_welcomemessage']) . "<br /><br /></td></tr>";
            } else {
                $channel = $this->getChannelInfos($cid, true);
                $out = "<tr><td><span class=\"fontBold\">Channel:</span></td></tr>\n";
                $out .= "<tr><td>" . rep2($channel['channel_name']) . "<br /><br /></td></tr>\n";
                $out .= "<tr><td><span class=\"fontBold\">Topic:</span></td></tr>\n";
                $out .= "<tr><td>" . (empty($channel['channel_topic']) ? '-' : rep2($channel['channel_topic'])) . "<br /><br /></td></tr>\n";
                $out .= "<tr><td><span class=\"fontBold\">User in channel:</span></td></tr>\n";
                $out .= "<tr><td>" . ($channel['channel_flag_default'] == 1 ? $channel['total_clients'] - 1 : $channel['total_clients']) . ($channel['channel_maxclients'] == -1 ? '' : '/' . $channel['channel_maxclients']) . "<br /><br /></td></tr>\n";
                $out .= "<tr><td><input type=\"button\" id=\"submit\" onclick=\"DZCP.popup('login.php?ts3&amp;cName=" . $cname . "&amp;pw=" . $channel['channel_flag_password'] . "', '600', '150');\" value=\"Join Channel\" class=\"submit\" /></td></tr>\n";
            }
        } else return $this->error;

        return $out;
    }
}

function rep2($var)
{
    $var = secure_dzcp($var);
    $var = preg_replace("/\[(.*?)spacer(.*?)\]/", "", $var);
    return strtr($var, array(
        chr(194) => '',
        '\/' => '/',
        '\s' => ' ',
        '\p' => '|',
        'ö' => '',
        '<' => '&lt;',
        '>' => '&gt;',
        '[URL]' => '',
        '[/URL]' => ''
    ));
}

function secure_dzcp($replace)
{
    $replace = str_replace("\"", "&quot;", $replace);
    /* Only do the slow convert if there are 8-bit characters */
    /* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
    if (!preg_match("[\200-\237]", $replace) && !preg_match("[\241-\377]", $replace))
        return $replace;
    // decode three byte unicode characters
    $replace = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e", "'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'", $replace);
    // decode two byte unicode characters
    $replace = preg_replace("/([\300-\337])([\200-\277])/e", "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'", $replace);
    return $replace;
}