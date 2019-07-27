<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START #
define('basePath', dirname(dirname(__FILE__) . '../'));
ob_start();
ob_implicit_flush(false);
if (version_compare(phpversion(), '7.0', '<')) {
    die('Bitte verwende PHP-Version 7.0 oder h&ouml;her.<p>Please use PHP-Version 7.0 or higher.');
}

$ajaxJob = true;

## INCLUDES ##
include(basePath . '/vendor/autoload.php');

use GUMP\GUMP;
$gump = GUMP::get_instance();

include(basePath . "/inc/debugger.php");
include(basePath . "/inc/config.php");
include(basePath . "/inc/bbcode.php");

use BrightNucleus\CountryCodes\Country;

if((settings('last_conjob',false)+90) <= time()) {
    @ignore_user_abort(true);
    @set_time_limit(90);
    db("UPDATE `" . $db['settings'] . "` SET `last_conjob` = ".time()." WHERE `id` = 1;"); //Update

    //Update longitudes & latitude for membermap for PHP
    if(api_enabled) {
        $qry = db("SELECT `id`,`city`,`country` FROM `" . $db['users'] . "` WHERE `city` != '' AND (`geolocation` IS NULL OR `geolocation` = '');");
        while ($get = _fetch($qry)) {
            $adress = re($get['city']);
            if (!empty($get['country'])) {
                $name = Country::getNameFromCode(strtoupper(re($get['country'])));
                $adress = (str_replace(' ', '+', $name) . '+' . $adress);
            }

            $geolocation = $api->get_geolocation($adress);
            if ($geolocation && !$geolocation['error'] && $geolocation['status'] == 'OK') {
                db("UPDATE `" . $db['users'] . "` SET `geolocation` = '" .
                    json_encode($geolocation['results'][0]['geometry']['location']) . "', `city` = '" .
                    up($geolocation['results'][0]['address_components'][0]['long_name']) . "' WHERE `id` = " . $get['id'] . ";");
            }
        }
    }

    //-> Automatische Datenbank Optimierung
    if (auto_db_optimize && settings('db_optimize', false) <= time()) {
        db("UPDATE `" . $db['settings'] . "` SET `db_optimize` = '" . (time() + auto_db_optimize_interval) . "' WHERE `id` = 1;");
        db_optimize();
    }

    set_time_limit(30);
    @ignore_user_abort(false);
}

if (!mysqli_persistconns)
    $mysql->close(); //MySQL

ob_end_flush();