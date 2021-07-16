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
$where = $where.': '._artikel;

switch(common::$do) {
    case 'add':
        $qryk = common::$sql['default']->select("SELECT `id`,`kategorie` FROM `{prefix_news_kats}`;"); $kat = '';
        foreach($qryk as $getk) {
            $kat .= common::select_field($getk['id'],false,stringParser::decode($getk['kategorie']));
        }

        $smarty->caching = false;
        $smarty->assign('head',_artikel_add);
        $smarty->assign('autor',common::autor(common::$userid));
        $smarty->assign('kat',$kat);
        $smarty->assign('do','insert');
        $smarty->assign('error','');
        $smarty->assign('titel','');
        $smarty->assign('artikeltext','');
        $smarty->assign('link1','');
        $smarty->assign('link2','');
        $smarty->assign('link3','');
        $smarty->assign('url1','');
        $smarty->assign('url2','');
        $smarty->assign('url3','');
        $smarty->assign('button',_button_value_add);
        $smarty->assign('n_artikelpic','');
        $smarty->assign('delartikelpic','');
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel_form.tpl');
        $smarty->clearAllAssign();
    break;
    case 'insert':
        if(empty($_POST['titel']) || empty($_POST['artikel'])) {
            $error = _empty_artikel;
            if(empty($_POST['titel']))
                $error = _empty_artikel_title;

            $qryk = common::$sql['default']->select("SELECT `id`,`kategorie` FROM `{prefix_news_kats}`;"); $kat = '';
            foreach($qryk as $getk) {
                $sel = ($_POST['kat'] == $getk['id'] ? 'selected="selected"' : '');
                $kat .= common::select_field($getk['id'],($_POST['kat'] == $getk['id']),stringParser::decode($getk['kategorie']));
            }

            $smarty->caching = false;
            $smarty->assign('error',$error);
            $error = $smarty->fetch('file:['.common::$tmpdir.']errors/errortable.tpl');
            $smarty->clearAllAssign();


            $smarty->caching = false;
            $smarty->assign('head',_artikel_add);
            $smarty->assign('autor',common::autor(common::$userid));
            $smarty->assign('kat',$kat);
            $smarty->assign('do',"insert");
            $smarty->assign('titel',stringParser::decode($_POST['titel']));
            $smarty->assign('artikeltext',stringParser::decode($_POST['artikel']));
            $smarty->assign('link1',stringParser::decode($_POST['link1']));
            $smarty->assign('link2',stringParser::decode($_POST['link2']));
            $smarty->assign('link3',stringParser::decode($_POST['link3']));
            $smarty->assign('url1',$_POST['url1']);
            $smarty->assign('url2',$_POST['url2']);
            $smarty->assign('url3',$_POST['url3']);
            $smarty->assign('button',_button_value_add);
            $smarty->assign('error',$error);
            $smarty->assign('n_artikelpic','');
            $smarty->assign('delartikelpic','');
            $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel_form.tpl');
            $smarty->clearAllAssign();
        } else {
            if(isset($_POST)) {
                common::$sql['default']->insert("INSERT INTO `{prefix_artikel}` SET `autor` = ?, `kat` = ?, `titel` = ?, `text` = ?, "
                            ."`link1`  = ?, `link2`  = ?, `link3`  = ?, `url1`   = ?, `url2`   = ?, `url3`   = ?;",
                [(int)(common::$userid),(int)($_POST['kat']),stringParser::encode($_POST['titel']),stringParser::encode($_POST['artikel']),stringParser::encode($_POST['link1']),
                        stringParser::encode($_POST['link2']),stringParser::encode($_POST['link3']),stringParser::encode(common::links($_POST['url1'])),stringParser::encode(common::links($_POST['url2'])),
                    stringParser::encode(common::links($_POST['url3']))]);

                if(isset($_FILES['artikelpic']['tmp_name']) && !empty($_FILES['artikelpic']['tmp_name'])) {
                    $endung = explode(".", $_FILES['artikelpic']['name']);
                    $endung = strtolower($endung[count($endung)-1]);
                    move_uploaded_file($_FILES['artikelpic']['tmp_name'], basePath."/inc/images/uploads/artikel/".common::$sql['default']->lastInsertId().".".strtolower($endung));
                }
            }
            
            $show = common::info(_artikel_added, "?admin=artikel");
        }
    break;
    case 'edit':
        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_artikel}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        $qryk = common::$sql['default']->select("SELECT `id`,`kategorie` FROM `{prefix_news_kats}`;"); $kat = '';
        foreach($qryk as $getk) {
            $kat .= common::select_field($getk['id'],($get['kat'] == $getk['id']),stringParser::decode($getk['kategorie']));
        }

        $artikelimage = ""; $delartikelpic = "";
        foreach(common::SUPPORTED_PICTURE as $tmpendung) {
            if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung)) {
                $artikelimage = common::img_size('inc/images/uploads/artikel/'.(int)($_GET['id']).'.'.$tmpendung)."<br /><br />";
                $delartikelpic = '<a href="?admin=artikel&do=delartikelpic&id='.$_GET['id'].'">'._artikelpic_del.'</a><br /><br />';
            }
        }

        $smarty->caching = false;
        $smarty->assign('head',_artikel_edit);
        $smarty->assign('nautor',_autor);
        $smarty->assign('autor',common::autor(common::$userid));
        $smarty->assign('nkat',_news_admin_kat);
        $smarty->assign('preview',_preview);
        $smarty->assign('kat',$kat);
        $smarty->assign('do','editartikel&amp;id='.$_GET['id']);
        $smarty->assign('ntitel',_titel);
        $smarty->assign('titel',stringParser::decode($get['titel']));
        $smarty->assign('artikeltext',stringParser::decode($get['text']));
        $smarty->assign('link1',stringParser::decode($get['link1']));
        $smarty->assign('link2',stringParser::decode($get['link2']));
        $smarty->assign('link3',stringParser::decode($get['link3']));
        $smarty->assign('url1',stringParser::decode($get['url1']));
        $smarty->assign('url2',stringParser::decode($get['url2']));
        $smarty->assign('url3',stringParser::decode($get['url3']));
        $smarty->assign('ntext',_eintrag);
        $smarty->assign('error','');
        $smarty->assign('button',_button_value_edit);
        $smarty->assign('linkname',_linkname);
        $smarty->assign('aimage',_artikel_userimage);
        $smarty->assign('n_artikelpic',$artikelimage);
        $smarty->assign('delartikelpic',$delartikelpic);
        $smarty->assign('nurl',_url);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/artikel_form.tpl');
        $smarty->clearAllAssign();
    break;
    case 'editartikel':
        if(isset($_POST)) {
            common::$sql['default']->update("UPDATE `{prefix_artikel}` SET `kat` = ?, `titel` = ?, `text` = ?, `link1` = ?, "
            . "`link2` = ?, `link3` = ?, `url1` = ?, `url2` = ?, `url3` = ? WHERE `id` = ?;",
            [(int)($_POST['kat']),stringParser::encode($_POST['titel']),stringParser::encode($_POST['artikel']),stringParser::encode($_POST['link1']),
                stringParser::encode($_POST['link2']),stringParser::encode($_POST['link3']),stringParser::encode(common::links($_POST['url1'])),
                stringParser::encode(common::links($_POST['url2'])),stringParser::encode(common::links($_POST['url3'])),(int)($_GET['id'])]);

            if(isset($_FILES['artikelpic']['tmp_name']) && !empty($_FILES['artikelpic']['tmp_name'])) {
                foreach(common::SUPPORTED_PICTURE as $tmpendung) {
                    if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung))
                        @unlink(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung);
                }

                //Remove minimize
                $files = common::get_files(basePath."/inc/images/uploads/artikel/",false,true, common::SUPPORTED_PICTURE);
                if($files) {
                    foreach ($files as $file) {
                        if(preg_match("#".(int)($_GET['id'])."(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                            $res = preg_match("#".(int)($_GET['id'])."_(.*)#",$file,$match);
                            if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id'])."_".$match[1]))
                                @unlink(basePath."/inc/images/uploads/artikel/".(int)($_GET['id'])."_".$match[1]);
                        }
                    }
                }

                $endung = explode(".", $_FILES['artikelpic']['name']);
                $endung = strtolower($endung[count($endung)-1]);
                move_uploaded_file($_FILES['artikelpic']['tmp_name'], basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".strtolower($endung));
            }

            $show = common::info(_artikel_edited, "?admin=artikel");
        }
    break;
    case 'delete':
        common::$sql['default']->delete("DELETE FROM `{prefix_artikel}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        common::$sql['default']->delete("DELETE FROM `{prefix_artikel_comments}` WHERE `artikel` = ?;", [(int)($_GET['id'])]);

        //Remove Pic
        foreach(common::SUPPORTED_PICTURE as $tmpendung) {
            if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung))
                @unlink(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung);
        }

        //Remove minimize
        $files = common::get_files(basePath."/inc/images/uploads/artikel/",false,true, common::SUPPORTED_PICTURE);
        if($files) {
            foreach ($files as $file) {
                if(preg_match("#".(int)($_GET['id'])."(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                    $res = preg_match("#".(int)($_GET['id'])."_(.*)#",$file,$match);
                    if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id'])."_".$match[1]))
                        @unlink(basePath."/inc/images/uploads/artikel/".(int)($_GET['id'])."_".$match[1]);
                }
            }
        }

        $show = common::info(_artikel_deleted, "?admin=artikel");
    break;
    case 'delartikelpic':
        //Remove Pic
        foreach(common::SUPPORTED_PICTURE as $tmpendung) {
            if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung))
                @unlink(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung);
        }

        //Remove minimize
        $files = common::get_files(basePath."/inc/images/uploads/artikel/",false,true, common::SUPPORTED_PICTURE);
        if($files) {
            foreach ($files as $file) {
                if(preg_match("#".(int)($_GET['id'])."(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                    $res = preg_match("#".(int)($_GET['id'])."_(.*)#",$file,$match);
                    if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id'])."_".$match[1]))
                        @unlink(basePath."/inc/images/uploads/artikel/".(int)($_GET['id'])."_".$match[1]);
                }
            }
        }

        $show = common::info(_newspic_deleted, "?admin=artikel&do=edit&id=".(int)($_GET['id'])."");
    break;
    case 'public':
        if(isset($_GET['what']) && $_GET['what'] == 'set')
            common::$sql['default']->update("UPDATE `{prefix_artikel}` SET `public` = 1, `datum`  = ? WHERE `id` = ?", [time(),(int)($_GET['id'])]);
        else
            common::$sql['default']->update("UPDATE `{prefix_artikel}` SET `public` = 0 WHERE `id` = ?;", [(int)($_GET['id'])]);

        header("Location: ?admin=artikel");
    break;
    default:
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_artikel}` ".common::orderby_sql(["titel","datum","autor"],'ORDER BY `public` ASC, `datum` DESC')." LIMIT ".(common::$page - 1)*settings::get('m_adminartikel').",".settings::get('m_adminartikel').";");
        foreach($qry as $get) {
            /** @var TYPE_NAME $admin */
            $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_artikel);

            $smarty->caching = false;
            $smarty->assign('titel',common::cut(stringParser::decode($get['titel']),settings::get('l_newsadmin')));
            $smarty->assign('id',$get['id']);
            $titel = $smarty->fetch('string:'._artikel_show_link);
            $smarty->clearAllAssign();

            $public = ($get['public'] ? '<a href="?admin=artikel&amp;do=public&amp;id='.$get['id'].'&amp;what=unset"><img src="../inc/images/public.gif" alt="" title="'._non_public.'" /></a>'
                    : '<a href="?admin=artikel&amp;do=public&amp;id='.$get['id'].'&amp;what=set"><img src="../inc/images/nonpublic.gif" alt="" title="'._public.'" /></a>');

            $datum = empty($get['datum']) ? _no_public : date("d.m.y H:i", $get['datum'])._uhr;
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;

            $smarty->caching = false;
            $smarty->assign('date',$datum);
            $smarty->assign('titel',$titel);
            $smarty->assign('class',$class);
            $smarty->assign('autor',common::autor($get['autor']));
            $smarty->assign('intnews','');
            $smarty->assign('sticky','');
            $smarty->assign('id',$get['id']);
            $smarty->assign('public',$public);
            $smarty->assign('edit',$edit);
            $smarty->assign('delete',$delete);
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/admin_show.tpl');
            $smarty->clearAllAssign();
        }

        if(empty($show))
            $show = '<tr><td colspan="6" class="contentMainSecond">'._no_entrys.'</td></tr>';

        $entrys = common::cnt('{prefix_artikel}');
        $nav = common::nav($entrys,settings::get('m_adminnews'),"?admin=artikel".(isset($_GET['show']) ? $_GET['show'] : '').common::orderby_nav());
        $smarty->caching = false;
        $smarty->assign('head',_artikel);
        $smarty->assign('nav',$nav);
        $smarty->assign('order_autor',common::orderby('autor'));
        $smarty->assign('order_date',common::orderby('datum'));
        $smarty->assign('order_titel',common::orderby('titel'));
        $smarty->assign('show',$show);
        $smarty->assign('val',"artikel");
        $smarty->assign('add',_artikel_add);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/admin_news.tpl');
        $smarty->clearAllAssign();




    break;
}