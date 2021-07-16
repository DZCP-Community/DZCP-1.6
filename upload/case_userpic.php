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

if(defined('_Upload')) {
    if(common::$chkMe >= 1 && common::$userid) {
        switch(common::$do) {
            case 'upload':
                $tmpname = $_FILES['file']['tmp_name'];
                if(!$tmpname) {
                    $index = common::error(_upload_no_data, 1);
                } else {
                    $file_info = getimagesize($tmpname);
                    if(!$file_info) {
                        $index = common::error(_upload_error, 1);
                    } else {
                        $file_info['width']  = $file_info[0];
                        $file_info['height'] = $file_info[1];
                        $file_info['mime']   = $file_info[2];
                        unset($file_info[3],$file_info['bits'],$file_info['channels'],
                            $file_info[0],$file_info[1],$file_info[2]);

                        if(!array_key_exists($file_info['mime'], config::$extensions)) {
                            $smarty->caching = false;
                            $smarty->assign('userpicsize',settings::get('upicsize'));
                            $error = $smarty->fetch('string:'._upload_userpic_info);
                            $smarty->clearAllAssign();

                            $index = common::error($error, 1);
                        } else {
                            if($_FILES['file']['size'] > (settings::get('upicsize')*1000)) {
                                $index = common::error(_upload_wrong_size, 1);
                            } else {
                                foreach(common::SUPPORTED_PICTURE as $tmpendung) {
                                    if(file_exists(basePath."/inc/images/uploads/userpics/".common::$userid.".".$tmpendung))
                                        unlink(basePath."/inc/images/uploads/userpics/".common::$userid.".".$tmpendung);
                                }
                                
                                if(!move_uploaded_file($tmpname, basePath."/inc/images/uploads/userpics/".common::$userid.".".config::$extensions[$file_info['mime']])) {
                                    $index = common::error(_upload_error, 1);
                                } else {
                                    $index = common::info(_info_upload_success, "../user/?action=editprofile", 5,false);
                                }
                            }
                        }
                    }
                }
            break;
            case 'deletepic':
                foreach(common::SUPPORTED_PICTURE as $tmpendung) {
                    if(file_exists(basePath."/inc/images/uploads/userpics/".common::$userid.".".$tmpendung))
                        unlink(basePath."/inc/images/uploads/userpics/".common::$userid.".".$tmpendung);
                }

                $index = common::info(_delete_pic_successful, "../user/?action=editprofile");
            break;
            default:
                $smarty->caching = false;
                $smarty->assign('userpicsize',settings::get('upicsize'));
                $infos = $smarty->fetch('string:'._upload_userpic_info);
                $smarty->clearAllAssign();

                $smarty->caching = false;
                $smarty->assign('uploadhead',_upload_head);
                $smarty->assign('name',"file");
                $smarty->assign('action',"?action=userpic&amp;do=upload");
                $smarty->assign('infos',$infos);
                $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/upload.tpl');
                $smarty->clearAllAssign();
            break;
        }
    }
}