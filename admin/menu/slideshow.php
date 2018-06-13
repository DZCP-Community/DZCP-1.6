<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;

$where = $where.': '._slider; $entry = '';
$qry = db("SELECT * FROM ".$db['slideshow']." ORDER BY `pos` ASC");
while($get = _fetch($qry)) {
    $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
    $edit = show("page/button_edit_single", array("id" => $get['id'],
        "action" => "admin=slideshow&amp;do=edit",
        "title" => _button_title_edit));

    $delete = show("page/button_delete_single", array("id" => $get['id'],
        "action" => "admin=slideshow&amp;do=delete",
        "title" => _button_title_del,
        "del" => convSpace(_slider_admin_del)));

    $entry .= show($dir."/slideshow_show", array("id" => $get['id'],
        "class" => $class,
        "bez" => $get['bez'],
        "edit" => $edit,
        "del" => $delete));
}

if(empty($entry)) {
    $entry = show(_no_entrys_yet, array("colspan" => "0"));
}

$show = show($dir."/slideshow", array("head" => _slider,
    "bezeichnung" => _slider_bezeichnung,
    "add" => _slider_admin_add,
    "show" => $entry));

if($do == 'new'){
    $qry = db("SELECT * FROM ".$db['slideshow']." ORDER BY `pos` ASC;"); $positions = '';
    while($get = _fetch($qry)) {
        $positions .= show(_select_field, array("value" => (int)$get['pos']+1, "what" => _nach.': '.re($get['bez']), "sel" => ""));
    }

    $show = show($dir."/slideshow_form", array(
        "id" => "",
        "error" => "",
        "do" => "add",
        "head" => _slider_admin_add,
        "value" => _button_value_add,
        "ja" => _yes,
        "nein" => _no,
        "bezeichnung" => _slider_bezeichnung,
        "desc" => _slider_desc,
        "tdesc" => '',
        "t_zeichen" => _zeichen,
        "noch" => _noch,
        "url" => _slider_url,
        "new_window" => _slider_new_window,
        "pic" => _slider_pic,
        "position" => _slider_position,
        "first" => _slider_position_first,
        "v_bezeichnung" => "",
        "v_pos_none" => "",
        "v_position" => $positions,
        "v_url" => "http://",
        "selected0" => "",
        "selected1" => "",
        "selected_txt" => 'selected="selected"',
        "v_pic" => ""));
}elseif($do == 'add'){
    if(empty($_FILES['bild']['tmp_name']) || empty($_POST['bez']) || empty($_POST['url']) || $_POST['url'] == "http://") {
        if(!$_FILES['bild']['tmp_name']) $error = _slider_admin_error_nopic;
        elseif(empty($_POST['bez'])) $error = _slider_admin_error_empty_bezeichnung;
        elseif(empty($_POST['url']) OR $_POST['url'] == "http://") $error = _slider_admin_error_empty_url;

        $error = show("errors/errortable", array("error" => $error));

        if($_POST['target'] == 1) $selected = 'selected="selected"';
        if($get['showbez'] == 1) $selected_txt = 'selected="selected"';

        $qry = db("SELECT * FROM ".$db['slideshow']."
                  ORDER BY `pos` ASC;");
        while($get = _fetch($qry)) {
            $positions .= show(_select_field, array("value" => $get['pos']+1,
                "what" => _nach.': '.$get['bez'],
                "sel" => ""));
        }

        $show = show($dir."/slideshow_form", array("id" => "",
            "error" => $error,
            "do" => "add",
            "head" => _slider_admin_add,
            "value" => _button_value_add,
            "ja" => _yes,
            "nein" => _no,
            "bezeichnung" => _slider_bezeichnung,
            "desc" => _slider_desc,
            "tdesc" => re($_POST['desc']),
            "t_zeichen" => _zeichen,
            "noch" => _noch,
            "url" => _slider_url,
            "new_window" => _slider_new_window,
            "pic" => _slider_pic,
            "position" => _slider_position,
            "first" => _slider_position_first,
            "v_bezeichnung" => re($_POST['bez'],true),
            "v_pos_none" => "",
            "v_position" => $positions,
            "v_url" => re($_POST['url']),
            "selected" => $selected,
            "selected_txt" => $selected_txt,
            "v_pic" => ""));
    } else {
        if($_POST['position'] == "1" || "2") $sign = ">= ";
        else  $sign = "> ";

        db("UPDATE ".$db['slideshow']." SET `pos` = pos+1 WHERE `pos` ".$sign." '".(int)($_POST['position'])."'");
        db("INSERT INTO ".$db['slideshow']."
                   SET `pos` = '".((int)$_POST['position'])."',
                       `bez` = '".up($_POST['bez'])."',
                       `showbez` = '".((int)($_POST['showbez']))."',
                       `desc` = '".up($_POST['desc'])."',
                       `url` = '".up($_POST['url'])."',
                       `target` = '".up($_POST['target'])."'");

        $tmpname = $_FILES['bild']['tmp_name'];
        if (($info = getimagesize($tmpname)) === FALSE) {
            $show = info("Unable to determine image type of uploaded file", "?admin=slideshow");
        } else {
            if (($info[2] === IMAGETYPE_GIF) && ($info[2] === IMAGETYPE_JPEG) && ($info[2] === IMAGETYPE_PNG)) {
                switch ($info[2]) {
                    case IMAGETYPE_GIF:
                        $ext = '.gif';
                        break;
                    default:
                    case IMAGETYPE_JPEG:
                        $ext = '.jpg';
                        break;
                    case IMAGETYPE_PNG:
                        $ext = '.png';
                        break;
                }

                move_uploaded_file($tmpname, basePath."/inc/images/slideshow/".mysqli_insert_id($mysql).".".$ext);
            } else {
                $show = info("Not a IMAGE", "?admin=slideshow");
            }
        }

        if(empty($show))
            $show = info(_slider_admin_add_done, "?admin=slideshow");
    }
}elseif($do == 'edit'){
    $qrypos = db("SELECT * FROM ".$db['slideshow']." WHERE `id` != '".(int)($_GET['id'])."' ORDER BY `pos` ASC");
    $positions = '';
    while($getpos = _fetch($qrypos)) {
        $positions .= show(_select_field, array("value" => $getpos['pos']+1, "what" => _nach.': '.re($getpos['bez']), "sel" => ""));
    }

    $get = db("SELECT * FROM ".$db['slideshow']." WHERE `id` = '".(int)($_GET['id'])."'",false,true);
    if($get['target'])
        $selected = 'selected="selected"';

    if($get['showbez'])
        $selected_txt = 'selected="selected"';

    $show = show($dir."/slideshow_form", array("id" => re($get['id']),
        "error" => "",
        "do" => "editdo",
        "head" => _slider_admin_edit,
        "value" => _button_value_edit,
        "ja" => _yes,
        "nein" => _no,
        "bezeichnung" => _slider_bezeichnung,
        "desc" => _slider_desc,
        "tdesc" => re($get['desc']),
        "t_zeichen" => _zeichen,
        "noch" => _noch,
        "url" => _slider_url,
        "new_window" => _slider_new_window,
        "pic" => _slider_pic,
        "position" => _slider_position,
        "first" => _slider_position_first,
        "v_bezeichnung" => re($get['bez']),
        "v_pos_none" => _slider_position_lazy,
        "v_position" => $positions,
        "v_url" => re($get['url']),
        "selected" => $selected,
        "selected_txt" => $selected_txt,
        "v_pic" => img_size('inc/images/slideshow/'.$get['id'].'.jpg')."<br />"));
}elseif($do == 'editdo'){
    if(empty($_POST['bez']) || empty($_POST['url']) || $_POST['url'] == "http://") {
        if(empty($_POST['bez'])) $error = _slider_admin_error_empty_bezeichnung;
        elseif(empty($_POST['url']) OR $_POST['url'] == "http://") $error = _slider_admin_error_empty_url;

        $error = show("errors/errortable", array("error" => $error));

        if($_POST['target'])
            $selected = 'selected="selected"';

        if($get['showbez'])
            $selected_txt = 'selected="selected"';

        $show = show($dir."/slideshow_form", array("id" => re($_POST['id']),
            "error" => $error,
            "do" => "editdo",
            "head" => _slider_admin_edit,
            "value" => _button_value_edit,
            "ja" => _yes,
            "nein" => _no,
            "bezeichnung" => _slider_bezeichnung,
            "desc" => _slider_desc,
            "tdesc" => re($_POST['desc'],true),
            "t_zeichen" => _zeichen,
            "noch" => _noch,
            "url" => _slider_url,
            "new_window" => _slider_new_window,
            "pic" => _slider_pic,
            "position" => _slider_position,
            "first" => _slider_position_first,
            "v_bezeichnung" => re($_POST['bez'],true),
            "v_pos_none" => _slider_position_lazy,
            "v_position" => $positions,
            "v_url" => re($_POST['url']),
            "selected" => $selected,
            "selected_txt" => $selected_txt,
            "v_pic" => img_size('inc/images/slideshow/'.$_POST['id'].'.jpg')."<br />"));
    } else {
        $pos = "";
        if($_POST['position'] != "lazy") {
            if($_POST['position'] == "1" || "2") $sign = ">= ";
            else  $sign = "> ";

            db("UPDATE ".$db['slideshow']." SET `pos` = (pos+1) WHERE `pos` ".$sign." '".(int)($_POST['position'])."'");

            $pos = "`pos` = ".((int)$_POST['position']).",";
        }

        db("UPDATE ".$db['slideshow']." SET ".$pos." `bez` = '".up($_POST['bez'])."', `showbez` = ".((int)($_POST['showbez'])).
            ", `url` = '".up($_POST['url'])."', `desc` = '".up($_POST['desc'])."', `target` = '".up($_POST['target']).
            "' WHERE `id` = ".(int)($_POST['id']).";");

        if($_FILES['bild']['tmp_name']) {
            $tmpname = $_FILES['bild']['tmp_name'];
            foreach($picformat as $end) {
                if(file_exists(basePath."/inc/images/slideshow/".(int)($_GET['id']).".".$end)) {
                    @unlink(basePath . "/inc/images/slideshow/".(int)($_GET['id']).".".$end);
                }
            }

            if (($info = getimagesize($tmpname)) === FALSE) {
                $show = info("Unable to determine image type of uploaded file", "?admin=slideshow");
            } else {
                if (($info[2] === IMAGETYPE_GIF) && ($info[2] === IMAGETYPE_JPEG) && ($info[2] === IMAGETYPE_PNG)) {
                    switch ($info[2]) {
                        case IMAGETYPE_GIF:
                            $ext = '.gif';
                            break;
                        default:
                        case IMAGETYPE_JPEG:
                            $ext = '.jpg';
                            break;
                        case IMAGETYPE_PNG:
                            $ext = '.png';
                            break;
                    }

                    move_uploaded_file($tmpname, basePath."/inc/images/slideshow/".(int)($_POST['id']).".".$ext);
                } else {
                    $show = info("Not a IMAGE", "?admin=slideshow");
                }
            }
        }

        if(empty($show))
            $show = info(_slider_admin_edit_done, "?admin=slideshow");
    }
}elseif($do == 'delete'){
    db("DELETE FROM `".$db['slideshow']."` WHERE `id` = ".(int)($_GET['id']).";");

    foreach($picformat as $end) {
        if(file_exists(basePath."/inc/images/slideshow/".(int)($_GET['id']).".".$end)) {
            @unlink(basePath . "/inc/images/slideshow/".(int)($_GET['id']).".".$end);
        }
    }

    $show = info(_slider_admin_del_done, "?admin=slideshow");
}