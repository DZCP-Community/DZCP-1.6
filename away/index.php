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
$dir = "away";
$where = _site_away;

## SECTIONS ##
switch ($action):
    default:
        $where = $where . ' - ' . _away_list;
        if (!$chkMe || $chkMe < 2) {
            $index = error(_error_wrong_permissions, 1);
        } else {
            $qry = db("SELECT * FROM `" . $db['away'] . "`" .
                orderby_sql(array("userid", "start", "end"), 'ORDER BY id DESC') .
                " LIMIT " . ($page - 1) * config('m_away') . "," . config('m_away') . ";");
            $color = 0;
            while ($get = _fetch($qry)) {
                if ($get['start'] > time())
                    $status = _away_status_new;
                if ($get['start'] <= time() && $get['end'] >= time())
                    $status = _away_status_now;
                if ($get['end'] < time())
                    $status = _away_status_done;

                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst";
                $color++;
                $value = "&nbsp;";
                if ($userid == $get['userid'] || $chkMe == 4 && $get['end'] > time()) {
                    $value = show("page/button_edit_single", array("id" => $get['id'], "action" => "action=edit", "title" => _button_title_edit));
                }

                $delete = "&nbsp;";
                if ($chkMe == 4) {
                    $delete = show("page/button_delete_single", array("id" => $get['id'],
                        "action" => "action=del",
                        "title" => _button_title_del,
                        "del" => convSpace(_confirm_del_entry)));
                }

                $info = show($dir . "/button_info", array("id" => $get['id'], "action" => "action=info", "title" => "Info"));

                $show .= show($dir . "/away_show", array("class" => $class,
                    "id" => $get["id"],
                    "status" => $status,
                    "von" => date("d.m.y", $get['start']),
                    "bis" => date("d.m.y", $get['end']),
                    "grund" => re($get["titel"]),
                    "value" => $value,
                    "del" => $delete,
                    "nick" => autor($get['userid']),
                    "details" => $info));
            }

            if (empty($show))
                $show = _away_no_entry;

            $nav = nav(cnt($db['away']), config('m_away'), "?" .
                (isset($_GET['show']) ? $_GET['show'] : '') . orderby_nav());

            $index = show($dir . "/away", array("head" => _away_head,
                "show" => $show,
                "user" => _user,
                "titel" => _banned_reason,
                "from" => _from,
                "to" => _away_to,
                "status" => _status,
                "order_user" => orderby('userid'),
                "order_status" => orderby('end'),
                "order_from" => orderby('start'),
                "order_to" => orderby('end'),
                "submit" => _button_value_addto,
                "nav" => $nav));
        }
        break;
    case 'new';
        $where = $where . ' - ' . _away_new;
        if (!$chkMe || $chkMe < 2) {
            $index = error(_error_wrong_permissions, 1);
        } else {

            $date1 = show(_dropdown_date, array("day" => dropdown("day", date("d", time())),
                "month" => dropdown("month", date("m", time())),
                "year" => dropdown("year", date("Y", time()))));

            $date2 = show(_dropdown_date2, array("tag" => dropdown("day", date("d", time())),
                "monat" => dropdown("month", date("m", time())),
                "jahr" => dropdown("year", date("Y", time()))));

            $index = show($dir . "/form_away", array("head" => _away_new_head,
                "action" => "new&amp;do=set",
                "error" => "",
                "reason" => _away_reason,
                "from" => _from,
                "to" => _away_to,
                "date1" => $date1,
                "date2" => $date2,
                "comment" => _news_kommentar,
                "titel" => "",
                "text" => "",
                "submit" => _button_value_add));

            if ($do == "set") {
                $abdata = mktime(0, 0, 0, $_POST['m'], $_POST['t'], $_POST['j']);
                $bisdata = mktime(0, 0, 0, $_POST['monat'], $_POST['tag'], $_POST['jahr']);

                if (empty($_POST['titel']) || empty($_POST['reason']) || $bisdata == $abdata || $abdata > $bisdata) {
                    if (empty($_POST['titel']))
                        $error = show("errors/errortable", array("error" => _away_empty_titel));

                    if (empty($_POST['reason']))
                        $error = show("errors/errortable", array("error" => _away_empty_reason));

                    if ($bisdata == $abdata)
                        $error = show("errors/errortable", array("error" => _away_error_1));

                    if ($abdata > $bisdata)
                        $error = show("errors/errortable", array("error" => _away_error_2));

                    $date1 = show(_dropdown_date, array("day" => dropdown("day", (int)$_POST['t']),
                        "month" => dropdown("month", (int)$_POST['m']),
                        "year" => dropdown("year", (int)$_POST['j'])));

                    $date2 = show(_dropdown_date2, array("tag" => dropdown("day", (int)$_POST['tag']),
                        "monat" => dropdown("month", (int)$_POST['monat']),
                        "jahr" => dropdown("year", (int)$_POST['jahr'])));

                    $index = show($dir . "/form_away", array("head" => _away_new_head,
                        "action" => "new&amp;do=set",
                        "error" => $error,
                        "reason" => _away_reason,
                        "from" => _from,
                        "to" => _away_to,
                        "date1" => $date1,
                        "date2" => $date2,
                        "comment" => _news_kommentar,
                        "titel" => $_POST['titel'],
                        "text" => re($_POST['reason'], true),
                        "submit" => _button_value_add));
                } else {
                    $time = mktime(23, 59, 59, $_POST['monat'], $_POST['tag'], $_POST['jahr']);
                    $qry = db("INSERT INTO `" . $db['away'] . "` SET `userid`= " . ((int)$userid) . ",`start`= " .
                        ((int)$abdata) . ",`end`= " . ((int)$time) . ",`titel`= '" .
                        up($_POST['titel']) . "',`reason`= '" .
                        up($_POST['reason']) . "',`date`= " . time() . ";");

                    $index = info(_away_successful_added, "../away/");
                }
            }
        }
        break;
    case 'info';
        $where = $where . ' - ' . _info;
        if (!$chkMe || $chkMe < 2) {
            $index = error(_error_wrong_permissions, 1);
        } else {
            $get = db("SELECT * FROM `" . $db['away'] . "` WHERE `id` = " . (int)($_GET['id']) . ";", false, true);

            if ($get['start'] > time())
                $status = _away_status_new;
            if ($get['start'] <= time() && $get['end'] >= time())
                $status = _away_status_now;
            if ($get['end'] < time())
                $status = _away_status_done;

            $edit = "&nbsp;";
            if (!empty($get['lastedit']))
                $edit = bbcode(re($get['lastedit']));

            $index = show($dir . "/info", array("head" => _away_info_head,
                "i_reason" => _away_reason,
                "i_addeton" => _away_addon,
                "i_from_to" => _away_formto,
                "i_status" => _status,
                "i_info" => _info,
                "back" => _away_back,
                "nick" => autor($get['userid']),
                "von" => date("d.m.Y", $get['start']),
                "bis" => date("d.m.Y", $get['end']),
                "text" => bbcode(re($get['reason'])),
                "titel" => re($get['titel']),
                "edit" => $edit,
                "status" => $status,
                "addnew" => date("d.m.Y", $get['date']) . " " . _away_on . " " .
                    date("H:i", $get['date']) . _uhr));
        }
        break;
    case 'del';
        if (!$chkMe || $chkMe < 2) {
            $index = error(_error_wrong_permissions, 1);
        } else {
            $qry = db("DELETE FROM `" . $db['away'] . "` WHERE `id` = " . (int)($_GET['id']) . ";");
            $index = info(_away_successful_del, "../away/");
        }
        break;
    case 'edit';
        if (!$chkMe || $chkMe < 2) {
            $index = error(_error_wrong_permissions, 1);
        } else {
            $get = db("SELECT * FROM `" . $db['away'] . "` WHERE `id` = " . (int)($_GET['id']) . ";", false, true);
            $date1 = show(_dropdown_date, array("day" => dropdown("day", date("d", $get['start'])),
                "month" => dropdown("month", date("m", $get['start'])),
                "year" => dropdown("year", date("Y", $get['start']))));

            $date2 = show(_dropdown_date2, array("tag" => dropdown("day", date("d", $get['end'])),
                "monat" => dropdown("month", date("m", $get['end'])),
                "jahr" => dropdown("year", date("Y", $get['end']))));

            $index = show($dir . "/form_away", array("head" => _away_edit_head,
                "action" => "edit&amp;do=set&amp;id=" . $get['id'],
                "error" => "",
                "reason" => _away_reason,
                "from" => _from,
                "to" => _away_to,
                "date1" => $date1,
                "date2" => $date2,
                "comment" => _news_kommentar,
                "titel" => $get['titel'],
                "text" => $get['reason'],
                "submit" => _button_value_edit));

            $abdata = mktime(0, 0, 0,
                (isset($_POST['m']) ? (int)$_POST['m'] : 0),
                (isset($_POST['t']) ? (int)$_POST['t'] : 0),
                (isset($_POST['j']) ? (int)$_POST['j'] : 0));
            $bisdata = mktime(0, 0, 0,
                (isset($_POST['monat']) ? (int)$_POST['monat'] : 0),
                (isset($_POST['tag']) ? (int)$_POST['tag'] : 0),
                (isset($_POST['jahr']) ? (int)$_POST['jahr'] : 0));
            if ($do == "set") {
                if (empty($_POST['titel']) || empty($_POST['reason']) || $bisdata == $abdata || $abdata > $bisdata) {
                    if (empty($_POST['titel'])) $error = show("errors/errortable", array("error" => _away_empty_titel));
                    if (empty($_POST['reason'])) $error = show("errors/errortable", array("error" => _away_empty_reason));
                    if ($bisdata == $abdata) $error = show("errors/errortable", array("error" => _away_error_1));
                    if ($abdata > $bisdata) $error = show("errors/errortable", array("error" => _away_error_2));

                    $date1 = show(_dropdown_date, array("day" => dropdown("day", (int)$_POST['t']),
                        "month" => dropdown("month", (int)$_POST['m']),
                        "year" => dropdown("year", (int)$_POST['j'])));

                    $date2 = show(_dropdown_date2, array("tag" => dropdown("day", (int)$_POST['tag']),
                        "monat" => dropdown("month", (int)$_POST['monat']),
                        "jahr" => dropdown("year", (int)$_POST['jahr'])));

                    $index = show($dir . "/form_away", array("head" => _away_new_head,
                        "action" => "edit&amp;do=set&amp;id=" . $get['id'],
                        "error" => $error,
                        "reason" => _away_reason,
                        "from" => _from,
                        "to" => _away_to,
                        "date1" => $date1,
                        "date2" => $date2,
                        "comment" => _news_kommentar,
                        "titel" => $_POST['titel'],
                        "text" => $_POST['reason'],
                        "submit" => _button_value_add));

                } else {
                    $time = mktime(23, 59, 59, $_POST['monat'], $_POST['tag'], $_POST['jahr']);
                    $editedby = show(_edited_by, array("autor" => autor($userid),
                        "time" => date("d.m.Y H:i", time()) . _uhr));

                    db("UPDATE `" . $db['away'] . "` SET `start`= " . (int)$abdata . ", `end` = " .
                        ((int)$time) . ", `titel` = '" . up($_POST['titel']) . "', `reason` = '" .
                        up($_POST['reason']) . "', `lastedit` = '" .
                        up($editedby) . "' WHERE `id` = " . (int)($_GET['id']) . ";");

                    $index = info(_away_successful_edit, "../away/");
                }
            }
        }
        break;
endswitch;

## INDEX OUTPUT ##
$title = $pagetitle . " - " . $where . "";
page($index, $title, $where);