<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

namespace Application\Interfaces;

/**
 * Interface IAbstractBase
 * @package Application\Interfaces
 */
interface IAbstractBase
{
    public function run(String $action): void;
}