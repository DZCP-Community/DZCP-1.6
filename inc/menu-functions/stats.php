<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */
function stats()
{
    global $db, $CrawlerDetect;
    if (!$CrawlerDetect->isCrawler()) {
        $counter = show("menu/counter", array(
            "head_online" => _head_online,
            "head_visits" => _head_visits,
            "head_max" => _head_max,
            "today" => _cnt_today,
            "comments" => cnt($db['newscomments']),
            "yesterday" => _cnt_yesterday,
            "all" => _cnt_all,
            "percentperday" => _cnt_pperday,
            "perday" => _cnt_perday,
            "member" => "Reg. Mitglieder",
            "mem" => cnt($db['users']),
            "news" => cnt($db['news']),
            "dl" => cnt($db['downloads']),
            "fpost" => cnt($db['f_posts']) + cnt($db['f_threads']),
            "online" => _cnt_online));

        return '<table class="navContent" cellspacing="0">' . $counter . '</table>';
    }
}