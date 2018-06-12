<?php
//-> Funktion um bei Clanwars Details Endergebnisse auszuwerten ohne bild
function cw_result_details(int $punkte, int $gpunkte) {
    if($punkte > $gpunkte)
        return '<td class="contentMainFirst" align="center"><span class="CwWon">'.$punkte.'</span></td><td class="contentMainFirst" align="center"><span class="CwLost">'.$gpunkte.'</span></td>';
    else if($punkte < $gpunkte)
        return '<td class="contentMainFirst" align="center"><span class="CwLost">'.$punkte.'</span></td><td class="contentMainFirst" align="center"><span class="CwWon">'.$gpunkte.'</span></td>';
    else
        return '<td class="contentMainFirst" align="center"><span class="CwDraw">'.$punkte.'</span></td><td class="contentMainFirst" align="center"><span class="CwDraw">'.$gpunkte.'</span></td>';
}

//-> Funktion um bei Clanwars Endergebnisse auszuwerten ohne bild und ohne farbe
function cw_result_nopic_nocolor(int $punkte, int $gpunkte) {
    if($punkte > $gpunkte)
        return $punkte.':'.$gpunkte;
    else if($punkte < $gpunkte)
        return $punkte.':'.$gpunkte;
    else
        return $punkte.':'.$gpunkte;
}

function cw_result_pic(int $punkte, int $gpunkte) {
    if($punkte > $gpunkte)
        return '<img src="../inc/images/won.gif" alt="" class="icon" />';
    else if($punkte < $gpunkte)
        return '<img src="../inc/images/lost.gif" alt="" class="icon" />';
    else
        return '<img src="../inc/images/draw.gif" alt="" class="icon" />';
}

//-> Funktion um bei Clanwars Endergebnisse auszuwerten ohne bild
function cw_result_nopic(int $punkte, int $gpunkte) {
    if($punkte > $gpunkte)
        return '<span class="CwWon">'.$punkte.':'.$gpunkte.'</span>';
    else if($punkte < $gpunkte)
        return '<span class="CwLost">'.$punkte.':'.$gpunkte.'</span>';
    else
        return '<span class="CwDraw">'.$punkte.':'.$gpunkte.'</span>';
}