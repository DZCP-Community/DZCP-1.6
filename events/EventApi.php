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
 * Class EventApi
 */
class EventApi extends BaseEventAbstract {
    /**
     * EventApi constructor.
     * @param BaseSystem $baseSystem
     */
    public function __construct(BaseSystem $baseSystem)
    {
        try {
            parent::__construct($baseSystem);
        } catch (Exception $e) {
        }

        $this->useCert(false);

        try {
            $this->getLogger()->pushHandler(new StreamHandler(LOG_PATH . '/' . __CLASS__ . '.log',
                DEBUG ? Logger::DEBUG : Logger::WARNING));
        } catch (Exception $e) {
        }
    }

    /**
     * Call the functions
     */
    public function __run(): void
    {
        parent::__run();

        if($this->isRedirect())
            return;

        $function = $this->getEventCall();
        if(method_exists($this,$function)) {
            $this->$function();
        }
    }

    /* ################################## CALLS ################################## */
    /**
     * URL: https://api.dzcp.de/?event=api&call=version
     * Output: {"results":{"version":"1.0.0","maintenance":false},"status":"ok","code":200,"error":false,"crc32":1678422275}
     */
    private function Version() {
        $this->setContent(['results' => [
            'version' => SERVER_VERSION,
            'maintenance' => SERVER_MAINTENANCE
        ]]);
    }

    /**
     * Certs for Server
     */
    private function Cert() {
        if(array_key_exists('cert',$this->getBaseSystem()->getInput()) && !empty($this->getBaseSystem()->getInput()['cert'])) {
            if($this->getBaseSystem()->getDatabase()->query('SELECT `id` FROM `dzcp_server_certs` WHERE `indent` = ? AND `enabled` = 1;',
                $this->getBaseSystem()->getInput()['cert'])->getRowCount()) {
                $this->setContent(['results' => ['cert'=>'valid']]);
                return;
            }
        }

        $this->setContent(["results" => [], 'status' => 'forbidden', 'code' => 403, 'error' => true]);
    }
}