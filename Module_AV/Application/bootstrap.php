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
use Webmasters\Doctrine\{Bootstrap,Configuration};
use Symfony\Component\Filesystem\Filesystem;
use Application\Controllers\IndexController;
use Wixel\GUMP\GUMP;
use Application\Logger\Logger;
use Monolog as MLogger;

/**
 * Include Config
 */
$applicationConfig = new Configuration();
$applicationConfig->getCustomConfig()->set('installed',false);
require_once(_RootPath_."Config/default-config.php");
if(file_exists(_RootPath_."Config/app-config.php") &&
    !empty(filesize(_RootPath_."Config/app-config.php"))) {
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
        $filesystem->mkdir(_RootPath_.$dir);
    }
} unset($dirs,$dir);
$applicationConfig->getCustomConfig()->set('dir',$dir_index);
$applicationConfig->setProxyDir(_RootPath_."Proxy");
unset($dir_index);

/**
 *  ######### Logger Instance #########
 */
$logger = new Logger(MLogger\Logger::DEBUG);

/**
 *  ######### Bootstrap Instance #########
 */
$bootstrap = Bootstrap::getInstance($applicationConfig);

/**
 * ######### GUMP Instance #########
 */
$gump = GUMP::getInstance();
$input = $gump->sanitize($_GET);

$gump->validation_rules(['controller' => 'alpha|max_len,50|min_len,2', 'action' => 'alpha|max_len,50|min_len,2']);
$gump->filter_rules(['controller' => 'trim|sanitize_string|ucfirst', 'action' => 'trim|sanitize_string']);

$controllerData = $gump->run($input); //Check inputs
$controllerData += ['controller'=>'Index'];
$controllerData += ['action'=>'index'];

$controllerName = 'Application\\Controllers\\'.$controllerData['controller'].'Controller';
if (class_exists($controllerName)) {
    $_GET = $controllerData; // Add sanitize inputs
    $requestController = new $controllerName($bootstrap);
    if(method_exists($requestController,'run')) {
        $requestController->run($controllerData['action']);
        $bootstrap->getEntityManager()->close();
    } else {
        $requestController->render404();
    }
} else {
    $requestController = new IndexController($bootstrap);
    $requestController->render404();
}

$bootstrap->getEntityManager()->close();