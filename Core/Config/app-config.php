<?php
$applicationConfig->setDebugMode(true); //Debug Mode for Doctrine
$applicationConfig->getCustomConfig()->set('logger_level',Monolog\Logger::DEBUG); //Debug Mode for Logger

//Smarty Config
$applicationConfig->getCustomConfig()->set('smarty_caching', false);
$applicationConfig->getCustomConfig()->set('smarty_debug', true);
$applicationConfig->getCustomConfig()->set('smarty_compile_check', false);
$applicationConfig->getCustomConfig()->set('smarty_force_compile', true);
#$applicationConfig->getCustomConfig()->set('smarty_cache_lifetime', 3600);