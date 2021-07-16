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

if (!defined('_Tutorials')) exit();

$qry = common::$sql['default']->select("SELECT * FROM `{prefix_tutorials_kats}` WHERE `level` <= ? AND (`addons` = ? OR `addons` = -1) ORDER BY `pos`;",
    [
        common::$chkMe,
        (int)common::$is_addons,
    ]);

$show = '';
if(common::$sql['default']->rowCount()) {
    foreach ($qry as $get) {
        $pic = 'https://static.dzcp.de/thumbgen.php?img=images/tutorials/kats/default.jpg&width=200';
        foreach(common::SUPPORTED_PICTURE as $tmpendung) {
            if(file_exists(rootPath."/static/images/tutorials/kats/".$get['id'].".".$tmpendung)) {
                $pic = 'https://static.dzcp.de/thumbgen.php?img=images/tutorials/kats/'.$get['id'].'.'.$tmpendung.'&width=200';
                break;
            }
        }

        /** @var Smarty $smarty */
        $smarty->assign('id',$get['id']);
        $smarty->assign('tuts',common::$sql['default']->rows("SELECT `id` FROM `{prefix_tutorials}` WHERE `kat` = ? ;",[$get['id']]),true);
        $smarty->assign('bezeichnung',(string)stringParser::decode($get['name']));
        $smarty->assign('anzahl',common::cnt("{prefix_tutorials}", "WHERE `kat` = " . $get['id']));
        $smarty->assign('pic',$pic);
        $smarty->assign('beschreibung',BBCode::parse_html((string)$get['beschreibung']));
        $show .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/kategorie/kat_show.tpl');
        $smarty->clearAllAssign();
    }
}

/** @var Smarty $smarty */
$smarty->caching = false;
$smarty->assign('head',_tutorials." - "._tutorials_kategorie);
$smarty->assign('kats',$show);
$index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/kategorie/kats.tpl');
$smarty->clearAllAssign();