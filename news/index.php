<?php
/**
 * DZCP - deV!L`z ClanPortal - Mainpage ( dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * Diese Datei ist ein Bestandteil von dzcp.de
 * Diese Version wurde speziell von Lucas Brucksch (Codedesigns) für dzcp.de entworfen bzw. verändert.
 * Eine Weitergabe dieser Datei außerhalb von dzcp.de ist nicht gestattet.
 * Sie darf nur für die Private Nutzung (nicht kommerzielle Nutzung) verwendet werden.
 *
 * Homepage: http://www.dzcp.de
 * E-Mail: info@web-customs.com
 * E-Mail: lbrucksch@codedesigns.de
 * Copyright 2017 © CodeKing, my-STARMEDIA, Codedesigns
 */

## OUTPUT BUFFER START ##
if(!ob_start("ob_gzhandler")) ob_start();
define('basePath', dirname(dirname(__FILE__).'../'));

## INCLUDES ##
include(basePath."/inc/common.php");

use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

## SETTINGS ##
$where = _site_news;
$dir = "news";
define('_News', true);
$smarty = common::getSmarty(); //Use Smarty

## SECTIONS ##

//-> Entfernt fuehrende Nullen bei Monatsangaben
function nonum($i) {
    if (preg_match("=10=Uis", $i) == false) {
        return preg_replace("=0=", "", $i);
    }

    return $i;
}

if(settings::get('news_feed')) { //NewsFeed
    if(!file_exists(basePath.'/rss.xml') || time() - filemtime(basePath.'/rss.xml') > config::$feed_update_time) {
        $feed = new Feed();
        $host = common::GetServerVars('HTTP_HOST');
        $pfad = preg_replace("#^(.*?)\/(.*?)#Uis", "$1", dirname(common::GetServerVars('PHP_SELF')));
        $channel = new Channel();
        $channel
            ->title(common::$pagetitle)
            ->description('Clannews von ' . stringParser::decode(settings::get('clanname')))
            ->url('http://' . $host)
            ->language('de-DE')
            ->copyright(date("Y", time()) . ' ' . stringParser::decode(settings::get('clanname')))
            ->pubDate(time())
            ->lastBuildDate(time())
            ->ttl(60)
            ->appendTo($feed);

        $qry = common::$sql['default']->select("SELECT `id`,`autor`,`datum`,`titel`,`text`,`kat` FROM `{prefix_news}` WHERE `intern` = 0 AND `public` = 1 ORDER BY `datum` DESC LIMIT 15;");
        if (common::$sql['default']->rowCount()) {
            foreach ($qry as $get) {
                $kategorie = common::$sql['default']->fetch("SELECT `kategorie` FROM `{prefix_news_kats}` WHERE `id` = ?;", [$get['kat']],'kategorie');
                if(!common::$sql['default']->rowCount())
                    $kategorie = '';

                $item = new Item();
                $item
                    ->title(stringParser::decode($get['titel']))
                    ->description(stringParser::decode($get['text']))
                    ->contentEncoded(stringParser::decode($get['text']))
                    ->url('http://' . $host . $pfad . '/news/?action=show&id=' . $get['id'])
                    ->author(stringParser::decode(common::data('nick', $get['autor'])))
                    ->pubDate($get['datum'])
                    ->guid('http://' . $host . $pfad . '/news/?action=show&id=' . $get['id'], true)
                    ->preferCdata(true)// By this, title and description become CDATA wrapped HTML.
                    ->category(stringParser::decode($kategorie))
                    ->appendTo($channel);
            }

            unset($get,$item,$kategorie);
        }

        file_put_contents(basePath . '/rss.xml', $feed);
    }

    unset($feed,$host,$pfad,$channel,$qry);
}

if (file_exists(basePath . "/news/case_" . common::$action . ".php")) {
    require_once(basePath . "/news/case_" . common::$action . ".php");
}

## INDEX OUTPUT ##
$title = common::$pagetitle." - ".$where;
common::page($index, $title, $where);