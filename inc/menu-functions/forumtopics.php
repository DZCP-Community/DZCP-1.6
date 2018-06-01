<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 * @param bool $right
 * @return string
 */
function forumtopics($right = false) {
    global $db, $page, $lftopics;

    $ftopics = ''; $fintern = '';
    if (!permission("intforum"))
        $fintern = "AND s3.intern = 0";

    $qry = db("SELECT s1.*,s2.`kattopic`,s2.`id` AS `subid` FROM `" . $db['f_threads'] . "` AS `s1`, `" . $db['f_skats'] . "` AS `s2`, `" .
        $db['f_kats'] . "` AS `s3` WHERE s1.`kid` = s2.`id` " . $fintern . " AND s2.`sid` = s3.`id` ORDER BY s1.`lp` DESC " .
        (empty($right) ? 'LIMIT 4;' : 'LIMIT 4, 4;') . ";");

    while ($get = _fetch($qry)) {
        if (fintern($get['kid'])) {
            $lp = cnt($db['f_posts'], " WHERE `sid` = " . $get['id']);
            $reg = db("SELECT `reg` FROM `" . $db['f_posts'] . "` WHERE `sid` = " . $get['id'] . " ORDER BY `date` DESC;",false,true);

            $info = '';
            if (config('allowhover') == 1)
                $info = 'onmouseover="DZCP.showInfo(\'<tr><td colspan=2 align=center padding=3 class=infoTop>' .
                    jsconvert(re($get['topic'])) . '</td></tr><tr><td><b>' . _forum_posts . ':</b></td><td>' . $lp . '</td></tr><tr><td><b>' .
                    _forum_lpost . ':</b></td><td>' . date("d.m.Y H:i", $get['lp']) . _uhr . '</td></tr>\')" onmouseout="DZCP.hideInfo()"';

            $ftopics .= show("menu/forum_topics", array("id" => $get['id'],
                "pagenr" => $page,
                "p" => $lp + 1,
                "autor" => autor(empty($reg['reg']) ? $get['t_reg'] : $reg['reg']),
                "datum" => date("d.m.Y H:i", $get['lp']) . _uhr,
                "kat" => cut(re($get['topic']), (empty($get['subtopic']) ? (2 * $lftopics) : $lftopics)),
                "subkat" => (empty($get['subtopic']) ? '' : ' &raquo; ' . cut(re($get['subtopic']), $lftopics)),
                "info" => $info,
                "kategorie" => cut(re($get['kattopic']), 18),
                "kid" => $get['kid']));
        }
    }
    return empty($ftopics) ? '' : '<table class="navContent" cellspacing="0">' . $ftopics . '</table>';
}