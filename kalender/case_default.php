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

if (!defined('_Kalender')) exit();

$monat = date("m");
if(isset($_POST['monat'])) 
    $monat = (int)($_POST['monat']);
else if(isset($_GET['m']))  
    $monat = (int)($_GET['m']);

$monat = str_pad($monat, 2 ,'0', STR_PAD_LEFT);

$jahr = date("Y");
if(isset($_POST['jahr'])) 
    $jahr = (int)($_POST['jahr']);
else if(isset($_GET['y'])) 
    $jahr = (int)($_GET['y']);

$month = '';
for($i = 1; $i <= 12; $i++) {
    $mname = ["1" => _jan,
                   "2" => _feb,
                   "3" => _mar,
                   "4" => _apr,
                   "5" => _mai,
                   "6" => _jun,
                   "7" => _jul,
                   "8" => _aug,
                   "9" => _sep,
                   "10" => _okt,
                   "11" => _nov,
                   "12" => _dez];

    $month .= common::select_field( common::cal($i),$monat == $i,$mname[$i]);
}

$year = '';
for( $i = date("Y")-5; $i < date("Y")+3; $i++) {
    $year .= common::select_field( $i,$jahr == $i,$i);
}

$ktoday = mktime(0,0,0,date("n"),date("d"),date("Y"));
$i = 1;
while($i <= 31 && checkdate($monat, $i, $jahr)) {
    $data = '';
    for($iw = 1; $iw <= 7; $iw++) {
        unset($bdays, $cws, $infoBday, $infoCW, $infoEvent);
        $datum = mktime(0,0,0,$monat,$i,$jahr);
        $wday = getdate($datum);
        $wday = $wday['wday'];

        if(!$wday) {
            $wday = 7;
        }

        if($wday != $iw) {
            $data .= '<td class="calDay"></td>';
        } else {

            //User Birthday 
            $infoBday = ''; $bdays = ""; $CountBday = 0;
            $qry = common::$sql['default']->select("SELECT `id`,`bday`,`nick` FROM `{prefix_users}` WHERE `bday` != 0;");
            foreach($qry as $get) {
                if((int)$get['bday'] >= 1) {
                    if (date("d.m", $get['bday']) == common::cal($i) . "." . $monat) {
                        $infoBday .= '&lt;img src=../inc/images/bday.gif class=icon alt= /&gt;' . '&nbsp;' . common::jsconvert(_kal_birthday . common::rawautor($get['id'])) . '<br />';
                        $CountBday++;
                    }
                }
            }

            if($CountBday >= 1) {
                $info = '';
                if(!common::$mobile->isMobile() || common::$mobile->isTablet()) {
                    $info = ' onmouseover="DZCP.showInfo(\'' . $infoBday . '\')" onmouseout="DZCP.hideInfo()"';
                }
                $bdays = '<a href="../user/?action=userlist&amp;show=bday&amp;time='.$datum.'"'.$info.'><img src="../inc/images/bday.gif" alt="" /></a>';
            }

            //Events
            $event = "";
            $qry = common::$sql['default']->select("SELECT `datum`,`title` FROM `{prefix_events}` WHERE DATE_FORMAT(FROM_UNIXTIME(datum), '%d.%m.%Y') = ?;", [common::cal($i).".".$monat.".".$jahr]);
            if(common::$sql['default']->rowCount()) {
                $infoEvent = '';
                foreach($qry as $get) {
                    $infoEvent .='&lt;img src=../inc/images/event.png class=icon alt= /&gt;'.'&nbsp;'.common::jsconvert(_kal_event.stringParser::decode($get['title'])).'<br />';
                }

                $info = '';
                if(!common::$mobile->isMobile() || common::$mobile->isTablet()) {
                    $info = ' onmouseover="DZCP.showInfo(\'' . $infoEvent . '\')" onmouseout="DZCP.hideInfo()"';
                }
                $event = '<a href="?action=show&amp;time='.$datum.'"'.$info.'><img src="../inc/images/event.png" alt="" /></a>';
            }

            $events = $bdays." ".$event;

            if(isset($_GET['hl']) && (int)($_GET['hl']) == $i)
                $day = '<span class="fontMarked">'.common::cal($i).'</span>';
            else 
                $day = common::cal($i);

            if(!checkdate($monat, $i, $jahr)) {
                $data .= '<td class="calDay"></td>';
            } elseif($datum == $ktoday) {
                $smarty->caching = false;
                $smarty->assign('day',$day);
                $smarty->assign('event',$events);
                $smarty->assign('class',"calToday");
                $data .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/day.tpl');
                $smarty->clearAllAssign();
            } else {
                $smarty->caching = false;
                $smarty->assign('day',$day);
                $smarty->assign('event',$events);
                $smarty->assign('class',"calDay");
                $data .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/day.tpl');
                $smarty->clearAllAssign();
            }

            $i++;
        }
    }

    $show .= "<tr>".$data."</tr>";
}

$smarty->caching = false;
$smarty->assign('monate',$month);
$smarty->assign('jahr',$year);
$smarty->assign('show',$show);
$index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/kalender.tpl');
$smarty->clearAllAssign();