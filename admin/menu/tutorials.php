<?php
/**
 * DZCP - deV!L`z ClanPortal - Mainpage ( dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * Diese Datei ist ein Bestandteil von dzcp.de
 * Diese Version wurde speziell von Lucas Brucksch (Codedesigns) für dzcp.de entworfen bzw. verändert.
 * Eine Weitergabe dieser Datei außerhalb von dzcp.de ist nicht gestattet.
 * Sie darf nur für die Private Nutzung (nicht kommerzielle Nutzung) verwendet werden.
 *
 * Homepage: http://www.dzcp.de
 * E-Mail: info@web-customs.com
 * E-Mail: lbrucksch@codedesigns.de
 * Copyright 2017 © CodeKing, my-STARMEDIA, Codedesigns
 */

if (_adminMenu != 'true') exit;
$where = $where . ': ' . _tutorials;

switch (common::$do):
    default:
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_tutorials_kats}` ORDER BY `pos`;");
        $kats = '';
        if(common::$sql['default']->rowCount()) {
            foreach ($qry as $get) {
                $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=kat_edit");
                $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=kat_delete",_button_title_del,_confirm_del_kat);

                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                $smarty->caching = false;
                $smarty->assign('class',$class);
                $smarty->assign('edit',$edit);
                $smarty->assign('delete',$delete);
                $smarty->assign('id',$get['id']);
                $smarty->assign('bezeichnung',stringParser::decode($get['name']));
                $smarty->assign('anzahl_tutorials',common::cnt("{prefix_tutorials}", " WHERE `kat` = ".$get['id'].";"));
                $kats .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/tutorials_kats_show.tpl');
                $smarty->clearAllAssign();
            }
        }

        $smarty->caching = false;
        $smarty->assign('kats',$kats);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/tutorials_kats.tpl');
        $smarty->clearAllAssign();
    break;
    case 'kat_new':
        $user = ""; $trial = ""; $member = ""; $admin = "";
        if($_POST) {
            if ($_FILES['pic']['name'] == "" || ($_FILES['pic']['type'] != "image/gif" AND $_FILES['pic']['type'] != "image/pjpeg" AND $_FILES['pic']['type'] != "image/jpeg" AND $_FILES['pic']['type'] != "image/png") || empty($_POST['bezeichnung'])) {
                if ($_FILES['pic']['name'] == "")
                    notification::add_error(_tutorials_error_empty_pic);
                else
                    if ($_FILES['pic']['type'] != "image/gif" AND $_FILES['pic']['type'] != "image/pjpeg" AND $_FILES['pic']['type'] != "image/jpeg" AND $_FILES['pic']['type'] != "image/png")
                        notification::add_error(_tutorials_error_wrong_filetyp);
                    else if (empty($_POST['bezeichnung']))
                            notification::add_error(_tutorials_error_empty_bezeichnung);
            }

            if(notification::has()) {
                if ($_POST['level'] == 1)
                    $user = "selected=\"selected\"";
                elseif ($_POST['level'] == 2)
                    $trial = "selected=\"selected\"";
                elseif ($_POST['level'] == 3)
                    $member = "selected=\"selected\"";
                elseif ($_POST['level'] == 4)
                    $admin = "selected=\"selected\"";
            } else {
                //Save
                $sign = "> ";
                if ($_POST['position'] == 1 || $_POST['position'] == 2)
                    $sign = ">= ";

                common::$sql['default']->update("UPDATE `{prefix_tutorials_kats}` SET `pos` = pos+1 WHERE `pos` " . $sign . " " . intval($_POST['position']) . ";");

                common::$sql['default']->insert("INSERT INTO `{prefix_tutorials_kats}` SET `name` = ?, `pos` = ?, `level` = ?, `beschreibung` = ?;",
                    [stringParser::encode($_POST['bezeichnung']),((int)$_POST['position']),((int)$_POST['level']),stringParser::encode($_POST['beschreibung'])]);

                if ($_FILES['pic']['name'] != "") {
                    $endung = explode(".", $_FILES['pic']['name']);
                    $endung = strtolower($endung[count($endung) - 1]);
                    copy($_FILES['pic']['tmp_name'], basePath . "/inc/images/uploads/tutorials/kats/" . common::$sql['default']->lastInsertId() . "." . strtolower($endung) . "");
                    @unlink($_FILES['pic']['tmp_name']);
                }

                notification::add_success(_tutorials_kat_added, 'global', '?admin=tutorials');
            }
        }

        //SHOW
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_tutorials_kats}` ORDER BY `pos`;");
        $kats = ''; $positions = ''; $selected = false;
        if(common::$sql['default']->rowCount()) {
            if($_POST) {
                if ($_POST['position'] == 1)
                    $selected = true;
            }
            $positions .= common::select_field(1 ,$selected,_tutorials_position_first);
            foreach ($qry as $get) {
                $selected = false;
                $get['pos'] = $get['pos'] +1;
                if($_POST) {
                    if ($_POST['position'] == $get['pos'] - 1)
                        $selected = true;
                }

                $positions .= common::select_field((int)$get['pos'] ,$selected,_nach . ' ' . stringParser::decode($get['name']));
            }
        }

        $smarty->caching = false;
        $smarty->assign('positions',$positions);
        $smarty->assign('do','kat_new');
        $smarty->assign('v_bezeichnung',$_POST ? $_POST['bezeichnung'] : '');
        $smarty->assign('v_user',$user);
        $smarty->assign('v_trial',$trial);
        $smarty->assign('v_member',$member);
        $smarty->assign('v_admin',$admin);
        $smarty->assign('v_pos_none','');
        $smarty->assign('v_pic','');
        $smarty->assign('v_beschreibung', $_POST ? $_POST['beschreibung'] : '');
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/tutorials_kats_form.tpl');
        $smarty->clearAllAssign();
    break;
    case 'kat_edit':
        $qrypositions = common::$sql['default']->select("SELECT * FROM `{prefix_tutorials_kats}` ORDER BY `pos`;");
        if(common::$sql['default']->rowCount()) {
            foreach ($qrypositions as $getpositions) {
                if ($getpositions['id'] == $_GET['id'])
                    $positions .= "";
                else
                    $positions .= show(_select_field, array("value" => $getpositions['pos'] + 1,
                        "what" => _nach . ' ' . re($getpositions['name']),
                        "sel" => ""));
            }
        }

        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_tutorials_kats}` WHERE `id` = '" . (int)$_GET['id'] . "'");

        $pfad = "inc/images/uploads/tutorials/kats/" . $get['id'];
        if (file_exists(basePath . "/" . $pfad . ".gif"))
            $pic = show(_tutorials_pic_show, array("pic" => img_size($pfad . ".gif")));
        elseif (file_exists(basePath . "/" . $pfad . ".jpg"))
            $pic = show(_tutorials_pic_show, array("pic" => img_size($pfad . ".jpg")));
        elseif (file_exists(basePath . "/" . $pfad . ".png"))
            $pic = show(_tutorials_pic_show, array("pic" => img_size($pfad . ".png")));

## >Added in 1.1 - START
        if ($get['level'] == 1)
            $user = "selected=\"selected\"";
        elseif ($get['level'] == 2)
            $trial = "selected=\"selected\"";
        elseif ($get['level'] == 3)
            $member = "selected=\"selected\"";
        elseif ($get['level'] == 4)
            $admin = "selected=\"selected\"";
## >Added in 1.1 - END

        $show = show($dir . "/tutorials_kats_form", array("id" => re($_GET['id']),
            "error" => "",
            "do" => "kat_editdo",
            "value" => _tutorials_edit_kat,
            "head" => _tutorials . " - " . _tutorials_edit_kat,
            "bezeichnung" => _tutorials_bezeichnung,
            "position" => _tutorials_position,

## >Added in 1.1 - START
            "level" => _tutorials_level,
            "unregged" => _status_unregged,
            "user" => _status_user,
            "trial" => _status_trial,
            "member" => _status_member,
            "admin" => _status_admin,
            "v_user" => $user,
            "v_trial" => $trial,
            "v_member" => $member,
            "v_admin" => $admin,
## >Added in 1.1 - END

            "pic" => _tutorials_pic,
            "picpflicht" => $picpflicht,
            "pic_info" => _tutorials_katpic_info,
            "beschreibung" => _tutorials_beschreibung,
            "pflicht" => _tutorials_pflichtfeld,
            "v_bezeichnung" => re($get['name']),
            "v_pos_none" => _tutorials_position_lazy,
            "v_pos_first" => _tutorials_position_first,
            "v_positions" => $positions,
            "v_pic" => $pic,
            "v_beschreibung" => re_bbcode($get['beschreibung'])));
        break;

    case 'kat_editdo':
        if ($_FILES['pic']['name'] != "") {
            if (($_FILES['pic']['type'] != "image/gif" AND $_FILES['pic']['type'] != "image/pjpeg" AND $_FILES['pic']['type'] != "image/jpeg" AND $_FILES['pic']['type'] != "image/png") || empty($_POST['bezeichnung'])) {
                if ($_FILES['pic']['type'] != "image/gif" AND $_FILES['pic']['type'] != "image/pjpeg" AND $_FILES['pic']['type'] != "image/jpeg" AND $_FILES['pic']['type'] != "image/png") $error = _tutorials_error_wrong_filetyp;
                elseif (empty($_POST['bezeichnung'])) $error = _tutorials_error_empty_bezeichnung;
                $control = 0;
            } else  $control = 1;
        } else {
            if (empty($_POST['bezeichnung'])) {
                $error = _tutorials_error_empty_bezeichnung;
                $control = 0;
            } else $control = 1;
        }

        if ($control == 0) {
            $error = show("errors/errortable", array("error" => $error));

            $selectedpos = $_POST['position'] - 1;
            $qrypositions = db("SELECT * FROM " . $sql_prefix . "tutorials_kats  
                        WHERE `id` != '" . intval($_POST['id']) . "'                
                        ORDER BY `pos`");
            while ($getpositions = _fetch($qrypositions)) {
                $positions .= show(_select_field, array("value" => $getpositions['pos'] + 1,
                    "what" => _nach . ' ' . re($getpositions['name']),
                    "sel" => ""));
            }

            $qry = db("SELECT * FROM " . $sql_prefix . "tutorials_kats    
               WHERE `id` = '" . intval($_POST['id']) . "'");
            $get = _fetch($qry);

            $pfad = "inc/images/uploads/tutorials/kats/" . $get['id'];
            if (file_exists(basePath . "/" . $pfad . ".gif")) $pic = show(_tutorials_pic_show, array("pic" => img_size($pfad . ".gif")));
            elseif (file_exists(basePath . "/" . $pfad . ".jpg")) $pic = show(_tutorials_pic_show, array("pic" => img_size($pfad . ".jpg")));
            elseif (file_exists(basePath . "/" . $pfad . ".png")) $pic = show(_tutorials_pic_show, array("pic" => img_size($pfad . ".png")));

## >Added in 1.1 - START
            if ($_POST['level'] == 1) $user = "selected=\"selected\"";
            elseif ($_POST['level'] == 2) $trial = "selected=\"selected\"";
            elseif ($_POST['level'] == 3) $member = "selected=\"selected\"";
            elseif ($_POST['level'] == 4) $admin = "selected=\"selected\"";
## >Added in 1.1 - END

            $show = show($dir . "/tutorials_kats_form", array("id" => re($_POST['id']),
                "error" => $error,
                "do" => "kat_editdo",
                "value" => _tutorials_edit_kat,
                "head" => _tutorials . " - " . _tutorials_edit_kat,
                "bezeichnung" => _tutorials_bezeichnung,
                "position" => _tutorials_position,

## >Added in 1.1 - START
                "level" => _tutorials_level,
                "unregged" => _status_unregged,
                "user" => _status_user,
                "trial" => _status_trial,
                "member" => _status_member,
                "admin" => _status_admin,
                "v_user" => $user,
                "v_trial" => $trial,
                "v_member" => $member,
                "v_admin" => $admin,
## >Added in 1.1 - END

                "pic" => _tutorials_pic,
                "picpflicht" => $picpflicht,
                "pic_info" => _tutorials_katpic_info,
                "beschreibung" => _tutorials_beschreibung,
                "pflicht" => _tutorials_pflichtfeld,
                "v_bezeichnung" => re($_POST['bezeichnung']),
                "v_pos_none" => _tutorials_position_lazy,
                "v_pos_first" => _tutorials_position_first,
                "v_positions" => $positions,
                "v_pic" => $pic,
                "v_beschreibung" => re_bbcode($_POST['beschreibung'])));
        } else {
            if ($_POST['position'] != "lazy") {
                if ($_POST['position'] == 1 || $_POST['position'] == 2) $sign = ">= ";
                else $sign = "> ";

                $posi = db("UPDATE " . $sql_prefix . "tutorials_kats
                  SET `pos` = pos+1
                  WHERE `pos` " . $sign . " '" . intval($_POST['position']) . "'");
                $newpos = "`pos` = '" . ((int)$_POST['position']) . "',";
            }

            if ($_FILES['pic']['name'] != "") {
                $pfad = basePath . "/inc/images/uploads/tutorials/kats/" . intval($_POST['id']);
                @unlink($pfad . ".gif");
                @unlink($pfad . ".jpg");
                @unlink($pfad . ".png");

                $endung = explode(".", $_FILES['pic']['name']);
                $endung = strtolower($endung[count($endung) - 1]);
                copy($_FILES['pic']['tmp_name'], basePath . "/inc/images/uploads/tutorials/kats/" . intval($_POST['id']) . "." . strtolower($endung) . "");
                @unlink($_FILES['pic']['tmp_name']);
            }

            $qry = db("UPDATE " . $sql_prefix . "tutorials_kats
               SET `name` = '" . up($_POST['bezeichnung']) . "',
                   " . $newpos . "
																			
## >Added in 1.1 - START
                   `level` = '" . ((int)$_POST['level']) . "',
## >Added in 1.1 - END																			
																			
                   `beschreibung` = '" . up($_POST['beschreibung'], 1) . "'
               WHERE `id` = '" . intval($_POST['id']) . "'");

            $show = info(_tutorials_kat_edited, "?admin=tutorials");
        }
        break;

    case 'kat_delete':

## >Added in 1.1 - START
        $qry = db("SELECT `id` FROM " . $sql_prefix . "tutorials
												 WHERE `kat` = '" . (int)$_GET['id'] . "'");
        while ($get = _fetch($qry)) {
            $del = db("DELETE FROM " . $sql_prefix . "tutorials_comments
														 WHERE `tutorial` = '" . intval($get['id']) . "'");
        }
## >Added in 1.1 - END

        $qry = db("DELETE FROM " . $sql_prefix . "tutorials
             WHERE `kat` = '" . (int)$_GET['id'] . "'");
        $qry = db("DELETE FROM " . $sql_prefix . "tutorials_kats
             WHERE `id` = '" . (int)$_GET['id'] . "'");
        $pfad = basePath . "/inc/images/uploads/tutorials/kats/" . (int)$_GET['id'];
        @unlink($pfad . ".gif");
        @unlink($pfad . ".jpg");
        @unlink($pfad . ".png");

        $show = info(_tutorials_kat_deleted, "?admin=tutorials");
        break;

    case 'tut':
        if (isset($_GET['sort1'])) {
            if ($_GET['sort1'] == 1) {
                $sort = "`pos` " . $_GET['sort2'];
                $sort_select1 = "selected=\"selected\"";
            } elseif ($_GET['sort1'] == 2) {
                $sort = "`datum` " . $_GET['sort2'];
                $sort_select2 = "selected=\"selected\"";
            } elseif ($_GET['sort1'] == 3) {
                $sort = "`name` " . $_GET['sort2'];
                $sort_select3 = "selected=\"selected\"";
            } elseif ($_GET['sort1'] == 4) {
                $sort = "`schwierigkeit` " . $_GET['sort2'] . ",pos " . $_GET['sort2'];
                $sort_select4 = "selected=\"selected\"";
            }

            $sort1 = $_GET['sort1'];
            $sort2 = $_GET['sort2'];
        } else {
            $sort = "`pos` ASC";
            $sort1 = "1";
            $sort2 = "ASC";
        }

        if ($_GET['sort2'] == "DESC") $v_desc = "selected=\"selected\"";
        else $v_asc = "selected=\"selected\"";

        $qrykat = db("SELECT * FROM " . $sql_prefix . "tutorials_kats
                WHERE `id` = '" . intval($_GET['kat']) . "'");
        $getkat = _fetch($qrykat);

        $qry = db("SELECT * FROM " . $sql_prefix . "tutorials
             WHERE `kat` = '" . intval($_GET['kat']) . "'
             ORDER BY " . $sort . "
             LIMIT " . ($page - 1) * $settings['maxtutorials'] . "," . $settings['maxtutorials'] . "");
        while ($get = _fetch($qry)) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst";
            $color++;
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                "action" => "admin=tutorials&amp;do=tut_edit&amp;kat=" . intval($_GET['kat']) . "",
                "title" => _button_title_edit));
            $delete = show("page/button_delete_single", array("id" => $get['id'],
                "action" => "admin=tutorials&amp;do=tut_delete&amp;kat=" . intval($_GET['kat']) . "",
                "title" => _button_title_del,
                "del" => convSpace(_tutorials_confirm_del_tutorial)));

            $tutorials .= show($dir . "/tutorials_show", array("class" => $class,
                "edit" => $edit,
                "delete" => $delete,
                "id" => $get['id'],
                "bezeichnung" => cut($get['name'], 50, true),
                "datum" => date("d.m.Y H:i", $get['datum']),
                "autor" => autor($get['autor']),
                "schwierigkeit" => $get['schwierigkeit']));
        }

        $show = show($dir . "/tutorials", array("id" => $_GET['kat'],
            "head" => _tutorials . " <font style=\"font-weight:normal\">(Kategorie: " . $getkat['name'] . ")</font>",
            "new" => _tutorials_new_tutorial,
            "sortieren" => _tutorials_sortieren,
            "steigend" => _tutorials_steigend,
            "fallend" => _tutorials_fallend,
            "sortierung1" => $sort1,
            "sortierung2" => $sort2,
            "select1" => $sort_select1,
            "select2" => $sort_select2,
            "select3" => $sort_select3,
            "select4" => $sort_select4,
            "v_asc" => $v_asc,
            "v_desc" => $v_desc,
            "seiten" => nav(cnt($sql_prefix . "tutorials", " WHERE kat = '" . intval($_GET['kat']) . "'"), $settings['maxtutorials'], "?admin=tutorials&sort1=" . $sort1 . "&sort2=" . $sort2 . "&id=" . (int)$_GET['id']),
            "position" => _tutorials_position,
            "bezeichnung" => _tutorials_bezeichnung,
            "datum" => _tutorials_erstellt_am,
            "autor" => _tutorials_autor,
            "schwierigkeit" => _tutorials_schwierigkeit,
            "tutorials" => $tutorials));
        break;

    case 'tut_new':
        $wysiwyg = '_word';
        $qry = db("SELECT * FROM " . $sql_prefix . "tutorials  
													WHERE `kat` = '" . intval($_GET['kat']) . "'               
													ORDER BY `pos`");
        while ($get = _fetch($qry)) {
            $positions .= show(_select_field, array("value" => $get['pos'] + 1,
                "what" => _nach . ' ' . re($get['name']),
                "sel" => ""));
        }

        if ($settings['pic_pflicht']['pic_pflicht'] == 1) $picpflicht = "<span class=\"fontWichtig\">*</span>";
        $qrykat = db("SELECT `name` FROM " . $sql_prefix . "tutorials_kats
																WHERE `id` = '" . intval($_GET['kat']) . "'");
        $getkat = _fetch($qrykat);

        $show = show($dir . "/tutorials_form", array("id" => "",
            "katid" => $_GET['kat'],
            "error" => "",
            "do" => "tut_add",
            "value" => _tutorials_new_tutorial,
            "preview" => _preview,
            "head" => _tutorials . " - " . _tutorials_new_tutorial,
            "kategorie" => _tutorials_kategorie,
            "bezeichnung" => _tutorials_bezeichnung,
            "position" => _tutorials_position,
            "pic" => _tutorials_pic,
            "picpflicht" => $picpflicht,
            "picinfo" => _tutorials_pic_info,
            "schwierigkeit" => _tutorials_schwierigkeit,
            "schwierigkeit_lazy" => _tutorials_schwierigkeit_lazy,
            "schwierigkeit1" => _tutorials_schwierigkeit_sehr_leicht,
            "schwierigkeit2" => _tutorials_schwierigkeit_leicht,
            "schwierigkeit3" => _tutorials_schwierigkeit_mittel,
            "schwierigkeit4" => _tutorials_schwierigkeit_schwer,
            "schwierigkeit5" => _tutorials_schwierigkeit_sehr_schwer,
            "beschreibung" => _tutorials_beschreibung,
            "pflicht" => _tutorials_pflichtfeld,
            "v_kategorie" => re($getkat['name']),
            "moveto" => "",
            "v_bezeichnung" => "",
            "v_pos_lazy" => "",
            "v_pos_first" => _tutorials_position_first,
            "v_positions" => $positions,
            "v_pic" => "",
            "v_beschreibung" => "",
            "v_tutorial" => ""));
        break;

    case 'tut_add':
        $wysiwyg = '_word';

        if ($settings['pic_pflicht'] == 1) {
            if ($_FILES['pic']['name'] == "" || ($_FILES['pic']['type'] != "image/gif" AND $_FILES['pic']['type'] != "image/pjpeg" AND $_FILES['pic']['type'] != "image/jpeg" AND $_FILES['pic']['type'] != "image/png") || empty($_POST['bezeichnung']) || $_POST['schwierigkeit'] == "lazy" || empty($_POST['beschreibung']) || empty($_POST['tutorial'])) {
                if ($_FILES['pic']['name'] == "") $error = _tutorials_error_empty_pic;
                elseif ($_FILES['pic']['type'] != "image/gif" AND $_FILES['pic']['type'] != "image/pjpeg" AND $_FILES['pic']['type'] != "image/jpeg" AND $_FILES['pic']['type'] != "image/png") $error = _tutorials_error_wrong_filetyp;
                elseif (empty($_POST['bezeichnung'])) $error = _tutorials_error_empty_bezeichnung;
                elseif ($_POST['schwierigkeit'] == "lazy") $error = _tutorials_error_schwierigkeit;
                elseif (empty($_POST['beschreibung'])) $error = _tutorials_error_empty_beschreibung;
                elseif (empty($_POST['tutorial'])) $error = _tutorials_error_empty_tutorial;

                $picpflicht = "<span class=\"fontWichtig\">*</span>";
                $control = 0;
            } else $control = 1;
        } else {
            if (empty($_POST['bezeichnung']) || $_POST['schwierigkeit'] == "lazy" || empty($_POST['beschreibung']) || empty($_POST['tutorial'])) {
                if (empty($_POST['bezeichnung'])) $error = _tutorials_error_empty_bezeichnung;
                elseif ($_POST['schwierigkeit'] == "lazy") $error = _tutorials_error_schwierigkeit;
                elseif (empty($_POST['beschreibung'])) $error = _tutorials_error_empty_beschreibung;
                elseif (empty($_POST['tutorial'])) $error = _tutorials_error_empty_tutorial;
                $control = 0;
            } else $control = 1;
        }

        if ($_POST['schwierigkeit'] == 1) $v_schwierigkeit1 = "selected=\"selected\"";
        elseif ($_POST['schwierigkeit'] == 2) $v_schwierigkeit2 = "selected=\"selected\"";
        elseif ($_POST['schwierigkeit'] == 3) $v_schwierigkeit3 = "selected=\"selected\"";
        elseif ($_POST['schwierigkeit'] == 4) $v_schwierigkeit4 = "selected=\"selected\"";
        elseif ($_POST['schwierigkeit'] == 5) $v_schwierigkeit5 = "selected=\"selected\"";

        if ($control == 0) {
            $error = show("errors/errortable", array("error" => $error));

            $qry = db("SELECT * FROM " . $sql_prefix . "tutorials
															WHERE `kat` = '" . intval($_POST['kat']) . "'
															ORDER BY `pos` ASC;");
            while ($get = _fetch($qry)) {
                if ($_POST['position'] - 1 == $get['pos']) $selected = "selected=\"selected\"";
                else $selected = "";

                $positions .= show(_select_field, array("value" => $get['pos'] + 1,
                    "what" => _nach . ': ' . re(cut($get['name'], 100)),
                    "sel" => $selected));
            }

            $qrykat = db("SELECT `name` FROM " . $sql_prefix . "tutorials_kats
																		WHERE `id` = '" . intval($_POST['kat']) . "'");
            $getkat = _fetch($qrykat);

            $show = show($dir . "/tutorials_form", array("id" => "",
                "katid" => $_POST['kat'],
                "error" => $error,
                "do" => "tut_add",
                "value" => _tutorials_new_tutorial,
                "preview" => _preview,
                "head" => _tutorials . " - " . _tutorials_new_tutorial,
                "kategorie" => _tutorials_kategorie,
                "bezeichnung" => _tutorials_bezeichnung,
                "position" => _tutorials_position,
                "pic" => _tutorials_pic,
                "picpflicht" => $picpflicht,
                "picinfo" => _tutorials_pic_info,
                "schwierigkeit" => _tutorials_schwierigkeit,
                "schwierigkeit_lazy" => _tutorials_schwierigkeit_lazy,
                "schwierigkeit1" => _tutorials_schwierigkeit_sehr_leicht,
                "schwierigkeit2" => _tutorials_schwierigkeit_leicht,
                "schwierigkeit3" => _tutorials_schwierigkeit_mittel,
                "schwierigkeit4" => _tutorials_schwierigkeit_schwer,
                "schwierigkeit5" => _tutorials_schwierigkeit_sehr_schwer,
                "beschreibung" => _tutorials_beschreibung,
                "pflicht" => _tutorials_pflichtfeld,
                "v_kategorie" => re($getkat['name']),
                "moveto" => "",
                "v_bezeichnung" => re($_POST['bezeichnung']),
                "v_pos_lazy" => "",
                "v_pos_first" => _tutorials_position_first,
                "v_positions" => $positions,
                "v_pic" => "",
                "v_schwierigkeit1" => $v_schwierigkeit1,
                "v_schwierigkeit2" => $v_schwierigkeit2,
                "v_schwierigkeit3" => $v_schwierigkeit3,
                "v_schwierigkeit4" => $v_schwierigkeit4,
                "v_schwierigkeit5" => $v_schwierigkeit5,
                "v_beschreibung" => re_bbcode($_POST['beschreibung']),
                "v_tutorial" => re_bbcode($_POST['tutorial'])));
        } else {
            if ($_POST['position'] == 1 || $_POST['position'] == 2) $sign = ">= ";
            else $sign = "> ";

            $posi = db("UPDATE " . $sql_prefix . "tutorials
																	SET `pos` = pos+1
																	WHERE `kat` = '" . intval($_POST['kat']) . "' AND pos " . $sign . " '" . intval($_POST['position']) . "'");

            $qry = db("INSERT INTO " . $sql_prefix . "tutorials
																SET `kat` = '" . ((int)$_POST['kat']) . "',
																				`pos` = '" . ((int)$_POST['position']) . "',
																				`datum` = '" . ((int)time()) . "',
																				`autor` = '" . ((int)$userid) . "',
																				`name` = '" . up($_POST['bezeichnung']) . "',
																				`beschreibung` = '" . up($_POST['beschreibung'], 1) . "',
																				`tutorial` = '" . up($_POST['tutorial'], 1) . "',
																				`schwierigkeit` = '" . ((int)$_POST['schwierigkeit']) . "'");

            if ($_FILES['pic']['name'] != "") {
                $endung = explode(".", $_FILES['pic']['name']);
                $endung = strtolower($endung[count($endung) - 1]);
                copy($_FILES['pic']['tmp_name'], basePath . "/inc/images/uploads/tutorials/" . mysql_insert_id() . "." . strtolower($endung) . "");
                @unlink($_FILES['pic']['tmp_name']);
            }

            $show = info(_tutorials_tutorial_added, "?admin=tutorials&amp;do=tut&amp;kat=" . $_POST['kat'] . "");
        }
        break;

    case 'tut_edit':
        $wysiwyg = '_word';

        $qry = db("SELECT * FROM " . $sql_prefix . "tutorials
													WHERE `id` = '" . (int)$_GET['id'] . "'");
        $get = _fetch($qry);

        $qrypos = db("SELECT * FROM " . $sql_prefix . "tutorials                 
																WHERE `id` != '" . (int)$_GET['id'] . "'
																		AND `kat` = '" . $get['kat'] . "'
																ORDER BY `pos`");
        while ($getpos = _fetch($qrypos)) {
            if ($get['position'] == $getpos['pos']) $selected = "selected=\"selected\"";
            else $selected = "";

            $positions .= show(_select_field, array("value" => $getpos['pos'] + 1,
                "what" => _nach . ' ' . cut(re($getpos['name']), 50, true),
                "sel" => $selected));
        }

        $pfad = "inc/images/uploads/tutorials/" . $get['id'];
        if (file_exists(basePath . "/" . $pfad . ".gif")) $pic = show(_tutorials_pic_show, array("pic" => img_size($pfad . ".gif")));
        elseif (file_exists(basePath . "/" . $pfad . ".jpg")) $pic = show(_tutorials_pic_show, array("pic" => img_size($pfad . ".jpg")));
        elseif (file_exists(basePath . "/" . $pfad . ".png")) $pic = show(_tutorials_pic_show, array("pic" => img_size($pfad . ".png")));

        if ($get['schwierigkeit'] == 1) $v_schwierigkeit1 = "selected=\"selected\"";
        elseif ($get['schwierigkeit'] == 2) $v_schwierigkeit2 = "selected=\"selected\"";
        elseif ($get['schwierigkeit'] == 3) $v_schwierigkeit3 = "selected=\"selected\"";
        elseif ($get['schwierigkeit'] == 4) $v_schwierigkeit4 = "selected=\"selected\"";
        elseif ($get['schwierigkeit'] == 5) $v_schwierigkeit5 = "selected=\"selected\"";

        $qrykat = db("SELECT `name` FROM " . $sql_prefix . "tutorials_kats
																WHERE `id`  = '" . intval($_GET['kat']) . "'");
        $getkat = _fetch($qrykat);

        $qrykats = db("SELECT id,name FROM " . $sql_prefix . "tutorials_kats
																	WHERE `id` != '" . intval($_GET['kat']) . "'
																	ORDER BY `name`");
        while ($getkats = _fetch($qrykats)) {
            $kats .= show(_select_field, array("value" => $getkats['id'],
                "what" => cut(re($getkats['name']), 50, true),
                "sel" => ""));
        }
        $moveto = show(_tutorials_moveto, array("id" => $_GET['id'],
            "kat" => $_GET['kat'],
            "kats" => $kats));

        $show = show($dir . "/tutorials_form", array("id" => $get['id'],
            "katid" => $_GET['kat'],
            "error" => "",
            "do" => "tut_editdo",
            "value" => _tutorials_edit_tutorial,
            "preview" => _preview,
            "head" => _tutorials . " - " . _tutorials_edit_tutorial,
            "kategorie" => _tutorials_kategorie,
            "bezeichnung" => _tutorials_bezeichnung,
            "position" => _tutorials_position,
            "pic" => _tutorials_pic,
            "picpflicht" => "",
            "picinfo" => _tutorials_pic_info,
            "schwierigkeit" => _tutorials_schwierigkeit,
            "schwierigkeit_lazy" => "",
            "schwierigkeit1" => _tutorials_schwierigkeit_sehr_leicht,
            "schwierigkeit2" => _tutorials_schwierigkeit_leicht,
            "schwierigkeit3" => _tutorials_schwierigkeit_mittel,
            "schwierigkeit4" => _tutorials_schwierigkeit_schwer,
            "schwierigkeit5" => _tutorials_schwierigkeit_sehr_schwer,
            "beschreibung" => _tutorials_beschreibung,
            "pflicht" => _tutorials_pflichtfeld,
            "v_kategorie" => re($getkat['name']),
            "moveto" => $moveto,
            "v_bezeichnung" => re($get['name']),
            "v_pos_lazy" => _tutorials_position_lazy,
            "v_pos_first" => _tutorials_position_first,
            "v_positions" => $positions,
            "v_pic" => $pic,
            "v_schwierigkeit1" => $v_schwierigkeit1,
            "v_schwierigkeit2" => $v_schwierigkeit2,
            "v_schwierigkeit3" => $v_schwierigkeit3,
            "v_schwierigkeit4" => $v_schwierigkeit4,
            "v_schwierigkeit5" => $v_schwierigkeit5,
            "v_beschreibung" => re_bbcode($get['beschreibung']),
            "v_tutorial" => re_bbcode($get['tutorial'])));
        break;

    case 'tut_editdo':
        $wysiwyg = '_word';

        if ($_FILES['pic']['name'] != "") {
            if (($_FILES['pic']['type'] != "image/gif" AND $_FILES['pic']['type'] != "image/pjpeg" AND $_FILES['pic']['type'] != "image/jpeg" AND $_FILES['pic']['type'] != "image/png") || empty($_POST['bezeichnung']) || empty($_POST['beschreibung']) || empty($_POST['tutorial'])) {
                if ($_FILES['pic']['type'] != "image/gif" AND $_FILES['pic']['type'] != "image/pjpeg" AND $_FILES['pic']['type'] != "image/jpeg" AND $_FILES['pic']['type'] != "image/png") $error = _tutorials_error_wrong_filetyp;
                elseif (empty($_POST['bezeichnung'])) $error = _tutorials_error_empty_bezeichnung;
                elseif (empty($_POST['beschreibung'])) $error = _tutorials_error_empty_beschreibung;
                elseif (empty($_POST['tutorial'])) $error = _tutorials_error_empty_tutorial;
                $control = 0;
            } else    $control = 1;
        } else {
            if (empty($_POST['bezeichnung']) || empty($_POST['beschreibung']) || empty($_POST['tutorial'])) {
                if (empty($_POST['bezeichnung'])) $error = _tutorials_error_empty_bezeichnung;
                elseif (empty($_POST['beschreibung'])) $error = _tutorials_error_empty_beschreibung;
                elseif (empty($_POST['tutorial'])) $error = _tutorials_error_empty_tutorial;
                $control = 0;
            } else    $control = 1;
        }

        if ($control == 0) {
            $error = show("errors/errortable", array("error" => $error));

            $qrypos = db("SELECT * FROM " . $sql_prefix . "tutorials                 
																		WHERE `id` != '" . intval($_POST['id']) . "'
																				AND `kat` = '" . intval($_POST['kat']) . "'
																		ORDER BY `pos`");
            while ($getpos = _fetch($qrypos)) {
                $positions .= show(_select_field, array("value" => $getpos['pos'] + 1,
                    "what" => _nach . ' ' . cut(re($getpos['name']), 50, true),
                    "sel" => ""));
            }

            $pfad = "inc/images/uploads/tutorials/" . $_POST['id'];
            if (file_exists(basePath . "/" . $pfad . ".gif")) $pic = show(_tutorials_pic_show, array("pic" => img_size($pfad . ".gif")));
            elseif (file_exists(basePath . "/" . $pfad . ".jpg")) $pic = show(_tutorials_pic_show, array("pic" => img_size($pfad . ".jpg")));
            elseif (file_exists(basePath . "/" . $pfad . ".png")) $pic = show(_tutorials_pic_show, array("pic" => img_size($pfad . ".png")));

            if ($_POST['schwierigkeit'] == 1) $v_schwierigkeit1 = "selected=\"selected\"";
            elseif ($_POST['schwierigkeit'] == 2) $v_schwierigkeit2 = "selected=\"selected\"";
            elseif ($_POST['schwierigkeit'] == 3) $v_schwierigkeit3 = "selected=\"selected\"";
            elseif ($_POST['schwierigkeit'] == 4) $v_schwierigkeit4 = "selected=\"selected\"";
            elseif ($_POST['schwierigkeit'] == 5) $v_schwierigkeit5 = "selected=\"selected\"";

            $qrykat = db("SELECT name FROM " . $sql_prefix . "tutorials_kats
																		WHERE id = '" . intval($_POST['kat']) . "'");
            $getkat = _fetch($qrykat);

            $qrykats = db("SELECT id,name FROM " . $sql_prefix . "tutorials_kats
																			WHERE `id` != '" . intval($_POST['kat']) . "'
																			ORDER BY `name`");
            while ($getkats = _fetch($qrykats)) {
                $kats .= show(_select_field, array("value" => $getkats['id'],
                    "what" => cut(re($getkats['name']), 50, true),
                    "sel" => ""));
            }
            $moveto = show(_tutorials_moveto, array("id" => $_POST['id'],
                "kat" => $_POST['kat'],
                "kats" => $kats));

            $show = show($dir . "/tutorials_form", array("id" => $_POST['id'],
                "katid" => $_POST['kat'],
                "error" => $error,
                "do" => "tut_editdo",
                "value" => _tutorials_edit_tutorial,
                "preview" => _preview,
                "head" => _tutorials . " - " . _tutorials_edit_tutorial,
                "kategorie" => _tutorials_kategorie,
                "bezeichnung" => _tutorials_bezeichnung,
                "position" => _tutorials_position,
                "pic" => _tutorials_pic,
                "picpflicht" => "",
                "picinfo" => _tutorials_pic_info,
                "schwierigkeit" => _tutorials_schwierigkeit,
                "schwierigkeit_lazy" => "",
                "schwierigkeit1" => _tutorials_schwierigkeit_sehr_leicht,
                "schwierigkeit2" => _tutorials_schwierigkeit_leicht,
                "schwierigkeit3" => _tutorials_schwierigkeit_mittel,
                "schwierigkeit4" => _tutorials_schwierigkeit_schwer,
                "schwierigkeit5" => _tutorials_schwierigkeit_sehr_schwer,
                "beschreibung" => _tutorials_beschreibung,
                "pflicht" => _tutorials_pflichtfeld,
                "v_kategorie" => re($getkat['name']),
                "moveto" => $moveto,
                "v_bezeichnung" => re($_POST['bezeichnung']),
                "v_pos_lazy" => _tutorials_position_lazy,
                "v_pos_first" => _tutorials_position_first,
                "v_positions" => $positions,
                "v_pic" => $pic,
                "v_schwierigkeit1" => $v_schwierigkeit1,
                "v_schwierigkeit2" => $v_schwierigkeit2,
                "v_schwierigkeit3" => $v_schwierigkeit3,
                "v_schwierigkeit4" => $v_schwierigkeit4,
                "v_schwierigkeit5" => $v_schwierigkeit5,
                "v_beschreibung" => re_bbcode($_POST['beschreibung']),
                "v_tutorial" => re_bbcode($_POST['tutorial'])));
        } else {
            if ($_POST['position'] != "lazy") {
                if ($_POST['position'] == 1 || $_POST['position'] == 2) $sign = ">= ";
                else $sign = "> ";

                $posi = db("UPDATE " . $sql_prefix . "tutorials
																		SET `pos` = pos+1
																		WHERE `pos` " . $sign . " '" . intval($_POST['position']) . "'");
                $newpos = "`pos` = '" . ((int)$_POST['position']) . "',";
            }

            if ($_FILES['pic']['name'] != "") {
                $pfad = basePath . "/inc/images/uploads/tutorials/" . $_POST['id'];
                @unlink($pfad . ".gif");
                @unlink($pfad . ".jpg");
                @unlink($pfad . ".png");

                $endung = explode(".", $_FILES['pic']['name']);
                $endung = strtolower($endung[count($endung) - 1]);
                copy($_FILES['pic']['tmp_name'], basePath . "/inc/images/uploads/tutorials/" . $_POST['id'] . "." . strtolower($endung) . "");
                @unlink($_FILES['pic']['tmp_name']);
            }

            $qry = db("UPDATE " . $sql_prefix . "tutorials
															SET `name` = '" . up($_POST['bezeichnung']) . "',
																			" . $newpos . "               																			
																			`beschreibung` = '" . up($_POST['beschreibung'], 1) . "',
																			`tutorial` = '" . up($_POST['tutorial'], 1) . "',
																			`schwierigkeit` = '" . ((int)$_POST['schwierigkeit']) . "'
															WHERE `id` = '" . intval($_POST['id']) . "'");

            $show = info(_tutorials_tutorial_edited, "?admin=tutorials&amp;do=tut&amp;kat=" . $_POST['kat'] . "");
        }
        break;

    case 'tut_delete':

## >Added in 1.1 - START
        $qry = db("DELETE FROM " . $sql_prefix . "tutorials_comments
													WHERE `tutorial` = '" . (int)$_GET['id'] . "'");
## >Added in 1.1 - END

        $qry = db("DELETE FROM " . $sql_prefix . "tutorials
													WHERE `id` = '" . (int)$_GET['id'] . "'");
        @unlink(basePath . "/inc/images/uploads/tutorials/" . $_GET['id'] . ".gif");
        @unlink(basePath . "/inc/images/uploads/tutorials/" . $_GET['id'] . ".jpg");
        @unlink(basePath . "/inc/images/uploads/tutorials/" . $_GET['id'] . ".png");

        $show = info(_tutorials_tutorial_deleted, "?admin=tutorials&amp;do=tut&amp;kat=" . $_GET['kat'] . "");
        break;

    case 'tut_moveto':
        if ($_GET['moveto'] == "lazy") {
            $show = error(_tutorials_error_moveto_lazy, 1);
        } else {
            $qry = db("UPDATE " . $sql_prefix . "tutorials
															SET `kat` = '" . intval($_GET['moveto']) . "'
															WHERE `id` = '" . (int)$_GET['id'] . "'");
            $qry = db("SELECT `name` FROM " . $sql_prefix . "tutorials_kats
															WHERE `id` = '" . intval($_GET['moveto']) . "'");
            $get = _fetch($qry);

            $text = show(_tutorials_tutorial_moved, array("kat" => $get['name']));
            $show = info($text, "?admin=tutorials&amp;do=tut&amp;kat=" . $_GET['kat'] . "");
        }
        break;
    case 'settings':
        if (isset($_POST['submit'])) {
            $qry = db("UPDATE " . $sql_prefix . "tutorials_settings
															SET `pic_pflicht`    = '" . ((int)$_POST['pic_pflicht']) . "',    
																			`katpic_pflicht` = '" . ((int)$_POST['katpic_pflicht']) . "', 
																			`maxtutorials`   = '" . ((int)$_POST['maxtutorials']) . "',  
																			`max_comments`   = '" . ((int)$_POST['max_comments']) . "',  
																			`reg_comments`   = '" . ((int)$_POST['reg_comments']) . "',  
																			`float_comments` = '" . ((int)$_POST['float_comments']) . "'");

            $show = info(_tutorials_settings_done, "?admin=tutorials&amp;do=settings");
        } else {
            $qry = db("SELECT * FROM " . $sql_prefix . "tutorials_settings");
            $get = _fetch($qry);

            if ($get['pic_pflicht'] == 1) $sel_tut_picpflicht = "selected=\"selected\"";
            if ($get['katpic_pflicht'] == 1) $sel_tut_katpicpflicht = "selected=\"selected\"";
            if ($get['reg_comments'] == 1) $sel_tut_regcomments = "selected=\"selected\"";

            $show = show($dir . "/tutorials_settings", array("head" => _tutorials . " - " . _tutorials_settings,
                "value" => _tutorials_save,
                "tutorials_info" => _tutorials_info,
                "ja" => _tutorials_ja,
                "nein" => _tutorials_nein,
                "katpic_pflicht" => _tutorials_katpic_pflicht,
                "pic_pflicht" => _tutorials_pic_pflicht,
                "max_tutorials" => _tutorials_max_tutorials,
                "max_comments" => _tutorials_max_comments,
                "reg_comments" => _tutorials_reg_comments,
                "float_comments" => _tutorials_float_comments,
                "v_katpic_pflicht" => $sel_tut_katpicpflicht,
                "v_pic_pflicht" => $sel_tut_picpflicht,
                "v_max_tutorials" => $get['maxtutorials'],
                "v_max_comments" => $get['max_comments'],
                "v_reg_comments" => $sel_tut_regcomments,
                "v_float_comments" => $get['float_comments']));
        }
    break;
endswitch;