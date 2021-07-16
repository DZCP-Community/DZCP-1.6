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

if(_adminMenu != 'true') exit;
$where = $where.': '._config_newskats_edit_head;

switch(common::$do) {
    case 'delete':
        $get = common::$sql['default']->fetch("SELECT `id`,`katimg` FROM `{prefix_news_kats}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        if(common::$sql['default']->rowCount()) {
            if(file_exists(basePath."/inc/images/uploads/newskat/".stringParser::decode($get['katimg']))) {
                unlink(basePath."/inc/images/uploads/newskat/".stringParser::decode($get['katimg']));
            }
            common::$sql['default']->delete("DELETE FROM `{prefix_news_kats}` WHERE `id` = ?;", [(int)($get['id'])]);
            $show = common::info(_config_newskat_deleted, "?admin=news");
        }
    break;
    case 'add':
        $files = common::get_files(basePath.'/inc/images/uploads/newskat/',false,true); $img = "";
        for($i=0; $i<count($files); $i++) {
            $img .= common::select_field($files[$i],false,$files[$i]);
        }

        $smarty->caching = false;
        $smarty->assign('head',_config_newskats_add_head);
        $smarty->assign('kat','');
        $smarty->assign('value',_button_value_add);
        $smarty->assign('nothing','');
        $smarty->assign('do',"addnewskat");
        $smarty->assign('upload',_config_neskats_katbild_upload);
        $smarty->assign('img',$img);
        $smarty->assign('color','#ffcc00');
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/newskatform.tpl');
        $smarty->clearAllAssign();
    break;
    case 'addnewskat':
        if(empty($_POST['kat'])) {
            $show = common::error(_config_empty_katname,1);
        } else {
            common::$sql['default']->insert("INSERT INTO `{prefix_news_kats}` SET `color` = ?, `katimg` = ?, `kategorie` = ?;",
                    [stringParser::encode($_POST['color']),stringParser::encode($_POST['img']),stringParser::encode($_POST['kat'])]);
            $show = common::info(_config_newskats_added, "?admin=news");
        }
    break;
    case 'edit':
        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_news_kats}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        $files = common::get_files(basePath.'/inc/images/uploads/newskat/',false,true); $img = '';
        for($i=0; $i<count($files); $i++) {
            $img .= common::select_field($files[$i],($get['katimg'] == $files[$i]),$files[$i]);
        }

        $smarty->caching = false;
        $smarty->assign('id',$_GET['id']);
        $upload = $smarty->fetch('string:'._config_neskats_katbild_upload_edit);
        $smarty->clearAllAssign();

        $smarty->caching = false;
        $smarty->assign('head',_config_newskats_edit_head);
        $smarty->assign('kat',stringParser::decode($get['kategorie']));
        $smarty->assign('value',_button_value_edit);
        $smarty->assign('id',(int)($_GET['id']));
        $smarty->assign('do','editnewskat&amp;id='.$_GET['id']);
        $smarty->assign('upload',$upload);
        $smarty->assign('img',$img);
        $smarty->assign('color',$get['color']);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/newskatform.tpl');
        $smarty->clearAllAssign();
    break;
    case 'editnewskat':
        if(empty($_POST['kat'])) {
            $show = common::error(_config_empty_katname,1);
        } else {
            $katimg = ($_POST['img'] == "lazy" ? "" : "`katimg` = '".stringParser::encode($_POST['img'])."',");
            common::$sql['default']->update("UPDATE `{prefix_news_kats}` SET `color` = ?, ".$katimg." `kategorie` = ? WHERE id = ?;",
                    [stringParser::encode($_POST['color']),stringParser::encode($_POST['kat']),(int)($_GET['id'])]);

            //Clear News Cache
            $qry = common::$sql['default']->select("SELECT `id` FROM `{prefix_news_kats}` WHERE `kategorie` = ?;",[(int)($_GET['id'])]);
            foreach($qry as $get) {
                $smarty->clearCache('file:['.common::$tmpdir.']'.$dir.'/news_show.tpl', common::getSmartyCacheHash('news_'.$get['id']));
            }

            $show = common::info(_config_newskats_edited, "?admin=news");
        }
    break;
    default:
        $qry = common::$sql['default']->select("SELECT `id`,`katimg`,`kategorie` FROM `{prefix_news_kats}` ORDER BY `kategorie`;"); $kats = '';
        foreach($qry as $get) {
            $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_kat);
            $smarty->caching = false;
            $smarty->assign('img',stringParser::decode($get['katimg']));
            $img = $smarty->fetch('string:'._config_newskats_img);
            $smarty->clearAllAssign();
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;

            $smarty->caching = false;
            $smarty->assign('mainkat',stringParser::decode($get['kategorie']));
            $smarty->assign('class',$class);
            $smarty->assign('img',$img);
            $smarty->assign('delete',$delete);
            $smarty->assign('edit',$edit);
            $kats .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/newskats_show.tpl');
            $smarty->clearAllAssign();
        }

        $smarty->caching = false;
        $smarty->assign('kats',$kats);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/newskats.tpl');
        $smarty->clearAllAssign();
    break;
}