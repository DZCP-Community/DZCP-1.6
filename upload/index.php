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

## SETTINGS ##
$where = _site_upload;
$title = $pagetitle." - ".$where."";
$dir = "upload";
$index = '';
$extensions = array(IMAGETYPE_GIF => "gif",
                    IMAGETYPE_JPEG => "jpg",
                    IMAGETYPE_PNG => "png");

## SECTIONS ##
switch ($action):
    case 'squads':
        if(permission("editsquads")) {
            $set_action = isset($_GET['id']) ? "&amp;edit=1&amp;id=".$_GET['id'] : "";
            $infos = show(_upload_usergallery_info, array("userpicsize" => config('upicsize')));
            $index = show($dir."/upload", array("uploadhead" => _upload_icons_head,
                                                "file" => _upload_file,
                                                "name" => "file",
                                                "action" => "?action=squads&amp;do=upload".$set_action,
                                                "upload" => _button_value_upload,
                                                "info" => _upload_info,
                                                "infos" => $infos));

            if($do == "upload") {
                $tmpname = $_FILES['file']['tmp_name'];
                if(!$tmpname) {
                    $index = error(_upload_no_data, 1);
                } else {
                    $file_info = getimagesize($tmpname);
                    if(!$file_info) {
                        $index = error(_upload_error, 1);
                    } else {
                        $file_info['width']  = $file_info[0];
                        $file_info['height'] = $file_info[1];
                        $file_info['mime']   = $file_info[2];
                        unset($file_info[3],$file_info['bits'],$file_info['channels'],
                            $file_info[0],$file_info[1],$file_info[2]);

                        if(!array_key_exists($file_info['mime'], $extensions)) {
                           $error = show(_upload_usergallery_info, array('userpicsize' => config('upicsize')));
                           $index = error($error, 1);
                        } else {
                            if($_FILES['file']['size'] > (config('upicsize')*1000)) {
                                $index = error(_upload_wrong_size, 1);
                            } else {
                                if(!move_uploaded_file($tmpname, basePath."/inc/images/gameicons/".$_FILES['file']['name'])) {
                                    $index = error(_upload_error, 1);
                                } else {
                                    $link_to = isset($_GET['edit']) && isset($_GET['id']) ? "edit&id=".$_GET['id'] : "add";
                                    $index = info(_info_upload_success, "../admin/?admin=squads&amp;do=".$link_to);
                                }
                            }
                        }
                    }
                }
            }
        }
        else
            $index = error(_error_wrong_permissions, 1);
    break;
    case 'servericons':
        if(permission("editserver")) {
            $set_action = isset($_GET['id']) ? "&amp;edit=1&amp;id=".$_GET['id'] : "";
            $infos = show(_upload_usergallery_info, array("userpicsize" => config('upicsize')));
            $index = show($dir."/upload", array("uploadhead" => _upload_icons_head,
                                                "file" => _upload_file,
                                                "name" => "file",
                                                "action" => "?action=servericons&amp;do=upload".$set_action,
                                                "upload" => _button_value_upload,
                                                "info" => _upload_info,
                                                "infos" => $infos));

            if($do == "upload") {
                $tmpname = $_FILES['file']['tmp_name'];
                if(!$tmpname) {
                    $index = error(_upload_no_data, 1);
                } else {
                    $file_info = getimagesize($tmpname);
                    if(!$file_info) {
                        $index = error(_upload_error, 1);
                    } else {
                        $file_info['width']  = $file_info[0];
                        $file_info['height'] = $file_info[1];
                        $file_info['mime']   = $file_info[2];
                        unset($file_info[3],$file_info['bits'],$file_info['channels'],
                            $file_info[0],$file_info[1],$file_info[2]);

                        if(!array_key_exists($file_info['mime'], $extensions)) {
                           $error = show(_upload_usergallery_info, array('userpicsize' => config('upicsize')));
                           $index = error($error, 1);
                        } else {
                            if($_FILES['file']['size'] > (config('upicsize')*1000)) {
                                $index = error(_upload_wrong_size, 1);
                            } else {
                                if(!move_uploaded_file($tmpname, basePath."/inc/images/gameicons/".$_FILES['file']['name'])) {
                                    $index = error(_upload_error, 1);
                                } else {
                                    $link_to = isset($_GET['edit']) && isset($_GET['id']) ? "edit&id=".$_GET['id'] : "new";
                                    $index = info(_info_upload_success, "../admin/?admin=server&amp;do=".$link_to);
                                }
                            }
                        }
                    }
                }
            }
        } else
            $index = error(_error_wrong_permissions, 1);
        break;
    case 'partners';
        if(permission('partners')) {
            $infos = show(_upload_partners_info, array("userpicsize" => config('upicsize')));
            $index = show($dir."/upload", array("uploadhead" => _upload_partners_head,
                                                "file" => _upload_file,
                                                "name" => "file",
                                                "action" => "?action=partners&amp;do=upload",
                                                "upload" => _button_value_upload,
                                                "info" => _upload_info,
                                                "infos" => $infos));
            if($do == "upload") {
                $tmpname = $_FILES['file']['tmp_name'];
                if(!$tmpname) {
                    $index = error(_upload_no_data, 1);
                } else {
                    $file_info = getimagesize($tmpname);
                    if(!$file_info) {
                        $index = error(_upload_error, 1);
                    } else {
                        $file_info['width']  = $file_info[0];
                        $file_info['height'] = $file_info[1];
                        $file_info['mime']   = $file_info[2];
                        unset($file_info[3],$file_info['bits'],$file_info['channels'],
                            $file_info[0],$file_info[1],$file_info[2]);

                        if(!array_key_exists($file_info['mime'], $extensions)) {
                           $error = show(_upload_usergallery_info, array('userpicsize' => config('upicsize')));
                           $index = error($error, 1);
                        } else {
                            if($_FILES['file']['size'] > (config('upicsize')*1000)) {
                                $index = error(_upload_wrong_size, 1);
                            } else {
                                if(!move_uploaded_file($tmpname, basePath."/banner/partners/".$_FILES['file']['name'])) {
                                    $index = error(_upload_error, 1);
                                } else {
                                    $index = info(_info_upload_success, "../admin/?admin=partners&amp;do=add");
                                }
                            }
                        }
                    }
                }
            }
        } else
            $index = error(_error_wrong_permissions, 1);
    break;
    case 'newskats';
        if(permission('news') || permission('artikel')) {
            if(isset($_GET['edit']))
                $action = "?action=newskats&amp;do=upload&edit=".$_GET['edit']."";
            else
                $action = "?action=newskats&amp;do=upload";

            $infos = show(_upload_usergallery_info, array("userpicsize" => config('upicsize')));
            $index = show($dir."/upload", array("uploadhead" => _upload_newskats_head,
                                                "file" => _upload_file,
                                                "name" => "file",
                                                "action" => $action,
                                                "upload" => _button_value_upload,
                                                "info" => _upload_info,
                                                "infos" => "-"));

            if($do == "upload") {
                $tmpname = $_FILES['file']['tmp_name'];
                if(!$tmpname) {
                    $index = error(_upload_no_data, 1);
                } else {
                    $file_info = getimagesize($tmpname);
                    if(!$file_info) {
                        $index = error(_upload_error, 1);
                    } else {
                        $file_info['width']  = $file_info[0];
                        $file_info['height'] = $file_info[1];
                        $file_info['mime']   = $file_info[2];
                        unset($file_info[3],$file_info['bits'],$file_info['channels'],
                            $file_info[0],$file_info[1],$file_info[2]);

                        if(!array_key_exists($file_info['mime'], $extensions)) {
                           $error = show(_upload_usergallery_info, array('userpicsize' => config('upicsize')));
                           $index = error($error, 1);
                        } else {
                            if($_FILES['file']['size'] > (config('upicsize')*1000)) {
                                $index = error(_upload_wrong_size, 1);
                            } else {
                                if(!move_uploaded_file($tmpname, basePath."/inc/images/newskat/".$_FILES['file']['name'])) {
                                    $index = error(_upload_error, 1);
                                } else {
                                    if(isset($_GET['edit'])) {
                                        $index = info(_info_upload_success, "../admin/?admin=news&amp;do=edit&amp;id=".$_GET['edit']."");
                                    } else {
                                        $index = info(_info_upload_success, "../admin/?admin=news&amp;do=add");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else
            $index = error(_error_wrong_permissions, 1);
    break;
    case 'taktiken';
        if(permission("edittactics")) {
            $infos = show(_upload_usergallery_info, array("userpicsize" => 100));
            $index = show($dir."/upload", array("uploadhead" => _upload_taktiken_head,
                                                "file" => _upload_file,
                                                "name" => "file",
                                                "action" => "?action=taktiken&amp;do=upload",
                                                "upload" => _button_value_upload,
                                                "info" => _upload_info,
                                                "infos" => $infos));

        if($do == "upload") {
            $tmpname = $_FILES['file']['tmp_name'];
            if(!$tmpname) {
                $index = error(_upload_no_data, 1);
            } else {
                $file_info = getimagesize($tmpname);
                if(!$file_info) {
                    $index = error(_upload_error, 1);
                } else {
                    $file_info['width']  = $file_info[0];
                    $file_info['height'] = $file_info[1];
                    $file_info['mime']   = $file_info[2];
                    unset($file_info[3],$file_info['bits'],$file_info['channels'],
                        $file_info[0],$file_info[1],$file_info[2]);

                    if(!array_key_exists($file_info['mime'], $extensions)) {
                       $error = show(_upload_usergallery_info, array('userpicsize' => config('upicsize')));
                       $index = error($error, 1);
                    } else {
                        if($_FILES['file']['size'] > (config('upicsize')*1000)) {
                            $index = error(_upload_wrong_size, 1);
                        } else {
                            if(!move_uploaded_file($tmpname, basePath."/inc/images/uploads/taktiken/".$_FILES['file']['name'])) {
                                $index = error(_upload_error, 1);
                            } else {
                                $index = info(_info_upload_success, "../taktik/");
                            }
                        }
                    }
                }
            }
        }
    } else
        $index = error(_error_wrong_permissions, 1);
    break;
    case 'userpic';
        if($chkMe >= 1 && $userid) {
            $infos = show(_upload_userpic_info, array("userpicsize" => config('upicsize')));
            $index = show($dir."/upload", array("uploadhead" => _upload_head,
                                                "file" => _upload_file,
                                                "name" => "file",
                                                "action" => "?action=userpic&amp;do=upload",
                                                "upload" => _button_value_upload,
                                                "info" => _upload_info,
                                                "infos" => $infos));

            switch($do) {
                case 'upload':
                    $tmpname = $_FILES['file']['tmp_name'];
                    if(!$tmpname) {
                        $index = error(_upload_no_data, 1);
                    } else {
                        $file_info = getimagesize($tmpname);
                        if(!$file_info) {
                            $index = error(_upload_error, 1);
                        } else {
                            $file_info['width']  = $file_info[0];
                            $file_info['height'] = $file_info[1];
                            $file_info['mime']   = $file_info[2];
                            unset($file_info[3],$file_info['bits'],$file_info['channels'],
                                $file_info[0],$file_info[1],$file_info[2]);

                            if(!array_key_exists($file_info['mime'], $extensions)) {
                               $error = show(_upload_usergallery_info, array('userpicsize' => config('upicsize')));
                               $index = error($error, 1);
                            } else {
                                if($_FILES['file']['size'] > (config('upicsize')*1000)) {
                                    $index = error(_upload_wrong_size, 1);
                                } else {
                                    foreach($picformat as $tmpendung) {
                                        if(file_exists(basePath."/inc/images/uploads/userpics/".$userid.".".$tmpendung)) {
                                            @unlink(basePath."/inc/images/uploads/userpics/".$userid.".".$tmpendung);
                                        }
                                    }

                                    if(!move_uploaded_file($tmpname, basePath."/inc/images/uploads/userpics/".$userid.".".$extensions[$file_info['mime']])) {
                                        $index = error(_upload_error, 1);
                                    } else {
                                        $index = info(_info_upload_success, "../user/?action=editprofile");
                                    }
                                }
                            }
                        }
                    }
                break;
                case 'deletepic':
                    foreach($picformat as $tmpendung) {
                        if(file_exists(basePath."/inc/images/uploads/userpics/".$userid.".".$tmpendung))
                            @unlink(basePath."/inc/images/uploads/userpics/".$userid.".".$tmpendung);
                    }

                    $index = info(_delete_pic_successful, "../user/?action=editprofile");
                break;
            }
        } else
            $index = error(_error_wrong_permissions, 1);
    break;
    case 'avatar';
        if($chkMe >= 1) {
            $infos = show(_upload_userava_info, array("userpicsize" => config('upicsize')));
            $index = show($dir."/upload", array("uploadhead" => _upload_ava_head,
                                                "file" => _upload_file,
                                                "name" => "file",
                                                "action" => "?action=avatar&amp;do=upload",
                                                "upload" => _button_value_upload,
                                                "info" => _upload_info,
                                                "infos" => $infos));

            switch ($do) {
                case 'upload':
                    $tmpname = $_FILES['file']['tmp_name'];
                    if(!$tmpname) {
                        $index = error(_upload_no_data, 1);
                    } else {
                        $file_info = getimagesize($tmpname);
                        if(!$file_info) {
                            $index = error(_upload_error, 1);
                        } else {
                            $file_info['width']  = $file_info[0];
                            $file_info['height'] = $file_info[1];
                            $file_info['mime']   = $file_info[2];
                            unset($file_info[3],$file_info['bits'],$file_info['channels'],
                                $file_info[0],$file_info[1],$file_info[2]);

                            if(!array_key_exists($file_info['mime'], $extensions)) {
                               $error = show(_upload_usergallery_info, array('userpicsize' => config('upicsize')));
                               $index = error($error, 1);
                            } else {
                                if($_FILES['file']['size'] > (config('upicsize')*1000)) {
                                    $index = error(_upload_wrong_size, 1);
                                } else {
                                    foreach($picformat as $tmpendung) {
                                        if(file_exists(basePath."/inc/images/uploads/useravatare/".$userid.".".$tmpendung)) {
                                            @unlink(basePath."/inc/images/uploads/useravatare/".$userid.".".$tmpendung);
                                        }
                                    }

                                    if(!move_uploaded_file($tmpname, basePath."/inc/images/uploads/useravatare/".$userid.".".$extensions[$file_info['mime']])) {
                                        $index = error(_upload_error, 1);
                                    } else {
                                        $index = info(_info_upload_success, "../user/?action=editprofile");
                                    }
                                }
                            }
                        }
                    }
                break;
                case 'delete':
                    foreach($picformat as $tmpendung) {
                        if(file_exists(basePath."/inc/images/uploads/useravatare/".$userid.".".$tmpendung))
                            @unlink(basePath."/inc/images/uploads/useravatare/".$userid.".".$tmpendung);
                    }

                    $index = info(_delete_pic_successful, "../user/?action=editprofile");
                break;
            }
        } else
            $index = error(_error_wrong_permissions, 1);
    break;
    case 'usergallery';
        if($chkMe >= 1) {
            $infos = show(_upload_usergallery_info, array("userpicsize" => config('upicsize')));
            $index = show($dir."/usergallery", array("uploadhead" => _upload_head_usergallery,
                                                     "file" => _upload_file,
                                                     "name" => "file",
                                                     "upload" => _button_value_upload,
                                                     "beschreibung" => _upload_beschreibung,
                                                     "info" => _upload_info,
                                                     "infos" => $infos));

            switch ($do) {
                case 'upload':
                    $tmpname = $_FILES['file']['tmp_name'];
                    if(!$tmpname) {
                        $index = error(_upload_no_data, 1);
                    } else {
                        if(cnt($db['usergallery'], " WHERE user = ".$userid) == config('m_gallerypics')) {
                            $index = error(_upload_over_limit, 2);
                        } else {
                            $file_info = getimagesize($tmpname);
                            if(!$file_info) {
                                $index = error(_upload_error, 1);
                            } else {
                                $file_info['width']  = $file_info[0];
                                $file_info['height'] = $file_info[1];
                                $file_info['mime']   = $file_info[2];
                                unset($file_info[3],$file_info['bits'],$file_info['channels'],
                                    $file_info[0],$file_info[1],$file_info[2]);

                                if(!array_key_exists($file_info['mime'], $extensions)) {
                                   $error = show(_upload_usergallery_info, array('userpicsize' => config('upicsize')));
                                   $index = error($error, 1);
                                } else {
                                    if($_FILES['file']['size'] > (config('upicsize')*1000)) {
                                        $index = error(_upload_wrong_size, 1);
                                    } else {
                                        if(file_exists(basePath."/inc/images/uploads/usergallery/".$userid."_".strtolower($_FILES['file']['name']))) {
                                            $index = error(_upload_file_exists, 1);
                                        } else {
                                            if(!move_uploaded_file($tmpname, basePath."/inc/images/uploads/usergallery/".$userid."_".strtolower($_FILES['file']['name']))) {
                                                $index = error(_upload_error, 1);
                                            } else {
                                                db("INSERT INTO ".$db['usergallery']." "
                                                        . "SET `user` = '".((int)$userid)."', "
                                                        . "`beschreibung` = '".up($_POST['beschreibung'],1)."', "
                                                        . "`pic` = '".up(strtolower($_FILES['file']['name']))."'");

                                                $index = info(_info_upload_success, "../user/?action=editprofile&show=gallery");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                break;
                case 'edit':
                    $get = db("SELECT id,user,pic,beschreibung FROM ".$db['usergallery']." WHERE id = '".intval($_GET['gid'])."'",false,true);
                    if($get['user'] == $userid) {
                        $infos = show(_upload_usergallery_info, array("userpicsize" => config('upicsize')));
                        $index = show($dir."/usergallery_edit", array("uploadhead" => _upload_head_usergallery,
                                                                      "file" => _upload_file,
                                                                      "showpic" => img_size("inc/images/uploads/usergallery/".$get['user']."_".$get['pic']),
                                                                      "id" => $get['id'],
                                                                      "showbeschreibung" => re($get['beschreibung']),
                                                                      "name" => "file",
                                                                      "upload" => _button_value_edit,
                                                                      "beschreibung" => _upload_beschreibung,
                                                                      "info" => _upload_info,
                                                                      "infos" => $infos));
                    }
                    else
                        $index = error(_error_wrong_permissions, 1);
                break;
                case 'editfile':
                    $get = db("SELECT pic FROM ".$db['usergallery']." WHERE id = ".intval($_POST['id']),false,true);
                    if(!empty($_FILES['file']['size'])) {
                        if(file_exists(basePath."/inc/images/uploads/usergallery/".$userid."_".$get['pic']))
                            @unlink(basePath."/inc/images/uploads/usergallery/".$userid."_".$get['pic']);

                        @unlink(show(_gallery_edit_unlink, array("img" => $get['pic'], "user" => $userid)));
                        $tmpname = $_FILES['file']['tmp_name'];
                        if(!$tmpname) {
                            $index = error(_upload_no_data, 1);
                        } else {
                            $file_info = getimagesize($tmpname);
                            if(!$file_info) {
                                $index = error(_upload_error, 1);
                            } else {
                                $file_info['width']  = $file_info[0];
                                $file_info['height'] = $file_info[1];
                                $file_info['mime']   = $file_info[2];
                                unset($file_info[3],$file_info['bits'],$file_info['channels'],
                                    $file_info[0],$file_info[1],$file_info[2]);

                                if(!array_key_exists($file_info['mime'], $extensions)) {
                                   $error = show(_upload_usergallery_info, array('userpicsize' => config('upicsize')));
                                   $index = error($error, 1);
                                } else {
                                    if($_FILES['file']['size'] > (config('upicsize')*1000)) {
                                        $index = error(_upload_wrong_size, 1);
                                    } else {
                                        if(!move_uploaded_file($tmpname, basePath."/inc/images/uploads/usergallery/".$userid."_".strtolower($_FILES['file']['name']))) {
                                            $index = error(_upload_error, 1);
                                        } else {
                                            db("UPDATE ".$db['usergallery']." SET "
                                                    . "`pic` = '".$_FILES['file']['name']."',"
                                                    . "`beschreibung` = '".up($_POST['beschreibung'],1)."' "
                                                    . "WHERE id = '".intval($_POST['id'])."' AND `user` = '".((int)$userid)."'");
                                            
                                            $index = info(_edit_gallery_done, "../user/?action=editprofile&show=gallery");
                                        }
                                    }
                                }
                            }
                        }
                    }
                break;
            }
        } else
            $index = error(_error_wrong_permissions, 1);
    break;
    default:
        $index = error(_error_wrong_permissions, 1);
endswitch;

## INDEX OUTPUT ##
page($index, $title, $where);