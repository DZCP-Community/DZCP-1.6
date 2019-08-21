<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

/**
 * Set RootPath to GLOBAL
 */
chdir(dirname(__DIR__));
define('_RootPath_', getcwd() . "/"); //Global

/**
 * Include Autoloader & Logger
 */
require_once(_RootPath_."vendor/autoload.php");
require_once(_RootPath_."Application/logger.php");

/**
 * Namespace Imports
 */
use Webmasters\Doctrine\{Bootstrap};
use Symfony\Component\Filesystem\Filesystem;
use Application\Controllers\IndexController;
use Wixel\GUMP\GUMP;
use Application\Logger\Logger;

/**
 * Include Config
 */
require_once(_RootPath_."Application/default-config.php");

/**
 *  ######### Logger Instance #########
 */
$logger = new Logger($applicationConfig->getCustomConfig()->get('logger_level'));

/**
 * Load user application config
 */
if(file_exists(_RootPath_."Config/app-config.php") &&
    !empty(filesize(_RootPath_."Config/app-config.php"))) {
    $logger->getDebugLogger()->debug('Load application config',[_RootPath_."Config/app-config.php"]);
    require_once(_RootPath_ . "Config/app-config.php");
    $applicationConfig->getCustomConfig()->set('installed',true);
}

/**
 * Init Symfony Filesystem
 */
$filesystem = new Filesystem();

/**
 * Check/Make System folders
 */
$dirs = ['Proxy','Cache','Modules','Logs'];
$dir_index = [];
foreach ($dirs as $dir) {
    $dir_index[$dir] = _RootPath_."/".$dir;
    if (!$filesystem->exists(_RootPath_.$dir)) {
        $logger->getDebugLogger()->debug('Create Dir: '._RootPath_.$dir);
        $filesystem->mkdir(_RootPath_.$dir);
    }
} unset($dirs,$dir);
$applicationConfig->getCustomConfig()->set('dir',$dir_index);
$applicationConfig->setProxyDir(_RootPath_."Proxy");
$applicationConfig->setBaseDir(_RootPath_);
unset($dir_index);

/**
 *  ######### Bootstrap Instance #########
 */
$bootstrap = Bootstrap::getInstance($applicationConfig);

/**
 * ######### GUMP Instance #########
 */
$gump = GUMP::getInstance();
$logger->getDebugLogger()->debug('GET-Input',$_GET);
$input = $gump->sanitize($_GET);
$logger->getDebugLogger()->debug('GET-Sanitize-Input',$input);

$gump->validation_rules(['controller' => 'alpha|max_len,50|min_len,2', 'action' => 'alpha|max_len,50|min_len,2']);
$gump->filter_rules(['controller' => 'trim|sanitize_string|ucfirst', 'action' => 'trim|sanitize_string']);

$controllerData = $gump->run($input); //Check inputs
$controllerData += ['controller'=>'Index'];
$controllerData += ['action'=>'index'];
$logger->getDebugLogger()->debug('Controller-Data',$controllerData);

$controllerName = 'Application\\Controllers\\'.$controllerData['controller'].'Controller';
if (class_exists($controllerName)) {
    $_GET = $controllerData; // Add sanitize inputs
    $requestController = new $controllerName($bootstrap,$logger);
    $logger->getDebugLogger()->debug('Request-Controller',[$controllerData['controller'].'Controller']);
    if(method_exists($requestController,'run')) {
        $logger->getDebugLogger()->debug('Call '.$controllerName.':'.$controllerData['action'],$_POST);
        $requestController->run($controllerData['action']);
        $bootstrap->getEntityManager()->close();
    } else {
        $logger->getDebugLogger()->debug('Render404');
        $requestController->render404();
    }
} else {
    $logger->getDebugLogger()->debug('Render404');
    $requestController = new IndexController($bootstrap);
    $requestController->render404();
}

$bootstrap->getEntityManager()->close();