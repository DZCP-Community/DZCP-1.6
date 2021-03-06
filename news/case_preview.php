<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if (defined('_News')) {
    header("Content-type: text/html; charset=utf-8");
    $getkat = db("SELECT katimg FROM " . $db['newskat'] . " WHERE id = '" . (int)($_POST['kat']) . "'", false, true);

    $klapp = "";
    if ($_POST['klapptitel']) {
        $klapp = show(_news_klapplink, array("klapplink" => re($_POST['klapptitel']),
            "which" => "collapse",
            "id" => 0));
    }

    $links1 = "";
    $rel = "";
    if (!empty($_POST['url1'])) {
        $rel = _related_links;
        $links1 = show(_news_link, array("link" => re($_POST['link1']),
            "url" => links(re($_POST['url1'], true))));
    }

    $links2 = "";
    if (!empty($_POST['url2'])) {
        $rel = _related_links;
        $links2 = show(_news_link, array("link" => re($_POST['link2']),
            "url" => links(re($_POST['url2'], true))));
    }

    $links3 = "";
    if (!empty($_POST['url3'])) {
        $rel = _related_links;
        $links3 = show(_news_link, array("link" => re($_POST['link3']),
            "url" => links(re($_POST['url3'], true))));
    }

    $links = '';
    if (!empty($links1) || !empty($links2) || !empty($links3)) {
        $links = show(_news_links, array("link1" => $links1,
            "link2" => $links2,
            "link3" => $links3,
            "rel" => $rel));
    }

    $intern = '';
    $sticky = '';
    if (isset($_POST['intern']) && $_POST['intern'])
        $intern = _votes_intern;

    if (isset($_POST['sticky']) && $_POST['sticky'])
        $sticky = _news_sticky;

    $newsimage = '../inc/images/newskat/' . re($getkat['katimg']);
    $viewed = show(_news_viewed, array("viewed" => '0'));
    $index = show($dir . "/news_show_full", array("titel" => re($_POST['titel']),
        "kat" => $newsimage,
        "id" => '_prev',
        "comments" => _news_comments_prev,
        "showmore" => "",
        "dp" => "",
        "edit" => "",
        "dir" => $designpath,
        "nautor" => _autor,
        "intern" => $intern,
        "sticky" => $sticky,
        "ndatum" => _datum,
        "ncomments" => _news_kommentare . ":",
        "klapp" => $klapp,
        "more" => bbcode(re($_POST['morenews'], true)),
        "viewed" => $viewed,
        "text" => bbcode(re($_POST['newstext'], true)),
        "datum" => date("d.m.y H:i", time()) . _uhr,
        "links" => $links,
        "autor" => autor($userid)));

    echo utf8_encode('<table class="mainContent" cellspacing="1">' . $index . '</table>');

    if (!mysqli_persistconns)
        $mysql->close(); //MySQL

    exit();
}