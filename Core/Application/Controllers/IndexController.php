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

use Entities\User;
use Webmasters\Doctrine\Bootstrap;
use Application\Logger\Logger;

/**
 * Class IndexController
 * @package Application\Controllers
 */
class IndexController extends AbstractBase
{
    public function __construct(Bootstrap $bootstrap, Logger $logger)
    {
        parent::__construct($bootstrap,$logger);

    }

    public function run(String $action): void
    {
        $test = new User();
        $test->setUsername('masterbee');
        $test->setPassword('sdfssdfsdfsdfsdf');

        $this->getBootstrap()->getEntityManager()->persist($test);
        $this->getBootstrap()->getEntityManager()->flush();

        echo "Created User with ID " . $test->getId() . PHP_EOL;

        echo 'test';
    }
}