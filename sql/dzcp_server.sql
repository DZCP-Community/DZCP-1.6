-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: sql125.your-server.de
-- Erstellungszeit: 06. Jun 2021 um 17:03
-- Server-Version: 5.7.34-2
-- PHP-Version: 7.4.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `dzcpad_db1`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_account`
--

DROP TABLE IF EXISTS `dzcp_server_account`;
CREATE TABLE `dzcp_server_account` (
                                       `id` int(11) NOT NULL,
                                       `uid` int(11) NOT NULL DEFAULT '0',
                                       `created` int(20) NOT NULL DEFAULT '0',
                                       `updated` int(20) NOT NULL DEFAULT '0',
                                       `action` text,
                                       `from` int(11) NOT NULL DEFAULT '0',
                                       `to` int(11) NOT NULL DEFAULT '0',
                                       `balance` int(50) NOT NULL DEFAULT '0',
                                       `type` enum('group','user') NOT NULL DEFAULT 'user',
                                       `transid` varchar(50) NOT NULL DEFAULT 'xxxxxxxx',
                                       `certid` int(11) NOT NULL DEFAULT '0',
                                       `info` text,
                                       `removed` int(1) NOT NULL DEFAULT '0',
                                       `payed` int(1) NOT NULL DEFAULT '0',
                                       `show` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_account`
--

INSERT INTO `dzcp_server_account` (`id`, `uid`, `created`, `updated`, `action`, `from`, `to`, `balance`, `type`, `transid`, `certid`, `info`, `removed`, `payed`, `show`) VALUES
(1, 39391, 1610318752, 0, 'test', 0, 0, 1000, 'user', 'DJ-45973568211785', 2, NULL, 0, 0, 1),
(2, 39391, 1610319752, 0, 'test1', 0, 0, -100, 'user', 'DJ-4445345345345', 2, NULL, 0, 0, 1),
(3, 39391, 1610419752, 0, 'test2', 0, 0, 600, 'user', 'DJ-678678676867', 2, NULL, 0, 0, 1),
(4, 39391, 1610419752, 0, 'test2', 0, 0, 600, 'user', 'DJ-678678676867', 2, NULL, 0, 0, 1),
(5, 39391, 1613318752, 0, 'testggfdgdfgfgdfgd', 0, 0, 2200, 'user', 'DJ-45973568211785', 2, NULL, 0, 0, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_addons`
--

DROP TABLE IF EXISTS `dzcp_server_addons`;
CREATE TABLE `dzcp_server_addons` (
                                      `id` int(11) NOT NULL,
                                      `aid` int(11) NOT NULL DEFAULT '0',
                                      `name` text,
                                      `autor` text,
                                      `url_title` text,
                                      `url` text,
                                      `file` varchar(200) DEFAULT '',
                                      `version` varchar(200) NOT NULL DEFAULT '1.0.0.0',
                                      `count` int(11) NOT NULL DEFAULT '0',
                                      `version_id` int(3) NOT NULL DEFAULT '1',
                                      `enabled` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `dzcp_server_addons`
--

INSERT INTO `dzcp_server_addons` (`id`, `aid`, `name`, `autor`, `url_title`, `url`, `file`, `version`, `count`, `version_id`, `enabled`) VALUES
(1, 11228281, 'DZCP.de / Addons', 'Cosmo', 'DZCP.de / Addons', 'http://www.modsbar.de/Addons/1676/werbebanner/', 'addon_werbebanner.zip', '1.0.0.0', 6, 2, 0),
(2, 68562258, 'Userlistpic', 'Cosmo', 'DZCP.de / Addons', NULL, 'http://www.modsbar.de/Modifikationen/1602/userlisten-rangmod/', '1.0.0.0', 0, 2, 0),
(3, 46482822, 'Userlistpic', 'Cosmo', 'DZCP.de / Addons', NULL, 'http://www.modsbar.de/Addons/1931/auto-playersheed---rahmen-addon/', '1.0.0.0', 0, 2, 0),
(4, 87984617, 'Origin Pack', 'Cosmo', 'DZCP.de / Addons', NULL, 'http://www.modsbar.de/Addons/1801/origin-pack---battlefield-3-addon/', '1.0.0.0', 0, 1, 0),
(5, 27734646, 'Push Nachrichten', 'Cosmo', 'DZCP.de / Addons', NULL, 'http://www.modsbar.de/Addons/1954/push-nachrichten--push-notifications/', '1.0.0.0', 0, 1, 0),
(6, 92379684, 'News Archiv', 'Cosmo', 'DZCP.de / Addons', NULL, 'http://www.modsbar.de/Addons/1734/news-archiv-mit-filtern/', '1.0.0.0', 0, 1, 0),
(7, 77889338, NULL, 'Cosmo', 'DZCP.de / Addons', NULL, 'http://www.modsbar.de/Addons/1729/navigation-nach-kategorien-anzeigen/', '1.0.0.0', 0, 1, 0),
(8, 83891988, 'Gravatar Plugin', 'Cosmo', 'DZCP.de / Addons', NULL, 'http://www.modsbar.de/Addons/1779/gravatar-plugin-dzcp/', '1.0.0.0', 0, 1, 0),
(9, 22986489, 'Addon Checker', 'DZCP-Team', 'DZCP.de / Addons', NULL, 'https://www.dzcp.de/downloads/22986489', '1.0.0.0', 65, 1, 1),
(10, 79844136, 'Akzeptieren Mod', 'Cosmo', 'DZCP.de / Addons', NULL, 'http://www.modsbar.de/Addons/1712/akzeptieren-mod/', '1.0.0.0', 1, 1, 0),
(11, 97457931, 'Activitylist Addon', 'Cosmo', 'DZCP.de / Addons', NULL, 'https://www.dzcp.de/addons/97457931', '1.1.0.0', 77, 1, 1),
(12, 82814392, 'Datum Facebook Like', 'Cosmo', 'DZCP.de / Addons', NULL, 'http://www.modsbar.de/Modifikationen/1932/datumsformat---facebook-like/', '1.0.0.0', 0, 1, 0),
(13, 93631988, 'Clanbewerbung', 'Cosmo', 'DZCP.de / Addons', NULL, 'http://www.modsbar.de/Addons/1724/ausfuehrliches-bewerbungsformular/', '1.0.0.0', 0, 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_certs`
--

DROP TABLE IF EXISTS `dzcp_server_certs`;
CREATE TABLE `dzcp_server_certs` (
                                     `id` int(11) NOT NULL,
                                     `indent` varchar(100) NOT NULL DEFAULT '',
                                     `ipv4` varchar(20) NOT NULL DEFAULT '0.0.0.0',
                                     `ipv6` varchar(40) DEFAULT '::',
                                     `name` varchar(200) NOT NULL DEFAULT '',
                                     `time` int(11) NOT NULL DEFAULT '0',
                                     `enabled` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_certs`
--

INSERT INTO `dzcp_server_certs` (`id`, `indent`, `ipv4`, `ipv6`, `name`, `time`, `enabled`) VALUES
(1, 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', '0.0.0.0', '::', 'Download Service', 0, 1),
(2, 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', '0.0.0.0', '::', 'DZCP.de Service', 0, 1),
(3, 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', '0.0.0.0', '::', 'File Update Service', 0, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_clients`
--

DROP TABLE IF EXISTS `dzcp_server_clients`;
CREATE TABLE `dzcp_server_clients` (
    `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_cronjob`
--

DROP TABLE IF EXISTS `dzcp_server_cronjob`;
CREATE TABLE `dzcp_server_cronjob` (
                                       `id` int(11) NOT NULL,
                                       `call` varchar(200) NOT NULL DEFAULT '',
                                       `last_call` int(11) NOT NULL DEFAULT '0',
                                       `next_call` int(11) NOT NULL DEFAULT '-1',
                                       `enabled` int(1) NOT NULL DEFAULT '1',
                                       `data` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_cronjob`
--

INSERT INTO `dzcp_server_cronjob` (`id`, `call`, `last_call`, `next_call`, `enabled`, `data`) VALUES
(1, 'updateDownloadInfos', 1622959203, 28800, 1, '[]'),
(2, 'dumpServerDatabase', 1622988001, 86400, 1, '[]'),
(3, 'clearServerDir', 1622923202, 172800, 1, '[]'),
(4, 'resetDemoDatabase', 0, -1, 0, '{}'),
(5, 'cleanupDownloadKeys', 1622988001, -1, 1, '[]'),
(6, 'rollServerLogs', 1620691204, 2592000, 1, '[]'),
(7, 'rollAccountingLogs', 0, 86400, 0, '{}');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_downloads`
--

DROP TABLE IF EXISTS `dzcp_server_downloads`;
CREATE TABLE `dzcp_server_downloads` (
                                         `id` int(11) NOT NULL,
                                         `fileID` int(11) NOT NULL DEFAULT '0',
                                         `pathID` int(11) NOT NULL DEFAULT '0',
                                         `catID` int(11) NOT NULL DEFAULT '1',
                                         `subCatID` int(11) NOT NULL DEFAULT '0',
                                         `top` tinyint(1) NOT NULL DEFAULT '0',
                                         `intern` bit(1) NOT NULL DEFAULT b'0',
                                         `public` tinyint(1) NOT NULL DEFAULT '1',
                                         `addons` int(11) NOT NULL DEFAULT '-1',
                                         `enabled` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_downloads`
--

INSERT INTO `dzcp_server_downloads` (`id`, `fileID`, `pathID`, `catID`, `subCatID`, `top`, `intern`, `public`, `addons`, `enabled`) VALUES
(1, 1, 2, 4, 6, 0, b'0', 1, -1, 1),
(2, 2, 2, 4, 6, 0, b'0', 1, 0, 1),
(3, 3, 2, 4, 6, 0, b'0', 1, 0, 1),
(4, 4, 2, 4, 6, 0, b'0', 1, 0, 1),
(5, 5, 2, 4, 6, 0, b'0', 1, 0, 1),
(6, 6, 2, 4, 6, 0, b'0', 1, 0, 1),
(7, 7, 2, 4, 6, 0, b'0', 1, 0, 1),
(8, 8, 2, 4, 6, 0, b'0', 1, 0, 1),
(9, 9, 2, 2, 2, 1, b'0', 1, 0, 1),
(10, 10, 2, 4, 7, 0, b'0', 1, 0, 1),
(11, 11, 2, 4, 7, 0, b'0', 1, 0, 1),
(12, 12, 2, 2, 3, 1, b'0', 1, 0, 1),
(13, 13, 2, 3, 11, 0, b'0', 1, 0, 1),
(14, 14, 2, 4, 5, 0, b'0', 1, 0, 1),
(15, 15, 2, 4, 5, 0, b'0', 1, 0, 1),
(16, 16, 2, 4, 5, 0, b'0', 1, 0, 1),
(17, 17, 2, 4, 5, 0, b'0', 1, 0, 1),
(18, 18, 2, 4, 5, 0, b'0', 1, 0, 1),
(19, 19, 2, 4, 5, 0, b'0', 1, 0, 1),
(20, 20, 2, 4, 5, 0, b'0', 1, 0, 1),
(21, 21, 2, 4, 5, 0, b'0', 1, 0, 1),
(22, 22, 2, 4, 5, 0, b'0', 1, 0, 1),
(23, 23, 2, 4, 5, 0, b'0', 1, 0, 1),
(24, 24, 2, 4, 5, 0, b'0', 1, 0, 1),
(25, 25, 2, 4, 5, 0, b'0', 1, 0, 1),
(26, 26, 2, 2, 1, 1, b'0', 1, 0, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_downloads_category`
--

DROP TABLE IF EXISTS `dzcp_server_downloads_category`;
CREATE TABLE `dzcp_server_downloads_category` (
                                                  `id` int(11) NOT NULL,
                                                  `name` varchar(250) NOT NULL DEFAULT '',
                                                  `addons` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_downloads_category`
--

INSERT INTO `dzcp_server_downloads_category` (`id`, `name`, `addons`) VALUES
(1, 'Sonstiges', -1),
(2, 'DZCP-Neueste', 0),
(3, 'DZCP-Updates', 0),
(4, 'DZCP-Archiv', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_downloads_files`
--

DROP TABLE IF EXISTS `dzcp_server_downloads_files`;
CREATE TABLE `dzcp_server_downloads_files` (
                                               `id` int(11) NOT NULL,
                                               `name` varchar(255) NOT NULL,
                                               `description` text NOT NULL,
                                               `file` varchar(250) NOT NULL DEFAULT 'xxx.zip',
                                               `crc` varchar(50) NOT NULL DEFAULT '',
                                               `time` int(11) NOT NULL DEFAULT '0',
                                               `updated` int(11) NOT NULL DEFAULT '0',
                                               `forum_url` varchar(250) NOT NULL DEFAULT '',
                                               `forum_url_id` int(4) NOT NULL DEFAULT '0',
                                               `speed` int(11) NOT NULL DEFAULT '512'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_downloads_files`
--

INSERT INTO `dzcp_server_downloads_files` (`id`, `name`, `description`, `file`, `crc`, `time`, `updated`, `forum_url`, `forum_url_id`, `speed`) VALUES
(1, 'DZCP 1.5.1', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.5.1.zip', '7a136f56687276ea4aef5eb46e5e15f9731802a7', 1561748113, 1561748113, '?action=show&kid=%', 2, 64),
(2, 'DZCP 1.5.2', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.5.2.zip', '7970ff3f78038994303a0030be74e7e4ec75a826', 1561748113, 1561748109, '?action=show&kid=%', 2, 64),
(3, 'DZCP 1.5.3', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.5.3.zip', '9e88ab765c86a545d559c74c338beaf7b4ad8cae', 1561748113, 1561748105, '?action=show&kid=%', 2, 64),
(4, 'DZCP 1.5.4', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.5.4.zip', '208caa9bc885bd930bd45d999b9d50004a6ea4ca', 1561748113, 1561747851, '?action=show&kid=%', 2, 64),
(5, 'DZCP 1.5.5', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.5.5.zip', '0f6c9d8d975e89c24910920d6c3ee1805e51711a', 1561748113, 1561747855, '?action=show&kid=%', 2, 64),
(6, 'DZCP 1.5.5.1', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.5.5.1.zip', '6c73137a09cb0d075f4603227c104e87c252f75f', 1561748113, 1561748067, '?action=show&kid=%', 2, 64),
(7, 'DZCP 1.5.5.2', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.5.5.2.zip', 'c6c44831a7d3b9a6e08ee59622e1df2dc80a7b4d', 1561748113, 1561748045, '?action=show&kid=%', 2, 64),
(8, 'DZCP 1.5.5.3', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.5.5.3.zip', 'da56b0ee55ba46d45000c20ef43fab0c6cf49a5f', 1561748113, 1561748050, '?action=show&kid=%', 2, 64),
(9, 'DZCP 1.5.5.4', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.5.5.4.zip', '0bc411991693675fa71f4661545b8006f32335b7', 1561748113, 1561747817, '?action=show&kid=%', 2, 64),
(10, 'DZCP 1.6.0.2', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.6.0.2.zip', '505540000caca8b2f5367714cb7c67dca65ed9e3', 1561748113, 1561748946, '?action=show&kid=%', 3, 64),
(11, 'DZCP 1.6.0.3', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.6.0.3.zip', '98e65155f8663b7239d45f8a25d4cebc5f6c6a84', 1561748113, 1561748947, '?action=show&kid=%', 3, 64),
(12, 'DZCP 1.6.0.4', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.6.0.4.zip', '47a13ac0a92b12297b2ed084f63b1aa71902a551', 1561748113, 1561748947, '?action=show&kid=%', 3, 64),
(13, 'DZCP 1.6.0.4 Update 1', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp1.6.0.4u1.zip', 'bb15f594f6b1c6430149e32ed1f709f806503eff', 1561748113, 1561748945, '?action=show&kid=%', 3, 64),
(14, 'DZCP 1.4.2', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.2.zip', 'ca03087abb6f26bbf465efdc44791bc044e1077f', 1561748113, 1561747968, '', 0, 64),
(15, 'DZCP 1.4.3', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.3.zip', '7f9b10e7ef6660dcb51f7f93a499bd59b74bb800', 1561748113, 1561747973, '', 0, 64),
(16, 'DZCP 1.4.4', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.4.zip', 'befee3f3e8500e5a7ef2bd73d471be3746356cad', 1561748113, 1561747976, '', 0, 64),
(17, 'DZCP 1.4.5', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.5.zip', '1dea2e3e162a3e0132ee1341c5e2fae91e45d208', 1561748113, 1561748430, '', 0, 64),
(18, 'DZCP 1.4.6', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.6.zip', '76a9d85d8f33bd49a45a2e8881169a4b6ead65f3', 1561748113, 1561748427, '', 0, 64),
(19, 'DZCP 1.4.7', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.7.zip', 'ac04e7105d8eee343781574fdbf20ad1b4e040bd', 1561748113, 1561748423, '', 0, 64),
(20, 'DZCP 1.4.8', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.8.zip', '129feca06e4232ef3d2435d10ef63c78ee30a620', 1561748113, 1561748502, '', 0, 64),
(21, 'DZCP 1.4.9', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.9.zip', '64b9d67eb1eaebf544c906f6c2066fa3f4aae2df', 1561748113, 1561748549, '', 0, 64),
(22, 'DZCP 1.4.9.1', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.9.1.zip', '02310a9c9b06f8da183cf3b57c71753b8cca2a27', 1561748113, 1561748656, '', 0, 64),
(23, 'DZCP 1.4.9.2', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.9.2.zip', 'da8b6cabcd012ae230a116f149038162f332b2ec', 1561748113, 1561748660, '', 0, 64),
(24, 'DZCP 1.4.9.3', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.9.3.zip', 'a166054a722caba5ba13fd974c50ed96f30b6863', 1561748113, 1561748664, '', 0, 64),
(25, 'DZCP 1.4.9.4', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.9.4.zip', '8df15a49c3792ce41522607960004e89211b9109', 1561748113, 1561748748, '', 0, 64),
(26, 'DZCP 1.4.9.5', 'Hierbei handelt es sich um eine alte DZCP Version.\r\n*Diese Version wird nicht mehr supported!', 'dzcp_v1.4.9.5.zip', '876c8cdf2333e7f1e422d89b45c977dbca191e21', 1561748113, 1561748744, '', 0, 64);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_downloads_keys`
--

DROP TABLE IF EXISTS `dzcp_server_downloads_keys`;
CREATE TABLE `dzcp_server_downloads_keys` (
                                              `id` int(11) NOT NULL,
                                              `key` varchar(250) NOT NULL DEFAULT '',
                                              `fileID` int(11) NOT NULL DEFAULT '0',
                                              `time` int(11) NOT NULL DEFAULT '0',
                                              `static` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_downloads_paths`
--

DROP TABLE IF EXISTS `dzcp_server_downloads_paths`;
CREATE TABLE `dzcp_server_downloads_paths` (
                                               `id` int(11) NOT NULL,
                                               `path` text NOT NULL,
                                               `name` varchar(250) NOT NULL DEFAULT 'Test'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_downloads_paths`
--

INSERT INTO `dzcp_server_downloads_paths` (`id`, `path`, `name`) VALUES
(1, 'addons/', 'DZCP-Addons'),
(2, 'dzcp/full/', 'DZCP-Fullversions'),
(3, 'dzcp/updates/', 'DZCP-Updates');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_downloads_stats`
--

DROP TABLE IF EXISTS `dzcp_server_downloads_stats`;
CREATE TABLE `dzcp_server_downloads_stats` (
                                               `id` int(11) NOT NULL,
                                               `fileID` int(11) NOT NULL DEFAULT '0',
                                               `downloads` int(15) NOT NULL DEFAULT '0',
                                               `traffic` bigint(90) NOT NULL DEFAULT '0',
                                               `size` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_downloads_stats`
--

INSERT INTO `dzcp_server_downloads_stats` (`id`, `fileID`, `downloads`, `traffic`, `size`) VALUES
(1, 1, 41, 107134189, 2613029),
(2, 2, 1, 2657677, 2657677),
(3, 9, 3, 7975668, 2658556),
(4, 8, 0, 0, 2679211),
(5, 7, 0, 0, 2681546),
(6, 6, 1, 2681962, 2681962),
(7, 5, 0, 0, 2684145),
(8, 4, 0, 0, 2827111),
(9, 3, 0, 0, 2684034),
(10, 10, 0, 0, 4275226),
(11, 11, 1, 4289402, 4289402),
(12, 13, 0, 0, 4616271),
(13, 12, 5, 23742630, 4748526),
(14, 25, 0, 0, 8182358),
(15, 24, 0, 0, 8198841),
(16, 23, 0, 0, 8226613),
(17, 22, 0, 0, 8206222),
(18, 21, 0, 0, 8206049),
(19, 20, 0, 0, 8209939),
(20, 19, 0, 0, 8209941),
(21, 18, 0, 0, 8214830),
(22, 17, 0, 0, 2518018),
(23, 16, 0, 0, 2518496),
(24, 15, 0, 0, 2812066),
(25, 14, 0, 0, 2843686),
(26, 26, 1, 0, 2576577);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_downloads_sub_category`
--

DROP TABLE IF EXISTS `dzcp_server_downloads_sub_category`;
CREATE TABLE `dzcp_server_downloads_sub_category` (
                                                      `id` int(11) NOT NULL,
                                                      `kid` int(11) NOT NULL DEFAULT '1',
                                                      `name` varchar(250) NOT NULL DEFAULT '',
                                                      `addons` int(11) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_downloads_sub_category`
--

INSERT INTO `dzcp_server_downloads_sub_category` (`id`, `kid`, `name`, `addons`) VALUES
(1, 2, 'DZCP-1.4.x', -1),
(2, 2, 'DZCP-1.5.x', -1),
(3, 2, 'DZCP-1.6.x', -1),
(4, 2, 'DZCP-1.8.x', -1),
(5, 4, 'DZCP-1.4.x', -1),
(6, 4, 'DZCP-1.5.x', -1),
(7, 4, 'DZCP-1.6.x', -1),
(8, 4, 'DZCP-1.8.x', -1),
(9, 3, 'DZCP-1.4.x', -1),
(10, 3, 'DZCP-1.5.x', -1),
(11, 3, 'DZCP-1.6.x', -1),
(12, 3, 'DZCP-1.8.x', -1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_geolocation`
--

DROP TABLE IF EXISTS `dzcp_server_geolocation`;
CREATE TABLE `dzcp_server_geolocation` (
                                           `id` int(11) NOT NULL,
                                           `hash` varchar(100) NOT NULL DEFAULT '',
                                           `data` json DEFAULT NULL,
                                           `created` int(11) NOT NULL DEFAULT '0',
                                           `updated` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_gspics`
--

DROP TABLE IF EXISTS `dzcp_server_gspics`;
CREATE TABLE `dzcp_server_gspics` (
                                      `id` int(11) NOT NULL,
                                      `protocol` varchar(200) NOT NULL DEFAULT '',
                                      `mod` varchar(200) NOT NULL DEFAULT '',
                                      `type` varchar(200) NOT NULL DEFAULT '',
                                      `mapname` varchar(200) NOT NULL DEFAULT '',
                                      `pic_hash` varchar(200) NOT NULL DEFAULT '',
                                      `searched` int(11) NOT NULL DEFAULT '0',
                                      `found` int(1) NOT NULL DEFAULT '0',
                                      `enabled` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_gspics`
--

INSERT INTO `dzcp_server_gspics` (`id`, `protocol`, `mod`, `type`, `mapname`, `pic_hash`, `searched`, `found`, `enabled`) VALUES
(1, 'source', 'valve', 'valve', 'crossfire', '', 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_logs`
--

DROP TABLE IF EXISTS `dzcp_server_logs`;
CREATE TABLE `dzcp_server_logs` (
                                    `id` int(11) NOT NULL,
                                    `certID` int(11) NOT NULL,
                                    `time` int(11) NOT NULL,
                                    `log` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_logs`
--

INSERT INTO `dzcp_server_logs` (`id`, `certID`, `time`, `log`) VALUES
(1, 1, 11111, 'ddfgdgdfgdfgd'),
(2, 1, 1593951840, 'HALLOO'),
(3, 1, 1593951865, 'HALLOO'),
(4, 1, 1593951870, 'HALLOO'),
(5, 1, 1593960709, 'HALLOO:array (\n) array (\n)'),
(6, 1, 1593960754, 'HALLOO:[] []');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_news`
--

DROP TABLE IF EXISTS `dzcp_server_news`;
CREATE TABLE `dzcp_server_news` (
                                    `id` int(11) NOT NULL,
                                    `text` text,
                                    `titel` varchar(200) NOT NULL DEFAULT '',
                                    `datum` int(11) NOT NULL DEFAULT '0',
                                    `url` varchar(250) NOT NULL DEFAULT '',
                                    `version` varchar(10) NOT NULL DEFAULT 'all',
                                    `public` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `dzcp_server_news`
--

INSERT INTO `dzcp_server_news` (`id`, `text`, `titel`, `datum`, `url`, `version`, `public`) VALUES
(1, 'DZCP.de ist im Moment im Neuaufbau, wir haben aktuell keine News f&uuml;r euch :)', 'Hard working...', 1561765041, 'https://www.dzcp.de', 'all', 1),
(2, 'Nur ein Test', 'Test', 23445234, 'sfdsfs', 'all', 1),
(3, 'DZCP.de ist im Moment im Neuaufbau, wir haben aktuell keine News f&uuml;r euch :)', 'Hard working...', 1561765041, 'https://www.dzcp.de', 'all', 1),
(4, 'DZCP.de ist im Moment im Neuaufbau, wir haben aktuell keine News f&uuml;r euch :)', 'Hard working...', 1561765041, 'https://www.dzcp.de', 'all', 1),
(5, 'DZCP.de ist im Moment im Neuaufbau, wir haben aktuell keine News f&uuml;r euch :)', 'Hard working...', 1561765041, 'https://www.dzcp.de', 'all', 1),
(6, 'DZCP.de ist im Moment im Neuaufbau, wir haben aktuell keine News f&uuml;r euch :)', 'Hard working...', 1561765041, 'https://www.dzcp.de', 'all', 1),
(7, 'DZCP.de ist im Moment im Neuaufbau, wir haben aktuell keine News f&uuml;r euch :)', 'Hard working...', 1561765041, 'https://www.dzcp.de', 'all', 1),
(8, 'DZCP.de ist im Moment im Neuaufbau, wir haben aktuell keine News f&uuml;r euch :)', 'Hard working...', 1561765041, 'https://www.dzcp.de', 'all', 1),
(9, 'DZCP.de ist im Moment im Neuaufbau, wir haben aktuell keine News f&uuml;r euch :)', 'Hard working...', 1561765041, 'https://www.dzcp.de', 'all', 1),
(10, 'DZCP.de ist im Moment im Neuaufbau, wir haben aktuell keine News f&uuml;r euch :)', 'Hard working...', 1561765041, 'https://www.dzcp.de', 'all', 1),
(11, 'DZCP.de ist im Moment im Neuaufbau, wir haben aktuell keine News f&uuml;r euch :)', 'Hard working...', 1561765041, 'https://www.dzcp.de', 'all', 1),
(12, 'Nur ein Test', 'Test', 23445234, 'sfdsfs', 'all', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_session`
--

DROP TABLE IF EXISTS `dzcp_server_session`;
CREATE TABLE `dzcp_server_session` (
                                       `id` int(11) NOT NULL,
                                       `ssid` varchar(100) NOT NULL DEFAULT '',
                                       `data` json NOT NULL,
                                       `update` int(10) NOT NULL DEFAULT '0',
                                       `created` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_version`
--

DROP TABLE IF EXISTS `dzcp_server_version`;
CREATE TABLE `dzcp_server_version` (
                                       `id` int(11) NOT NULL,
                                       `version` varchar(100) NOT NULL DEFAULT '',
                                       `release` varchar(100) NOT NULL DEFAULT '',
                                       `build` varchar(100) NOT NULL DEFAULT '',
                                       `static_version` varchar(100) NOT NULL DEFAULT '',
                                       `edition` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_version`
--

INSERT INTO `dzcp_server_version` (`id`, `version`, `release`, `build`, `static_version`, `edition`) VALUES
(1, '1.6.1.2', '21.02.2021', '1612.21.02', '1.6', 'dev'),
(2, '1.6.0.4', '30.09.2017', '1604.01.20', '1.6', 'final'),
(3, '1.8.0.0', '00.00.2019', '1800.00.00', '1.8', 'dev'),
(4, '1.8.0.0', '00.00.2019', '1800.00.00', '1.8', 'final'),
(5, '1.7.0.0', '15.01.2016', '1700.10.00', '1.7', 'dev'),
(6, '1.7.0.0', '15.01.2016', '1700.10.00', '1.7', 'final'),
(7, '1.5.5.4', '06.05.2012', '0000.00.00', '1.5', 'dev'),
(8, '1.5.5.4', '06.05.2012', '0000.00.00', '1.5', 'final'),
(9, '2.0.0.0', '00.00.2019', '1000.00.00', '2.0', 'dev'),
(10, '1.6.1.0', '10.12.2018', '1610.10.12', '1.6', 'cb'),
(11, '0.0.0.1', '01.01.2020', '0000.00.01', '0.0', 'society');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_version_lock`
--

DROP TABLE IF EXISTS `dzcp_server_version_lock`;
CREATE TABLE `dzcp_server_version_lock` (
                                            `id` int(11) NOT NULL,
                                            `vid` int(11) NOT NULL DEFAULT '0',
                                            `indent` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_version_stats`
--

DROP TABLE IF EXISTS `dzcp_server_version_stats`;
CREATE TABLE `dzcp_server_version_stats` (
                                             `id` int(11) NOT NULL,
                                             `version` varchar(100) NOT NULL DEFAULT '',
                                             `edition` varchar(100) NOT NULL DEFAULT '',
                                             `release` varchar(100) NOT NULL DEFAULT '',
                                             `build` varchar(100) NOT NULL DEFAULT '',
                                             `count` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dzcp_server_version_stats`
--

INSERT INTO `dzcp_server_version_stats` (`id`, `version`, `edition`, `release`, `build`, `count`) VALUES
(1, '0.0.0.1', 'society', '01.01.2020', '0000.00.01', 2),
(2, '1.6.1.0', 'dev', '10.12.2018', '1610.10.12', 10),
(3, '1.6.0.4', 'final', '30.09.2017', '1604.01.20', 1),
(4, '1.6', 'dev', '15.01.2016', '1700.10.00', 2),
(5, '1.6.1.0', 'dev', '10.12.2018', '1610.dev.', 1),
(6, '1.6.0.0', 'dev', '10.12.2018', '1600.dev.', 1),
(7, '1.6.0.0', 'dev', '11.12.2018', '1600.dev.', 1),
(8, '1.6.0.0', 'dev', '09.12.2018', '1600.dev.', 1),
(9, '1.6.1.0', 'dev', '09.12.2018', '1610.dev.', 1),
(10, '1.6.0.4', 'dev', '23.02.2018', '1604.23.02', 1),
(11, '1.6.0.4', 'final', '23.02.2018', '1604.23.02', 1),
(12, '1.6.1.0', 'cb', '10.12.2018', '1610.10.12', 1),
(13, '0.0.0.1', 'society', '01.01.2020', '0001.01.01', 1),
(14, '2.0.0.0', 'dev', '00.00.2019', '1000.00.00', 1),
(15, '1.6.1.0', 'dev', '01.05.2018', '1610.00.00', 1),
(16, '1.6.0.4', 'dev', '23.02.2018', '1604.03.00', 4),
(17, '1.6.1.1', 'dev', '25.06.2020', '1611.25.06', 16),
(18, '1.6.1.2', 'dev', '21.02.2021', '1612.21.02', 1);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `dzcp_server_account`
--
ALTER TABLE `dzcp_server_account`
    ADD UNIQUE KEY `id` (`id`);

--
-- Indizes für die Tabelle `dzcp_server_addons`
--
ALTER TABLE `dzcp_server_addons`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `aid` (`aid`),
    ADD KEY `aid_2` (`aid`);

--
-- Indizes für die Tabelle `dzcp_server_certs`
--
ALTER TABLE `dzcp_server_certs`
    ADD PRIMARY KEY (`id`),
    ADD KEY `indent` (`indent`);

--
-- Indizes für die Tabelle `dzcp_server_clients`
--
ALTER TABLE `dzcp_server_clients`
    ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `dzcp_server_cronjob`
--
ALTER TABLE `dzcp_server_cronjob`
    ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `dzcp_server_downloads`
--
ALTER TABLE `dzcp_server_downloads`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fileID` (`fileID`),
    ADD KEY `pathID` (`pathID`);

--
-- Indizes für die Tabelle `dzcp_server_downloads_category`
--
ALTER TABLE `dzcp_server_downloads_category`
    ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `dzcp_server_downloads_files`
--
ALTER TABLE `dzcp_server_downloads_files`
    ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `dzcp_server_downloads_keys`
--
ALTER TABLE `dzcp_server_downloads_keys`
    ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `dzcp_server_downloads_paths`
--
ALTER TABLE `dzcp_server_downloads_paths`
    ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `dzcp_server_downloads_stats`
--
ALTER TABLE `dzcp_server_downloads_stats`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fileID` (`fileID`);

--
-- Indizes für die Tabelle `dzcp_server_downloads_sub_category`
--
ALTER TABLE `dzcp_server_downloads_sub_category`
    ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `dzcp_server_geolocation`
--
ALTER TABLE `dzcp_server_geolocation`
    ADD PRIMARY KEY (`id`),
    ADD KEY `hash` (`hash`);

--
-- Indizes für die Tabelle `dzcp_server_gspics`
--
ALTER TABLE `dzcp_server_gspics`
    ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `dzcp_server_logs`
--
ALTER TABLE `dzcp_server_logs`
    ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `dzcp_server_news`
--
ALTER TABLE `dzcp_server_news`
    ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `dzcp_server_session`
--
ALTER TABLE `dzcp_server_session`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `ssid` (`ssid`);

--
-- Indizes für die Tabelle `dzcp_server_version`
--
ALTER TABLE `dzcp_server_version`
    ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `dzcp_server_version_lock`
--
ALTER TABLE `dzcp_server_version_lock`
    ADD PRIMARY KEY (`id`),
    ADD KEY `vid` (`vid`);

--
-- Indizes für die Tabelle `dzcp_server_version_stats`
--
ALTER TABLE `dzcp_server_version_stats`
    ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_account`
--
ALTER TABLE `dzcp_server_account`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_addons`
--
ALTER TABLE `dzcp_server_addons`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_certs`
--
ALTER TABLE `dzcp_server_certs`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_clients`
--
ALTER TABLE `dzcp_server_clients`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_cronjob`
--
ALTER TABLE `dzcp_server_cronjob`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_downloads`
--
ALTER TABLE `dzcp_server_downloads`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_downloads_category`
--
ALTER TABLE `dzcp_server_downloads_category`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_downloads_files`
--
ALTER TABLE `dzcp_server_downloads_files`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_downloads_keys`
--
ALTER TABLE `dzcp_server_downloads_keys`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_downloads_paths`
--
ALTER TABLE `dzcp_server_downloads_paths`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_downloads_stats`
--
ALTER TABLE `dzcp_server_downloads_stats`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_downloads_sub_category`
--
ALTER TABLE `dzcp_server_downloads_sub_category`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_gspics`
--
ALTER TABLE `dzcp_server_gspics`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_logs`
--
ALTER TABLE `dzcp_server_logs`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_news`
--
ALTER TABLE `dzcp_server_news`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_session`
--
ALTER TABLE `dzcp_server_session`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24672;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_version`
--
ALTER TABLE `dzcp_server_version`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT für Tabelle `dzcp_server_version_lock`
--
ALTER TABLE `dzcp_server_version_lock`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `dzcp_server_version_lock`
--
ALTER TABLE `dzcp_server_version_lock`
    ADD CONSTRAINT `dzcp_server_version_lock_ibfk_1` FOREIGN KEY (`vid`) REFERENCES `dzcp_server_version_stats` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
