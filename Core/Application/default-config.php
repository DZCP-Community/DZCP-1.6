<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

use Webmasters\Doctrine\Configuration;
use Webmasters\Doctrine\ORM\Util\OptionsCollection;

$applicationConfig = new Configuration();
$applicationConfig->setConnectionOptions(['driver' => 'pdo_sqlite', 'path' => _RootPath_ . 'dev.sqlite']);
$applicationConfig->setDebugMode(false);
$applicationConfig->setEntityDir(_RootPath_ . 'Application/Entities');
$applicationConfig->setEntityNamespace('Entities');
$applicationConfig->getCustomConfig()->set('installed',false);
$applicationConfig->getCustomConfig()->set('logger_level',Monolog\Logger::ALERT);

//Smarty Config
$applicationConfig->getCustomConfig()->set('smarty_caching', Smarty::CACHING_LIFETIME_CURRENT);
$applicationConfig->getCustomConfig()->set('smarty_debug', Smarty::DEBUG_OFF);
$applicationConfig->getCustomConfig()->set('smarty_compile_check', Smarty::COMPILECHECK_ON);
$applicationConfig->getCustomConfig()->set('smarty_force_compile', false);
$applicationConfig->getCustomConfig()->set('smarty_cache_lifetime', 3600);

//Sessions
$applicationConfig->getCustomConfig()->set('sessions_use_idle_time', false);
$applicationConfig->getCustomConfig()->set('sessions_max_idle_time', 3600);
$applicationConfig->getCustomConfig()->set('sessions_handler', 'php'); //php,mongodb,sql,couchbase
$applicationConfig->getCustomConfig()->set('sessions_mongodb', ['host'=>'','username'=>'','password'=>'','database'=>'']);
$applicationConfig->getCustomConfig()->set('sessions_couchbase', ['host'=>'','username'=>'','password'=>'','bucket'=>'']);

//Vserion
$applicationVersion = new OptionsCollection([]);
$applicationVersion->set('core','2.0.0');
$applicationVersion->set('edition','stable');
$applicationVersion->set('release','11.08.2019');

unset($applicationCustomConfig);