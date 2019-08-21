<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package Application\Libraries
 * @version 1.0
 */

namespace Application\Libraries;

use Application\Interfaces\ISessions;
use Webmasters\Doctrine\Bootstrap;
use Application\Logger\Logger;

use Application\Libraries\Sessions\FileSessionHandler;
use Application\Libraries\Sessions\MongoDBSessionHandler;
use Application\Libraries\Sessions\SQLSessionHandler;

/**
 * Class Sessions
 * @package Application\Libraries
 */
class Sessions implements ISessions {
    private $bootstrap = null;
    private $logger = null;
    const TAG = '[Sessions] ';

    /**
     * Sessions constructor.
     * @param Bootstrap $bootstrap
     * @param Logger $logger
     */
    public function __construct(Bootstrap $bootstrap, Logger $logger) {
        $this->bootstrap = $bootstrap;
        $this->logger = $logger;

        try { //Register the session handler
            switch ($bootstrap->getConfiguration()->getCustomConfig()->get('sessions_handler')) {
                case 'mongodb':
                    $this->logger->getSystemLogger()->debug(self::TAG.'Use MongoDB as session handler');
                    $handler = new MongoDBSessionHandler($bootstrap->getConfiguration()->getCustomConfig()->get('sessions_mongodb'),$this->logger);
                    session_set_save_handler([$handler, 'open'], [$handler, 'close'], [$handler, 'read'], [$handler, 'write'], [$handler, 'destroy'], [$handler, 'gc']);
                    register_shutdown_function('session_write_close');
                    break;
                case 'sql':
                    $this->logger->getSystemLogger()->debug(self::TAG.'Use SQL as session handler');
                    $handler = new SQLSessionHandler($this->bootstrap,$this->logger);
                    session_set_save_handler([$handler, 'open'], [$handler, 'close'], [$handler, 'read'], [$handler, 'write'], [$handler, 'destroy'], [$handler, 'gc']);
                    register_shutdown_function('session_write_close');
                    break;
                case 'couchbase':
                    $this->logger->getSystemLogger()->debug(self::TAG.'Use Couchbase as session handler');
                    $handler = new FileSessionHandler($bootstrap->getConfiguration()->getCustomConfig()->get('sessions_couchbase'),$this->logger);
                    session_set_save_handler([$handler, 'open'], [$handler, 'close'], [$handler, 'read'], [$handler, 'write'], [$handler, 'destroy'], [$handler, 'gc']);
                    register_shutdown_function('session_write_close');
                    break;
                case 'php':
                default: break;
            }

            //Start Session
            if (session_status() != PHP_SESSION_ACTIVE) {
                $this->logger->getSystemLogger()->debug(self::TAG.'New session has created!',[session_id()]);
                session_start();
            }

            if($bootstrap->getConfiguration()->getCustomConfig()->get('sessions_use_idle_time')) {
                $this->logger->getSystemLogger()->debug(self::TAG.'Use Auto-Destroy function, max Timeout-Idle',
                    [$bootstrap->getConfiguration()->getCustomConfig()->get('sessions_max_idle_time')]);
                if (!isset($_SESSION['timeout_idle'])) {
                    $_SESSION['timeout_idle'] = time() + $bootstrap->getConfiguration()->getCustomConfig()->get('sessions_use_idle_time');
                    $this->logger->getSystemLogger()->debug(self::TAG.'Session key "timeout_idle" has created',[$_SESSION['timeout_idle']]);
                } else {
                    if ($_SESSION['timeout_idle'] < time()) {
                        $this->logger->getSystemLogger()->debug(self::TAG.'Session has expired!',[$_SESSION['timeout_idle'],time()]);
                        $this->destroy();
                    } else {
                        $this->logger->getSystemLogger()->debug(self::TAG.'Session "timeout_idle" updated',[$_SESSION['timeout_idle']]);
                        $_SESSION['timeout_idle'] = time() + $bootstrap->getConfiguration()->getCustomConfig()->get('sessions_max_idle_time');
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->logger->getSystemLogger()->error($exception->getMessage(),[$exception->getTraceAsString()]);
            exit();
        }
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function destroy() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"],
                $params["domain"], $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        session_start();
    }

    public function get($key) {

    }

    public function set($key,$var): void {

    }

    public function exists($key): bool {

    }
}