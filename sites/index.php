<?php
## OUTPUT BUFFER START ##
include("../inc/buffer.php");
## INCLUDES ##
include(basePath."/inc/config.php");
include(basePath."/inc/bbcode.php");
## SETTINGS ##
$time_start = generatetime();
lang($language);
$dir = "sites";
## SECTIONS ##
if(!isset($_GET['action'])) $action = "";
else $action = $_GET['action'];

switch ($action):
default:
  $qry = db("SELECT s1.*,s2.internal FROM ".$db['sites']." AS s1
             LEFT JOIN ".$db['navi']." AS s2
             ON s1.id = s2.editor
             WHERE s1.id = '".intval($_GET['show'])."'");
  $get = _fetch($qry);

  if(_rows($qry))
  {
    if($get['internal'] == 1 && ($chkMe == 1 || $chkMe == "unlogged"))
      $index = error(_error_wrong_permissions, 1);
    else {
      $where = re($get['titel']);
      $title = $pagetitle." - ".$where."";
  
      if($get['html'] == "1") $inhalt = bbcode_html($get['text']);
      else $inhalt = bbcode($get['text']);
  
      $index = show($dir."/sites", array("titel" => re($get['titel']),
                                         "inhalt" => $inhalt));
    }
  } else $index = error(_sites_not_available,1);
break;
case 'preview';
  header("Content-type: text/html; charset=utf-8");
  if($_POST['html'] == "1") $inhalt = bbcode_html($_POST['inhalt'],1);
  else $inhalt = bbcode($_POST['inhalt'],1);

  $index = show($dir."/sites", array("titel" => re($_POST['titel']),
                                     "inhalt" => $inhalt));
                                     
  echo '<table class="mainContent" cellspacing="1"'.$index.'</table>';
  exit;
break;
endswitch;
## SETTINGS ##
$time_end = generatetime();
$time = round($time_end - $time_start,4);
page($index, $title, $where,$time);
## OUTPUT BUFFER END ##
gz_output();
?>