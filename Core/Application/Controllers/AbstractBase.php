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
use Webmasters\Doctrine\Bootstrap;
use Application\Interfaces\IAbstractBase;
use Application\Traits\TAbstractBase;
use Application\Logger\Logger;

/**
 * Class AbstractBase
 * @package Application\Controllers
 */
abstract class AbstractBase implements IAbstractBase
{
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

    public function __construct(Bootstrap $bootstrap, Logger $logger)
    {
        $this->bootstrap = $bootstrap;

        $this->logger = $logger;

        $this->smarty = $this->initSmarty();

        $this->smarty->debugging = true;

        echo '<pre>';
        var_dump($this->scandir('./'));

        $this->smarty->display('index.tpl');
    }

    /**
     * @return \Smarty
     */
    public function initSmarty(): \Smarty {
        $smarty = new \Smarty();
        $smarty->setPluginsDir(_RootPath_.'Application/Smarty/');
        $smarty->setTemplateDir(_RootPath_."Public/tpl/");
        $smarty->setCompileDir(_RootPath_."Compile/");
        $smarty->setConfigDir(_RootPath_."Config/");
        $smarty->setCacheDir(_RootPath_."Cache/");
        return $smarty;
    }

    /**
     * @param String $action
     */
    abstract public function run(String $action): void;
}