<?php
// Download Kategorie bestimmen
function dlkat($id)
{
    global $db;
    $get = db("SELECT s1.name AS subkat,s2.name AS kat FROM ".$db['dl_subkat']." AS s1
             LEFT JOIN ".$db['dl_kat']." As s2
             ON s1.kid = s2.id
             WHERE s1.id = '".$id."'",false,true);

    return $get['kat'].' / '.$get['subkat'];
}
// Filesize
function get_filesize($file,$type='dl')
{
    return '';

    global $db;

    if(@filesize($file) == false || substr(@filesize($file),0,1) == '-')
    {
        if($type == 'dl') {
            $qry = db("SELECT size FROM ".$db['downloads']."
                 WHERE url = '".up($file)."'");
            $get = _fetch($qry);

            $size = @round($get['size']*1048576,2);
        }
    } else  $size = @filesize($file);

    $size_gb = @round($size/1073741824,2);
    $size_mb = @round($size/1048576,2);
    $size_kb = @round($size/1024,2);

    if(substr($size_gb,0,1) != 0)     $size = $size_gb." GB";
    elseif(substr($size_mb,0,1) != 0) $size = $size_mb." MB";
    else                              $size = $size_kb." KB";

    return $size;
}
// Zeitdifferenz berechnen
function time_difference($time)
{
    $time = time()-$time;

    $days = floor($time/86400);
    $rest = $time-($days*86400);
    $std = floor($rest/3600);
    $rest = $rest-($std*3600);
    $min = floor($rest/60);
    $sec = $rest-($min*60);


    if($days != 0) $ret = $days.' T';
    if($std != 0)  $ret .= ' '.$std.'Std' ;
    if($min != 0)  $ret .= ' '.$min.'Min';
    if($sec != 0 && $min == 0 && $std == 0 && $days == 0)  $ret .= ' '.$sec.'Sec';


    return $ret;
}