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
 * Class EventVersion
 */
class EventVersion extends BaseEventAbstract {
    /**
     * EventVersion constructor.
     * @param BaseSystem $baseSystem
     * @throws Exception
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

    /**
     * URL: https://api.dzcp.de/?event=version&language=en&type=json&version=1.6&edition=dev&build=1700.10.00&release=15.01.2016
     * Output: {"results":{"version":"1.6.1.0","release":"10.12.2018","build":"1610.10.12"},"status":"ok","code":200,"error":false,"crc32":1689732450}
     */
    public function __run(): void
    {
        parent::__run();

        if($this->isRedirect())
            return;

        //Filter Input
        $this->getBaseSystem()->getGump()->validation_rules(['version' => 'required|min_len,3',
            'edition' => 'required|min_len,2',
            'build' => 'required|min_len,9',
            'release' => 'required|min_len,10']);

        $this->getBaseSystem()->getGump()->filter_rules(['version' => 'trim|sanitize_string',
            'edition' => 'trim|sanitize_string',
            'build' => 'trim|sanitize_string',
            'release' => 'trim|sanitize_string']);

        $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
        if($input !== false) {
            $client_hash = sha1($this->getBaseSystem()->getClientIP()['v4'].$this->getBaseSystem()->getClientIP()['v6']);

            $this->getLogger()->debug('Event: Begin search',$input);
            if(!$this->getBaseSystem()->getDatabase()->fetch('SELECT `id` FROM `dzcp_server_version_stats` WHERE `version` = ? AND `edition` = ? AND `release` = ? AND `build` = ?;',
                utf8_encode($input['version']),utf8_encode($input['edition']),utf8_encode($input['release']),utf8_encode($input['build'])))
            {
                //Insert Stats
                $this->getLogger()->debug('Event: Insert a new stats');
                $this->getBaseSystem()->getDatabase()->query('INSERT INTO `dzcp_server_version_stats`',
                    [
                        'version' => utf8_encode($input['version']),
                        'edition' => utf8_encode($input['edition']),
                        'release' => utf8_encode($input['release']),
                        'build'   => utf8_encode($input['build']),
                        'count'   => 1
                    ]);

                //Update Stats
                $id = $this->getBaseSystem()->getDatabase()->getInsertId();

                if(!$this->getBaseSystem()->getDatabase()->fetch('SELECT `id` FROM `dzcp_server_version_lock` WHERE `vid` = ? AND `indent` = ?;',
                    (int)$id,utf8_encode($client_hash)))
                {
                    $this->getLogger()->debug('Event: Insert the IP-Address "'.$this->getBaseSystem()->getClientIP()['v4'].' / '.
                        $this->getBaseSystem()->getClientIP()['v6'].'" for Stats');
                    $this->getBaseSystem()->getDatabase()->query('INSERT INTO `dzcp_server_version_lock`',['vid' => $id, 'indent' => utf8_encode($client_hash)]);
                }
            } else {
                //Update Stats
                $id = $this->getBaseSystem()->getDatabase()->fetch('SELECT `id` FROM `dzcp_server_version_stats` WHERE `version` = ? AND `edition` = ? AND `release` = ? AND `build` = ?;',
                    utf8_encode($input['version']),utf8_encode($input['edition']),utf8_encode($input['release']),utf8_encode($input['build']))->offsetGet('id');

                if(!$this->getBaseSystem()->getDatabase()->fetch('SELECT `id` FROM `dzcp_server_version_lock` WHERE `vid` = ? AND `indent` = ?;',$id,utf8_encode($client_hash)))
                {
                    $this->getLogger()->debug('Event: Update stats');
                    $this->getBaseSystem()->getDatabase()->query('UPDATE `dzcp_server_version_stats` SET `count` = (count+1) WHERE `version` = ? AND `edition` = ? AND `release` = ? AND `build` = ?',
                        utf8_encode($input['version']), utf8_encode($input['edition']), utf8_encode($input['release']), utf8_encode($input['build']));

                    $this->getLogger()->debug('Event: Insert the IP-Address "'.$this->getBaseSystem()->getClientIP()['v4'].' / '.
                        $this->getBaseSystem()->getClientIP()['v6'].'" for Stats');
                    $this->getBaseSystem()->getDatabase()->query('INSERT INTO `dzcp_server_version_lock`',['vid' => $id, 'indent' => utf8_encode($client_hash)]);
                }
            }

            //Versions Check
            $vsersion = explode('.',$input['version']);
            if(count($vsersion) <= 1) {
                $vsersion = [0=>'1',1=>'6'];
            }

            if($this->getBaseSystem()->getDatabase()->fetch('SELECT `id` FROM `dzcp_server_version` WHERE `static_version` = ? AND `edition` = ?;',
                utf8_encode($vsersion[0].'.'.$vsersion[1]),utf8_encode($input['edition']))) {
                $get = $this->getBaseSystem()->getDatabase()->fetch('SELECT * FROM `dzcp_server_version` WHERE `static_version` = ? AND `edition` = ?;',
                    utf8_encode($vsersion[0].'.'.$vsersion[1]),utf8_encode($input['edition']));

                $this->setContent(['results' => [
                    'version' => utf8_decode($get['version']),
                    'release' => utf8_decode($get['release']),
                    'build' => utf8_decode($get['build'])
                ]]);
            }
        } else {
            $this->setContent([
                'results' => $this->getBaseSystem()->getGump()->get_errors_array(),
                'status' => 'bad request',
                'code' => 400,
                'error' => true
            ]);
        }
    }
}