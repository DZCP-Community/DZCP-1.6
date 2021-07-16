<?php
/**
 * DZCP - deV!L`z ClanPortal - Server ( api.dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * DZCP - deV!L`z ClanPortal - Server
 * Homepage: https://www.dzcp.de
 * E-Mail: lbrucksch@hammermaps.de
 * Author Lucas Brucksch
 * Copyright 2021 © Codedesigns
 */

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class EventLogs
 */
class EventLogs extends BaseEventAbstract {
    /**
     * EventLogs constructor.
     * @param BaseSystem $baseSystem
     */
    public function __construct(BaseSystem $baseSystem)
    {
        try {
            parent::__construct($baseSystem);
        } catch (Exception $e) {
            exit();
        }

        $this->getBaseSystem()->enableSession(false);

        $this->useCert(true);

        $this->getLogger()->pushHandler(new StreamHandler(LOG_PATH.'/'.__CLASS__.'.log',
            DEBUG ? Logger::DEBUG : Logger::WARNING));
    }

    public function __run(): void
    {
        parent::__run();

        if($this->isRedirect())
            return;

        if(array_key_exists('log',$this->getBaseSystem()->getInput())) {
            if (!empty($this->getBaseSystem()->getInput()['log'])) {
                $certID = $this->getBaseSystem()->getDatabase()->fetch("SELECT `id` FROM `dzcp_server_certs` WHERE `indent` = ?;",
                    $this->getCert());
                if($certID->count()) {
                    $this->getBaseSystem()->getDatabase()->query("INSERT INTO `dzcp_server_logs` (`id`, `certID`, `time`, `log`) ".
                        "VALUES (NULL, ?, ?, ?);",$certID->offsetGet('id'),time(),$this->getBaseSystem()->getInput()['log']);
                }
            }
        }

        $this->setContent(["results" => [],"status" => "ok","code" => 200,"error" => false]);
    }
}