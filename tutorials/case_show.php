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

$index = common::error(_error_wrong_permissions);

$getkat = common::$sql['default']->fetch("SELECT `name`,`beschreibung`,`id` FROM `{prefix_tutorials_kats}` WHERE `level` <= ? AND `id` = ? ORDER BY `pos`;",
    [common::$chkMe,(int)($_GET['id'])]);

if(common::$sql['default']->rowCount()) {
    $qry = common::$sql['default']->select("SELECT * FROM `{prefix_tutorials}` WHERE `kat` = ? ".
        common::orderby_sql(["pos","datum","name","difficulty","rating"], 'ORDER BY `datum` DESC,`name`')." LIMIT "
        .((common::$page - 1)*settings::get('m_tutorials')).",".settings::get('m_tutorials').";",
        [$getkat['id']]);

    $tutorial = '';
    if(($entrys=common::$sql['default']->rowCount())) {
        foreach($qry as $get) {
            $pic = 'https://static.dzcp.de/thumbgen.php?img=images/tutorials/default.jpg&width=150';
            $pfad = "images/tutorials/".$get['id'];
            foreach(common::SUPPORTED_PICTURE as $tmpendung) {
                if(file_exists(rootPath."/static/".$pfad.".".$tmpendung)) {
                    $pic = 'https://static.dzcp.de/thumbgen.php?img='.$pfad.'.'.$tmpendung.'&width=150';
                    break;
                }
            } //foreach

            $level_text = ''; $level = 0;
            switch ($get['difficulty']) {
                default:
                case 1:
                    $level_text = _tutorials_schwierigkeit_leicht;
                    $level = 1;
                    break;
                case 2:
                    $level_text = _tutorials_schwierigkeit_mittel;
                    $level = 2;
                    break;
                case 3:
                    $level_text = _tutorials_schwierigkeit_schwer;
                    $level = 3;
                    break;
                case 4:
                    $level_text = _tutorials_schwierigkeit_experte;
                    $level = 4;
                    break;
            }

            $smarty->caching = false;
            $smarty->assign('id',$get['id']);
            $smarty->assign('bezeichnung',stringParser::decode($get['name']));
            $smarty->assign('datum',date("d.m.Y H:i",$get['datum']));
            $smarty->assign('pic',$pic);
            $smarty->assign('level',$level);
            $smarty->assign('beschreibung',BBCode::parse_html((string)$get['beschreibung']));
            $smarty->assign('level_text',$level_text);
            $smarty->assign('autor',common::autor($get['autor']));
            $tutorial .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/tutorials/tutorials_show.tpl');
            $smarty->clearAllAssign();
        } //foreach
    }

    $pic_kat = 'https://static.dzcp.de/images/tutorials/kats/default.jpg';
    foreach(common::SUPPORTED_PICTURE as $tmpendung) {
        if(file_exists(rootPath."/static/images/tutorials/kats/".$getkat['id'].".".$tmpendung)) {
            $pic_kat = 'https://static.dzcp.de/images/tutorials/kats/'.$getkat['id'].'.'.$tmpendung;
            break;
        }
    }

    $smarty->caching = false;
    $smarty->assign('katpic',$pic_kat);
    $smarty->assign('kat_beschreibung',BBCode::parse_html((string)$getkat['beschreibung']));
    $smarty->assign('id',$getkat['id']);
    $smarty->assign('kategorie_name',stringParser::decode($getkat['name']));
    $smarty->assign('nav',common::nav(common::cnt("{prefix_tutorials}", " WHERE `kat` = ?", "id", [$getkat['id']]),
        settings::get('m_tutorials'),"?action=show&id=".$getkat['id']));
    $smarty->assign('tutorials',$tutorial);
    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/tutorials/tutorials.tpl');
    $smarty->clearAllAssign();
}
