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
        echo 'test';
    }

    public function render404(): void
    {
        echo '404';
    }
}