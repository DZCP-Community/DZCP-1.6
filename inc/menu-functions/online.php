<?php
function online()
{
    global $CrawlerDetect;
    if (!$CrawlerDetect->isCrawler()) {
        $users_reg = online_reg();
        $users_gust = online_guests();
        $users_sum = $users_gust + $users_reg;
    } else {
        $users_sum = 0;
    }

    return abs($users_sum);
}