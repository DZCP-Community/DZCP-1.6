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

$applicationConfig = new Configuration();
$applicationConfig->setConnectionOptions(['driver' => 'pdo_sqlite', 'path' => _RootPath_ . '/dev.sqlite']);
$applicationConfig->setDebugMode(false);
$applicationConfig->setEntityDir(_RootPath_ . '/Application/Entities');
$applicationConfig->getCustomConfig()->set('installed',false);
$applicationConfig->getCustomConfig()->set('logger_level',Monolog\Logger::ALERT);

//Smarty Config
$applicationConfig->getCustomConfig()->set('smarty_caching', Smarty::CACHING_LIFETIME_CURRENT);
$applicationConfig->getCustomConfig()->set('smarty_debug', Smarty::DEBUG_OFF);
$applicationConfig->getCustomConfig()->set('smarty_compile_check', Smarty::COMPILECHECK_ON);
$applicationConfig->getCustomConfig()->set('smarty_force_compile', false);
$applicationConfig->getCustomConfig()->set('smarty_cache_lifetime', 3600);

unset($applicationCustomConfig);