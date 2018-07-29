<?php
function time_convert($time, $ms = false) {
    if($ms) $time = $time / 1000;

    $day = floor($time/86400);
    $hours = floor(($time%86400)/3600);
    $minutes = floor(($time%3600)/60);
    $seconds = floor($time%60);

    if($day>0) $time = $day."d ".$hours."h ".$minutes."m ".$seconds."s";
    elseif($hours>0) $time = $hours."h ".$minutes."m ".$seconds."s";
    elseif($minutes>0) $time = $minutes."m ".$seconds."s";
    else $time = $seconds."s";

    return $time;
}