<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if (_adminMenu != 'true') exit;
$where = $where . ': ' . _config_title_datenschutz;

switch ($do) {
    case 'save':
        $qry = db("SELECT * FROM `" . $db['dsgvo'] . "` WHERE `for_dsgvo` = 1;");
        while ($get = _fetch($qry)) {
            if (isset($_POST['fid0_' . $get['id']])) {
                if ($_POST['fid0_' . $get['id']] && !$get['show']) {
                    db("UPDATE `" . $db['dsgvo'] . "` SET `show` = 1 WHERE `id` = " . $get['id'] . ";");
                } else if (!$_POST['fid0_' . $get['id']] && $get['show']) {
                    db("UPDATE `" . $db['dsgvo'] . "` SET `show` = 0 WHERE `id` = " . $get['id'] . ";");
                }
            }
        }

        $qry = db("SELECT * FROM `" . $db['dsgvo'] . "` WHERE `for_dsgvo_ak` = 1;");
        while ($get = _fetch($qry)) {
            if (isset($_POST['fid1_' . $get['id']])) {
                if ($_POST['fid1_' . $get['id']] && !$get['lock_show']) {
                    db("UPDATE `" . $db['dsgvo'] . "` SET `lock_show` = 1 WHERE `id` = " . $get['id'] . ";");
                } else if (!$_POST['fid1_' . $get['id']] && $get['lock_show']) {
                    db("UPDATE `" . $db['dsgvo'] . "` SET `lock_show` = 0 WHERE `id` = " . $get['id'] . ";");
                }
            }
        }
        $show = info(_config_set, "?admin=datenschutz", 4);
        break;
    case 'edit':
        if ($_POST) {
            db("UPDATE `" . $db['dsgvo_pers'] . "` SET " .
                "`organisation` = '" . up($_POST['organisation']) . "', " .
                "`titel` = '" . up($_POST['titel']) . "', " .
                "`first_name` = '" . up($_POST['first_name']) . "', " .
                "`last_name` = '" . up($_POST['last_name']) . "', " .
                "`address` = '" . up($_POST['address']) . "', " .
                "`zip_code` = " . ((int)$_POST['zip_code']) . ", " .
                "`place` = '" . up($_POST['place']) . "', " .
                "`country` = '" . up($_POST['country']) . "', " .
                "`e-mail` = '" . up($_POST['e-mail']) . "', " .
                "`phone` = '" . up($_POST['phone']) . "', " .
                "`website` = '" . up(links(re($_POST['website'], true))) . "' " .
                "WHERE `id` = " . (int)$_GET['id'] . ";");
            $show = info(_config_set, "?admin=datenschutz", 4);
        } else {
            $get = db("SELECT * FROM `" . $db['dsgvo_pers'] . "` WHERE `id` = " . (int)$_GET['id'] . ";", false, true);
            $head = ($get['id'] == 1 ? _datenschutz_rolle_1 : _datenschutz_rolle_2);
            $show = show($dir . "/datenschutz_edit_users", array('head' => $head,
                'organisation' => re($get['organisation']),
                'titel' => re($get['titel']),
                'first_name' => re($get['first_name']),
                'last_name' => re($get['last_name']),
                'address' => re($get['address']),
                'zip_code' => (int)($get['zip_code']),
                'place' => re($get['place']),
                'country' => re($get['country']),
                'e-mail' => re($get['e-mail']),
                'phone' => re($get['phone']),
                'website' => re($get['website']),
                'id' => $get['id'],
                'value' => _button_value_save));
        }
        break;
    default:
        $qry = db("SELECT `id`,`first_name`,`last_name` FROM `" . $db['dsgvo_pers'] . "`;");
        while ($get = _fetch($qry)) {
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                "action" => "admin=datenschutz&amp;do=edit",
                "title" => _button_title_edit));
            $rolle = ($get['id'] == 1 ? _datenschutz_rolle_1 : _datenschutz_rolle_2);
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst";
            $color++;
            $show .= show($dir . "/datenschutz_show", array('rolle' => $rolle, 'class' => $class,
                'first_name' => re($get['first_name']), 'last_name' => re($get['last_name']), 'edit' => $edit));
        }

        $selects_1 = '';
        $selects_2 = '';
        $qry = db("SELECT * FROM `" . $db['dsgvo'] . "` WHERE `for_dsgvo` = 1;");
        while ($get = _fetch($qry)) {
            $info = (defined(re($get['info_tag'])) ? constant(re($get['info_tag'])) : '');
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst";
            $color++;
            $selects_1 .= show($dir . "/datenschutz_select_loop", array('info' => $info, 'class' => $class,
                'is_on' => $get['show'] ? 'selected="selected"' : '',
                'is_off' => !$get['show'] ? 'selected="selected"' : '',
                'fid' => 'fid0_' . $get['id']));
        }

        $qry = db("SELECT * FROM `" . $db['dsgvo'] . "` WHERE `for_dsgvo_ak` = 1 ORDER BY `for_dsgvo` ASC;");
        while ($get = _fetch($qry)) {
            $info = (defined(re($get['info_tag'])) ? constant(re($get['info_tag'])) : '');
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst";
            $color++;
            $selects_2 .= show($dir . "/datenschutz_select_loop", array('info' => $info, 'class' => $class,
                'is_on' => $get['lock_show'] ? 'selected="selected"' : '',
                'is_off' => !$get['show'] ? 'selected="selected"' : '',
                'fid' => 'fid1_' . $get['id']));
        }

        $show = show($dir . "/datenschutz", array('show' => $show, 'selects_1' => $selects_1,
            'selects_2' => $selects_2, 'value' => _button_value_save));
        break;
}