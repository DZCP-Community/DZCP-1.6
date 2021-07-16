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
    if(common::permission('partners')) {
        if(common::$do == "upload") {
            $tmpname = $_FILES['file']['tmp_name'];
            $name = $_FILES['file']['name'];
            $type = $_FILES['file']['type'];
            $size = $_FILES['file']['size'];

            if(!$tmpname)
                $index = common::error(_upload_no_data, 1);
            else {
                if(move_uploaded_file($tmpname, basePath."/banner/partners/".$_FILES['file']['name']))
                    $index = common::info(_info_upload_success, "../admin/?admin=partners&amp;do=add");
                else
                    $index = common::error(_upload_error, 1);
            }
        }

        if(empty($index)) {
            $smarty->caching = false;
            $smarty->assign('userpicsize', settings::get('upicsize'));
            $infos = $smarty->fetch('string:' . _upload_partners_info);
            $smarty->clearAllAssign();

            $smarty->caching = false;
            $smarty->assign('uploadhead', _upload_partners_head);
            $smarty->assign('name', 'file');
            $smarty->assign('action', '?action=partners&amp;do=upload');
            $smarty->assign('infos', $infos);
            $index = $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/upload.tpl');
            $smarty->clearAllAssign();
        }
    }
}