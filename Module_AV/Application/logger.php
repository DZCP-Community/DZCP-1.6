<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

namespace Application\Logger;

use Monolog as MLogger;
use Monolog\Handler\StreamHandler;

class Logger {
    /**
     * @var array
     */
    private $log = [];

    /**
     * Logger constructor.
     * @param int $level
     * @throws \Exception
     */
    public function __construct($level = MLogger\Logger::WARNING) {
        $logger = ['Debug','System','Modules','Database'];
        foreach ($logger as $data) {
            $this->log[$data] = new MLogger\Logger($data);
            $this->log[$data]->pushHandler(new StreamHandler(_RootPath_.'Logs/'.strtolower($data).'.log', $level));
        }

        $this->test();
    }

    public function test(): void {
        foreach ($this->log as $key => $item) {
            $item->debug('test');
        }
    }

    public function getDebugLogger(): MLogger\Logger {
        return $this->log['Debug'];
    }

    public function getSystemLogger(): MLogger\Logger {
        return $this->log['System'];
    }

    public function getModulesLogger(): MLogger\Logger {
        return $this->log['Modules'];
    }

    public function getDatabaseLogger(): MLogger\Logger {
        return $this->log['Database'];
    }
}