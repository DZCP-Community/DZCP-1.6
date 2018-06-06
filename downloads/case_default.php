<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */
$qry = db("SELECT `id`,`name` FROM `".$db['dl_kat']."` ORDER BY `name`;"); $kats = '';
while($get = _fetch($qry)) {
    $qrydl = db("SELECT * FROM ".$db['downloads']." WHERE kat = '".$get['id']."' ORDER BY download");
    $show = "";
    if(_rows($qrydl)) {
        $i = 1;
        while ($getdl = _fetch($qrydl)) {
            if ($i == 0 || $i == 1) {
                $tr1 = '<tr>';
            }

            if ($i == 2) {
                $tr2 = '</tr>';
                $i = 0;
            }

            $target = '';
            $link = show(_downloads_link_new, array("id" => $getdl['id'], "download" => re($getdl['download']), "titel" => re($getdl['download'])));
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;

            $pic = '../inc/images/noimg.png';
            foreach ($picformat as $endung) {
                if (file_exists(basePath . "/inc/images/downloads/" . $getdl['id'] . "." . $endung)) {
                    $pic = '../inc/images/downloads/' . $getdl['id'] . '.' . $endung;
                    break;
                }
            }


            $show .= show($dir . "/downloads_show", array("class" => $class,
                "link" => $link,
                "tr1" => $tr1,
                "id" => $getdl['id'],
                "tr2" => $tr2,
                "pic" => $pic,
                "kid" => $get['id'],
                "display" => $display,
                "beschreibung" => bbcode($getdl['beschreibung']),
                "hits" => $getdl['hits']));
            $i++;
        }

        $cntKat = cnt($db['downloads'], " WHERE kat = '" . $get['id'] . "'");

        if (cnt($db['downloads'], "WHERE kat = '" . $get['id'] . "'") == 1) $dltitel = _dl_file;
        else $dltitel = _site_stats_files;

        $moreicon = '';
        $kat = show(_dl_titel, array("id" => $get['id'],
            "icon" => $moreicon,
            "file" => $dltitel,
            "cnt" => $cntKat,
            "name" => re($get['name'])));

        #$class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;

        $kats .= show($dir . "/download_kats", array("kat" => $kat,
            "class" => $class,
            "kid" => $get['id'],
            "img" => $img,
            "download" => _dl_file,
            "hits" => _hits,
            "show" => $show,
            "display" => $display));
    }
}

$index = show($dir."/downloads", array("kats" => $kats, "newest" => latest_download(), "hottest" => hottest_download()));