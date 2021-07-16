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
 * Copyright 2020 © CodeKing, my-STARMEDIA, Codedesigns
 */

if (!defined('_Downloads')) exit();

$_SESSION['dl_id'] = 0;
if(array_key_exists('id',$_GET) && isset($_GET['id'])) {
    $_SESSION['dl_id'] = (int)($_GET['id']);
}

if(settings::get("reg_dl") && !common::$chkMe)
    $index = common::error(_error_unregistered);
else if($_SESSION['dl_id'] >= 1) {
    $download = common::$server->getDownload(['id'=>$_SESSION['dl_id']]);

    if(!$download->isError() && !$download->isIntern() || ($download->isIntern() && common::checkme())) {
        $pic = '';
        foreach(common::SUPPORTED_PICTURE as $tmpendung) {
            if(file_exists(rootPath."/static/images/downloads/dl_".$download->getId().".".$tmpendung)) {
                $pic .= '<li data-thumb="https://static.dzcp.de/thumbgen.php?width=25&height=18&img=images/downloads/dl_'.$download->getId().'.'.
                    $tmpendung.'"><img src="https://static.dzcp.de/images/downloads/dl_'.$download->getId().'.'.$tmpendung.'" /></li>';
            }

            for ($i = 1; $i <= 6; $i++) {
                if(file_exists(rootPath."/static/images/downloads/dl_".$download->getId()."_".$i.".".$tmpendung)) {
                    $pic .= '<li data-thumb="https://static.dzcp.de/thumbgen.php?width=25&height=18&img=images/downloads/dl_'.$download->getId().'_'.$i.'.'.
                        $tmpendung.'"><img src="https://static.dzcp.de/images/downloads/dl_'.$download->getId().'_'.$i.'.'.$tmpendung.'" /></li>';
                }
            }
        }

        if(empty($pic)) {
            $pic = '<li data-thumb="../thumbgen.php?width=100&height=71&img='.common::getTplImgDir().'/downloads/nodl.jpg" ><img src="../'.common::getTplImgDir().'/downloads/nodl.jpg" /></li>';
        }

        $smarty->caching = false;
        $smarty->assign('date',date("d.m.Y H:i",$download->getTime())._uhr);
        $smarty->assign('id',$download->getId());
        $smarty->assign('kat',stringParser::decode($download->getCategoryName()));
        $smarty->assign('titel',stringParser::decode($download->getName()));
        $smarty->assign('hits',$download->getStats()->getDownloads());
        $smarty->assign('size',common::parser_filesize($download->getStats()->getSize()));
        $smarty->assign('desc',BBCode::parse_html($download->getDescription()));
        $smarty->assign('updated',date("d.m.Y H:i",$download->getUpdated())._uhr);
        $smarty->assign('forum_url',$download->getForumUrl());
        $smarty->assign('has_forum_url',$download->hasForumUrl());
        $smarty->assign('file',stringParser::decode($download->getFile()));
        $smarty->assign('crc',stringParser::decode($download->getCrc()));
        $smarty->assign('pic',$pic);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/info.tpl');
        $smarty->clearAllAssign();

        $qry = common::$server->getDlCategorys(1200); $kats = '';
        if(count($qry->getCategorys()) && !$qry->isError()) {
            foreach($qry->getCategorys() as $get) {
                /*
                 * SubKats
                 */
                $subkats = '';
                foreach($get->getSubCategorys() as $get_subkats) {
                    $smarty->cache_lifetime = 1200;
                    $smarty->assign('subkat',stringParser::decode($get_subkats['name']));
                    $smarty->assign('kid',stringParser::decode($get->getId()));
                    $smarty->assign('skid',stringParser::decode($get_subkats['id']));
                    $subkats .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/download_subkats.tpl');
                    $smarty->clearAllAssign();
                }

                /*
                 * Kats
                 */
                if(!empty($subkats)) {
                    $smarty->cache_lifetime = 1200;
                    $smarty->assign('kat', stringParser::decode($get->getName()));
                    $smarty->assign('subkats', $subkats);
                    $kats .= $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/download_kats.tpl');
                    $smarty->clearAllAssign();
                }
            }
        }

        $smarty->caching = false;
        $smarty->assign('kats',$kats);
        $smarty->assign('show',$show);
        $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/downloads_more.tpl');
        $smarty->clearAllAssign();
    } else if(!$download->isError() && $download->isIntern()) {
        $index = common::error(_error_no_access);
    } else {
        $index = common::error(_id_dont_exist,1);
    }
} else {
    $index = common::error(_id_dont_exist,1);
}