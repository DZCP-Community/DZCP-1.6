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

/**
 * Class AbstractBase
 * @package Application\Controllers
 */
abstract class AbstractBase implements IAbstractBase
{
    use TAbstractBase; //Use Trait

    public function __construct(Bootstrap $bootstrap)
    {

    }

    /**
     * @param String $action
     */
    abstract public function run(String $action): void;
}