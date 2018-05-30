<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

//MySQL-Daten einlesen
$installation = true;
include(basePath.'/inc/config.php');
set_time_limit(600);
ini_set('max_execution_time', 600);

function install_mysql($login, $nick, $pwd, $email) {
    global $db;
    //-> Tabellenstruktur für Tabelle `dzcp_acomments`
    db("DROP TABLE IF EXISTS `".$db['acomments']."`;");
    db("CREATE TABLE `".$db['acomments']."` (
      `id` int(10) NOT NULL,
      `artikel` int(10) NOT NULL DEFAULT '0',
      `nick` varchar(20) NOT NULL DEFAULT '',
      `datum` int(20) NOT NULL DEFAULT '0',
      `email` varchar(130) NOT NULL DEFAULT '',
      `hp` varchar(50) NOT NULL DEFAULT '',
      `reg` int(5) NOT NULL DEFAULT '0',
      `comment` text NOT NULL,
      `ip` varchar(50) NOT NULL DEFAULT '',
      `editby` text NOT NULL);");

    //-> Tabellenstruktur für Tabelle `dzcp_artikel`
    db("DROP TABLE IF EXISTS `".$db['artikel']."`;");
    db("CREATE TABLE `".$db['artikel']."` (
      `id` int(10) NOT NULL,
      `autor` varchar(5) NOT NULL DEFAULT '',
      `datum` varchar(20) NOT NULL DEFAULT '',
      `kat` int(2) NOT NULL DEFAULT '0',
      `titel` varchar(249) NOT NULL DEFAULT '',
      `text` text NOT NULL,
      `link1` varchar(100) NOT NULL DEFAULT '',
      `url1` varchar(200) NOT NULL DEFAULT '',
      `link2` varchar(100) NOT NULL DEFAULT '',
      `url2` varchar(200) NOT NULL DEFAULT '',
      `link3` varchar(100) NOT NULL DEFAULT '',
      `url3` varchar(200) NOT NULL DEFAULT '',
      `public` int(1) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `dzcp_artikel`
    db("INSERT INTO `".$db['artikel']."` VALUES (1, '1', '".time()."', 1, 'Testartikel', '<p>Hier k&ouml;nnte dein Artikel stehen!</p>\r\n<p> </p>', '', '', '', '', '', '', 1);");

    //-> Tabellenstruktur für Tabelle `dzcp_awards`
    db("DROP TABLE IF EXISTS `".$db['awards']."`;");
    db("CREATE TABLE `".$db['awards']."` (
      `id` int(5) NOT NULL,
      `squad` int(10) NOT NULL,
      `date` varchar(20) NOT NULL DEFAULT '',
      `postdate` varchar(20) NOT NULL DEFAULT '',
      `event` varchar(50) NOT NULL DEFAULT '',
      `place` varchar(5) NOT NULL DEFAULT '',
      `prize` text NOT NULL,
      `url` text NOT NULL);");

    //-> Tabellenstruktur für Tabelle `dzcp_away`
    db("DROP TABLE IF EXISTS `".$db['away']."`;");
    db("CREATE TABLE `".$db['away']."` (
      `id` int(5) NOT NULL,
      `userid` int(14) NOT NULL DEFAULT '0',
      `titel` varchar(30) NOT NULL,
      `reason` longtext NOT NULL,
      `start` int(20) NOT NULL DEFAULT '0',
      `end` int(20) NOT NULL DEFAULT '0',
      `date` text NOT NULL,
      `lastedit` text NOT NULL);");

    //-> Tabellenstruktur für Tabelle `dzcp_clankasse`
    db("DROP TABLE IF EXISTS `".$db['clankasse']."`;");
    db("CREATE TABLE `".$db['clankasse']."` (
      `id` int(20) NOT NULL,
      `datum` varchar(20) NOT NULL DEFAULT '',
      `member` varchar(50) NOT NULL DEFAULT '0',
      `transaktion` varchar(249) NOT NULL DEFAULT '',
      `pm` int(1) NOT NULL DEFAULT '0',
      `betrag` varchar(10) NOT NULL DEFAULT '0');");

    //-> Tabellenstruktur für Tabelle `dzcp_clankasse_kats`
    db("DROP TABLE IF EXISTS `".$db['c_kats']."`;");
    db("CREATE TABLE `".$db['c_kats']."` (
      `id` int(5) NOT NULL,
      `kat` varchar(30) NOT NULL DEFAULT '');");

    //-> Daten für Tabelle `dzcp_clankasse_kats`
    db("INSERT INTO `".$db['c_kats']."` VALUES (1, 'Servermiete'), (2, 'Serverbeitrag');");

    //-> Tabellenstruktur für Tabelle `dzcp_clankasse_payed`
    db("DROP TABLE IF EXISTS `".$db['c_payed']."`");
    db("CREATE TABLE `".$db['c_payed']."` (
      `id` int(5) NOT NULL,
      `user` int(5) NOT NULL DEFAULT '0',
      `payed` varchar(20) NOT NULL DEFAULT '0');");

    //-> Tabellenstruktur für Tabelle `dzcp_clanwars`
    db("DROP TABLE IF EXISTS `".$db['cw']."`;");
    db("CREATE TABLE `".$db['cw']."` (
      `id` int(5) NOT NULL,
      `squad_id` int(19) NOT NULL,
      `gametype` varchar(249) NOT NULL DEFAULT '',
      `gcountry` varchar(20) NOT NULL DEFAULT 'de',
      `matchadmins` varchar(249) NOT NULL DEFAULT '',
      `lineup` varchar(249) NOT NULL DEFAULT '',
      `glineup` varchar(249) NOT NULL DEFAULT '',
      `datum` int(20) NOT NULL DEFAULT '0',
      `clantag` varchar(20) NOT NULL DEFAULT '',
      `gegner` varchar(100) NOT NULL DEFAULT '',
      `url` varchar(249) NOT NULL DEFAULT '',
      `xonx` varchar(10) NOT NULL DEFAULT '',
      `liga` varchar(30) NOT NULL DEFAULT '',
      `punkte` int(5) NOT NULL DEFAULT '0',
      `gpunkte` int(5) NOT NULL DEFAULT '0',
      `maps` varchar(30) NOT NULL DEFAULT '',
      `serverip` varchar(50) NOT NULL DEFAULT '',
      `servername` varchar(249) NOT NULL DEFAULT '',
      `serverpwd` varchar(20) NOT NULL DEFAULT '',
      `bericht` text NOT NULL,
      `top` int(1) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `dzcp_clanwars`
    db("INSERT INTO `".$db['cw']."` VALUES (1, 1, '', 'de', '', '', '', ".(time()-1000).", 'DZCP', 'deV!L`z Clanportal', 'http://www.dzcp.de', '5on5', 'DZCP', 0, 21, 'de_dzcp', '', '', '', '', 1);");

    //-> Tabellenstruktur für Tabelle `dzcp_clanwar_players`
    db("DROP TABLE IF EXISTS `".$db['cw_player']."`;");
    db("CREATE TABLE `".$db['cw_player']."` (
      `cwid` int(5) NOT NULL DEFAULT '0',
      `member` int(5) NOT NULL DEFAULT '0',
      `status` int(5) NOT NULL DEFAULT '0');");

    //-> Tabellenstruktur für Tabelle `dzcp_config`
    db("DROP TABLE IF EXISTS `".$db['config']."`;");
    db("CREATE TABLE `".$db['config']."` (
      `id` int(1) NOT NULL DEFAULT '1',
      `upicsize` int(5) NOT NULL DEFAULT '100',
      `gallery` int(5) NOT NULL DEFAULT '4',
      `m_usergb` int(5) NOT NULL DEFAULT '10',
      `m_clanwars` int(5) NOT NULL DEFAULT '10',
      `maxshoutarchiv` int(5) NOT NULL DEFAULT '20',
      `m_clankasse` int(5) NOT NULL DEFAULT '20',
      `m_awards` int(5) NOT NULL DEFAULT '15',
      `m_userlist` int(5) NOT NULL DEFAULT '40',
      `m_banned` int(5) NOT NULL DEFAULT '40',
      `maxwidth` int(4) NOT NULL DEFAULT '400',
      `shout_max_zeichen` int(5) NOT NULL DEFAULT '100',
      `l_servernavi` int(5) NOT NULL DEFAULT '22',
      `m_adminnews` int(5) NOT NULL DEFAULT '20',
      `m_shout` int(5) NOT NULL DEFAULT '10',
      `m_comments` int(5) NOT NULL DEFAULT '10',
      `m_archivnews` int(5) NOT NULL DEFAULT '30',
      `m_gb` int(5) NOT NULL DEFAULT '10',
      `m_fthreads` int(5) NOT NULL DEFAULT '20',
      `m_fposts` int(5) NOT NULL DEFAULT '10',
      `m_news` int(5) NOT NULL DEFAULT '5',
      `f_forum` int(5) NOT NULL DEFAULT '20',
      `l_shoutnick` int(5) NOT NULL DEFAULT '20',
      `f_gb` int(5) NOT NULL DEFAULT '20',
      `f_membergb` int(5) NOT NULL DEFAULT '20',
      `f_shout` int(5) NOT NULL DEFAULT '20',
      `f_newscom` int(5) NOT NULL DEFAULT '20',
      `f_cwcom` int(5) NOT NULL DEFAULT '20',
      `f_artikelcom` int(5) NOT NULL DEFAULT '20',
      `l_newsadmin` int(5) NOT NULL DEFAULT '20',
      `l_shouttext` int(5) NOT NULL DEFAULT '22',
      `l_newsarchiv` int(5) NOT NULL DEFAULT '20',
      `l_forumtopic` int(5) NOT NULL DEFAULT '20',
      `l_forumsubtopic` int(5) NOT NULL DEFAULT '20',
      `l_clanwars` int(5) NOT NULL DEFAULT '30',
      `m_gallerypics` int(5) NOT NULL DEFAULT '5',
      `m_lnews` int(5) NOT NULL DEFAULT '6',
      `m_topdl` int(5) NOT NULL DEFAULT '5',
      `m_ftopics` int(5) NOT NULL DEFAULT '6',
      `m_lwars` int(5) NOT NULL DEFAULT '6',
      `m_nwars` int(5) NOT NULL DEFAULT '6',
      `l_topdl` int(5) NOT NULL DEFAULT '20',
      `l_ftopics` int(5) NOT NULL DEFAULT '28',
      `l_lnews` int(5) NOT NULL DEFAULT '22',
      `l_lwars` int(5) NOT NULL DEFAULT '12',
      `l_nwars` int(5) NOT NULL DEFAULT '12',
      `l_lreg` int(5) NOT NULL DEFAULT '12',
      `m_lreg` int(5) NOT NULL DEFAULT '5',
      `m_artikel` int(5) NOT NULL DEFAULT '15',
      `m_cwcomments` int(5) NOT NULL DEFAULT '10',
      `m_adminartikel` int(5) NOT NULL DEFAULT '15',
      `securelogin` int(1) NOT NULL DEFAULT '0',
      `allowhover` int(1) NOT NULL DEFAULT '1',
      `teamrow` int(1) NOT NULL DEFAULT '3',
      `l_lartikel` int(1) NOT NULL DEFAULT '18',
      `m_lartikel` int(1) NOT NULL DEFAULT '5',
      `l_team` int(5) NOT NULL DEFAULT '7',
      `m_events` int(5) NOT NULL DEFAULT '5',
      `m_away` int(5) NOT NULL DEFAULT '10',
      `cache_teamspeak` int(10) NOT NULL DEFAULT '30',
      `cache_server` int(10) NOT NULL DEFAULT '30',
      `direct_refresh` int(1) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `".$db['config']."`
    db("INSERT INTO `".$db['config']."` VALUES (1, 100, 4, 10, 10, 20, 20, 15, 40, 40, 500, 100, 22, 20, 10, 10, 30, 10, 20, 10, 5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 22, 20, 20, 20, 30, 5, 6, 5, 6, 6, 6, 20, 28, 22, 12, 12, 12, 5, 15, 10, 15, 0, 1, 3, 18, 5, 7, 5, 10, 30, 30, 0);");

    //-> Tabellenstruktur für Tabelle `".$db['counter']."`
    db("DROP TABLE IF EXISTS `".$db['counter']."`;");
    db("CREATE TABLE `".$db['counter']."` (
      `id` int(5) NOT NULL,
      `visitors` int(20) NOT NULL DEFAULT '0',
      `today` varchar(50) NOT NULL DEFAULT '0',
      `maxonline` int(5) NOT NULL DEFAULT '0');");

    //-> Tabellenstruktur für Tabelle `".$db['c_ips']."`
    db("DROP TABLE IF EXISTS `".$db['c_ips']."`;");
    db("CREATE TABLE `".$db['c_ips']."` (
      `id` int(10) NOT NULL,
      `ip` varchar(30) NOT NULL DEFAULT '0',
      `datum` int(20) NOT NULL DEFAULT '0');");

    //-> Tabellenstruktur für Tabelle `".$db['c_who']."`
    db("DROP TABLE IF EXISTS `".$db['c_who']."`;");
    db("CREATE TABLE `".$db['c_who']."` (
      `id` int(50) NOT NULL,
      `ip` char(50) NOT NULL DEFAULT '',
      `online` int(20) NOT NULL DEFAULT '0',
      `whereami` text NOT NULL,
      `login` int(1) NOT NULL DEFAULT '0');");

    //-> Tabellenstruktur für Tabelle `".$db['cw_comments']."`
    db("DROP TABLE IF EXISTS `".$db['cw_comments']."`;");
    db("CREATE TABLE `".$db['cw_comments']."` (
      `id` int(10) NOT NULL,
      `cw` int(10) NOT NULL DEFAULT '0',
      `nick` varchar(20) NOT NULL DEFAULT '',
      `datum` int(20) NOT NULL DEFAULT '0',
      `email` varchar(130) NOT NULL DEFAULT '',
      `hp` varchar(50) NOT NULL DEFAULT '',
      `reg` int(5) NOT NULL DEFAULT '0',
      `comment` text NOT NULL,
      `ip` varchar(50) NOT NULL DEFAULT '',
      `editby` text NOT NULL);");

    //-> Tabellenstruktur für Tabelle `".$db['downloads']."`
    db("DROP TABLE IF EXISTS `".$db['downloads']."`;");
    db("CREATE TABLE `".$db['downloads']."` (
      `id` int(11) NOT NULL,
      `download` varchar(249) NOT NULL DEFAULT '',
      `url` varchar(249) NOT NULL DEFAULT '',
      `beschreibung` varchar(249) DEFAULT NULL,
      `hits` int(50) NOT NULL DEFAULT '0',
      `kat` int(5) NOT NULL DEFAULT '0',
      `date` int(20) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `".$db['downloads']."`
    db("INSERT INTO `".$db['downloads']."` VALUES (1, 'Testdownload', 'http://www.url.de/test.zip', '<p>Das ist ein Testdownload</p>', 0, 1, ".(time()-405).");");

    //-> Tabellenstruktur für Tabelle `".$db['dl_kat']."`
    db("DROP TABLE IF EXISTS `".$db['dl_kat']."`;");
    db("CREATE TABLE `".$db['dl_kat']."` (
      `id` int(11) NOT NULL,
      `name` varchar(249) NOT NULL DEFAULT '');");

    //-> Daten für Tabelle `".$db['dl_kat']."`
    db("INSERT INTO `".$db['dl_kat']."` VALUES (1, 'Downloads'), (2, 'Demos'), (3, 'Stuff');");

    //-> Tabellenstruktur für Tabelle `".$db['events']."`
    db("DROP TABLE IF EXISTS `".$db['events']."`;");
    db("CREATE TABLE `".$db['events']."` (
      `id` int(5) NOT NULL,
      `datum` int(20) NOT NULL DEFAULT '0',
      `title` varchar(30) NOT NULL DEFAULT '',
      `event` text NOT NULL);");

    //-> Daten für Tabelle `".$db['events']."`
    db("INSERT INTO `".$db['events']."` VALUES (1, ".(time()+700).", 'Testevent', '<p>Das ist nur ein Testevent! :)</p>');");

    //-> Tabellenstruktur für Tabelle `".$db['f_kats']."`
    db("DROP TABLE IF EXISTS `".$db['f_kats']."`;");
    db("CREATE TABLE `".$db['f_kats']."` (
      `id` int(10) NOT NULL,
      `kid` int(10) NOT NULL DEFAULT '0',
      `name` varchar(50) NOT NULL DEFAULT '',
      `intern` int(1) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `".$db['f_kats']."`
    db("INSERT INTO `".$db['f_kats']."` VALUES (1, 1, 'Hauptforum', 0), (2, 2, 'OFFtopic', 0), (3, 3, 'Clanforum', 1);");

    //-> Tabellenstruktur für Tabelle `".$db['f_posts']."`
    db("DROP TABLE IF EXISTS `".$db['f_posts']."`;");
    db("CREATE TABLE `".$db['f_posts']."` (
      `id` int(10) NOT NULL,
      `kid` int(2) NOT NULL DEFAULT '0',
      `sid` int(2) NOT NULL DEFAULT '0',
      `date` int(20) NOT NULL DEFAULT '0',
      `nick` varchar(30) NOT NULL DEFAULT '',
      `reg` int(1) NOT NULL DEFAULT '0',
      `email` varchar(130) NOT NULL DEFAULT '',
      `text` text NOT NULL,
      `edited` text NOT NULL,
      `ip` varchar(50) NOT NULL DEFAULT '',
      `hp` varchar(249) NOT NULL DEFAULT '');");

    //-> Tabellenstruktur für Tabelle `".$db['f_skats']."`
    db("DROP TABLE IF EXISTS `".$db['f_skats']."`;");
    db("CREATE TABLE `".$db['f_skats']."` (
      `id` int(10) NOT NULL,
      `sid` int(10) NOT NULL DEFAULT '0',
      `kattopic` varchar(150) NOT NULL DEFAULT '',
      `subtopic` varchar(150) NOT NULL DEFAULT '');");

    //-> Daten für Tabelle `".$db['f_skats']."`
    db("INSERT INTO `".$db['f_skats']."` VALUES
    (1, 1, 'Allgemein', 'Allgemeines...'),
    (2, 1, 'Homepage', 'Kritiken/Anregungen/Bugs'),
    (3, 1, 'Server', 'Serverseitige Themen...'),
    (4, 1, 'Spam', 'Spamt die Bude voll ;)'),
    (5, 2, 'Sonstiges', ''),
    (6, 2, 'OFFtopic', ''),
    (7, 3, 'internes Forum', 'interne Angelegenheiten'),
    (8, 3, 'Server intern', 'interne Serverangelegenheiten'),
    (9, 3, 'War Forum', 'Alles &uuml;ber und rundum Clanwars');");

    //-> Tabellenstruktur für Tabelle `".$db['f_threads']."`
    db("DROP TABLE IF EXISTS `".$db['f_threads']."`;");
    db("CREATE TABLE `".$db['f_threads']."` (
      `id` int(10) NOT NULL,
      `kid` int(10) NOT NULL DEFAULT '0',
      `t_date` int(20) NOT NULL DEFAULT '0',
      `topic` varchar(249) NOT NULL DEFAULT '',
      `subtopic` varchar(100) NOT NULL DEFAULT '',
      `t_nick` varchar(30) NOT NULL DEFAULT '',
      `t_reg` int(1) NOT NULL DEFAULT '0',
      `t_email` varchar(130) NOT NULL DEFAULT '',
      `t_text` text NOT NULL,
      `hits` int(10) NOT NULL DEFAULT '0',
      `first` int(1) NOT NULL DEFAULT '0',
      `lp` int(20) NOT NULL DEFAULT '0',
      `sticky` int(1) NOT NULL DEFAULT '0',
      `closed` int(1) NOT NULL DEFAULT '0',
      `global` int(1) NOT NULL DEFAULT '0',
      `edited` text NOT NULL,
      `ip` varchar(50) NOT NULL DEFAULT '',
      `t_hp` varchar(249) NOT NULL DEFAULT '',
      `vote` varchar(10) NOT NULL DEFAULT '0');");

    //-> Tabellenstruktur für Tabelle `".$db['f_abo']."`
    db("DROP TABLE IF EXISTS `".$db['f_abo']."`;");
    db("CREATE TABLE `".$db['f_abo']."` (
      `id` int(10) NOT NULL,
      `fid` int(10) NOT NULL,
      `datum` int(20) NOT NULL,
      `user` int(5) NOT NULL);");

    //-> Tabellenstruktur für Tabelle `".$db['f_access']."`
    db("DROP TABLE IF EXISTS `".$db['f_access']."`;");
    db("CREATE TABLE `".$db['f_access']."` (
      `user` int(10) NOT NULL DEFAULT '0',
      `pos` int(1) NOT NULL,
      `forum` int(10) NOT NULL DEFAULT '0');");

    //-> Tabellenstruktur für Tabelle `".$db['gallery']."`
    db("DROP TABLE IF EXISTS `".$db['gallery']."`;");
    db("CREATE TABLE `".$db['gallery']."` (
      `id` int(5) NOT NULL,
      `datum` int(20) NOT NULL DEFAULT '0',
      `kat` varchar(200) NOT NULL DEFAULT '',
      `beschreibung` text);");

    //-> Daten für Tabelle `".$db['gallery']."`
    db("INSERT INTO `".$db['gallery']."` VALUES (1, ".time().", 'Testgalerie', '<p>Das ist die erste Testgalerie.</p>\r\n<p>Hier seht ihr ein paar Bilder die eigentlich nur als Platzhalter dienen :)</p>');");

    //-> Tabellenstruktur für Tabelle `".$db['gb']."`
    db("DROP TABLE IF EXISTS `".$db['gb']."`;");
    db("CREATE TABLE `".$db['gb']."` (
      `id` int(5) NOT NULL,
      `datum` int(20) NOT NULL DEFAULT '0',
      `nick` varchar(30) NOT NULL DEFAULT '',
      `email` varchar(130) NOT NULL DEFAULT '',
      `hp` varchar(30) NOT NULL DEFAULT '',
      `reg` int(1) NOT NULL DEFAULT '0',
      `nachricht` text NOT NULL,
      `ip` varchar(50) NOT NULL DEFAULT '',
      `editby` text NOT NULL,
      `public` int(1) NOT NULL);");

    //-> Tabellenstruktur für Tabelle `".$db['glossar']."`
    db("DROP TABLE IF EXISTS `".$db['glossar']."`;");
    db("CREATE TABLE `".$db['glossar']."` (
      `id` int(11) NOT NULL,
      `word` varchar(200) NOT NULL,
      `glossar` text NOT NULL);");

    //-> Daten für Tabelle `".$db['glossar']."`
    db("INSERT INTO `".$db['glossar']."` VALUES (1, 'DZCP', '<p>deV!L`z Clanportal - kurz DZCP - ist ein CMS-System speziell f&uuml;r Onlinegaming Clans.</p>\r\n<p>Viele schon in der Grundinstallation vorhandene Module erleichtern die Verwaltung einer Clan-Homepage ungemein.</p>');");

    //-> Tabellenstruktur für Tabelle `".$db['ipcheck']."`
    db("DROP TABLE IF EXISTS `".$db['ipcheck']."`;");
    db("CREATE TABLE `".$db['ipcheck']."` (
      `id` int(11) NOT NULL,
      `ip` varchar(100) NOT NULL DEFAULT '',
      `what` varchar(40) NOT NULL DEFAULT '',
      `time` int(20) NOT NULL DEFAULT '0');");

//-> Tabellenstruktur für Tabelle `".$db['links']."`
    db("DROP TABLE IF EXISTS `".$db['links']."`;");
    db("CREATE TABLE `".$db['links']."` (
      `id` int(5) NOT NULL,
      `url` varchar(249) NOT NULL DEFAULT '',
      `text` varchar(249) NOT NULL DEFAULT '',
      `banner` int(1) NOT NULL DEFAULT '0',
      `beschreibung` text,
      `hits` int(50) NOT NULL DEFAULT '0');");

//-> Daten für Tabelle `".$db['links']."`
    db("INSERT INTO `".$db['links']."` VALUES (1, 'http://www.dzcp.de', 'http://www.dzcp.de/banner/dzcp.gif', 1, 'deV!L`z Clanportal', 0);");

//-> Tabellenstruktur für Tabelle `".$db['linkus']."`
    db("DROP TABLE IF EXISTS `".$db['linkus']."`;");
    db("CREATE TABLE `".$db['linkus']."` (
      `id` int(5) NOT NULL,
      `url` varchar(249) NOT NULL DEFAULT '',
      `text` varchar(249) NOT NULL DEFAULT '',
      `banner` int(1) NOT NULL DEFAULT '0',
      `beschreibung` varchar(249) DEFAULT NULL);");

//-> Daten für Tabelle `".$db['linkus']."`
    db("INSERT INTO `".$db['linkus']."` VALUES (1, 'http://www.dzcp.de', 'http://www.dzcp.de/banner/button.gif', 1, 'deV!L`z Clanportal');");

    //-> Tabellenstruktur für Tabelle `".$db['msg']."`
    db("DROP TABLE IF EXISTS `".$db['msg']."`;");
    db("CREATE TABLE `".$db['msg']."` (
      `id` int(5) NOT NULL,
      `datum` int(20) NOT NULL DEFAULT '0',
      `von` int(5) NOT NULL DEFAULT '0',
      `an` int(5) NOT NULL DEFAULT '0',
      `see_u` int(1) NOT NULL,
      `page` int(1) NOT NULL,
      `titel` varchar(80) NOT NULL DEFAULT '',
      `nachricht` text NOT NULL,
      `see` int(1) NOT NULL DEFAULT '0',
      `readed` int(1) NOT NULL DEFAULT '0',
      `sendmail` int(1) DEFAULT '0',
      `sendnews` int(1) NOT NULL DEFAULT '0',
      `senduser` int(5) NOT NULL DEFAULT '0',
      `sendnewsuser` int(5) NOT NULL DEFAULT '0');");

    //-> Tabellenstruktur für Tabelle `".$db['navi']."`
    db("DROP TABLE IF EXISTS `".$db['navi']."`;");
    db("CREATE TABLE `".$db['navi']."` (
      `id` int(11) NOT NULL,
      `pos` int(20) NOT NULL DEFAULT '0',
      `kat` varchar(20) DEFAULT '',
      `shown` int(1) NOT NULL DEFAULT '0',
      `name` varchar(249) DEFAULT '',
      `url` varchar(249) DEFAULT '',
      `target` int(1) NOT NULL DEFAULT '0',
      `type` int(1) NOT NULL DEFAULT '0',
      `internal` int(1) NOT NULL DEFAULT '0',
      `wichtig` int(1) NOT NULL DEFAULT '0',
      `editor` int(10) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `".$db['navi']."`
    db("INSERT INTO `".$db['navi']."` VALUES
    (1, 1, 'nav_main', 1, '_news_', '../news/', 0, 1, 0, 0, 0),
    (2, 2, 'nav_main', 1, '_newsarchiv_', '../news/?action=archiv', 0, 1, 0, 0, 0),
    (3, 3, 'nav_main', 1, '_artikel_', '../artikel/', 0, 1, 0, 0, 0),
    (4, 4, 'nav_main', 1, '_forum_', '../forum/', 0, 1, 0, 0, 0),
    (5, 5, 'nav_main', 1, '_gb_', '../gb/', 0, 1, 0, 0, 0),
    (6, 1, 'nav_server', 1, '_server_', '../server/', 0, 1, 0, 0, 0),
    (7, 6, 'nav_main', 1, '_kalender_', '../kalender/', 0, 1, 0, 0, 0),
    (8, 7, 'nav_main', 1, '_votes_', '../votes/', 0, 1, 0, 0, 0),
    (9, 8, 'nav_main', 1, '_links_', '../links/', 0, 1, 0, 0, 0),
    (10, 9, 'nav_main', 1, '_sponsoren_', '../sponsors/', 0, 1, 0, 0, 0),
    (11, 10, 'nav_main', 1, '_downloads_', '../downloads/', 0, 1, 0, 0, 0),
    (12, 11, 'nav_main', 1, '_userlist_', '../user/?action=userlist', 0, 1, 0, 0, 0),
    (13, 1, 'nav_clan', 1, '_squads_', '../squads/', 0, 1, 0, 0, 0),
    (14, 3, 'nav_clan', 1, '_cw_', '../clanwars/', 0, 1, 0, 0, 0),
    (15, 4, 'nav_clan', 1, '_awards_', '../awards/', 0, 1, 0, 0, 0),
    (16, 5, 'nav_clan', 1, '_rankings_', '../rankings/', 0, 1, 0, 0, 0),
    (17, 2, 'nav_server', 1, '_serverlist_', '../serverliste/', 0, 1, 0, 0, 0),
    (18, 3, 'nav_server', 1, '_ts_', '../teamspeak/', 0, 1, 0, 0, 0),
    (20, 2, 'nav_misc', 1, '_galerie_', '../gallery/', 0, 1, 0, 0, 0),
    (21, 3, 'nav_misc', 1, '_kontakt_', '../contact/', 0, 1, 0, 0, 0),
    (22, 4, 'nav_misc', 1, '_joinus_', '../contact/?action=joinus', 0, 1, 0, 0, 0),
    (23, 5, 'nav_misc', 1, '_fightus_', '../contact/?action=fightus', 0, 1, 0, 0, 0),
    (24, 6, 'nav_misc', 1, '_linkus_', '../linkus/', 0, 1, 0, 0, 0),
    (25, 7, 'nav_misc', 1, '_stats_', '../stats/', 0, 1, 0, 0, 0),
    (26, 8, 'nav_misc', 1, '_impressum_', '../impressum/', 0, 1, 0, 0, 0),
    (27, 1, 'nav_admin', 1, '_admin_', '../admin/', 0, 1, 1, 1, 0),
    (28, 1, 'nav_user', 1, '_lobby_', '../user/?action=userlobby', 0, 1, 0, 0, 0),
    (29, 2, 'nav_user', 1, '_nachrichten_', '../user/?action=msg', 0, 1, 0, 0, 0),
    (30, 3, 'nav_user', 1, '_buddys_', '../user/?action=buddys', 0, 1, 0, 0, 0),
    (31, 4, 'nav_user', 1, '_edit_profile_', '../user/?action=editprofile', 0, 1, 0, 0, 0),
    (32, 5, 'nav_user', 1, '_logout_', '../user/?action=logout', 0, 1, 0, 1, 0),
    (34, 1, 'nav_member', 1, '_clankasse_', '../clankasse/', 0, 1, 0, 0, 0),
    (35, 2, 'nav_member', 1, '_taktiken_', '../taktik/', 0, 1, 0, 0, 0),
    (37, 2, 'nav_clan', 1, '_membermap_', '../membermap/', 0, 1, 0, 0, 0),
    (38, 12, 'nav_main', 1, '_glossar_', '../glossar/', 0, 1, 0, 0, 0),
    (39, 0, 'nav_main', 1, '_news_send_', '../news/send.php', 0, 1, 0, 0, 0),
    (40, 1, 'nav_trial', 1, '_awaycal_', '../away/', 0, 2, 1, 0, 0);");

    //-> Tabellenstruktur für Tabelle `".$db['navi_kats']."`
    db("DROP TABLE IF EXISTS `".$db['navi_kats']."`;");
    db("CREATE TABLE `".$db['navi_kats']."` (
      `id` int(10) NOT NULL,
      `name` varchar(200) NOT NULL,
      `placeholder` varchar(200) NOT NULL,
      `level` int(2) NOT NULL);");

    //-> Daten für Tabelle `".$db['navi_kats']."`
    db("INSERT INTO `".$db['navi_kats']."` VALUES
    (1, 'Clan Navigation', 'nav_clan', 0),
    (2, 'Main Navigation', 'nav_main', 0),
    (3, 'Server Navigation', 'nav_server', 0),
    (4, 'Misc Navigation', 'nav_misc', 0),
    (5, 'Trial Navigation', 'nav_trial', 2),
    (6, 'Admin Navigation', 'nav_admin', 4),
    (7, 'User Navigation', 'nav_user', 1),
    (8, 'Member Navigation', 'nav_member', 3);");

    //-> Tabellenstruktur für Tabelle `".$db['news']."`
    db("DROP TABLE IF EXISTS `".$db['news']."`;");
    db("CREATE TABLE `".$db['news']."` (
      `id` int(10) NOT NULL,
      `autor` varchar(5) NOT NULL DEFAULT '',
      `datum` varchar(20) NOT NULL DEFAULT '',
      `kat` int(2) NOT NULL DEFAULT '0',
      `sticky` int(20) NOT NULL DEFAULT '0',
      `titel` varchar(249) NOT NULL DEFAULT '',
      `intern` int(1) NOT NULL DEFAULT '0',
      `text` text NOT NULL,
      `klapplink` varchar(20) NOT NULL DEFAULT '',
      `klapptext` text NOT NULL,
      `link1` varchar(100) NOT NULL DEFAULT '',
      `url1` varchar(200) NOT NULL DEFAULT '',
      `link2` varchar(100) NOT NULL DEFAULT '',
      `url2` varchar(200) NOT NULL DEFAULT '',
      `link3` varchar(100) NOT NULL DEFAULT '',
      `url3` varchar(200) NOT NULL DEFAULT '',
      `viewed` int(10) NOT NULL DEFAULT '0',
      `public` int(1) NOT NULL DEFAULT '0',
      `timeshift` int(14) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `".$db['news']."`
    db("INSERT INTO `".$db['news']."` VALUES (1, '1', '".(time()-120)."', 1, 0, 'deV!L`z Clanportal', 0, '<p>deV!L`z Clanportal wurde erfolgreich installiert!</p><p>Bei Fragen oder Problemen kannst du gerne das Forum unter <a href=\"http://www.dzcp.de/\" target=\"_blank\">www.dzcp.de</a> kontaktieren.</p><p>Mehr Designtemplates und Modifikationen findest du unter <a href=\"http://www.templatebar.de/\" target=\"_blank\" title=\"Templates, Designs &amp; Modifikationen\">www.templatebar.de</a>.</p><p><br /></p><p>Viel Spass mit dem DZCP w&uuml;nscht dir das Team von www.dzcp.de.</p>', '', '', 'www.dzcp.de', 'http://www.dzcp.de', 'TEMPLATEbar.de', 'http://www.templatebar.de', '', '', 0, 1, 0);");

    //-> Tabellenstruktur für Tabelle `".$db['newscomments']."`
    db("DROP TABLE IF EXISTS `".$db['newscomments']."`;");
    db("CREATE TABLE `".$db['newscomments']."` (
      `id` int(10) NOT NULL,
      `news` int(10) NOT NULL DEFAULT '0',
      `nick` varchar(20) NOT NULL DEFAULT '',
      `datum` int(20) NOT NULL DEFAULT '0',
      `email` varchar(130) NOT NULL DEFAULT '',
      `hp` varchar(50) NOT NULL DEFAULT '',
      `reg` int(5) NOT NULL DEFAULT '0',
      `comment` text NOT NULL,
      `ip` varchar(50) NOT NULL DEFAULT '',
      `editby` text NOT NULL);");

    //-> Tabellenstruktur für Tabelle `".$db['newskat']."`
    db("DROP TABLE IF EXISTS `".$db['newskat']."`;");
    db("CREATE TABLE `".$db['newskat']."` (
      `id` int(5) NOT NULL,
      `katimg` varchar(20) NOT NULL DEFAULT '',
      `kategorie` varchar(40) NOT NULL DEFAULT '');");

    //-> Daten für Tabelle `".$db['newskat']."`
    db("INSERT INTO `".$db['newskat']."` VALUES (1, 'hp.jpg', 'Homepage');");

    //-> Tabellenstruktur für Tabelle `".$db['partners']."`
    db("DROP TABLE IF EXISTS `".$db['partners']."`;");
    db("CREATE TABLE `".$db['partners']."` (
      `id` int(5) NOT NULL,
      `link` varchar(100) NOT NULL DEFAULT '',
      `banner` varchar(100) NOT NULL DEFAULT '',
      `textlink` int(1) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `".$db['partners']."`
    db("INSERT INTO `".$db['partners']."` VALUES
    (1, 'http://www.hogibo.net', 'hogibo.gif', 0),
    (2, 'http://www.dzcp.de', 'dzcp.gif', 0),
    (3, 'http://www.dzcp.de', 'dzcp.de', 1),
    (4, 'http://www.hogibo.net', 'Webspace', 1);");

    //-> Tabellenstruktur für Tabelle `".$db['permissions']."`
    db("DROP TABLE IF EXISTS `".$db['permissions']."`;");
    db("CREATE TABLE `".$db['permissions']."` (
      `id` int(5) NOT NULL,
      `user` int(5) NOT NULL DEFAULT '0',
      `pos` int(1) NOT NULL,
      `intforum` int(1) NOT NULL DEFAULT '0',
      `clankasse` int(1) NOT NULL DEFAULT '0',
      `clanwars` int(1) NOT NULL DEFAULT '0',
      `shoutbox` int(1) NOT NULL DEFAULT '0',
      `serverliste` int(1) NOT NULL DEFAULT '0',
      `editusers` int(1) NOT NULL DEFAULT '0',
      `edittactics` int(1) NOT NULL DEFAULT '0',
      `editsquads` int(1) NOT NULL DEFAULT '0',
      `editserver` int(1) NOT NULL DEFAULT '0',
      `editkalender` int(1) NOT NULL DEFAULT '0',
      `news` int(1) NOT NULL DEFAULT '0',
      `gb` int(1) NOT NULL DEFAULT '0',
      `forum` int(1) NOT NULL DEFAULT '0',
      `votes` int(1) NOT NULL DEFAULT '0',
      `gallery` int(1) NOT NULL DEFAULT '0',
      `votesadmin` int(1) NOT NULL DEFAULT '0',
      `links` int(1) NOT NULL DEFAULT '0',
      `downloads` int(1) NOT NULL DEFAULT '0',
      `newsletter` int(1) NOT NULL DEFAULT '0',
      `intnews` int(1) NOT NULL DEFAULT '0',
      `rankings` int(1) NOT NULL DEFAULT '0',
      `contact` int(1) NOT NULL DEFAULT '0',
      `joinus` int(1) NOT NULL DEFAULT '0',
      `awards` int(1) NOT NULL DEFAULT '0',
      `artikel` int(1) NOT NULL DEFAULT '0',
      `receivecws` int(1) NOT NULL DEFAULT '0',
      `editor` int(1) NOT NULL DEFAULT '0',
      `glossar` int(1) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `".$db['permissions']."`
    db("INSERT INTO `".$db['permissions']."` VALUES (1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);");

    //-> Tabellenstruktur für Tabelle `".$db['pos']."`
    db("DROP TABLE IF EXISTS `".$db['pos']."`;");
    db("CREATE TABLE `".$db['pos']."` (
      `id` int(2) NOT NULL,
      `pid` int(2) NOT NULL DEFAULT '0',
      `position` varchar(30) NOT NULL DEFAULT '',
      `nletter` int(1) NOT NULL);");

    //-> Daten für Tabelle `".$db['pos']."`
    db("INSERT INTO `".$db['pos']."` VALUES (1, 1, 'Leader', 0), (2, 2, 'Co-Leader', 0), (3, 3, 'Webmaster', 0), (4, 4, 'Member', 0);");

    //-> Tabellenstruktur für Tabelle `".$db['profile']."`
    db("DROP TABLE IF EXISTS `".$db['profile']."`;");
    db("CREATE TABLE `".$db['profile']."` (
      `id` int(5) UNSIGNED NOT NULL,
      `kid` int(11) NOT NULL DEFAULT '0',
      `name` varchar(200) NOT NULL,
      `feldname` varchar(20) NOT NULL DEFAULT '',
      `type` int(5) NOT NULL DEFAULT '1',
      `shown` int(5) NOT NULL DEFAULT '1');");

    //-> Daten für Tabelle `".$db['profile']."`
    db("INSERT INTO `".$db['profile']."` VALUES
    (2, 1, '_job_', 'job', 1, 1),
    (3, 1, '_hobbys_', 'hobbys', 1, 1),
    (4, 1, '_motto_', 'motto', 1, 1),
    (5, 2, '_exclans_', 'ex', 1, 1),
    (8, 4, '_drink_', 'drink', 1, 1),
    (9, 4, '_essen_', 'essen', 1, 1),
    (10, 4, '_film_', 'film', 1, 1),
    (11, 4, '_musik_', 'musik', 1, 1),
    (12, 4, '_song_', 'song', 1, 1),
    (13, 4, '_buch_', 'buch', 1, 1),
    (14, 4, '_autor_', 'autor', 1, 1),
    (15, 4, '_person_', 'person', 1, 1),
    (16, 4, '_sport_', 'sport', 1, 1),
    (17, 4, '_sportler_', 'sportler', 1, 1),
    (18, 4, '_auto_', 'auto', 1, 1),
    (19, 4, '_game_', 'game', 1, 1),
    (20, 4, '_favoclan_', 'favoclan', 1, 1),
    (21, 4, '_spieler_', 'spieler', 1, 1),
    (22, 4, '_map_', 'map', 1, 1),
    (23, 4, '_waffe_', 'waffe', 1, 1),
    (24, 5, '_system_', 'os', 1, 1),
    (25, 5, '_board_', 'board', 1, 1),
    (26, 5, '_cpu_', 'cpu', 1, 1),
    (27, 5, '_ram_', 'ram', 1, 1),
    (28, 5, '_graka_', 'graka', 1, 1),
    (29, 5, '_hdd_', 'hdd', 1, 1),
    (30, 5, '_monitor_', 'monitor', 1, 1),
    (31, 5, '_maus_', 'maus', 1, 1),
    (32, 5, '_mauspad_', 'mauspad', 1, 1),
    (33, 5, '_headset_', 'headset', 1, 1),
    (34, 5, '_inet_', 'inet', 1, 1);");

    //-> Tabellenstruktur für Tabelle `dzcp_rankings`
    db("DROP TABLE IF EXISTS `".$db['rankings']."`;");
    db("CREATE TABLE `".$db['rankings']."` (
      `id` int(5) NOT NULL,
      `league` varchar(50) NOT NULL,
      `lastranking` int(10) NOT NULL,
      `rank` int(10) NOT NULL,
      `squad` varchar(5) NOT NULL,
      `url` varchar(249) NOT NULL,
      `postdate` int(20) NOT NULL);");

    //-> Tabellenstruktur für Tabelle `dzcp_server`
    db("DROP TABLE IF EXISTS `".$db['server']."`;");
    db("CREATE TABLE `".$db['server']."` (
      `id` int(5) NOT NULL,
      `status` varchar(100) NOT NULL DEFAULT '',
      `shown` int(1) NOT NULL DEFAULT '1',
      `navi` int(1) NOT NULL DEFAULT '0',
      `bl_file` varchar(100) NOT NULL DEFAULT '',
      `bl_path` varchar(249) NOT NULL DEFAULT '',
      `ftp_pwd` varchar(100) NOT NULL DEFAULT '',
      `ftp_login` varchar(100) NOT NULL DEFAULT '',
      `ftp_host` varchar(100) NOT NULL DEFAULT '',
      `name` varchar(50) NOT NULL DEFAULT '',
      `ip` varchar(50) NOT NULL DEFAULT '0',
      `port` int(10) NOT NULL DEFAULT '0',
      `pwd` varchar(20) NOT NULL DEFAULT '',
      `game` varchar(30) NOT NULL DEFAULT '',
      `qport` varchar(10) NOT NULL DEFAULT '');");

    //-> Daten für Tabelle `dzcp_server`
    db("INSERT INTO `".$db['server']."` VALUES (1, 'bf2', 1, 1, '', '', '', '', '', 'Battlefield-Basis.de II von Hogibo.net', '80.190.178.115', 9260, '', 'bf2.gif', '');");

    //-> Tabellenstruktur für Tabelle `dzcp_serverliste`
    db("DROP TABLE IF EXISTS `".$db['serverliste']."`;");
    db("CREATE TABLE `".$db['serverliste']."` (
      `id` int(20) NOT NULL,
      `datum` int(4) NOT NULL DEFAULT '0',
      `clanname` varchar(30) NOT NULL DEFAULT '',
      `clanurl` varchar(255) NOT NULL,
      `ip` varchar(50) NOT NULL DEFAULT '',
      `port` varchar(10) NOT NULL DEFAULT '',
      `pwd` varchar(10) NOT NULL DEFAULT '',
      `checked` int(1) NOT NULL DEFAULT '0',
      `slots` char(2) NOT NULL DEFAULT '');");

    //-> Daten für Tabelle `dzcp_serverliste`
    db("INSERT INTO `".$db['serverliste']."` VALUES (1, ".time().", '[-tHu-] teamHanau', 'http://www.thu-clan.de', '82.98.216.10', '27015', '', 1, '17');");

    //-> Tabellenstruktur für Tabelle `dzcp_settings`
    db("DROP TABLE IF EXISTS `".$db['settings']."`;");
    db("CREATE TABLE `".$db['settings']."` (
      `id` int(1) NOT NULL DEFAULT '1',
      `clanname` varchar(50) NOT NULL DEFAULT 'Dein Clanname hier!',
      `pfad` varchar(50) NOT NULL DEFAULT '',
      `balken_vote` varchar(3) NOT NULL DEFAULT '2',
      `reg_forum` int(1) NOT NULL DEFAULT '1',
      `reg_cwcomments` int(1) NOT NULL DEFAULT '1',
      `counter_start` int(10) NOT NULL DEFAULT '0',
      `balken_vote_menu` varchar(3) NOT NULL DEFAULT '0.9',
      `balken_cw` varchar(3) NOT NULL DEFAULT '2.4',
      `reg_dl` int(1) NOT NULL DEFAULT '1',
      `reg_artikel` int(1) NOT NULL DEFAULT '1',
      `reg_newscomments` int(1) NOT NULL DEFAULT '1',
      `tmpdir` varchar(100) NOT NULL DEFAULT 'version1.6',
      `wmodus` int(1) NOT NULL DEFAULT '0',
      `persinfo` int(1) NOT NULL DEFAULT '1',
      `iban` varchar(100) NOT NULL DEFAULT '',
      `bic` varchar(100) NOT NULL DEFAULT '',
      `badwords` text NOT NULL,
      `pagetitel` varchar(50) NOT NULL DEFAULT 'Dein Seitentitel hier!',
      `last_backup` int(20) NOT NULL DEFAULT '0',
      `squadtmpl` int(1) NOT NULL DEFAULT '1',
      `i_domain` varchar(50) NOT NULL DEFAULT 'www.deineUrl.de',
      `i_autor` varchar(249) NOT NULL DEFAULT 'Max Mustermann',
      `k_nr` varchar(100) NOT NULL DEFAULT '123456789',
      `k_inhaber` varchar(50) NOT NULL DEFAULT 'Max Mustermann',
      `k_blz` varchar(100) NOT NULL DEFAULT '123456789',
      `k_bank` varchar(200) NOT NULL DEFAULT 'Musterbank',
      `k_waehrung` varchar(15) NOT NULL DEFAULT '&euro;',
      `ftp_host` varchar(100) NOT NULL DEFAULT '',
      `ftp_login` varchar(100) NOT NULL DEFAULT '',
      `ftp_pwd` varchar(100) NOT NULL DEFAULT '',
      `language` varchar(50) NOT NULL DEFAULT 'deutsch',
      `domain` varchar(200) NOT NULL DEFAULT 'localhost',
      `regcode` int(1) NOT NULL DEFAULT '1',
      `ts_ip` varchar(200) NOT NULL DEFAULT '',
      `mailfrom` varchar(200) NOT NULL DEFAULT 'info@localhost',
      `ts_port` int(10) NOT NULL DEFAULT '0',
      `ts_sport` int(10) NOT NULL DEFAULT '0',
      `ts_version` int(1) NOT NULL,
      `ts_width` int(10) NOT NULL DEFAULT '0',
      `bl_path` varchar(249) NOT NULL DEFAULT '',
      `eml_reg_subj` varchar(200) NOT NULL DEFAULT '',
      `eml_pwd_subj` varchar(200) NOT NULL DEFAULT '',
      `eml_nletter_subj` varchar(200) NOT NULL DEFAULT '',
      `eml_reg` text NOT NULL,
      `eml_pwd` text NOT NULL,
      `eml_nletter` text NOT NULL,
      `reg_shout` int(1) NOT NULL DEFAULT '1',
      `gmaps_key` varchar(200) NOT NULL,
      `gmaps_who` int(1) NOT NULL DEFAULT '1',
      `prev` int(3) NOT NULL DEFAULT '0',
      `eml_fabo_npost_subj` varchar(200) NOT NULL,
      `eml_fabo_tedit_subj` varchar(200) NOT NULL,
      `eml_fabo_pedit_subj` varchar(200) NOT NULL,
      `eml_pn_subj` varchar(200) NOT NULL,
      `eml_fabo_npost` text NOT NULL,
      `eml_fabo_tedit` text NOT NULL,
      `eml_fabo_pedit` text NOT NULL,
      `eml_pn` text NOT NULL,
      `k_vwz` varchar(200) NOT NULL,
      `double_post` int(1) NOT NULL DEFAULT '1',
      `forum_vote` int(1) NOT NULL DEFAULT '1',
      `gb_activ` int(1) NOT NULL DEFAULT '1');");

    //-> Daten für Tabelle `".$db['settings']."`
    db("INSERT INTO `".$db['settings']."` VALUES (1, 'Dein Clanname hier!', '', '2', 1, 1, 0, '0.9', '2.4', 1, 1, 1, 'version1.6', 0, 1, '', '', 'arsch,Arsch,arschloch,Arschloch,hure,Hure', 'Dein Seitentitel hier!', 0, 1, 'www.deineUrl.de', 'Max Mustermann', '123456789', 'Max Mustermann', '123456789', 'Musterbank', '&euro;', '', '', '', 'deutsch', 'localhost', 1, '80.190.204.164', 'info@localhost', 7000, 10011, 3, 0, '', 'Deine Registrierung', 'Deine Zugangsdaten', 'Newsletter', 'Du hast dich erfolgreich auf unserer Seite registriert!\r\nDeine Logindaten lauten:\r\n\r\n\r\n\r\n##########\r\n\r\nLoginname: [user]\r\n\r\nPasswort: [pwd]\r\n\r\n##########\r\n\r\n\r\n\r\n[ Diese Email wurde automatisch generiert, bitte nicht antworten! ]', 'Ein neues Passwort wurde f&uuml;r deinen Account generiert!\r\n\r\n\r\n\r\n#########\r\n\r\nLogin-Name: [user]\r\n\r\nPasswort: [pwd]\r\n\r\n#########\r\n\r\n\r\n\r\n[ Diese Email wurde automatisch generiert, bitte nicht antworten! ]', '[text]\r\n\r\n\r\n\r\n\r\n[ Diese Email wurde automatisch generiert, bitte nicht antworten! ]', 1, '', 1, 661, 'Neuer Beitrag auf abonniertes Thema im [titel]', 'Thread auf abonniertes Thema im [titel] wurde editiert', 'Beitrag auf abonniertes Thema im [titel] wurde editiert', 'Neue PN auf [domain]', 'Hallo [nick],<br />\r\n<br />\r\n[postuser] hat auf das Thema: [topic] auf der Website: &#34;[titel]&#34; geantwortet.<br />\r\n<br />\r\nDen neuen Beitrag erreichst Du ber folgenden Link:<br />\r\n<a href=&#34;http://[domain]/forum/&euro;action=showthread&id=[id]&page=[page]#p[entrys]&#34;>http://[domain]/forum/&euro;action=showthread&id=[id]&page=[page]#p[entrys]</a><br />\r\n<br />\r\n[postuser] hat folgenden Text geschrieben:<br />\r\n-<br />\r\n[text]<br />\r\n-<br />\r\n<br />\r\nViele Gr&uuml;&szlig;e,<br />\r\n<br />\r\nDein [clan]<br />\r\n<br />\r\n[ Diese Email wurde automatisch generiert, bitte nicht antworten! ]', 'Hallo [nick],<br />\r\n         <br />\r\nDer Thread mit dem Titel: [topic] auf der Website: &#34;[titel]&#34; wurde soeben von [postuser] editiert.<br />\r\n<br />\r\nDen editierten Beitrag erreichst Du ber folgenden Link:<br />\r\n<a href=&#34;http://[domain]/forum/&euro;action=showthread&id=[id]&#34;>http://[domain]/forum/&euro;action=showthread&id=[id]</a><br />\r\n         <br />\r\n[postuser] hat folgenden neuen Text geschrieben:<br />\r\n-<br />\r\n[text]<br />\r\n-<br />\r\n         <br />\r\nViele Gr&uuml;&szlig;e,<br />\r\n<br />\r\nDein [clan]<br />\r\n<br />\r\n[ Diese Email wurde automatisch generiert, bitte nicht antworten! ]', 'Hallo [nick],<br />\r\n<br />\r\nEin Beitrag im Thread mit dem Titel: [topic] auf der Website: &#34;[titel]&#34; wurde soeben von [postuser] editiert.<br />\r\n<br />\r\nDen editierten Beitrag erreichst Du ber folgenden Link:<br />\r\n<a href=&#34;http://[domain]/forum/&euro;action=showthread&id=[id]&page=[page]#p[entrys]&#34;>http://[domain]/forum/&euro;action=showthread&id=[id]&page=[page]#p[entrys]</a><br />\r\n<br />\r\n[postuser] hat folgenden neuen Text geschrieben:<br />\r\n-<br />\r\n[text]<br />\r\n-<br />\r\n<br />\r\nViele Gr&uuml;&szlig;e,<br />\r\n<br />\r\nDein [clan]<br />\r\n<br />\r\n[ Diese Email wurde automatisch generiert, bitte nicht antworten! ]', '-<br />\r\n<br />\r\nHallo [nick],<br />\r\n<br />\r\nDu hast eine neue Nachricht in deinem Postfach.<br />\r\n<br />\r\nTitel: [titel]<br />\r\n<br />\r\n<a href=&#34;http://[domain]/user/index.php&euro;action=msg&#34;>Zum Nachrichten-Center</a><br />\r\n<br />\r\nVG<br />\r\n<br />\r\n[clan]<br />\r\n<br />\r\n-', '', 1, 1, 1);");

    //-> Tabellenstruktur für Tabelle `".$db['shout']."`
    db("DROP TABLE IF EXISTS `".$db['shout']."`;");
    db("CREATE TABLE `".$db['shout']."` (
      `id` int(11) NOT NULL,
      `datum` int(30) NOT NULL DEFAULT '0',
      `nick` varchar(30) NOT NULL DEFAULT '',
      `email` varchar(130) NOT NULL DEFAULT '',
      `text` text NOT NULL,
      `ip` varchar(50) NOT NULL DEFAULT '');");

    //-> Daten für Tabelle `".$db['shout']."`
    db("INSERT INTO `".$db['shout']."` VALUES (1, ".time().", 'deV!L', 'webmaster@dzcp.de', 'Viel Gl&uuml;ck und Erfolg mit eurem Clan!', '');");

    //-> Tabellenstruktur für Tabelle `".$db['sites']."`
    db("DROP TABLE IF EXISTS `".$db['sites']."`;");
    db("CREATE TABLE `".$db['sites']."` (
      `id` int(5) NOT NULL,
      `titel` varchar(50) NOT NULL DEFAULT '',
      `text` text NOT NULL,
      `html` int(1) NOT NULL);");

    //-> Tabellenstruktur für Tabelle `".$db['sponsoren']."`
    db("DROP TABLE IF EXISTS `".$db['sponsoren']."`;");
    db("CREATE TABLE `".$db['sponsoren']."` (
      `id` int(5) NOT NULL,
      `name` varchar(249) NOT NULL,
      `link` varchar(249) NOT NULL,
      `beschreibung` text NOT NULL,
      `site` int(1) NOT NULL DEFAULT '0',
      `send` varchar(5) NOT NULL,
      `slink` varchar(249) NOT NULL,
      `banner` int(1) NOT NULL DEFAULT '0',
      `bend` varchar(5) NOT NULL,
      `blink` varchar(249) NOT NULL,
      `box` int(1) NOT NULL DEFAULT '0',
      `xend` varchar(5) NOT NULL,
      `xlink` varchar(255) NOT NULL,
      `pos` int(5) NOT NULL,
      `hits` int(50) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `".$db['sponsoren']."`
    db("INSERT INTO `".$db['sponsoren']."` VALUES (1, 'DZCP', 'http://www.dzcp.de', '<p>deV!L\'z Clanportal, das CMS for Online-Clans!</p>', 0, '', '', 0, '', '', 1, 'gif', '', 7, 0),(2, 'DZCP Rotationsbanner', 'http://www.dzcp.de', '<p>deV!L`z Clanportal</p>', 0, '', '', 1, '', 'http://www.dzcp.de/banner/dzcp.gif', 0, '', '', 5, 0);");

    //-> Tabellenstruktur für Tabelle `".$db['squads']."`
    db("DROP TABLE IF EXISTS `".$db['squads']."`;");
    db("CREATE TABLE `".$db['squads']."` (
      `id` int(5) NOT NULL,
      `name` varchar(40) NOT NULL DEFAULT '',
      `game` varchar(40) NOT NULL DEFAULT '',
      `icon` varchar(20) NOT NULL DEFAULT '',
      `pos` int(1) NOT NULL DEFAULT '0',
      `shown` int(1) NOT NULL DEFAULT '0',
      `navi` int(1) NOT NULL DEFAULT '1',
      `status` int(1) NOT NULL DEFAULT '1',
      `beschreibung` text,
      `team_show` int(1) NOT NULL DEFAULT '1');");

    //-> Daten für Tabelle `".$db['squads']."`
    db("INSERT INTO `".$db['squads']."` VALUES (1, 'Testsquad', 'Counter-Strike', 'cs.gif', 1, 1, 1, 1, NULL, 1);");

    //-> Tabellenstruktur für Tabelle `".$db['squaduser']."`
    db("DROP TABLE IF EXISTS `".$db['squaduser']."`;");
    db("CREATE TABLE `".$db['squaduser']."` (
      `id` int(5) NOT NULL,
      `user` int(5) NOT NULL DEFAULT '0',
      `squad` int(2) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `".$db['squaduser']."`
    db("INSERT INTO `".$db['squaduser']."` VALUES (1, 1, 1);");

    //-> Tabellenstruktur für Tabelle `".$db['taktik']."`
    db("DROP TABLE IF EXISTS `".$db['taktik']."`;");
    db("CREATE TABLE `".$db['taktik']."` (
      `id` int(10) NOT NULL,
      `datum` int(20) NOT NULL DEFAULT '0',
      `map` varchar(20) NOT NULL DEFAULT '',
      `spart` text NOT NULL,
      `standardt` text NOT NULL,
      `sparct` text NOT NULL,
      `standardct` text NOT NULL,
      `autor` int(5) NOT NULL DEFAULT '0');");

    //-> Tabellenstruktur für Tabelle `".$db['buddys']."`
    db("DROP TABLE IF EXISTS `".$db['buddys']."`;");
    db("CREATE TABLE `".$db['buddys']."` (
      `id` int(10) NOT NULL,
      `user` int(5) NOT NULL DEFAULT '0',
      `buddy` int(5) NOT NULL DEFAULT '0');");

    //-> Tabellenstruktur für Tabelle `".$db['usergallery']."`
    db("DROP TABLE IF EXISTS `".$db['usergallery']."`;");
    db("CREATE TABLE `".$db['usergallery']."` (
      `id` int(5) NOT NULL,
      `user` int(5) NOT NULL DEFAULT '0',
      `beschreibung` text,
      `pic` varchar(200) NOT NULL DEFAULT '');");

    //-> Tabellenstruktur für Tabelle `".$db['usergb']."`
    db("DROP TABLE IF EXISTS `".$db['usergb']."`;");
    db("CREATE TABLE `".$db['usergb']."` (
      `id` int(5) NOT NULL,
      `user` int(5) NOT NULL DEFAULT '0',
      `datum` int(20) NOT NULL DEFAULT '0',
      `nick` varchar(30) NOT NULL DEFAULT '',
      `email` varchar(130) NOT NULL DEFAULT '',
      `hp` varchar(100) NOT NULL DEFAULT '',
      `reg` int(1) NOT NULL DEFAULT '0',
      `nachricht` text NOT NULL,
      `ip` varchar(50) NOT NULL DEFAULT '',
      `editby` text NOT NULL);");

    //-> Tabellenstruktur für Tabelle `".$db['userpos']."`
    db("DROP TABLE IF EXISTS `".$db['userpos']."`;");
    db("CREATE TABLE `".$db['userpos']."` (
      `id` int(11) NOT NULL,
      `user` int(5) NOT NULL DEFAULT '0',
      `posi` int(5) NOT NULL DEFAULT '0',
      `squad` int(5) NOT NULL DEFAULT '0');");

    //-> Tabellenstruktur für Tabelle `".$db['users']."`
    db("DROP TABLE IF EXISTS `".$db['users']."`;");
    db("CREATE TABLE `".$db['users']."` (
      `id` int(5) NOT NULL,
      `user` varchar(200) NOT NULL DEFAULT '',
      `nick` varchar(200) NOT NULL DEFAULT '',
      `pwd` varchar(255) NOT NULL DEFAULT '',
      `sessid` varchar(32) DEFAULT NULL,
      `country` varchar(20) NOT NULL DEFAULT 'de',
      `ip` varchar(50) NOT NULL DEFAULT '',
      `regdatum` int(20) NOT NULL DEFAULT '0',
      `email` varchar(200) NOT NULL DEFAULT '',
      `icq` varchar(20) NOT NULL DEFAULT '',
      `hlswid` varchar(100) NOT NULL DEFAULT '',
      `steamid` varchar(20) NOT NULL DEFAULT '',
      `level` varchar(15) NOT NULL DEFAULT '',
      `rlname` varchar(200) NOT NULL DEFAULT '',
      `city` varchar(200) NOT NULL DEFAULT '',
      `sex` int(1) NOT NULL DEFAULT '0',
      `bday` varchar(20) NOT NULL DEFAULT '',
      `hobbys` varchar(249) NOT NULL DEFAULT '',
      `motto` varchar(249) NOT NULL DEFAULT '',
      `hp` varchar(200) NOT NULL DEFAULT '',
      `cpu` varchar(200) NOT NULL DEFAULT '',
      `ram` varchar(200) NOT NULL DEFAULT '',
      `monitor` varchar(200) NOT NULL DEFAULT '',
      `maus` varchar(200) NOT NULL DEFAULT '',
      `mauspad` varchar(200) NOT NULL DEFAULT '',
      `headset` varchar(200) NOT NULL DEFAULT '',
      `board` varchar(200) NOT NULL DEFAULT '',
      `os` varchar(200) NOT NULL DEFAULT '',
      `graka` varchar(200) NOT NULL DEFAULT '',
      `hdd` varchar(200) NOT NULL DEFAULT '',
      `inet` varchar(200) NOT NULL DEFAULT '',
      `signatur` text,
      `position` int(2) NOT NULL DEFAULT '0',
      `status` int(1) NOT NULL DEFAULT '1',
      `ex` varchar(200) NOT NULL DEFAULT '',
      `job` varchar(200) NOT NULL DEFAULT '',
      `time` int(20) NOT NULL DEFAULT '0',
      `listck` int(1) NOT NULL DEFAULT '0',
      `online` int(1) NOT NULL DEFAULT '0',
      `nletter` int(1) NOT NULL DEFAULT '1',
      `whereami` text NOT NULL,
      `drink` varchar(249) NOT NULL DEFAULT '',
      `essen` varchar(249) NOT NULL DEFAULT '',
      `film` varchar(249) NOT NULL DEFAULT '',
      `musik` varchar(249) NOT NULL DEFAULT '',
      `song` varchar(249) NOT NULL DEFAULT '',
      `buch` varchar(249) NOT NULL DEFAULT '',
      `autor` varchar(249) NOT NULL DEFAULT '',
      `person` varchar(249) NOT NULL DEFAULT '',
      `sport` varchar(249) NOT NULL DEFAULT '',
      `sportler` varchar(249) NOT NULL DEFAULT '',
      `auto` varchar(249) NOT NULL DEFAULT '',
      `game` varchar(249) NOT NULL DEFAULT '',
      `favoclan` varchar(249) NOT NULL DEFAULT '',
      `spieler` varchar(249) NOT NULL DEFAULT '',
      `map` varchar(249) NOT NULL DEFAULT '',
      `waffe` varchar(249) NOT NULL DEFAULT '',
      `rasse` varchar(249) NOT NULL DEFAULT '',
      `url2` varchar(249) NOT NULL DEFAULT '',
      `url3` varchar(249) NOT NULL DEFAULT '',
      `beschreibung` text,
      `gmaps_koord` varchar(249) NOT NULL,
      `pnmail` int(1) NOT NULL DEFAULT '1');");

    //-> Daten für Tabelle `".$db['users']."`
    db("INSERT INTO `".$db['users']."` VALUES (1, '".up($login)."', '".up($nick)."', '".md5($pwd)."', '', 'de', '', ".time().", '".up($email)."', '', '', '', '4', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 1, '', '', 0, 0, 1, 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', 1);");

    //-> Tabellenstruktur für Tabelle `".$db['userstats']."`
    db("DROP TABLE IF EXISTS `".$db['userstats']."`;");
    db("CREATE TABLE `".$db['userstats']."` (
      `id` int(5) NOT NULL,
      `user` int(10) NOT NULL DEFAULT '0',
      `logins` int(100) NOT NULL DEFAULT '0',
      `writtenmsg` int(10) NOT NULL DEFAULT '0',
      `lastvisit` int(20) NOT NULL DEFAULT '0',
      `hits` int(249) NOT NULL DEFAULT '0',
      `votes` int(5) NOT NULL DEFAULT '0',
      `profilhits` int(20) NOT NULL DEFAULT '0',
      `forumposts` int(5) NOT NULL DEFAULT '0',
      `cws` int(5) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `dzcp_userstats`
    db("INSERT INTO `".$db['userstats']."` VALUES (1, 1, 0, 0, 0, 0, 0, 0, 0, 0);");

    //-> Tabellenstruktur für Tabelle `dzcp_votes'`
    db("DROP TABLE IF EXISTS `".$db['votes']."`;");
    db("CREATE TABLE `".$db['votes']."` (
      `id` int(5) NOT NULL,
      `datum` int(20) NOT NULL DEFAULT '0',
      `titel` varchar(249) NOT NULL DEFAULT '',
      `intern` int(1) NOT NULL DEFAULT '0',
      `menu` int(1) NOT NULL DEFAULT '0',
      `closed` int(1) NOT NULL DEFAULT '0',
      `von` int(10) NOT NULL DEFAULT '0',
      `forum` int(1) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `dzcp_votes`
    db("INSERT INTO `".$db['votes']."` VALUES (1, ".(time()-200).", 'Wie findet ihr unsere Seite?', 0, 1, 0, 1, 0);");

    //-> Tabellenstruktur für Tabelle `dzcp_vote_results`
    db("DROP TABLE IF EXISTS `".$db['vote_results']."`;");
    db("CREATE TABLE `".$db['vote_results']."` (
      `id` int(5) NOT NULL,
      `vid` int(5) NOT NULL DEFAULT '0',
      `what` varchar(5) NOT NULL DEFAULT '',
      `sel` varchar(80) NOT NULL DEFAULT '',
      `stimmen` int(5) NOT NULL DEFAULT '0');");

    //-> Daten für Tabelle `dzcp_vote_results`
    db("INSERT INTO `".$db['vote_results']."` VALUES (1, 1, 'a1', 'Gut', 0), (2, 1, 'a2', 'Schlecht', 0);");

    //-> Indizes für die Tabellen
    db("ALTER TABLE `".$db['acomments']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['artikel']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['awards']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['away']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['clankasse']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['c_kats']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['c_payed']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['cw']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['counter']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['c_ips']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['c_who']."` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `ip` (`ip`);");
    db("ALTER TABLE `".$db['cw_comments']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['downloads']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['dl_kat']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['events']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['f_kats']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['f_posts']."` ADD PRIMARY KEY (`id`), ADD KEY `sid` (`sid`), ADD KEY `date` (`date`);");
    db("ALTER TABLE `".$db['f_skats']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['f_threads']."` ADD PRIMARY KEY (`id`), ADD KEY `kid` (`kid`), ADD KEY `lp` (`lp`), ADD KEY `topic` (`topic`), ADD KEY `first` (`first`);");
    db("ALTER TABLE `".$db['f_abo']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['gallery']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['gb']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['glossar']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['ipcheck']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['links']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['linkus']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['msg']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['navi']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['navi_kats']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['news']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['newscomments']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['newskat']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['partners']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['permissions']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['pos']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['profile']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['rankings']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['server']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['serverliste']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['shout']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['sites']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['sponsoren']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['squads']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['squaduser']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['taktik']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['buddys']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['usergallery']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['usergb']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['userpos']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['users']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['userstats']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['votes']."` ADD PRIMARY KEY (`id`);");
    db("ALTER TABLE `".$db['vote_results']."` ADD PRIMARY KEY (`id`);");

    //-> AUTO_INCREMENT für Tabellen
    db("ALTER TABLE `".$db['acomments']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['artikel']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['awards']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['away']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['clankasse']."` MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['c_kats']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;");
    db("ALTER TABLE `".$db['c_payed']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['cw']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['counter']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['c_ips']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['c_who']."` MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['cw_comments']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['downloads']."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['dl_kat']."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;");
    db("ALTER TABLE `".$db['events']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['f_kats']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;");
    db("ALTER TABLE `".$db['f_posts']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['f_skats']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;");
    db("ALTER TABLE `".$db['f_threads']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['f_abo']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['gallery']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['gb']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['glossar']."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['ipcheck']."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['links']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['linkus']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['msg']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['navi']."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;");
    db("ALTER TABLE `".$db['navi_kats']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;");
    db("ALTER TABLE `".$db['news']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['newscomments']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['newskat']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['partners']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;");
    db("ALTER TABLE `".$db['permissions']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['pos']."` MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;");
    db("ALTER TABLE `".$db['profile']."` MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;");
    db("ALTER TABLE `".$db['rankings']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['server']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['serverliste']."` MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['shout']."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['sites']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['sponsoren']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;");
    db("ALTER TABLE `".$db['squads']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['squaduser']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['taktik']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['buddys']."` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['usergallery']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['usergb']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['userpos']."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
    db("ALTER TABLE `".$db['users']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['userstats']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['votes']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
    db("ALTER TABLE `".$db['vote_results']."` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;");
}

function update_mysql_1_6() {
    global $db;
    db("ALTER TABLE `".$db['f_threads']."` CHANGE `edited` `edited` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL");
    db("ALTER TABLE `".$db['users']."` CHANGE `whereami` `whereami` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL");
    db("ALTER TABLE `".$db['downloads']."` ADD `last_dl` INT( 20 ) NOT NULL DEFAULT '0' AFTER `date`");
    db("ALTER TABLE `".$db['settings']."` CHANGE `i_autor` `i_autor` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL");
    db("ALTER TABLE `".$db['gb']."` CHANGE `hp` `hp` VARCHAR(130) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL");
    db("ALTER TABLE `".$db['permissions']."` ADD `gs_showpw` INT(1) NOT NULL DEFAULT '0'");
    db("ALTER TABLE `".$db['permissions']."` ADD `slideshow` INT(1) NOT NULL DEFAULT '0'");
    db("ALTER TABLE `".$db['permissions']."` ADD `galleryintern` INT(1) NOT NULL DEFAULT '0'");
    db("ALTER TABLE `".$db['permissions']."` ADD `dlintern` INT(1) NOT NULL DEFAULT '0'");
    db("ALTER TABLE `".$db['gallery']."` ADD `intern` INT(1) NOT NULL DEFAULT '0'");
    db("ALTER TABLE `".$db['downloads']."` ADD `intern` INT(1) NOT NULL DEFAULT '0'");
    db("ALTER TABLE `".$db['settings']."` ADD `urls_linked` INT(1) NOT NULL DEFAULT '1', ADD `ts_customicon` INT(1) NOT NULL DEFAULT '1' AFTER `ts_version`, ADD `ts_showchannel` INT(1) NOT NULL DEFAULT '0' AFTER `ts_customicon`");
    db("ALTER TABLE `".$db['msg']."` CHANGE `see_u` `see_u` INT( 1 ) NOT NULL DEFAULT '0'");
    db("ALTER TABLE `".$db['newskat']."` CHANGE `katimg` `katimg` tinytext CHARACTER SET latin1 COLLATE latin1_swedish_ci");
    db("ALTER TABLE `".$db['users']."` ADD `pkey` VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER `sessid`;");
    db("ALTER TABLE `".$db['gb']."` CHANGE `editby` `editby` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL");
    db("ALTER TABLE `".$db['usergb']."` CHANGE `editby` `editby` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL");
    db("ALTER TABLE `".$db['newscomments']."` CHANGE `editby` `editby` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL");
    db("ALTER TABLE `".$db['acomments']."` CHANGE `editby` `editby` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL");
    db("ALTER TABLE `".$db['cw_comments']."` CHANGE `editby` `editby` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL");
    db("ALTER TABLE `".$db['f_posts']."` CHANGE `edited` `edited` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL");
    db("ALTER TABLE `".$db['gb']."` CHANGE `public` `public` INT( 1 ) NOT NULL DEFAULT '0'");
    db("ALTER TABLE `".$db['msg']."` CHANGE `page` `page` INT( 1 ) NOT NULL DEFAULT '0'");
    db("ALTER TABLE `".$db['settings']."` CHANGE `pagetitel` `pagetitel` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;");
    db("ALTER TABLE `".$db['settings']."` CHANGE `clanname` `clanname` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;");
    db("ALTER TABLE `".$db['sites']."` CHANGE `titel` `titel` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;");
    db("ALTER TABLE `".$db['clankasse']."` CHANGE `betrag` `betrag` FlOAT(10) NOT NULL");
    db("ALTER TABLE `".$db['users']."` CHANGE `gmaps_koord` `gmaps_koord` VARCHAR( 249 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';");
    db("ALTER TABLE `".$db['permissions']."` CHANGE `pos` `pos` INT( 1 ) NOT NULL DEFAULT '0';");
    db("ALTER TABLE `".$db['rankings']."` CHANGE `lastranking` `lastranking` INT( 10 ) NOT NULL DEFAULT '0';");
    db("ALTER TABLE `".$db['users']."` ADD `xboxid` VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER `steamid`;");
    db("ALTER TABLE `".$db['users']."` ADD `psnid` VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER `steamid`;");
    db("ALTER TABLE `".$db['users']."` ADD `skypename` VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER `steamid`;");
    db("ALTER TABLE `".$db['users']."` ADD `originid` VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER `steamid`;");
    db("ALTER TABLE `".$db['users']."` ADD `battlenetid` VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER `steamid`;");
    db("ALTER TABLE `".$db['users']."` ADD `perm_gb`INT(1) NOT NULL DEFAULT '1' AFTER `pnmail`;");
    db("ALTER TABLE `".$db['users']."` ADD `perm_gallery`INT(1) NOT NULL DEFAULT '0' AFTER `pnmail`;");
    db("ALTER TABLE `".$db['squads']."` ADD `team_joinus`INT(1) NOT NULL DEFAULT '1';");
    db("ALTER TABLE `".$db['squads']."` ADD `team_fightus`INT(1) NOT NULL DEFAULT '1';");
    db("ALTER TABLE `".$db['users']."` ADD `banned`INT(1) NOT NULL DEFAULT '0' AFTER `level`;");
    db("ALTER TABLE `".$db['permissions']."` ADD `backup` INT(1) NOT NULL DEFAULT '0' AFTER `artikel`;");
    db("ALTER TABLE `".$db['permissions']."` ADD `clear` INT(1) NOT NULL DEFAULT '0' AFTER `clanwars`;");
    db("ALTER TABLE `".$db['permissions']."` ADD `config` INT(1) NULL DEFAULT '0' AFTER `clear`;");
    db("ALTER TABLE `".$db['permissions']."` ADD `forumkats` INT(1) NOT NULL DEFAULT '0' AFTER `forum`;");
    db("ALTER TABLE `".$db['permissions']."` ADD `partners` INT(1) NOT NULL DEFAULT '0' AFTER `gb`;");
    db("ALTER TABLE `".$db['permissions']."` ADD `profile` INT(1) NOT NULL DEFAULT '0' AFTER `partners`;");
    db("ALTER TABLE `".$db['permissions']."` ADD `positions` INT(1) NOT NULL DEFAULT '0' AFTER `pos`;");
    db("ALTER TABLE `".$db['permissions']."` ADD `protocol` INT(1) NOT NULL DEFAULT '0' AFTER `profile`;");
    db("ALTER TABLE `".$db['permissions']."` ADD `smileys` INT(1) NOT NULL DEFAULT '0' AFTER `slideshow`;");
    db("ALTER TABLE `".$db['permissions']."` ADD `support` INT(1) NOT NULL DEFAULT '0' AFTER `smileys`;");
    db("ALTER TABLE `".$db['permissions']."` ADD `impressum` INT(1) NOT NULL DEFAULT '0' AFTER `intnews`;");
    db("ALTER TABLE `".$db['f_threads']."` CHANGE `t_reg` `t_reg` INT(11) NOT NULL DEFAULT '0';");
    db("ALTER TABLE `".$db['settings']."` DROP `pfad`;");
    db("ALTER TABLE `".$db['server']."` DROP `bl_file`, DROP `bl_path`, DROP `ftp_pwd`, DROP `ftp_login`, DROP `ftp_host`;");
    db("ALTER TABLE `".$db['settings']."` DROP `gmaps_key`;");
    db("ALTER TABLE `".$db['config']."` ADD `m_membermap` INT(5) NOT NULL DEFAULT '10' AFTER `m_banned`;");
    db("ALTER TABLE `".$db['settings']."` DROP `ftp_host`, DROP `ftp_login`, DROP `ftp_pwd`, DROP `bl_path`;");
    db("ALTER TABLE `".$db['settings']."` DROP `balken_vote`, DROP `balken_vote_menu`, DROP `balken_cw`;");
    db("ALTER TABLE `".$db['settings']."` DROP `squadtmpl`;");
    db("ALTER TABLE `".$db['downloads']."` CHANGE `beschreibung` `beschreibung` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;");
    db("ALTER TABLE `".$db['away']."` CHANGE `lastedit` `lastedit` TEXT NULL DEFAULT NULL;");
    db("ALTER TABLE `".$db['serverliste']."` CHANGE `slots` `slots` CHAR(11) NOT NULL DEFAULT '';");
    db("ALTER TABLE `".$db['serverliste']."` CHANGE `clanname` `clanname` VARCHAR(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';");
    db("ALTER TABLE `".$db['serverliste']."` CHANGE `pwd` `pwd` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';");
    db("ALTER TABLE `".$db['ipcheck']."` ADD `user_id` INT(11) NOT NULL DEFAULT '0' AFTER `ip`;");
    db("ALTER TABLE `".$db['settings']."` ADD `steam_api_key` VARCHAR(50) NOT NULL DEFAULT '' AFTER `urls_linked`;");
    db("ALTER TABLE `".$db['settings']."` ADD `db_optimize` INT(20) NOT NULL DEFAULT '0' AFTER `steam_api_key`;");
    db("ALTER TABLE `".$db['f_access']."` ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);");

    //->Add new Indexes * MySQL optimize
    db("ALTER TABLE `".$db['users']."` ADD INDEX(`pwd`);");
    db("ALTER TABLE `".$db['users']."` ADD INDEX(`time`);");
    db("ALTER TABLE `".$db['users']."` ADD INDEX(`bday`);");
    db("ALTER TABLE `".$db['navi']."` ADD INDEX(`url`);");
    db("ALTER TABLE `".$db['ipcheck']."` ADD INDEX(`ip`);");
    db("ALTER TABLE `".$db['ipcheck']."` ADD INDEX(`what`);");
    db("ALTER TABLE `".$db['userpos']."` ADD INDEX(`user`);");
    db("ALTER TABLE `".$db['userpos']."` ADD INDEX(`squad`);");
    db("ALTER TABLE `".$db['msg']."` ADD INDEX(`an`);");
    db("ALTER TABLE `".$db['f_access']."` ADD INDEX(`user`);");
    db("ALTER TABLE `".$db['f_access']."` ADD INDEX(`forum`);");
    db("ALTER TABLE `".$db['c_ips']."` ADD INDEX(`ip`);");
    db("ALTER TABLE `".$db['counter']."` ADD INDEX(`today`);");

    //-> Fix Settings Table
    if(db("SELECT * FROM `".$db['settings']."`",true) >= 2) {
        $get_settings = db("SELECT * FROM `".$db['settings']."` WHERE `id` = 1",false,true);
        db("TRUNCATE TABLE `".$db['settings']."`");
        $sql = "INSERT INTO `".$db['settings']."` SET ";
        foreach ($get_settings as $key => $var) {
            $sql .= "`".$key."` = '".$var."',";
        }
        db(substr($sql, 0, -1));
    }

    //-> Fix Config Table
    if(db("SELECT * FROM `".$db['config']."`",true) >= 2) {
        $get_config = db("SELECT * FROM `".$db['config']."` WHERE `id` = 1",false,true);
        db("TRUNCATE TABLE `".$db['config']."`");
        $sql = "INSERT INTO `".$db['config']."` SET ";
        foreach ($get_config as $key => $var) {
            $sql .= "`".$key."` = '".$var."',";
        }
        db(substr($sql, 0, -1));
    }

    db("UPDATE `".$db['settings']."` SET `tmpdir` = 'version1.6' WHERE `id` = 1;"); //Set Template 1.6

    //Add UNIQUE KEY
    db("ALTER TABLE `".$db['config']."` ADD UNIQUE(`id`);");
    db("ALTER TABLE `".$db['settings']."` ADD UNIQUE(`id`);");

    $qry = db("SELECT `id`,`level`,`bday` FROM `".$db['users']."`;");
    if(_rows($qry)>= 1)
        while($get = _fetch($qry)) {
            $banned = $get['level'] == 'banned' ? 1 : 0;
            $level = $get['level'] == 'banned' ? 0 : $get['level'];
            db("UPDATE ".$db['users']." SET `level` = ".$level.", `banned` = ".$banned.", `bday` = ".(!empty($get['bday']) ? strtotime($get['bday']) : 0)." WHERE `id` = ".$get['id']);
        }
    unset($level,$banned);

    db("ALTER TABLE ".$db['users']." CHANGE `level` `level` INT( 2 ) NOT NULL DEFAULT '0';"); //Set level to int
    db("ALTER TABLE ".$db['users']." CHANGE `bday` `bday` INT(11) NOT NULL DEFAULT '0';");

    //-> Forum Sortieren
    db("ALTER TABLE ".$db['f_skats']." ADD `pos` int(5) NOT NULL;");

    //-> Forum Sortieren funktion: schreibe id von spalte in pos feld um konflikte zu vermeiden!
    $qry = db("SELECT id FROM ".$db['f_skats'].";");
    while($get = _fetch($qry)){
        db("UPDATE ".$db['f_skats']." SET `pos` = '".$get['id']."' WHERE `id` = '".$get['id']."'");
    }

    //-> Alte Artikelkommentare l�schen wo f�r es keinen Artikel mehr gibt
    $qry = db("SELECT id FROM `".$db['artikel']."`"); $artikel_index = array();
    while($get = _fetch($qry)){ $artikel_index[$get['id']] = true; }

    $qry = db("SELECT id,artikel FROM `".$db['acomments']."`");
    while($get = _fetch($qry)){
        if(!array_key_exists($get['artikel'], $artikel_index))
            db("DELETE FROM `".$db['acomments']."` WHERE `id` = ".$get['id']);
    }

    //-> Slideshow
    db("DROP TABLE IF EXISTS ".$db['slideshow'].";");
    db("CREATE TABLE ".$db['slideshow']." (
        `id` int(11) NOT NULL auto_increment,
        `pos` int(5) NOT NULL default '0',
        `bez` varchar(200) NOT NULL default '',
        `showbez` int(1) NOT NULL default '1',
        `desc` varchar(249) NOT NULL default '',
        `url` varchar(200) NOT NULL default '',
        `target` int(1) NOT NULL default '0',
        PRIMARY KEY  (`id`));");

    $qry = db("SELECT `id` FROM `".$db['users']."` WHERE `level` = 4;");
    if(_rows($qry)>= 1) {
        while ($get = _fetch($qry)) {
            db("UPDATE " . $db['permissions'] . " SET `slideshow` = 1, `gs_showpw` = 1 WHERE `id` = " . $get['id'] . ";");
        }
    }
}

function update_mysql_1_6_0_4() {
    global $db;
    db("ALTER TABLE `".$db['ipcheck']."` ADD `created` INT(11) NOT NULL DEFAULT '0' AFTER `time`;");
    db("ALTER TABLE `".$db['c_ips']."` ADD  `agent` text DEFAULT NULL AFTER `datum`;");
}

function update_mysql_1_6_1_0() {
    global $db,$updater;
    db("ALTER TABLE `".$db['users']."` ADD `dsgvo_lock` INT(1) NOT NULL DEFAULT '1' AFTER `level`;");
    db("ALTER TABLE `".$db['users']."` ADD `pwd_md5` INT(1) NOT NULL DEFAULT '1' AFTER `pwd`;");
    db("ALTER TABLE `".$db['users']."` ADD `show` INT(1) NOT NULL DEFAULT '4' AFTER `perm_gb`;");

    db("DROP TABLE IF EXISTS `".$db['dsgvo']."`;");
    db("CREATE TABLE `".$db['dsgvo']."` (
          `id` int(11) NOT NULL,
          `title` varchar(255) NOT NULL DEFAULT '',
          `info_tag` varchar(200) DEFAULT '',
          `text_tag` varchar(200) DEFAULT '',
          `persid` int(1) NOT NULL DEFAULT '0',
          `show` int(1) NOT NULL DEFAULT '0',
          `lock_show` int(1) NOT NULL DEFAULT '0',
          `for_dsgvo` int(1) NOT NULL DEFAULT '1',
          `for_dsgvo_ak` int(1) NOT NULL DEFAULT '0',
          `sort` int(3) NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`));");

    db("INSERT INTO `".$db['dsgvo']."` (`id`, `title`, `info_tag`, `text_tag`, `persid`, `show`, `lock_show`, `for_dsgvo`, `for_dsgvo_ak`, `sort`) VALUES
        (1, '_dsgvo_base_title_001', '_dsgvo_base_001', '_dsgvo_base_text_001', 0, 1, 0, 1, 0, 1),
        (2, '_dsgvo_base_title_002', '_dsgvo_base_002', '_dsgvo_base_text_002', 0, 1, 0, 1, 1, 2),
        (3, '_dsgvo_base_title_003', '_dsgvo_base_003', '_dsgvo_base_text_003', 0, 1, 0, 1, 1, 3),
        (4, '_dsgvo_base_title_004', '_dsgvo_base_004', '_dsgvo_base_text_004', 0, 1, 0, 1, 1, 4),
        (5, '_dsgvo_base_title_005', '_dsgvo_base_005', '_dsgvo_base_text_005', 0, 1, 0, 1, 1, 5),
        (6, '_dsgvo_base_title_006', '_dsgvo_base_006', '_dsgvo_base_text_006', 0, 1, 0, 1, 1, 6),
        (7, '_dsgvo_base_title_007', '_dsgvo_base_007', '_dsgvo_base_text_007', 1, 1, 0, 1, 1, 7),
        (8, '_dsgvo_base_title_008', '_dsgvo_base_008', '_dsgvo_base_text_008', 2, 0, 0, 1, 1, 8),
        (9, '_dsgvo_base_title_009', '_dsgvo_base_009', '_dsgvo_base_text_009', 0, 1, 0, 1, 1, 9),
        (10, '_dsgvo_base_title_010', '_dsgvo_base_010', '_dsgvo_base_text_010', 0, 1, 0, 1, 1, 10),
        (11, '_dsgvo_base_title_011', '_dsgvo_base_011', '_dsgvo_base_text_011', 0, 1, 0, 1, 1, 11),
        (12, '_dsgvo_base_title_012', '_dsgvo_base_012', '_dsgvo_base_text_012', 0, 1, 0, 1, 1, 12),
        (13, '_dsgvo_base_title_013', '_dsgvo_base_013', '_dsgvo_base_text_013', 0, 1, 0, 1, 1, 13),
        (14, '_dsgvo_base_title_014', '_dsgvo_base_014', '_dsgvo_base_text_014', 0, 0, 0, 1, 1, 14),
        (15, '_dsgvo_base_title_015', '_dsgvo_base_015', '_dsgvo_base_text_015', 0, 1, 0, 1, 1, 15),
        (16, '_dsgvo_base_title_016', '_dsgvo_base_016', '_dsgvo_base_text_016', 0, 1, 0, 1, 1, 16),
        (17, '_dsgvo_base_title_017', '_dsgvo_base_017', '_dsgvo_base_text_017', 0, 1, 0, 1, 1, 17),
        (18, '_dsgvo_base_title_018', '_dsgvo_base_018', '_dsgvo_base_text_018', 0, 1, 0, 1, 1, 18),
        (19, '_dsgvo_base_title_019', '_dsgvo_base_019', '_dsgvo_base_text_019', 0, 0, 0, 1, 1, 19),
        (20, '_dsgvo_base_title_020', '_dsgvo_base_020', '_dsgvo_base_text_020', 0, 0, 0, 1, 1, 20),
        (21, '_dsgvo_base_title_021', '_dsgvo_base_021', '_dsgvo_base_text_021', 0, 0, 0, 1, 1, 21),
        (22, '_dsgvo_base_title_022', '_dsgvo_base_022', '_dsgvo_base_text_022', 0, 0, 0, 1, 1, 22),
        (23, '_dsgvo_base_title_023', '_dsgvo_base_023', '_dsgvo_base_text_023', 0, 0, 0, 1, 1, 23),
        (24, '_dsgvo_base_title_024', '_dsgvo_base_024', '_dsgvo_base_text_024', 0, 0, 0, 1, 1, 24),
        (25, '_dsgvo_base_title_025', '_dsgvo_base_025', '_dsgvo_base_text_025', 0, 0, 0, 1, 1, 25),
        (26, '_dsgvo_base_title_026', '_dsgvo_base_026', '_dsgvo_base_text_026', 0, 0, 0, 1, 1, 26),
        (27, '_dsgvo_base_title_027', '_dsgvo_base_027', '_dsgvo_base_text_027', 0, 0, 0, 1, 1, 27),
        (28, '_dsgvo_base_title_028', '_dsgvo_base_028', '_dsgvo_base_text_028', 0, 0, 0, 1, 1, 28),
        (29, '_dsgvo_base_title_029', '_dsgvo_base_029', '_dsgvo_base_text_029', 0, 1, 0, 1, 1, 29),
        (30, '_dsgvo_base_title_030', '_dsgvo_base_030', '_dsgvo_base_text_030', 0, 0, 0, 1, 1, 30),
        (31, '_dsgvo_base_title_031', '_dsgvo_base_031', '_dsgvo_base_text_031', 0, 0, 0, 1, 1, 31),
        (32, '_dsgvo_base_title_032', '_dsgvo_base_032', '_dsgvo_base_text_032', 0, 0, 0, 1, 1, 32),
        (33, '_dsgvo_base_title_033', '_dsgvo_base_033', '_dsgvo_base_text_033', 0, 1, 0, 1, 1, 33),
        (34, '_dsgvo_base_title_034', '_dsgvo_base_034', '_dsgvo_base_text_034', 0, 1, 0, 1, 1, 34),
        (35, '_dsgvo_base_title_035', '_dsgvo_base_035', '_dsgvo_base_text_035', 0, 1, 0, 1, 1, 35),
        (36, '_dsgvo_base_title_036', '_dsgvo_base_036', '_dsgvo_base_text_036', 0, 1, 0, 1, 1, 36),
        (37, '_dsgvo_base_title_037', '_dsgvo_base_037', '_dsgvo_base_text_037', 0, 1, 0, 1, 1, 37),
        (38, '_dsgvo_base_title_201', '_dsgvo_base_201', '_dsgvo_base_text_201', 0, 0, 1, 0, 1, 1);");

    db("DROP TABLE IF EXISTS `".$db['dsgvo_pers']."`;");
    db("CREATE TABLE `".$db['dsgvo_pers']."` (
        `id` int(5) NOT NULL auto_increment,
        `organisation` text,
        `titel` text,
        `first_name` text,
        `last_name` text,
        `address` text,
        `zip_code` text,
        `place` text,
        `country` text,
        `e-mail` text,
        `phone` text,
        `website` text,
        PRIMARY KEY (`id`));");

    db("INSERT INTO `".$db['dsgvo_pers']."` (`id`, `organisation`,`titel`, `first_name`, `last_name`, `address`, `zip_code`, `place`, `country`, `e-mail`, `phone`, `website`) VALUES ".
        "(1, 'Muster Unternehmen', 'Herr', 'Max', 'Mustermann', 'Eisenweg. 1', '123456', 'Musterdorf', 'Germany', 'max.mustermann@musterdorf.de', '049 12345 67891234566', 'http://www.deineUrl.de'),".
        "(2, 'Muster Unternehmen', 'Herr', 'Max', 'Mustermann', 'Eisenweg. 1', '123456', 'Musterdorf', 'Germany', 'max.mustermann@musterdorf.de', '049 12345 67891234566', 'http://www.deineUrl.de');");

    db("ALTER TABLE `".$db['permissions']."` ADD `datenschutz` INT(1) NOT NULL DEFAULT '0' AFTER `dlintern`;");

    db("DROP TABLE IF EXISTS `".$db['dsgvo_log']."`;");
    db("CREATE TABLE `".$db['dsgvo_log']."` (
      `id` int(11) NOT NULL auto_increment,
      `uid` int(11) NOT NULL DEFAULT '0',
      `ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',
      `date` int(11) NOT NULL DEFAULT '0',
      `agent` text,
      PRIMARY KEY (`id`));");

    db("ALTER TABLE `".$db['dsgvo_log']."` ADD INDEX(`uid`);");
    db("ALTER TABLE `".$db['f_threads']."` ADD `dsgvo` INT(1) NOT NULL DEFAULT '0' AFTER `vote`;");
    db("TRUNCATE `".$db['f_abo']."`;");

    db("DROP TABLE IF EXISTS `".$db['prefix']."banned`;"); //Remove old table
    db("ALTER TABLE `".$db['users']."` ADD `language` varchar(249) NOT NULL default '' AFTER `show`;");
    db("ALTER TABLE `".$db['settings']."` DROP `persinfo`;");

    $gets = db("SELECT * FROM `".$db['settings']."`;",false,true);
    if(array_key_exists('last_backup',$gets)) {
        db("ALTER TABLE `" . $db['settings'] . "` DROP `last_backup`;");
    }

    //Check & Fix Users Table
    $qry = db("SELECT `id` FROM `".$db['users']."`;");
    while ($get = _fetch($qry)) {
        //Fix Userstats Table
        if(!db("SELECT `id` FROM `".$db['userstats']."` WHERE `user` = ".$get['id'].";",true)) {
            db("INSERT INTO `".$db['userstats']."` SET `user` =  ".$get['id'].";");
        }

        //Fix Permissions Table
        if(!db("SELECT `id` FROM `".$db['permissions']."` WHERE `user` = ".$get['id'].";",true)) {
            db("INSERT INTO `".$db['permissions']."` SET `user` = ".$get['id'].";");
        }
    }

    //Cleanup old rows
    $qry = db("SELECT `id`,`user` FROM `".$db['userstats']."`;");
    while ($get = _fetch($qry)) {
        if(!db("SELECT `id` FROM `".$db['users']."` WHERE `id` = ".$get['user'].";",true)) {
            db("DELETE FROM `".$db['userstats']."` WHERE `id` = ".$get['id'].";");
        }
    }

    $qry = db("SELECT `id`,`user` FROM `".$db['permissions']."`;");
    while ($get = _fetch($qry)) {
        if(!db("SELECT `id` FROM `".$db['users']."` WHERE `id` = ".$get['user'].";",true)) {
            db("DELETE FROM `".$db['permissions']."` WHERE `id` = ".$get['id'].";");
        }
    }
    //End Cleanup Rows

    if($updater) {
        db("UPDATE `".$db['settings']."` SET `db_optimize` = '".(time()+auto_db_optimize_interval)."' WHERE `id` = 1;");
        db_optimize();
    }

    ignore_user_abort(false);
    set_time_limit(30);
}