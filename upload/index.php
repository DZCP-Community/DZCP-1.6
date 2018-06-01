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
    default:
        $index = error(_error_wrong_permissions, 1);
endswitch;

## INDEX OUTPUT ##
page($index, $title, $where);