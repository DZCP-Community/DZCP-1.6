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
 * Class EventProxy
 */
class EventProxy extends BaseEventAbstract {
    /**
     * EventProxy constructor.
     * @param BaseSystem $baseSystem
     */
    public function __construct(BaseSystem $baseSystem)
    {
        try {
            parent::__construct($baseSystem);
        } catch (Exception $e) {
            exit();
        }

        $this->useCert();

        $this->getLogger()->pushHandler(new StreamHandler(LOG_PATH.'/'.__CLASS__.'.log',
            DEBUG ? Logger::DEBUG : Logger::WARNING));
    }

    public function __run(): void
    {
        parent::__run();

        if($this->isRedirect())
            return;

        $function = $this->getEventCall();
        if(method_exists($this,$function)) {
            $this->getLogger()->debug('Call event',[__CLASS__,$function]);
            $this->$function();
        }
    }

    /**
     * Return lat & lng from google api -> Membermap
     * https://api.dzcp.de/?event=proxy&call=getGeocode&address=erkrath+nenaderstra%C3%9Fe+32a
     */
    public function getGeocode() {
        $geocode = new GoogleGeocoding($this);

        try {
            $this->getBaseSystem()->getGump()->validation_rules(['address' => 'required|min_len,4|max_len,255']);
            $this->getBaseSystem()->getGump()->filter_rules(['address' => 'trim|sanitize_string']);

            $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
            if($input !== false) {
                $input['address'] = str_replace(['ö','ü','ä','ß'],
                    ['o','u','a','ss'],strtolower($input['address']));
                $geocode->setAddress($input['address']);
                $geocode->run();
            }
        } catch (Exception $e) {
            $this->getLogger()->critical('Exception in '.__CLASS__.':'.__METHOD__,$e);
        }

        $this->setContent(['results' => $geocode->getLocation()->asArray()]);
    }
}