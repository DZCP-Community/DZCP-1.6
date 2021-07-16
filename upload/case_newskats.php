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
    if(common::permission('news') || common::permission('artikel')) {
        switch (common::$do) {
            case 'upload':
                $tmpname = $_FILES['file']['tmp_name'];
                $name = $_FILES['file']['name'];
                $type = $_FILES['file']['type'];
                $size = $_FILES['file']['size'];

                if(!$tmpname)
                    $index = common::error(_upload_no_data, 1);
                else if($size > settings::get('upicsize')."000")
                    $index = common::error(_upload_wrong_size, 1);
                else {
                    if(move_uploaded_file($tmpname, basePath."/inc/images/uploads/newskat/".$_FILES['file']['name'])) {
                        if(isset($_GET['edit']))
                            $index = common::info(_info_upload_success, "../admin/?admin=news&amp;do=edit&amp;id=".$_GET['edit']."");
                        else
                            $index = common::info(_info_upload_success, "../admin/?admin=news&amp;do=add");
                    }
                    else
                        $index = common::error(_upload_error, 1);
                }
            break;
            default:
                if(isset($_GET['edit']))
                    $action = "?action=newskats&amp;do=upload&edit=".$_GET['edit']."";
                else
                    $action = "?action=newskats&amp;do=upload";

                $smarty->caching = false;
                $smarty->assign('userpicsize', settings::get('upicsize'));
                $infos = $smarty->fetch('string:' . _upload_usergallery_info);
                $smarty->clearAllAssign();

                $smarty->caching = false;
                $smarty->assign('uploadhead', _upload_newskats_head);
                $smarty->assign('name', 'file');
                $smarty->assign('action', $action);
                $smarty->assign('infos', $infos);
                $index = $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/upload.tpl');
                $smarty->clearAllAssign();
            break;
        }
    }
}