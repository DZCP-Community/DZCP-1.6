<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

$applicationConfig->setConnectionOptions(['driver' => 'pdo_sqlite', 'path' => _RootPath_ . '/dev.sqlite']);
$applicationConfig->setDebugMode(false);
$applicationConfig->setEntityDir(_RootPath_ . '/Application/Entities');