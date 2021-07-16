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
 * Class EventHandshake
 */
class EventHandshake extends BaseEventAbstract {
    /**
     * EventHandshake constructor.
     * @param BaseSystem $baseSystem
     */
    public function __construct(BaseSystem $baseSystem)
    {
        try {
            parent::__construct($baseSystem);
        } catch (Exception $e) {
            exit();
        }

        $this->getBaseSystem()->enableSession(true);

        $this->useCert();

        $this->getLogger()->pushHandler(new StreamHandler(LOG_PATH.'/'.__CLASS__.'.log',
            DEBUG ? Logger::DEBUG : Logger::WARNING));
    }

    public function __run(): void
    {
        parent::__run();

        if($this->isRedirect())
            return;

        $this->setContent(['results' => [
            'ssid' => $this->getBaseSystem()->getSession()->getSessionId()
        ]]);
    }
}