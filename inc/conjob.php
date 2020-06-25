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
        //Update
        $mme_qry = db('SELECT `id`, `city`, `country` FROM `'.$db['users'].'` WHERE `gmaps_koord` IS NULL OR `gmaps_koord` = "" ORDER BY id;');
        while($mme_get = _fetch($mme_qry)) {
            $geo = null;
            if(!empty($mme_get['city']) && !empty($mme_get['country'])) {
                $geo = $api->getGeoLocation(strtolower(re($mme_get['city'])).','.strtolower(getCountryName($mme_get['country'])));
            }
            else if(!empty($mme_get['city'])) {
                $geo = $api->getGeoLocation(strtolower(re($mme_get['city'])));
            }
            else if(!empty($mme_get['country'])) {
                $geo = $api->getGeoLocation(strtolower(getCountryName($mme_get['country'])));
            }

            if(!is_null($geo) && !$geo['error'] && array_key_exists('lat',$geo['results']) && array_key_exists('lng',$geo['results']) &&
                !empty($geo['results']['lat']) && $geo['results']['lat'] != 0 && !empty($geo['results']['lng']) && $geo['results']['lng'] != 0) {
                db("UPDATE `" . $db['users'] . "` SET `gmaps_koord` = '".$geo['results']['lat'].",".$geo['results']['lng']."' WHERE `id` = " . $mme_get['id'] . ";");
            }
        } unset($mme_qry,$mme_get,$geo);
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