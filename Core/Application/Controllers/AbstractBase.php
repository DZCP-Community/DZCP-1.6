<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

namespace Application\Controllers;

/**
 * Namespace Imports
 */

use Symfony\Component\Filesystem\Filesystem;
use Webmasters\Doctrine\Bootstrap;
use Application\Interfaces\IAbstractBase;
use Application\Traits\TAbstractBase;
use Application\Logger\Logger;
use Application\Helper\{Smarty_CacheResource_Doctrine};

/**
 * Class AbstractBase
 * @package Application\Controllers
 */
abstract class AbstractBase implements IAbstractBase {
    use TAbstractBase; //Use Trait

    /**
     * @var Bootstrap|null
     */
    private $bootstrap = null;

    /**
     * @var Logger|null
     */
    private $logger = null;

    /**
     * @var \Smarty|null
     */
    public $smarty = null;

    /**
     * @var Filesystem|null
     */
    public $filesystem = null;

    /**
     * AbstractBase constructor.
     * @param Bootstrap $bootstrap
     * @param Logger $logger
     * @throws \SmartyException
     */
    public function __construct(Bootstrap $bootstrap, Logger $logger) {
        global $filesystem;
        if(!$filesystem instanceof Filesystem) {
            $logger->getSystemLogger()->critical('$filesystem is not instanceof Filesystem!');
            $this->showCriticalError(); //Show CriticalError page and exit()
        }

        //Link basic classes
        $this->filesystem = $filesystem;
        $this->bootstrap = $bootstrap;
        $this->logger = $logger;

        //Register Smarty
        try {
            $this->smarty = $this->initSmarty();
        } catch (\Exception $exception) {
            $this->logger->getSystemLogger()->critical('Can not create a Smarty instance!',$exception->getTrace());
            $this->showCriticalError(); //Show CriticalError page and exit()
        }

        $this->smarty->display('index.tpl');
    }

    /**
     * @return \Smarty
     * @throws \Exception
     * Create a smarty instance.
     */
    public function initSmarty(): \Smarty {
        $smarty = new \Smarty();

        //Smarty system dirs
        $smarty->setPluginsDir(_RootPath_.'Application/Smarty/');
        $smarty->setTemplateDir(_RootPath_."Public/tpl/");
        $smarty->setCompileDir(_RootPath_."Compile/");
        $smarty->setConfigDir(_RootPath_."Config/");
        $smarty->setCacheDir(_RootPath_."Cache/");

        //Smarty options
        $options = $this->bootstrap->getConfiguration()->getCustomConfig();
        $smarty->setCaching((int)$options->get('smarty_caching'));
        $smarty->setDebugging((int)$options->get('smarty_debug'));
        $smarty->setCompileCheck((int)$options->get('smarty_compile_check'));
        $smarty->setForceCompile((int)$options->get('smarty_force_compile'));
        $smarty->setCacheLifetime((int)$options->get('smarty_cache_lifetime'));
        unset($options);

        //Register Cache
        $smarty->registerCacheResource('doctrine', new Smarty_CacheResource_Doctrine($this->bootstrap));
        return $smarty;
    }

    /**
     * Exit the script on critical error.
     */
    public function showCriticalError(): void {
        echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />".
            "<title>Critical error!</title></head><body><h3>This page has a critical error!  <br />".
            "Please check the logs.</h3></body></html>";
        exit();
    }

    /**
     * @param String $action
     */
    abstract public function run(String $action): void;
}