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

use SessionHandlerInterface;
use Application\Logger\Logger;
use Application\Libraries\Sessions;

/**
 * Class FileSessionHandler
 * @package Application\Libraries\Sessions
 */
class FileSessionHandler implements SessionHandlerInterface
{
    /**
     * @var string
     */
    private $savePath = '';

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var Logger
     */
    private $logger;

    /**
     * FileSessionHandler constructor.
     * @param array $options
     * @param Logger $logger
     */
    public function __construct(array $options, Logger $logger) {
        $this->options = $options;
        $this->logger = $logger;
    }

    /**
     * @param string $savePath
     * @param string $sessionName
     * @return bool
     */
    public function open($savePath, $sessionName): bool {
        global $filesystem;
        $this->savePath = _RootPath_.'Data/Session';
        $this->logger->getSystemLogger()->debug(Sessions::TAG.'savePath is "'.$this->savePath.'"');
        if (!is_dir($this->savePath)) {
            $filesystem->mkdir($this->savePath);
        }

        return true;
    }

    /**
     * @return bool
     */
    public function close(): bool {
        return true;
    }

    public function read($id) {
        if(!file_exists($this->savePath."/sess_".$id)) {
            return '';
        }

        $data = file_get_contents($this->savePath."/sess_".$id);
        return (string)$data;
    }

    function write($id, $data)
    {
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }

    function destroy($id): bool
    {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    function gc($maxlifetime): bool
    {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}