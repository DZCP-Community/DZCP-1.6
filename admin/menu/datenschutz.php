<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._config_title_datenschutz;

switch ($do)
{
    case 'save':
        //Todo
        break;
    case 'edit':
        //Todo
        break;
    default:
        $qry = db("SELECT `id`,`first_name`,`last_name` FROM ".$db['dsgvo_pers'].";");
        while ($get = _fetch($qry)) {
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                "action" => "admin=datenschutz&amp;do=edit",
                "title" => _button_title_edit));
            $rolle = ($get['id'] == 1 ? _datenschutz_rolle_1 : _datenschutz_rolle_2);
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/datenschutz_show", array('rolle' => $rolle,'class'=>$class,
                'first_name'=>re($get['first_name']),'last_name'=>re($get['last_name']),'edit'=>$edit));
        }

        $selects_1 = ''; $selects_2 = '';
        $qry = db("SELECT * FROM ".$db['dsgvo']." WHERE `for_dsgvo` = 1;");
        while ($get = _fetch($qry)) {
            $info = (defined(re($get['info_tag'])) ? constant(re($get['info_tag'])) : '');
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $selects_1 .= show($dir . "/datenschutz_select_loop", array('info' => $info, 'class' => $class,
                'is_on' => $get['show'] ? 'selected="selected"' : '', 'fid' => 'fid_'.$get['id']));
        }

        $qry = db("SELECT * FROM ".$db['dsgvo']." WHERE `for_dsgvo_ak` = 1 ORDER BY `for_dsgvo` ASC;");
        while ($get = _fetch($qry)) {
            $info = (defined(re($get['info_tag'])) ? constant(re($get['info_tag'])) : '');
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $selects_2 .= show($dir . "/datenschutz_select_loop", array('info' => $info, 'class' => $class,
                'is_on' => $get['lock_show'] ? 'selected="selected"' : '', 'fid' => 'fid_'.$get['id']));
        }

        $show = show($dir."/datenschutz", array('show' => $show,'selects_1' => $selects_1,'selects_2' => $selects_2, 'value' => _button_value_save));
        break;
}