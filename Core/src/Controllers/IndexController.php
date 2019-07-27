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

class IndexController extends AbstractBase {
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);

    }

    public function run(String $action): void
    {

    }

    public function render404(): void
    {

    }
}