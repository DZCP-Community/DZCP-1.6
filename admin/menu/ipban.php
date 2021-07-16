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
$where = $where.': '._ipban_head_admin;
switch (common::$do) {
    case 'add':
        if(empty($_POST['ip']))
            $show = common::error(_ip_empty);
        else if(common::validateIpV4Range($_POST['ip'], '[192].[168].[0-255].[0-255]') ||
                common::validateIpV4Range($_POST['ip'], '[127].[0].[0-255].[0-255]') ||
                common::validateIpV4Range($_POST['ip'], '[10].[0-255].[0-255].[0-255]') ||
                common::validateIpV4Range($_POST['ip'], '[172].[16-31].[0-255].[0-255]'))
            $show = common::error(_ipban_error_pip);
        else {
            if(empty($_POST['info']))
                $info = '*Keine Info*';
            else
                $info = stringParser::encode($_POST['info']);

            $data_array = [];
            $data_array['confidence'] = ''; $data_array['frequency'] = ''; $data_array['lastseen'] = '';
            $data_array['banned_msg'] = $info;
            common::$sql['default']->insert("INSERT INTO `{prefix_ipban}` SET `time` = ?, `ipv4` = ?, `data` = ?, `typ` = 3;",
                    [time(),stringParser::encode($_POST['ip']),serialize($data_array)]);
            $show = common::info(_ipban_admin_added, "?admin=ipban");
        }
    break;
    case 'delete':
        common::$sql['default']->delete("DELETE FROM `{prefix_ipban}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        $show = common::info(_ipban_admin_deleted, "?admin=ipban");
    break;
    case 'edit':
        $get = common::$sql['default']->fetch("SELECT `ipv4`,`data` FROM `{prefix_ipban}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        $data_array = unserialize($get['data']);
        $smarty->caching = false;
        $smarty->assign('newhead',_ipban_edit_head);
        $smarty->assign('do',"edit_save&amp;id=".$_GET['id']."");
        $smarty->assign('ip_set',stringParser::decode($get['ipv4']));
        $smarty->assign('info',stringParser::decode($data_array['banned_msg']));
        $smarty->assign('what',_button_value_edit);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban_form.tpl');
        $smarty->clearAllAssign();

    break;
    case 'edit_save':
        if(empty($_POST['ip']))
            $show = common::error(_ip_empty);
        else {
            $get = common::$sql['default']->fetch("SELECT `id`,`data` FROM `{prefix_ipban}` WHERE `id` = ?;", [(int)($_GET['id'])]);
            $data_array = unserialize($get['data']);
            $data_array['banned_msg'] = stringParser::decode($_POST['info']);
            common::$sql['default']->update("UPDATE `{prefix_ipban}` SET `ipv4` = ?, `time` = ?, `data` = ? WHERE `id` = ?;",
                    [stringParser::encode($_POST['ip']),time(),serialize($data_array),(int)($get['id'])]);
            $show = common::info(_ipban_admin_edited, "?admin=ipban");
        }
    break;
    case 'enable':
        $get = common::$sql['default']->fetch("SELECT `id`,`enable` FROM `{prefix_ipban}` WHERE `id` = ?;", [(int)($_GET['id'])]);
        common::$sql['default']->update("UPDATE `{prefix_ipban}` SET `enable` = ? WHERE `id` = ?;", [($get['enable'] ? 0 : 1),$get['id']]);
        $show = header("Location: ?admin=ipban&sfs_side=".(isset($_GET['sfs_side']) ? $_GET['sfs_side'] : 1)."&ub_side=".(isset($_GET['ub_side']) ? $_GET['ub_side'] : 1));
    break;
    case 'new':
        $smarty->caching = false;
        $smarty->assign('newhead',_ipban_new_head);
        $smarty->assign('do',"add");
        $smarty->assign('ip_set','');
        $smarty->assign('info','');
        $smarty->assign('what',_button_value_add);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban_form.tpl');
        $smarty->clearAllAssign();
    break;
    case 'search':
        $qry = common::$sql['default']->select("SELECT * FROM `{prefix_ipban}` WHERE `ipv4` LIKE '%?%' ORDER BY `ipv4` ASC;", [stringParser::encode($_POST['ip'])]); //Suche
        $color = 1; $show_search = '';
        foreach($qry as $get) {
            $data_array = unserialize($get['data']);

            $edit = '';
            if($get['typ'] == '3')
                $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
            
            $action = "?admin=ipban&amp;do=enable&amp;id=".$get['id']."&amp;ub_side=".(isset($_GET['ub_side']) ? $_GET['ub_side'] : 1)."&amp;sfs_side=".(isset($_GET['sfs_side']) ? $_GET['sfs_side'] : 1);

            if($get['enable']) {
                $smarty->caching = false;
                $smarty->assign('ip',$get['ipv4']);
                $info = $smarty->fetch('string:'._confirm_disable_ipban);
                $smarty->clearAllAssign();

                $smarty->caching = false;
                $smarty->assign('id',$get['id']);
                $smarty->assign('action',$action);
                $smarty->assign('info',$info);
                $unban = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban_menu_icon_enable.tpl');
                $smarty->clearAllAssign();
            } else {
                $smarty->caching = false;
                $smarty->assign('ip',$get['ipv4']);
                $info = $smarty->fetch('string:'._confirm_enable_ipban);
                $smarty->clearAllAssign();

                $smarty->caching = false;
                $smarty->assign('id',$get['id']);
                $smarty->assign('action',$action);
                $smarty->assign('info',$info);
                $unban = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban_menu_icon_disable.tpl');
                $smarty->clearAllAssign();
            }

            $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_ipban);
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $smarty->caching = false;
            $smarty->assign('ip',stringParser::decode($get['ipv4']));
            $smarty->assign('bez',stringParser::decode($data_array['banned_msg']));
            $smarty->assign('rep',stringParser::decode($data_array['frequency']));
            $smarty->assign('zv',stringParser::decode($data_array['confidence']).'%');
            $smarty->assign('class',$class);
            $smarty->assign('delete',$delete);
            $smarty->assign('edit',$edit);
            $smarty->assign('unban',$unban);
            $show_search .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban_show_user.tpl');
            $smarty->clearAllAssign();
        }

        if(empty($show_search))
            $show_search = '<tr><td colspan="7" class="contentMainSecond">'._no_entrys.'</td></tr>';

        $smarty->caching = false;
        $smarty->assign('value',_button_value_save);
        $smarty->assign('show',$show_search);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban_search.tpl');
        $smarty->clearAllAssign();

    break;
    default:
        //typ: 0 = Off, 1 = GSL, 2 = SysBan, 3 = Ipban
        $show = ''; $show_sfs = ''; $show_user = '';
        $pager_sfs = ''; $pager_user = '';

        $count_spam = common::$sql['default']->rows("SELECT `id` FROM `{prefix_ipban}` WHERE `typ` = 1;"); //Type 1 => Global Stopforumspam.com List
        if($count_spam >= 1) {
            $site = (isset($_GET['sfs_side']) ? $_GET['sfs_side'] : 1);
            if($site < 1) $site = 1; $end = $site*20; $start = $end-20;
            $count_spam_nav = common::$sql['default']->rows("SELECT id FROM `{prefix_ipban}` WHERE `typ` = 1 ORDER BY `id` DESC LIMIT ".$start.", 20;"); //Type Userban ROW
            if($start != 0)
                $pager_sfs = '<a href="?admin=ipban&sfs_side='.($site-1).'&ub_side='.(isset($_GET['ub_side']) ? $_GET['ub_side'] : 1).'"><img align="absmiddle" src="../inc/images/previous.png" alt="left" /></a>';
            else
                $pager_sfs = '<img src="../inc/images/previous.png" align="absmiddle" alt="left" class="disabled" />';

            $pager_sfs .=  '&nbsp;'.($start+1).' bis '.($count_spam_nav+$start).'&nbsp;';

            if($count_spam_nav >= 20 )
                $pager_sfs .=  '<a href="?admin=ipban&sfs_side='.($site+1).'&ub_side='.(isset($_GET['ub_side']) ? $_GET['ub_side'] : 1).'"><img align="absmiddle" src="../inc/images/next.png" alt="right" /></a>';
            else
                $pager_sfs .= '<img src="../inc/images/next.png" alt="right" align="absmiddle" class="disabled" />';

            $qry = common::$sql['default']->select("SELECT * FROM `{prefix_ipban}` WHERE `typ` = 1 ORDER BY `id` DESC LIMIT ".$start.", 20;"); $color = 1;
            foreach($qry as $get) {
                $data_array = unserialize($get['data']);
                $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_ipban);
                $action = "?admin=ipban&amp;do=enable&amp;id=".$get['id']."&amp;sfs_side=".($site)."&amp;ub_side=".(isset($_GET['ub_side']) ? $_GET['ub_side'] : 1);

                if($get['enable']) {
                    $smarty->caching = false;
                    $smarty->assign('ip',$get['ipv4']);
                    $info = $smarty->fetch('string:'._confirm_disable_ipban);
                    $smarty->clearAllAssign();

                    $smarty->caching = false;
                    $smarty->assign('id',$get['id']);
                    $smarty->assign('action',$action);
                    $smarty->assign('info',$info);
                    $unban = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban_menu_icon_enable.tpl');
                    $smarty->clearAllAssign();
                } else {
                    $smarty->caching = false;
                    $smarty->assign('ip',$get['ipv4']);
                    $info = $smarty->fetch('string:'._confirm_enable_ipban);
                    $smarty->clearAllAssign();

                    $smarty->caching = false;
                    $smarty->assign('id',$get['id']);
                    $smarty->assign('action',$action);
                    $smarty->assign('info',$info);
                    $unban = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban_menu_icon_disable.tpl');
                    $smarty->clearAllAssign();
                }

                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                $smarty->caching = false;
                $smarty->assign('ip',stringParser::decode($get['ipv4']));
                $smarty->assign('bez',stringParser::decode($data_array['banned_msg']));
                $smarty->assign('rep',stringParser::decode($data_array['frequency']));
                $smarty->assign('zv',stringParser::decode($data_array['confidence']).'%');
                $smarty->assign('class',$class);
                $smarty->assign('delete',$delete);
                $smarty->assign('unban',$unban);
                $show_sfs .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban_show_sfs.tpl');
                $smarty->clearAllAssign();
            }
        }

        //Empty
        if(empty($show_sfs))
            $show_sfs = '<tr><td colspan="8" class="contentMainSecond">'._no_entrys.'</td></tr>';

        $count_user = common::$sql['default']->rows("SELECT id FROM `{prefix_ipban}` WHERE typ = 3;"); //Type 3 => Usersban
        if($count_user >= 1) {
            $site = (isset($_GET['ub_side']) ? $_GET['ub_side'] : 1);

            if($site < 1) $site = 1;
            $end = $site*20;
            $start = $end-20;

            $count_user_nav = common::$sql['default']->rows("SELECT id FROM `{prefix_ipban}` WHERE typ = 3 ORDER BY id DESC LIMIT ".$start.", 20;"); //Type System Ban ROW

            if($start != 0)
                $pager_user = '<a href="?admin=ipban&ub_side='.($site-1).'&sfs_side='.(isset($_GET['sfs_side']) ? $_GET['sfs_side'] : 1).'"><img align="absmiddle" src="../inc/images/previous.png" alt="left" /></a>';
            else
                $pager_user = '<img src="../inc/images/previous.png" align="absmiddle" alt="left" class="disabled" />';

            $pager_user .=  '&nbsp;'.($start+1).' bis '.($count_user_nav+$start).'&nbsp;';

            if($count_user_nav >= 20 )
                $pager_user .=  '<a href="?admin=ipban&ub_side='.($site+1).'&sfs_side='.(isset($_GET['sfs_side']) ? $_GET['sfs_side'] : 1).'"><img align="absmiddle" src="../inc/images/next.png" alt="right" /></a>';
            else
                $pager_user .= '<img src="../inc/images/next.png" alt="right" align="absmiddle" class="disabled" />';

            $qry = common::$sql['default']->select("SELECT * FROM `{prefix_ipban}` WHERE typ = 3 ORDER BY id DESC LIMIT ".$start.", 20;"); $color = 1;
            foreach($qry as $get) {
                $data_array = unserialize($get['data']);
                $edit = common::getButtonEditSingle($get['id'],"admin=".$admin."&amp;do=edit");
                $delete = common::button_delete_single($get['id'],"admin=".$admin."&amp;do=delete",_button_title_del,_confirm_del_ipban);
                $action = "?admin=ipban&amp;do=enable&amp;id=".$get['id']."&amp;ub_side=".($site)."&amp;sfs_side=".(isset($_GET['sfs_side']) ? $_GET['sfs_side'] : 1);

                if($get['enable']) {
                    $smarty->caching = false;
                    $smarty->assign('ip',$get['ipv4']);
                    $info = $smarty->fetch('string:'._confirm_disable_ipban);
                    $smarty->clearAllAssign();

                    $smarty->caching = false;
                    $smarty->assign('id',$get['id']);
                    $smarty->assign('action',$action);
                    $smarty->assign('info',$info);
                    $unban = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban_menu_icon_enable.tpl');
                    $smarty->clearAllAssign();
                } else {
                    $smarty->caching = false;
                    $smarty->assign('ip',$get['ipv4']);
                    $info = $smarty->fetch('string:'._confirm_enable_ipban);
                    $smarty->clearAllAssign();

                    $smarty->caching = false;
                    $smarty->assign('id',$get['id']);
                    $smarty->assign('action',$action);
                    $smarty->assign('info',$info);
                    $unban = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban_menu_icon_disable.tpl');
                    $smarty->clearAllAssign();
                }

                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                $smarty->caching = false;
                $smarty->assign('ip',stringParser::decode($get['ipv4']));
                $smarty->assign('bez',stringParser::decode($data_array['banned_msg']));
                $smarty->assign('class',$class);
                $smarty->assign('delete',$delete);
                $smarty->assign('edit',$edit);
                $smarty->assign('unban',$unban);
                $show_user .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban_show_user.tpl');
                $smarty->clearAllAssign();
            }
        }

        if(empty($show_user))
            $show_user = '<tr><td colspan="8" class="contentMainSecond">'._no_entrys.'</td></tr>';

        $smarty->caching = false;
        $smarty->assign('show_spam',$show_sfs);
        $smarty->assign('show_user',$show_user);
        $smarty->assign('count_user',$count_user);
        $smarty->assign('count_spam',$count_spam);
        $smarty->assign('pager_sfs',$pager_sfs);
        $smarty->assign('pager_user',$pager_user);
        $show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/ipban.tpl');
        $smarty->clearAllAssign();
    break;
}