<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

namespace Application\Libraries\Sessions;

use Webmasters\Doctrine\Bootstrap;
use Application\Logger\Logger;
use SessionHandlerInterface;

class MongoDBSessionHandler implements SessionHandlerInterface
{
    private $bootstrap;
    private $logger;

    public function __construct(Bootstrap $bootstrap, Logger $logger)
    {
        $this->bootstrap = $bootstrap;
        $this->logger = $logger;
    }

    public function open($savePath, $sessionName): bool
    {
        // TODO: Implement open() method.
    }

    public function close(): bool
    {
        // TODO: Implement close() method.
    }

    public function read($id)
    {
        // TODO: Implement read() method.
    }

    public function write($id, $data)
    {
        // TODO: Implement write() method.
    }

    public function destroy($id): bool
    {
        // TODO: Implement destroy() method.
    }

    public function gc($maxlifetime): bool
    {
        // TODO: Implement gc() method.
    }
}