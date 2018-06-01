<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 * Menu: Last News
 */
function l_news() {
    global $db;

    $qry = db("SELECT `id`,`titel`,`autor`,`datum`,`kat`,`public`,`timeshift` FROM ".$db['news']."
               WHERE `public` = 1
               AND datum <= ".time()."
               ".(permission("intnews") ? "" : "AND `intern` = 0")."
               ORDER BY id DESC
               LIMIT ".config('m_lnews'));

    $l_news = '';
    if(_rows($qry)) {
        while($get = _fetch($qry)) {
          $getkat = db("SELECT `kategorie` FROM ".$db['newskat']." WHERE `id` = '".$get['kat']."'",false,true);

          if(config('allowhover') == 1)
              $info = 'onmouseover="DZCP.showInfo(\''.jsconvert(re($get['titel'])).'\', \''._datum.';'._autor.';'._news_admin_kat.';'._comments_head.'\', \''.date("d.m.Y H:i", $get['datum'])._uhr.';'.fabo_autor($get['autor']).';'.jsconvert(re($getkat['kategorie'])).';'.cnt($db['newscomments'],"WHERE news = '".$get['id']."'").'\')" onmouseout="DZCP.hideInfo()"';

          $l_news .= show("menu/last_news", array("id" => $get['id'],
                                                  "titel" => re(cut($get['titel'],config('l_lnews'),true,false)),
                                                  "datum" => date("d.m.Y", $get['datum']),
                                                  "info" => $info));
        }
    }

    if(empty($l_news)) {
        $l_news = show(_no_entrys_yet, array("colspan" => "0"));
    }

    return '<table class="navContent" cellspacing="0">'.$l_news.'</table>';
}