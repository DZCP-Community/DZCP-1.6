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
$where = $where.': '._news_admin_head;

switch (common::$do) {
    case 'add':
        //Insert
        $notification_p = ""; $saved = false;
        if(isset($_POST['titel'])) {
            if(empty($_POST['titel']) || empty($_POST['newstext'])) {
                if(empty($_POST['newstext'])) {
                    notification::add_error(_empty_news);
                }
                
                if(empty($_POST['titel'])) {
                    notification::add_error(_empty_news_title);
                }

                if(notification::has()) {
                    javascript::set('AnchorMove', 'notification-box');
                }
            } else {
                $timeshift = ''; $public = ''; $datum = ''; $params = [];
                $stickytime = isset($_POST['sticky']) ? mktime($_POST['h'],$_POST['min'],0,$_POST['m'],$_POST['t'],$_POST['j']) : '0';
                if(isset($_POST['timeshift'])) {
                    $timeshifttime = mktime($_POST['h_ts'],$_POST['min_ts'],0,$_POST['m_ts'],$_POST['t_ts'],$_POST['j_ts']);
                    $timeshift = "`timeshift` = 1,";
                    $public = "`public` = 1,";
                    $params[] = (int)($timeshifttime);
                    $datum = "`datum` = ?,";
                }

                common::$sql['default']->insert("INSERT INTO `{prefix_news}` SET `autor` = ?,`kat` = ?,`titel` = ?,`text` = ?,`more` = ?,"
                        . "`link1` = ?,`link2` = ?,`link3` = ?,`url1` = ?,`url2` = ?,`url3` = ?,`intern` = ?,".$timeshift."".$public."".$datum."`sticky` = ?;",
                        array_merge([(int)(common::$userid),(int)($_POST['kat']),stringParser::encode($_POST['titel']),stringParser::encode($_POST['newstext']),
                            stringParser::encode($_POST['morenews']),stringParser::encode($_POST['link1']),stringParser::encode($_POST['link2']),stringParser::encode($_POST['link3']),
                            stringParser::encode(common::links($_POST['url1'])),stringParser::encode(common::links($_POST['url2'])),stringParser::encode(common::links($_POST['url3'])),(isset($_POST['intern']) ? 1 : 0)],
                                $params, [(int)($stickytime)]));

                $picUploadError = false;
                if(isset($_FILES['newspic']['tmp_name']) && !empty($_FILES['newspic']['tmp_name'])) {
                    $tmpname = $_FILES['newspic']['tmp_name'];
                    $file_name = $_FILES['newspic']['name'];
                    if($tmpname) {
                        $file_info = getimagesize($tmpname);
                        if(!$file_info) {
                            notification::add_error(_upload_error);
                            $picUploadError = true;
                        } else {
                            $file_info['width']  = $file_info[0];
                            $file_info['height'] = $file_info[1];
                            $file_info['mime']   = $file_info[2];
                            unset($file_info[3],$file_info['bits'],$file_info['channels'],
                                $file_info[0],$file_info[1],$file_info[2]);

                            if(!array_key_exists($file_info['mime'], config::$extensions)) {
                                notification::add_error(_upload_ext_error);
                                $picUploadError = true;
                            } else {
                                $endung = explode(".", $file_name);
                                $endung = strtolower($endung[count($endung)-1]);
                                if(!move_uploaded_file($tmpname, basePath."/inc/images/uploads/news/".common::$sql['default']->lastInsertId().".".strtolower($endung))) {
                                    notification::add_error(_upload_error);
                                    $picUploadError = true;
                                }
                            }
                        }
                    }
                }

                if(!$picUploadError) {
                    if(notification::has()) {
                        javascript::set('AnchorMove', 'notification-box');
                    }
                    notification::add_success(_news_sended, "?admin=newsadmin",2);
                    $saved = true;
                } else {
                    if(notification::has()) {
                        javascript::set('AnchorMove', 'notification-box');
                    }
                }
            }
        }
        
        //Show
        $qryk = common::$sql['default']->select("SELECT id,kategorie FROM `{prefix_news_kats}`"); $kat = '';
        foreach($qryk as $getk) {
            $kat .= common::select_field($getk['id'],(isset($_POST['kat']) && $_POST['kat'] == $getk['id']),stringParser::decode($getk['kategorie']));
        }

        $dropdown_date = common::dropdown_date(common::dropdown("day",isset($_POST['t']) ? (int)($_POST['t']) : date("d")),
            common::dropdown("month",isset($_POST['m']) ? (int)($_POST['m']) : date("m")),
            common::dropdown("year",isset($_POST['j']) ? (int)($_POST['j']) : date("Y")));

        $dropdown_time = common::dropdown_time(common::dropdown("hour",isset($_POST['h']) ? (int)($_POST['h']) : date("H")),
            common::dropdown("minute",isset($_POST['min']) ? (int)($_POST['min']) : date("i")));

        $smarty->caching = false;
        $smarty->assign('nr',"ts");
        $smarty->assign('day',common::dropdown("day",isset($_POST['t_ts']) ? (int)($_POST['t_ts']) : date("d")));
        $smarty->assign('month', common::dropdown("month",isset($_POST['m_ts']) ? (int)($_POST['m_ts']) : date("m")));
        $smarty->assign('year', common::dropdown("year",isset($_POST['j_ts']) ? (int)($_POST['j_ts']) : date("Y")));
        $timeshift_date = $smarty->fetch('string:'._dropdown_date_ts);
        $smarty->clearAllAssign();

        $smarty->caching = false;
        $smarty->assign('nr',"ts");
        $smarty->assign('hour',common::dropdown("hour",isset($_POST['h_ts']) ? (int)($_POST['h_ts']) : date("H")));
        $smarty->assign('minute', common::dropdown("minute",isset($_POST['min_ts']) ? (int)($_POST['min_ts']) : date("i")));
        $smarty->assign('uhr',_uhr);
        $timeshift_time = $smarty->fetch('string:'._dropdown_time_ts);
        $smarty->clearAllAssign();

        $smarty->caching = false;
        $smarty->assign('head',_admin_news_head);
        $smarty->assign('autor',common::autor());
        $smarty->assign('n_newspic','');
        $smarty->assign('delnewspic','');
        $smarty->assign('kat',$kat);
        $smarty->assign('do',"add");
        $smarty->assign('all_disabled',($saved ? " disabled" : ""));
        $smarty->assign('titel',(isset($_POST['titel']) ? $_POST['titel'] : ''));
        $smarty->assign('newstext',(isset($_POST['newstext']) ? $_POST['newstext'] : ''));
        $smarty->assign('morenews',(isset($_POST['morenews']) ? $_POST['morenews'] : ''));
        $smarty->assign('link1',(isset($_POST['link1']) ? $_POST['link1'] : ''));
        $smarty->assign('link2',(isset($_POST['link2']) ? $_POST['link2'] : ''));
        $smarty->assign('link3',(isset($_POST['link3']) ? $_POST['link3'] : ''));
        $smarty->assign('url1',(isset($_POST['url1']) ? $_POST['url1'] : ''));
        $smarty->assign('url2',(isset($_POST['url2']) ? $_POST['url2'] : ''));
        $smarty->assign('url3',(isset($_POST['url3']) ? $_POST['url3'] : ''));
        $smarty->assign('klapplink',(isset($_POST['klapptitel']) ? $_POST['klapptitel'] : ''));
        $smarty->assign('sticky',(isset($_POST['sticky']) ? 'checked="checked"' : ''));
        $smarty->assign('button',_button_value_add);
        $smarty->assign('intern',(isset($_POST['intern']) ? 'checked="checked"' : ''));
        $smarty->assign('dropdown_time',$dropdown_time);
        $smarty->assign('dropdown_date',$dropdown_date);
        $smarty->assign('timeshift_date',$timeshift_date);
        $smarty->assign('timeshift_time',$timeshift_time);
        $smarty->assign('timeshift',(isset($_POST['timeshift']) ? 'checked="checked"' : ''));
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/news_form.tpl');
        $smarty->clearAllAssign();
    break;
    case 'edit':
        if(isset($_GET['id'])) {
            $_SESSION['editID'] = (int)($_GET['id']);
        } else if(!array_key_exists('editID', $_SESSION)) {
            $_SESSION['editID'] = 0;
        }
        
        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_news}` WHERE `id` = ?;",[$_SESSION['editID']]);
        if(isset($_POST['titel'])) {
            if(empty($_POST['titel']) || empty($_POST['newstext'])) {
                if(empty($_POST['newstext'])) {
                    notification::add_error(_empty_news);
                }
                
                if(empty($_POST['titel'])) {
                    notification::add_error(_empty_news_title);
                }

                if(notification::has()) {
                    javascript::set('AnchorMove', 'notification-box');
                }
            } else {
                $timeshift = ''; $public = ''; $datum = ''; $params = [];
                $stickytime = isset($_POST['sticky']) ? mktime($_POST['h'],$_POST['min'],0,$_POST['m'],$_POST['t'],$_POST['j']) : '0';
                if(isset($_POST['timeshift'])) {
                    $timeshifttime = mktime($_POST['h_ts'],$_POST['min_ts'],0,$_POST['m_ts'],$_POST['t_ts'],$_POST['j_ts']);
                    $timeshift = "`timeshift` = 1,";
                    $public = "`public` = 1,";
                    $params[] = (int)($timeshifttime);
                    $datum = "`datum` = ?,";
                }

                $picUploadError = false;
                if(isset($_FILES['newspic']['tmp_name']) && !empty($_FILES['newspic']['tmp_name'])) {
                    $tmpname = $_FILES['newspic']['tmp_name'];
                    $file_name = $_FILES['newspic']['name'];
                    if($tmpname) {
                        $file_info = getimagesize($tmpname);
                        if(!$file_info) {
                            notification::add_error(_upload_error);
                            $picUploadError = true;
                        } else {
                            $file_info['width']  = $file_info[0];
                            $file_info['height'] = $file_info[1];
                            $file_info['mime']   = $file_info[2];
                            unset($file_info[3],$file_info['bits'],$file_info['channels'],
                                $file_info[0],$file_info[1],$file_info[2]);

                            if(!array_key_exists($file_info['mime'], config::$extensions)) {
                                notification::add_error(_upload_ext_error);
                                $picUploadError = true;
                            } else {
                                //Remove Pic
                                foreach(common::SUPPORTED_PICTURE as $tmpendung) {
                                    if(file_exists(basePath."/inc/images/uploads/news/".(int)($get['id']).".".$tmpendung))
                                        @unlink(basePath."/inc/images/uploads/news/".(int)($get['id']).".".$tmpendung);
                                }

                                //Remove minimize
                                $files = common::get_files(basePath."/inc/images/uploads/news/",false,true,common::SUPPORTED_PICTURE);
                                if($files) {
                                    foreach ($files as $file) {
                                        if(preg_match("#".(int)($_GET['id'])."(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                                            $res = preg_match("#".(int)($_GET['id'])."_(.*)#",$file,$match);
                                            if(file_exists(basePath."/inc/images/uploads/news/".(int)($get['id'])."_".$match[1]))
                                                @unlink(basePath."/inc/images/uploads/news/".(int)($get['id'])."_".$match[1]);
                                        }
                                    }
                                }
                                
                                $endung = explode(".", $file_name);
                                $endung = strtolower($endung[count($endung)-1]);
                                if(!move_uploaded_file($tmpname, basePath."/inc/images/uploads/news/".$get['id'].".".strtolower($endung))) {
                                    notification::add_error(_upload_error);
                                    $picUploadError = true;
                                }
                            }
                        }
                    }
                }

                if(!$picUploadError) {
                    common::$sql['default']->update("UPDATE `{prefix_news}`
                        SET `kat`        = '".(int)($_POST['kat'])."',
                            `titel`      = '".stringParser::encode($_POST['titel'])."',
                            `text`       = '".stringParser::encode($_POST['newstext'])."',
                            `more`  = '".stringParser::encode($_POST['morenews'])."',
                            `link1`      = '".stringParser::encode($_POST['link1'])."',
                            `url1`       = '".stringParser::encode(common::links($_POST['url1']))."',
                            `link2`      = '".stringParser::encode($_POST['link2'])."',
                            `url2`       = '".stringParser::encode(common::links($_POST['url2']))."',
                            `link3`      = '".stringParser::encode($_POST['link3'])."',
                            `intern`     = '".(isset($_POST['intern']) ? (int)($_POST['intern']) : 0)."',
                            `url3`       = '".stringParser::encode(common::links($_POST['url3']))."',
                            ".$timeshift."
                            ".$public."
                            ".$datum."
                            `sticky`     = '".(int)($stickytime)."'
                        WHERE id = ".$get['id'].";");
                    
                    $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_news}` WHERE id = ".$get['id'].";");
                    if(notification::has()) {
                        javascript::set('AnchorMove', 'notification-box');
                    }
                    notification::add_success(_news_edited, "?admin=newsadmin",2);
                    $saved = true;
                } else {
                    if(notification::has()) {
                        javascript::set('AnchorMove', 'notification-box');
                    }
                }
            }
        }
        
        $qryk = common::$sql['default']->select("SELECT `id`,`kategorie` FROM `{prefix_news_kats}`"); $kat = '';
        foreach($qryk as $getk) {
            $kat .= common::select_field($getk['id'],($get['kat'] == $getk['id']),stringParser::decode($getk['kategorie']));
        } unset($qryk,$getk);

        $int = ($get['intern'] ? 'checked="checked"' : '');
        $timeshift = ($get['timeshift'] ? 'checked="checked"' : '');
        $sticky = ($get['sticky'] ? 'checked="checked"' : '');

        //dropdown_date
        $smarty->caching = false;
        $smarty->assign('nr', "ts");
        $smarty->assign('day',common::dropdown("day",date("d")));
        $smarty->assign('month',common::dropdown("month",date("m")));
        $smarty->assign('year',common::dropdown("year",date("Y")));
        $dropdown_date = $smarty->fetch('string:'._dropdown_date_ts);
        $smarty->clearAllAssign();

        //dropdown_time
        $smarty->caching = false;
        $smarty->assign('nr', "ts");
        $smarty->assign('hour', common::dropdown("hour",date("H")));
        $smarty->assign('minute',common::dropdown("minute",date("i")));
        $smarty->assign('uhr',_uhr);
        $dropdown_time = $smarty->fetch('string:'._dropdown_time_ts);
        $smarty->clearAllAssign();

        if($get['sticky']) {
            $dropdown_date = common::dropdown_date(common::dropdown("day",date("d",$get['sticky'])),
                common::dropdown("month",date("m",$get['sticky'])),
                common::dropdown("year",date("Y",$get['sticky'])));

            $dropdown_time = common::dropdown_time(common::dropdown("hour",date("H",$get['sticky'])),
                common::dropdown("minute",date("i",$get['sticky'])));
        }

        $smarty->caching = false;
        $smarty->assign('nr', "ts");
        $smarty->assign('day', common::dropdown("day",date("d")));
        $smarty->assign('month',common::dropdown("month",date("m")));
        $smarty->assign('year',common::dropdown("year",date("Y")));
        $timeshift_date = $smarty->fetch('string:'._dropdown_date_ts);
        $smarty->clearAllAssign();

        $smarty->caching = false;
        $smarty->assign('nr','ts');
        $smarty->assign('hour', common::dropdown("hour",date("H")));
        $smarty->assign('minute',common::dropdown("minute",date("i")));
        $smarty->assign('uhr',_uhr);
        $timeshift_time = $smarty->fetch('string:'._dropdown_time_ts);
        $smarty->clearAllAssign();

        if($get['timeshift']) {
            $smarty->caching = false;
            $smarty->assign('nr', "ts");
            $smarty->assign('day',common::dropdown("day",date("d",$get['datum'])));
            $smarty->assign('month',common::dropdown("month",date("m",$get['datum'])));
            $smarty->assign('year',common::dropdown("year",date("Y",$get['datum'])));
            $timeshift_date = $smarty->fetch('string:'._dropdown_date_ts);
            $smarty->clearAllAssign();

            $smarty->caching = false;
            $smarty->assign('nr','ts');
            $smarty->assign('hour', common::dropdown("hour",date("H",$get['datum'])));
            $smarty->assign('minute',common::dropdown("minute",date("i",$get['datum'])));
            $smarty->assign('uhr',_uhr);
            $dropdown_time = $smarty->fetch('string:'._dropdown_time_ts);
            $smarty->clearAllAssign();
        }

        $newsimage = ""; $delnewspic = "";
        foreach(common::SUPPORTED_PICTURE as $tmpendung) {
            if(file_exists(basePath."/inc/images/uploads/news/".$get['id'].".".$tmpendung)) {
                $newsimage = common::img_size('inc/images/uploads/news/'.$get['id'].'.'.$tmpendung)."<br /><br />";
                $delnewspic = '<a href="?admin=newsadmin&do=delnewspic&id='.$get['id'].'">'._newspic_del.'</a><br /><br />';
                break;
            }
        }

        $smarty->caching = false;
        $smarty->assign('head',_admin_news_edit_head);
        $smarty->assign('autor',common::autor($get['autor']));
        $smarty->assign('n_newspic',$newsimage);
        $smarty->assign('delnewspic',$delnewspic);
        $smarty->assign('kat',$kat);
        $smarty->assign('do',"edit");
        $smarty->assign('all_disabled','');
        $smarty->assign('titel',stringParser::decode($get['titel']));
        $smarty->assign('newstext',stringParser::decode($get['text']));
        $smarty->assign('morenews', stringParser::decode($get['more']));
        $smarty->assign('link1',stringParser::decode($get['link1']));
        $smarty->assign('link2',stringParser::decode($get['link2']));
        $smarty->assign('link3',stringParser::decode($get['link3']));
        $smarty->assign('url1',stringParser::decode($get['url1']));
        $smarty->assign('url2',stringParser::decode($get['url2']));
        $smarty->assign('url3',stringParser::decode($get['url3']));
        $smarty->assign('dropdown_time',$dropdown_time);
        $smarty->assign('dropdown_date',$dropdown_date);
        $smarty->assign('timeshift_date',$timeshift_date);
        $smarty->assign('timeshift_time',$timeshift_time);
        $smarty->assign('timeshift',$timeshift);
        $smarty->assign('error','');
        $smarty->assign('button',_button_value_edit);
        $smarty->assign('intern',$int);
        $smarty->assign('sticky',$sticky);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/news_form.tpl');
        $smarty->clearAllAssign();
    break;
    case 'public':
        $get = common::$sql['default']->fetch("SELECT `public` FROM `{prefix_news}` WHERE `id` = ?;",[(int)($_GET['id'])]);
        if(!$get['public'])
            common::$sql['default']->update("UPDATE `{prefix_news}` SET `public` = 1, `datum`  = ? WHERE `id` = ?;",[time(),(int)($_GET['id'])]);
        else
            common::$sql['default']->update("UPDATE `{prefix_news}` SET `public` = 0 WHERE `id` = ?;",[(int)($_GET['id'])]);

        header("Location: ?admin=newsadmin");
    break;
    case 'delete':
        common::$sql['default']->delete("DELETE FROM `{prefix_news}` WHERE id = '".(int)($_GET['id'])."'");
        common::$sql['default']->delete("DELETE FROM `{prefix_news_comments}` WHERE news = '".(int)($_GET['id'])."'");

        //Remove Pic
        foreach(common::SUPPORTED_PICTURE as $tmpendung) {
            if(file_exists(basePath."/inc/images/uploads/news/".(int)($_GET['id']).".".$tmpendung))
                @unlink(basePath."/inc/images/uploads/news/".(int)($_GET['id']).".".$tmpendung);
        }

        //Remove minimize
        $files = common::get_files(basePath."/inc/images/uploads/news/",false,true, common::SUPPORTED_PICTURE);
        if($files) {
            foreach ($files as $file) {
                if(preg_match("#".(int)($_GET['id'])."(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                    $res = preg_match("#".(int)($_GET['id'])."_(.*)#",$file,$match);
                    if(file_exists(basePath."/inc/images/uploads/news/".(int)($_GET['id'])."_".$match[1]))
                        @unlink(basePath."/inc/images/uploads/news/".(int)($_GET['id'])."_".$match[1]);
                }
            }
        }

        notification::add_success(_news_deleted, "?admin=newsadmin",2);
    break;
    case 'delnewspic':
        //Remove Pic
        foreach(common::SUPPORTED_PICTURE as $tmpendung) {
            if(file_exists(basePath."/inc/images/uploads/news/".(int)($_GET['id']).".".$tmpendung))
                @unlink(basePath."/inc/images/uploads/news/".(int)($_GET['id']).".".$tmpendung);
        }

        //Remove minimize
        $files = common::get_files(basePath."/inc/images/uploads/news/",false,true,common::SUPPORTED_PICTURE);
        foreach ($files as $file) {
            if(preg_match("#".(int)($_GET['id'])."(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                $res = preg_match("#".(int)($_GET['id'])."_(.*)#",$file,$match);
                if(file_exists(basePath."/inc/images/uploads/news/".(int)($_GET['id'])."_".$match[1]))
                    @unlink(basePath."/inc/images/uploads/news/".(int)($_GET['id'])."_".$match[1]);
            }
        }

        $show = common::info(_newspic_deleted, "?admin=newsadmin&do=edit&id=".(int)($_GET['id'])."");
    break;
    default:
        $entrys = common::cnt('{prefix_news}'); $show_ = '';
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_news}` ".common::orderby_sql(["titel","datum","autor"], 'ORDER BY `public` ASC, `datum` DESC')."
                   LIMIT ".(common::$page - 1)*settings::get('m_adminnews').",".settings::get('m_adminnews').";");
        foreach($qry as $get) {
            $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_news);

            $intern = ($get['intern'] ? _votes_intern : '');
            $sticky = ($get['sticky'] ? _news_sticky : '');
            $datum = empty($get['datum']) ? _no_public : date("d.m.y H:i", $get['datum'])._uhr;
            $public = ($get['public'] ? '<a href="?admin=newsadmin&amp;do=public&amp;id='.$get['id'].'"><img src="../inc/images/public.gif" alt="" title="'._non_public.'" /></a>'
                    : '<a href="?admin=newsadmin&amp;do=public&amp;id='.$get['id'].'"><img src="../inc/images/nonpublic.gif" alt="" title="'._public.'" /></a>');

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $smarty->assign('date',$datum);
            $smarty->assign('titel',stringParser::decode(common::cut($get['titel'],settings::get('l_newsadmin'))));
            $smarty->assign('class',$class);
            $smarty->assign('autor',common::autor($get['autor']));
            $smarty->assign('intnews',$intern);
            $smarty->assign('sticky',$sticky);
            $smarty->assign('public',$public);
            $smarty->assign('edit',$edit);
            $smarty->assign('delete',$delete);
            $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/admin_show.tpl');
            $smarty->clearAllAssign();
        }

        if(empty($show))
            $show = '<tr><td colspan="3" class="contentMainSecond">'._no_entrys.'</td></tr>';

        $nav = common::nav($entrys,settings::get('m_adminnews'),"?admin=newsadmin".(isset($_GET['show']) ? $_GET['show'].common::orderby_nav() : common::orderby_nav()));

        $smarty->caching = false;
        $smarty->assign('nav',$nav);
        $smarty->assign('show',$show);
        $smarty->assign('order_autor',common::orderby('autor'));
        $smarty->assign('order_date',common::orderby('datum'));
        $smarty->assign('order_titel',common::orderby('titel'));
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/admin_news.tpl');
        $smarty->clearAllAssign();
    break;
}