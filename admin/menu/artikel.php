<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._artikel;
$wysiwyg = '_word';

switch($do) {
    case 'add':
        $qryk = db("SELECT id,kategorie FROM ".$db['newskat'].""); $kat = '';
        while($getk = _fetch($qryk)) {
            $kat .= show(_select_field, array("value" => $getk['id'],
                                              "sel" => "",
                                              "what" => re($getk['kategorie'])));
        }

        $show = show($dir."/artikel_form", array("head" => _artikel_add,
                                                 "nautor" => _autor,
                                                 "autor" => autor($userid),
                                                 "nkat" => _news_admin_kat,
                                                 "kat" => $kat,
                                                 "preview" => _preview,
                                                 "ntitel" => _titel,
                                                 "do" => "insert",
                                                 "ntext" => _eintrag,
                                                 "error" => "",
                                                 "titel" => "",
                                                 "artikeltext" => "",
                                                 "link1" => "",
                                                 "link2" => "",
                                                 "link3" => "",
                                                 "url1" => "",
                                                 "url2" => "",
                                                 "url3" => "",
                                                 "button" => _button_value_add,
                                                 "nmore" => _news_admin_more,
                                                 "linkname" => _linkname,
                                                 "aimage" => _artikel_userimage,
                                                 "n_artikelpic" => '',
                                                 "delartikelpic" => '',
                                                 "nurl" => _url));
    break;
    case 'insert':
        if(empty($_POST['titel']) || empty($_POST['artikel'])) {
            $error = _empty_artikel;
            if(empty($_POST['titel']))
                $error = _empty_artikel_title;

            $qryk = db("SELECT id,kategorie FROM ".$db['newskat'].""); $kat = '';
            while($getk = _fetch($qryk)) {
                $sel = ((int)$_POST['kat'] == $getk['id'] ? 'selected="selected"' : '');
                $kat .= show(_select_field, array("value" => $getk['id'],
                                                  "sel" => $sel,
                                                  "what" => $getk['kategorie']));
            }

            $error = show("errors/errortable", array("error" => $error));
            $show = show($dir."/artikel_form", array("head" => _artikel_add,
                                                     "nautor" => _autor,
                                                     "autor" => autor($userid),
                                                     "nkat" => _news_admin_kat,
                                                     "kat" => $kat,
                                                     "preview" => _preview,
                                                     "do" => "insert",
                                                     "ntitel" => _titel,
                                                     "titel" => re($_POST['titel']),
                                                     "artikeltext" => re_bbcode(re($_POST['artikel'],true)),
                                                     "link1" => re($_POST['link1']),
                                                     "link2" => re($_POST['link2']),
                                                     "link3" => re($_POST['link3']),
                                                     "url1" => links(re($_POST['url1'],true)),
                                                     "url2" => links(re($_POST['url2'],true)),
                                                     "url3" => links(re($_POST['url3'],true)),
                                                     "ntext" => _eintrag,
                                                     "button" => _button_value_add,
                                                     "error" => $error,
                                                     "nmore" => _news_admin_more,
                                                     "linkname" => _linkname,
                                                     "aimage" => _artikel_userimage,
                                                     "n_artikelpic" => '',
                                                     "delartikelpic" => '',
                                                     "nurl" => _url));
        } else {
            if(isset($_POST)) {
                db("INSERT INTO `".$db['artikel']."`
                    SET `autor`  = ".((int)$userid).",
                        `kat`    = ".((int)$_POST['kat']).",
                        `titel`  = '".up($_POST['titel'])."',
                        `text`   = '".up($_POST['artikel'],true)."',
                        `link1`  = '".up($_POST['link1'])."',
                        `link2`  = '".up($_POST['link2'])."',
                        `link3`  = '".up($_POST['link3'])."',
                        `url1`   = '".up(links(re($_POST['url1'],true)))."',
                        `url2`   = '".up(links(re($_POST['url2'],true)))."',
                        `url3`   = '".up(links(re($_POST['url3'],true)))."';");

                if(isset($_FILES['artikelpic']['tmp_name']) && !empty($_FILES['artikelpic']['tmp_name'])) {
                    $endung = explode(".", $_FILES['artikelpic']['name']);
                    $endung = strtolower($endung[count($endung)-1]);
                    move_uploaded_file($_FILES['artikelpic']['tmp_name'], basePath."/inc/images/uploads/artikel/".mysqli_insert_id($mysql).".".strtolower($endung));
                }
            }
            $show = info(_artikel_added, "?admin=artikel");
        }
    break;
    case 'edit':
        $get = db("SELECT * FROM ".$db['artikel']." WHERE id = '".(int)($_GET['id'])."'",false,true);
        $qryk = db("SELECT id,kategorie FROM ".$db['newskat'].""); $kat = '';
        while($getk = _fetch($qryk)) {
            $sel = ($get['kat'] == $getk['id'] ? 'selected="selected"' : '');
            $kat .= show(_select_field, array("value" => $getk['id'],
                                              "sel" => $sel,
                                              "what" => re($getk['kategorie'])));
        }

        $artikelimage = ""; $delartikelpic = "";
        foreach($picformat as $tmpendung) {
            if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung)) {
                $artikelimage = img_size('inc/images/uploads/artikel/'.(int)($_GET['id']).'.'.$tmpendung)."<br /><br />";
                $delartikelpic = '<a href="?admin=artikel&do=delartikelpic&id='.$_GET['id'].'">'._artikelpic_del.'</a><br /><br />';
            }
        }

        $do = show(_artikel_edit_link, array("id" => $_GET['id']));
        $show = show($dir."/artikel_form", array("head" => _artikel_edit,
                                                 "nautor" => _autor,
                                                 "autor" => autor($userid),
                                                 "nkat" => _news_admin_kat,
                                                 "preview" => _preview,
                                                 "kat" => $kat,
                                                 "do" => $do,
                                                 "ntitel" => _titel,
                                                 "titel" => re($get['titel']),
                                                 "artikeltext" => re_bbcode(re($get['text'])),
                                                 "link1" => re($get['link1']),
                                                 "link2" => re($get['link2']),
                                                 "link3" => re($get['link3']),
                                                 "url1" => $get['url1'],
                                                 "url2" => $get['url2'],
                                                 "url3" => $get['url3'],
                                                 "ntext" => _eintrag,
                                                 "error" => "",
                                                 "button" => _button_value_edit,
                                                 "linkname" => _linkname,
                                                 "aimage" => _artikel_userimage,
                                                 "n_artikelpic" => $artikelimage,
                                                 "delartikelpic" => $delartikelpic,
                                                 "nurl" => _url));
    break;
    case 'editartikel':
        if(isset($_POST)) {
            db("UPDATE ".$db['artikel']."
                SET `kat`    = ".((int)$_POST['kat']).",
                    `titel`  = '".up($_POST['titel'])."',
                    `text`   = '".up($_POST['artikel'])."',
                    `link1`  = '".up($_POST['link1'])."',
                    `link2`  = '".up($_POST['link2'])."',
                    `link3`  = '".up($_POST['link3'])."',
                    `url1`   = '".up(links(re($_POST['url1'],true)))."',
                    `url2`   = '".up(links(re($_POST['url2'],true)))."',
                    `url3`   = '".up(links(re($_POST['url3'],true)))."'
                WHERE `id` = ".(int)($_GET['id']).";");

            if(isset($_FILES['artikelpic']['tmp_name']) && !empty($_FILES['artikelpic']['tmp_name'])) {
                foreach($picformat as $tmpendung) {
                    if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung))
                        @unlink(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung);
                }

                //Remove minimize
                $files = get_files(basePath."/inc/images/uploads/artikel/",false,true,$picformat);
                foreach ($files as $file) {
                    if(preg_match("#".(int)($_GET['id'])."(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                        $res = preg_match("#".(int)($_GET['id'])."_(.*)#",$file,$match);
                        if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id'])."_".$match[1]))
                            @unlink(basePath."/inc/images/uploads/artikel/".(int)($_GET['id'])."_".$match[1]);
                    }
                }

                $endung = explode(".", $_FILES['artikelpic']['name']);
                $endung = strtolower($endung[count($endung)-1]);
                move_uploaded_file($_FILES['artikelpic']['tmp_name'], basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".strtolower($endung));
            }

            $show = info(_artikel_edited, "?admin=artikel");
        }
    break;
    case 'delete':
        db("DELETE FROM ".$db['artikel']." WHERE id = '".(int)($_GET['id'])."'");
        db("DELETE FROM ".$db['acomments']." WHERE artikel = '".(int)($_GET['id'])."'");

        //Remove Pic
        foreach($picformat as $tmpendung) {
            if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung))
                @unlink(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung);
        }

        //Remove minimize
        $files = get_files(basePath."/inc/images/uploads/artikel/",false,true,$picformat);
        foreach ($files as $file) {
            if(preg_match("#".(int)($_GET['id'])."(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                $res = preg_match("#".(int)($_GET['id'])."_(.*)#",$file,$match);
                if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id'])."_".$match[1]))
                    @unlink(basePath."/inc/images/uploads/artikel/".(int)($_GET['id'])."_".$match[1]);
            }
        }

        $show = info(_artikel_deleted, "?admin=artikel");
    break;
    case 'delartikelpic':
        //Remove Pic
        foreach($picformat as $tmpendung) {
            if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung))
                @unlink(basePath."/inc/images/uploads/artikel/".(int)($_GET['id']).".".$tmpendung);
        }

        //Remove minimize
        $files = get_files(basePath."/inc/images/uploads/artikel/",false,true,$picformat);
        foreach ($files as $file) {
            if(preg_match("#".(int)($_GET['id'])."(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                $res = preg_match("#".(int)($_GET['id'])."_(.*)#",$file,$match);
                if(file_exists(basePath."/inc/images/uploads/artikel/".(int)($_GET['id'])."_".$match[1]))
                    @unlink(basePath."/inc/images/uploads/artikel/".(int)($_GET['id'])."_".$match[1]);
            }
        }

        $show = info(_newspic_deleted, "?admin=artikel&do=edit&id=".(int)($_GET['id'])."");
    break;
    case 'public':
        if(isset($_GET['what']) && $_GET['what'] == 'set')
            db("UPDATE ".$db['artikel']." SET `public` = '1', `datum`  = '".time()."' WHERE id = '".(int)($_GET['id'])."'");
        else
            db("UPDATE ".$db['artikel']." SET `public` = '0' WHERE id = '".(int)($_GET['id'])."'");

        header("Location: ?admin=artikel");
    break;
    default:
        $entrys = cnt($db['artikel']);
        $qry = db("SELECT * FROM ".$db['artikel']."
                  ".orderby_sql(array("titel","datum","autor"),'ORDER BY `public` ASC, `datum` DESC')."
                  LIMIT ".($page - 1)*config('m_adminartikel').",".config('m_adminartikel')."");
        while($get = _fetch($qry))
        {
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=artikel&amp;do=edit",
                                                          "title" => _button_title_edit));

            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=artikel&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => convSpace(_confirm_del_artikel)));

            $titel = show(_artikel_show_link, array("titel" => re(cut($get['titel'],config('l_newsadmin'),true,false)), "id" => $get['id']));

            $public = ($get['public'] ? '<a href="?admin=artikel&amp;do=public&amp;id='.$get['id'].'&amp;what=unset"><img src="../inc/images/public.gif" alt="" title="'._non_public.'" /></a>'
                    : '<a href="?admin=artikel&amp;do=public&amp;id='.$get['id'].'&amp;what=set"><img src="../inc/images/nonpublic.gif" alt="" title="'._public.'" /></a>');

            $datum = empty($get['datum']) ? _no_public : date("d.m.y H:i", $get['datum'])._uhr;
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/admin_show", array("date" => $datum,
                                                    "titel" => $titel,
                                                    "class" => $class,
                                                    "autor" => autor($get['autor']),
                                                    "intnews" => "",
                                                    "sticky" => "",
                                                    "public" => $public,
                                                    "edit" => $edit,
                                                    "delete" => $delete));
        }

        $nav = nav($entrys,config('m_adminnews'),"?admin=artikel".(isset($_GET['show']) ? $_GET['show'] : '').orderby_nav());
        $show = show($dir."/admin_news", array("head" => _artikel,
                                               "nav" => $nav,
                                               "autor" => _autor,
                                               "titel" => _titel,
                                               "date" => _datum,
                                               "order_autor" => orderby('autor'),
                                               "order_date" => orderby('datum'),
                                               "order_titel" => orderby('titel'),
                                               "show" => $show,
                                               "val" => "artikel",
                                               "edit" => _editicon_blank,
                                               "delete" => _deleteicon_blank,
                                               "add" => _artikel_add));
    break;
}