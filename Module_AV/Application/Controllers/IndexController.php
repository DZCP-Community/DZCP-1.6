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

/**
 * Class IndexController
 * @package Application\Controllers
 */
class IndexController extends AbstractBase
{
    public function __construct(Bootstrap $bootstrap)
    {
        parent::__construct($bootstrap);

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