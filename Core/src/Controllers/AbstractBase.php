<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

namespace DZCP\Controllers;

/**
 * Namespace Imports
 */
use Doctrine\ORM\EntityManager;
use DZCP\Interfaces\IAbstractBase;
use DZCP\Traits\TAbstractBase;

/**
 * Class AbstractBase
 * @package DZCP\Controllers
 */
abstract class AbstractBase implements IAbstractBase {
    use TAbstractBase; //Use Trait
    public function __construct(EntityManager $entityManager)
    {

    }

    /**
     * @param String $action
     */
    abstract public function run(String $action): void;
}