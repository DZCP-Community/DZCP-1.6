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
 * Class EventAddons
 */
class EventAddons extends BaseEventAbstract {
    /**
     * EventAddons constructor.
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
            $this->$function();
        }
    }

    //Neu nur abgleich
    public function checkAddonsNew() {
        //Filter Input
        $this->getBaseSystem()->getGump()->validation_rules(['addons' => 'required']);

        $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput()); $addons = [];
        if($input !== false) {
            $input['addons'] = unserialize($input['addons']); //DEV
            foreach ($input['addons'] as $addonID) {
                if ($get = $this->getBaseSystem()->getDatabase()->fetch('SELECT * FROM `dzcp_server_addons` WHERE `aid` = ?;', $addonID)) {
                    $addons[$addonID] = ['version' => $get->offsetGet('version')];
                    $this->getBaseSystem()->getDatabase()->query('UPDATE `dzcp_server_addons` SET `count` = (`count`+1) WHERE `aid` = ? AND `enabled` = 1;',
                        $addonID);
                }
            }
        }

        $this->setContent(['results' => $addons]);
    }

    public function checkAddons() {
        //Filter Input
        $this->getBaseSystem()->getGump()->validation_rules(['data' => 'required|min_len,2']);
        $this->getBaseSystem()->getGump()->filter_rules(['data' => 'trim|sanitize_string']);

        $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput()); $addons = [];
        if($input !== false) {
            try {
                $CachedServer = $this->getBaseSystem()->getCacheInstance()->getItem(md5($input['data']));
                if (is_null($CachedServer->get())) {
                    $input['data'] = json_decode(gzuncompress(hex2bin($input['data'])),true);
                    foreach ($input['data'] as $key => $addon) {
                        if ($get = $this->getBaseSystem()->getDatabase()->fetch('SELECT * FROM `dzcp_server_addons` WHERE `aid` = ? AND `enabled` = 1;', (int)$addon['AID'])) {
                            $server = array('Server' => array(
                                'Version' => $get->offsetGet('version'), //New Version
                                'URL' => DOWNLOAD_SERVER_URL . '/' . $get->offsetGet('file'), //Download URL
                                'Title' => $get->offsetGet('url_title'), //Download Title
                            ));

                            $this->getBaseSystem()->getDatabase()->query('UPDATE `dzcp_server_addons` SET `count` = (`count`+1) WHERE `aid` = ? AND `enabled` = 1;', (int)$addon['AID']);
                        } else {
                            $server = array('Server' => array(
                                'error' => true,
                                'msg' => 'no_ids',
                            ));
                        }

                        $addons[$key] = array_merge($addon, $server);
                    }

                    $CachedServer->set($addons)->expiresAfter(60);
                    $this->getBaseSystem()->getCacheInstance()->save($CachedServer);
                } else {
                    $addons = $CachedServer->get();
                }
            } catch (\phpFastCache\Exceptions\phpFastCacheInvalidArgumentException $e) {
                $this->common->logger['system']->critical('Addons-Event-Error: '.$e->getMessage(),$e);
                $this->setContent([
                    'results' => [$e->getMessage()],
                    'status' => 'bad request',
                    'code' => 500,
                    'error' => true
                ]);
                return;
            }

            $this->setContent(['results' => $addons]);
            return;
        }

        $this->setContent([
            'results' => [],
            'status' => 'bad request',
            'code' => 500,
            'error' => true
        ]);
    }

    /*
//Generate AIDs (not used)
private function genAID(int $count = 8): int {
    $nummer = '';
    for($i=1; $i < ($count+1); $i++) {
        $nummer .= rand(1,9);
    } return $nummer;
}
*/
}