<?php
//-> Top Downloads
function top_dl()
{
  global $db,$maxtopdl,$ltopdl,$chkMe;

  if($chkMe == "unlogged" || $chkMe < "1") $intern = 'WHERE intern = 0 AND public = 1';
  if($chkMe == "1") $intern = 'WHERE intern < 2 AND public = 1';
  if($chkMe == "2") $intern = 'WHERE intern < 3 AND public = 1';
  if($chkMe == "3") $intern = 'WHERE intern < 4 AND public = 1';
  if($chkMe == "4") $intern = 'WHERE public = 1';

  $qry = db("SELECT * FROM ".$db['downloads']."
             ".$intern."
             ORDER BY hits DESC
             LIMIT ".$maxtopdl."");
  if(_rows($qry))
  {
    while ($get = _fetch($qry))
    {
      $top_dl .= show("menu/top_dl", array("id" => $get['id'],
                                           "titel" => cut($get['download'],$ltopdl),
                                           "kat" => $get['kat'],
                                           "hits" => $get['hits']));
    }
  }

  return empty($top_dl) ? '' : '<table class="navContent" cellspacing="0">'.$top_dl.'</table>';
}
?>
