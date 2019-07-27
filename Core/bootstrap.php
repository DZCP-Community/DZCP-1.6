<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

ob_start();
define('_RootPath_', __DIR__ . "/");

require_once _RootPath_."vendor/autoload.php";

/**
 * Namespace Imports
 */
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\{ChainCache, ApcuCache, ArrayCache};

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration([_RootPath_."/config"], $isDevMode);

//######### Proxy #########
$config->setProxyDir(_RootPath_."/proxy");
if (!is_dir(_RootPath_."/proxy")) {
    mkdir(_RootPath_."/proxy", 0755, true);
}

//######### Cache #########
$chainCache = new ChainCache([
    new ApcuCache(), //-> 1
    new ArrayCache() //-> 2
]);

$config->setQueryCacheImpl($chainCache);
$config->setResultCacheImpl($chainCache);

//######### Database #########
$databaseOptions = [
    'driver' => 'pdo_sqlite',
    'path' => _RootPath_ . '/config/db.sqlite',
];

// ######### obtaining the entity manager #########
$entityManager = EntityManager::create($databaseOptions, $config);

// ######### start the cms #########
$gump = new GUMP();
$input = $gump->sanitize($_GET);
$gump->validation_rules([
    'controller' => 'alpha|max_len,50|min_len,2',
    'action' => 'alpha|max_len,50|min_len,2'
]);

$gump->filter_rules([
    'controller' => 'trim|sanitize_string',
    'action' => 'trim|sanitize_string'
]);

$controllerData = $gump->run($input);

if($controllerData !== false) {
    $controllerData += ['controller'=>'index'];
    $controllerData += ['action'=>'index'];

    $controllerName = 'DZCP\\Controllers\\' . ucfirst($controllerData['controller']) . 'Controller';
    if (class_exists($controllerName)) {
        $_GET = $controllerData; // Add sanitize inputs
        $requestController = new $controllerName($entityManager);
        if(method_exists($requestController,'run')) {
            $requestController->run($controllerData['action']);
            $entityManager->close();
            exit();
        }
        $requestController->render404();
    } else {
        $requestController = new DZCP\Controllers\IndexController($entityManager);
        $requestController->render404();
    }

    $entityManager->close();
    exit();
}

ob_end_flush();