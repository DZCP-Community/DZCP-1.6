<?php
//kalender
function kalender($month="",$year="")
{
  global $db;
    if(!empty($month) && !empty($year))
    {
      $monat = cal($month);
      $jahr = $year;
    } else {
      $monat = date("m");
      $jahr = date("Y");
    }

    for($i = 1; $i <= 12; $i++)
    {
      $mname = array("1" => _jan,
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
                     "12" => _dez);

      if($monat == $i) $month = $mname[$i];
    }

    $today = mktime(0,0,0,date("n"),date("d"),date("Y"));
    $i = 1;
    while($i <= 31 && checkdate($monat, $i, $jahr))
    {
      unset($event); unset($data); unset($bdays); unset($cws);

      for($iw = 1; $iw <= 7; $iw++)
      {
        unset($titlecw);  unset($titlebd); unset($titleev);

        $datum = mktime(0,0,0,$monat,$i,$jahr);
        $wday = getdate($datum);
        $wday = $wday['wday'];

        if(!$wday) $wday = 7;

        if($wday != $iw)
        {
          $data .= "<td class=\"navKalEmpty\"></td>";
        } else {
          $qry = db("SELECT id,bday FROM ".$db['users']." WHERE bday LIKE '".cal($i).".".$monat.".____"."'");
          if(_rows($qry))
          {
            while($get = _fetch($qry))
            {
              $bdays = "set";
              $titlebd .= '<tr><td><img src=../inc/images/bday.gif class=icon alt= /> '.jsconvert(_kal_birthday.rawautor($get['id'])).'</td></tr>';
            }
          } else {
            $bdays = "";
            $titlebd = "";
          }

          $qry = db("SELECT datum,gegner FROM ".$db['cw']." WHERE DATE_FORMAT(FROM_UNIXTIME(datum), '%d.%m.%Y') = '".cal($i).".".$monat.".".$jahr."'");
          if(_rows($qry))
          {
            while($get = _fetch($qry))
            {
              $cws = "set";
              $titlecw .= '<tr><td><img src=../inc/images/cw.gif class=icon alt= /> '.jsconvert(_kal_cw.re($get['gegner'])).'</td></tr>';
            }
          } else {
            $cws = "";
            $titlecw = "";
          }

          $qry = db("SELECT datum,title FROM ".$db['events']." WHERE DATE_FORMAT(FROM_UNIXTIME(datum), '%d.%m.%Y') = '".cal($i).".".$monat.".".$jahr."'");
          if(_rows($qry))
          {
            while($get = _fetch($qry))
            {
              $event = "set";
              $titleev .= '<tr><td><img src=../inc/images/event.gif class=icon alt= /> '.jsconvert(_kal_event.re($get['title'])).'</td></tr>';
            }
          } else {
            $event = "";
            $titleev = "";
          }

          $info = 'onmouseover="DZCP.showInfo(\'<tr><td colspan=2 align=center padding=3 class=infoTop>'.cal($i).'.'.$monat.'.'.$jahr.'</td></tr>'.$titlebd.$titlecw.$titleev.'\')" onmouseout="DZCP.hideInfo()"';

          if($event == "set" || $cws == "set" || $bdays == "set")
            $day = '<a class="navKal" href="../kalender/?m='.$monat.'&amp;y='.$jahr.'&amp;hl='.$i.'" '.$info.'>'.cal($i).'</a>';
          else $day = cal($i);

          if(!checkdate($monat, $i, $jahr))
          {
            $data .= '<td class="navKalEmpty"></td>';
          } elseif($datum == $today) {
            $data .= show("menu/kal_day", array("day" => $day,
                                                "id" => "navKalToday"));
          } else {
            $data .= show("menu/kal_day", array("day" => $day,
                                                "id" => "navKalDays"));
          }
          $i++;
        }
      }
      $show .= "<tr>".$data."</tr>";
    }

    if(($monat+1) == 13)
    {
      $nm = 1;
      $ny = $jahr+1;
    } else {
      $nm = $monat+1;
      $ny = $jahr;
    }

    if(($monat-1) == 0)
    {
      $lm = 12;
      $ly = $jahr-1;
    } else {
      $lm = $monat-1;
      $ly = $jahr;
    }

    $kalender = show("menu/kalender", array("monat" => $month,
                                            "show" => $show,
                                            "year" => $jahr,
                                            "nm" => $nm,
                                            "ny" => $ny,
                                            "lm" => $lm,
                                            "ly" => $ly,
                                            "montag" => _nav_montag,
                                            "dienstag" => _nav_dienstag,
                                            "mittwoch" => _nav_mittwoch,
                                            "donnerstag" => _nav_donnerstag,
                                            "freitag" => _nav_freitag,
                                            "samstag" => _nav_samstag,
                                            "sonntag" => _nav_sonntag));

  return '<div id="navKalender">'.$kalender.'</div>';
}
?>
