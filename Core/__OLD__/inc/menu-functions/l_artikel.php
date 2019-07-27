<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 * Menu: Last Articles
 */
function l_artikel()
{
    global $db;

    $qry = db("SELECT `id`,`titel`,`text`,`autor`,`datum`,`kat`,`public` FROM " . $db['artikel'] . "
               WHERE `public` = 1
               ORDER BY id DESC
               LIMIT " . config('m_lartikel'));

    $l_articles = '';
    if (_rows($qry)) {
        while ($get = _fetch($qry)) {
            $getkat = db("SELECT `kategorie` FROM " . $db['newskat'] . " WHERE `id` = '" . $get['kat'] . "'", false, true);
            $text = strip_tags($get['text']);

            if (config('allowhover') == 1)
                $info = 'onmouseover="DZCP.showInfo(\'' . up(re($get['titel'])) . '\', \'' . _datum . ';' . _autor . ';' . _news_admin_kat . ';' . _comments_head . '\', \'' . date("d.m.Y H:i", $get['datum']) . _uhr . ';' . fabo_autor($get['autor']) . ';' . jsconvert(re($getkat['kategorie'])) . ';' . cnt($db['acomments'], "WHERE artikel = '" . $get['id'] . "'") . '\')" onmouseout="DZCP.hideInfo()"';

            $l_articles .= show("menu/last_artikel", array("id" => $get['id'],
                "titel" => re(cut($get['titel'], config('l_lartikel'), true, false)),
                "text" => cut(bbcode($text), 260),
                "datum" => date("d.m.Y", $get['datum']),
                "info" => $info));
        }
    }

    if (empty($l_articles)) {
        $l_articles = show(_no_entrys_yet, array("colspan" => "0"));
    }

    return '<table class="navContent" cellspacing="0">' . $l_articles . '</table>';
}