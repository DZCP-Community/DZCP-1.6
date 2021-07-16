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

use GameQ\GameQ;
use GameQ\Protocol;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class EventGame
 */
class EventGame extends BaseEventAbstract {
    /**
     * @var string
     */
    private string $protocols_path;

    /**
     * @var array
     */
    private array $protocols;

    /**
     * @var array
     */
    private array $protocolsBlacklist;

    /**
     * @var GameQ
     */
    private GameQ $gameq;

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

        $this->setProtocolsPath(VENDOR_PATH . "/austinb/gameq/src/GameQ/Protocols/");

        $this->setProtocolsBlacklist(GAMEQ_BLACK_LIST);

        $this->setProtocols([]);

        $this->getLogger()->pushHandler(new StreamHandler(LOG_PATH.'/'.__CLASS__.'.log',
            DEBUG ? Logger::DEBUG : Logger::WARNING));
    }

    public function __run(): void
    {
        parent::__run();

        if($this->isRedirect())
            return;

        $this->gameq = new GameQ();

        $function = $this->getEventCall();
        if(method_exists($this,$function)) {
            $this->getLogger()->debug('Call event',[__CLASS__,$function]);
            $this->$function();
        }
    }

    /**
     * Get a list of supported games for proxy (STATE_STABLE)
     * @param bool $internal
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     */
    public function checkGames(bool $internal = false) {
        $CachedServer = $this->getBaseSystem()->getCacheInstance()->getItem('GameProtocols');
        if(!$this->isCached($CachedServer)) {
            foreach ($this->getBaseSystem()->scanDirectory($this->getProtocolsPath()) as $file) {
                try {
                    $reflection = new \ReflectionClass('\\GameQ\\Protocols\\' . pathinfo($file, PATHINFO_FILENAME));
                    if (!$reflection->IsInstantiable()) {
                        continue;
                    }

                    $class = $reflection->newInstance();
                    $this->protocols[strtolower($class->name())] = false;
                    if($class->state() == Protocol::STATE_STABLE &&
                        !array_key_exists(strtolower($class->name()),$this->getProtocolsBlacklist()) &&
                        !array_key_exists(strtolower($reflection->getProperty('protocol')->getName()),$this->getProtocolsBlacklist())) {
                        $this->protocols[strtolower($class->name())] = true;
                    }

                    if(is_array($this->protocols) && count($this->protocols) >= 2) {
                        $CachedServer->set($this->protocols)->expiresAfter(1200);
                        $this->getBaseSystem()->getCacheInstance()->save($CachedServer);
                    }
                } catch (ReflectionException $e) {
                    continue;
                }
            }
        } else {
            $this->setProtocols($CachedServer->get());
        }

        if(!$internal)
            $this->setContent(['results' => $this->getProtocols()]);
    }

    //Premium
    public function downloadProtocol() {
        try {
            $this->getBaseSystem()->getGump()->validation_rules(['protocol' => 'required|min_len,2|max_len,255']);
            $this->getBaseSystem()->getGump()->filter_rules(['protocol' => 'trim|sanitize_string']);

            $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
            if($input !== false) {
                $input['protocol'] = strtolower($input['protocol']);
                $this->checkGames(true);
                if(array_key_exists($input['protocol'],$this->getProtocols())) { //Protocol exist
                    if($this->getProtocols()[$input['protocol']]) { //Protocol enabled
                        if(file_exists($this->getProtocolsPath().ucfirst($input['protocol']).'.php')) {
                            $this->setContentType(BaseEventInterface::GZIP);
                            $this->setContent(['results' => file_get_contents($this->getProtocolsPath().ucfirst($input['protocol']).'.php')]);
                            return;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->getLogger()->critical('Exception in '.__CLASS__.':'.__METHOD__,$e);
        }
    }

    //Premium
    public function gameQProcessArray() {
        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {});
        $this->getBaseSystem()->getGump()->validation_rules(['servers' => 'required']);
        $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
        if($input !== false) {
            $servers = [];
            foreach ($input['servers'] as $id => $data) {
                $CachedServer = $this->getBaseSystem()->getCacheInstance()->getItem('gameq_'.crc32($data['type'].'_'.$data['host']));
                if(!$this->isCached($CachedServer)) {
                    $this->gameq->addServer(['type' => $data['type'], 'host' => $data['host']]);
                    try {
                        $host = explode(':',$input['host']);
                        if(GameServerCheckTools::pingServer($host[0],intval($host[1]))) {
                            $results = $this->gameq->process();
                            if(is_array($results[$input['host']]) && $results[$input['host']]['gq_online']) {
                                $CachedServer->set($results[$input['host']])->expiresAfter(600);
                                $this->getBaseSystem()->getCacheInstance()->save($CachedServer);
                            } $results = $results[$input['host']]; unset($host);
                            $servers[$id] = $results;
                        } else {
                            $servers[$id] = [];
                        }
                    } catch (Exception $e) {
                        $servers[$id] = [];
                    }
                } else {
                    $servers[$id] = $CachedServer->get();
                }
            }

            $this->setContent(['results' => $servers]);
        }
        restore_error_handler();
    }

    /**
     * https://api.dzcp.de/?event=game&call=GameQProcess&type=source&host=45.77.55.47:27015
     */
    public function gameQProcess() {
        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {});

        try {
            $this->getBaseSystem()->getGump()->validation_rules([
                'type' => 'required|min_len,2|max_len,255',
                'host' => 'required|min_len,9|max_len,25']);
            $this->getBaseSystem()->getGump()->filter_rules([
                'type' => 'trim|sanitize_string',
                'host' => 'trim|sanitize_string']);

            $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
            if($input !== false) {
                $CachedServer = $this->getBaseSystem()->getCacheInstance()->getItem('gameq_'.crc32($input['type'].'_'.$input['host']));
                if(!$this->isCached($CachedServer)) {
                    $this->gameq->addServer(['type' => $input['type'], 'host' => $input['host']]);
                    try {
                        $host = explode(':',$input['host']);
                        if(GameServerCheckTools::pingServer($host[0],intval($host[1]))) {
                            $results = $this->gameq->process();
                            if(is_array($results[$input['host']]) && $results[$input['host']]['gq_online']) {
                                $CachedServer->set($results[$input['host']])->expiresAfter(600);
                                $this->getBaseSystem()->getCacheInstance()->save($CachedServer);
                            } $results = $results[$input['host']]; unset($host);

                            $get = $this->getBaseSystem()->getDatabase()->fetch(
                                'SELECT `id`,`enabled`,`pic_path` FROM `dzcp_server_gspics` '.
                                'WHERE `protocol` = ? AND `mod` = ? AND `type` = ? AND `mapname` = ? LIMIT 1;',
                                strtolower($results['gq_protocol']),strtolower($results['gq_mod']),
                                strtolower($results['gq_type']),strtolower($results['gq_mapname']));

                            $client_hash = sha1($this->getBaseSystem()->getClientIP()['v4'].$this->getBaseSystem()->getClientIP()['v6']);

                            if(is_null($get)) {
                                $this->getBaseSystem()->getDatabase()->query(' INSERT INTO `dzcp_server_gspics` SET `protocol` = ?, `mod` = ?, `type` = ?, `mapname` = ?, `searched` = 1;',
                                    strtolower($results['gq_protocol']),strtolower($results['gq_mod']),
                                    strtolower($results['gq_type']),strtolower($results['gq_mapname']));

                                //Update Stats
                                $id = $this->getBaseSystem()->getDatabase()->getInsertId();

                                if(!$this->getBaseSystem()->getDatabase()->fetch('SELECT `id` FROM `dzcp_server_gspics_lock` WHERE `mapid` = ? AND `indent` = ?;',
                                    (int)$id,utf8_encode($client_hash))) {
                                    $this->getLogger()->debug('Event: Insert the IP-Address "'.$this->getBaseSystem()->getClientIP()['v4'].' / '.
                                        $this->getBaseSystem()->getClientIP()['v6'].'" for Lock');

                                    $this->getBaseSystem()->getDatabase()->query('INSERT INTO `dzcp_server_gspics_lock` SET `mapid` = ?, `indent` = ?',
                                        $id, utf8_encode($client_hash));
                                }
                            }

                            $this->setContent(['results' => $results]);
                        } else {
                            $this->setContent(['results' => 'not_connect','status'=>'error']);
                        }
                    } catch (Exception $e) {
                        $this->setContent(['results' => 'not_connect','status'=>'error']);
                    }
                } else {
                    $this->setContent(['results' => $CachedServer->get()]);
                }
            }
        } catch (Exception $e) {
            $this->getLogger()->critical('Exception in '.__CLASS__.':'.__METHOD__,$e);
        }

        restore_error_handler();
    }

    /*
     * https://api.dzcp.de/?event=game&call=SearchGSP&type=source&protocol=source&mod=valve&mapname=crossfire
     */
    public function searchGSP() {
        try {
            $this->getBaseSystem()->getGump()->validation_rules([
                'type' => 'required|min_len,2|max_len,255',
                'protocol' => 'required|min_len,2|max_len,30',
                'mod' => 'required|min_len,2|max_len,30',
                'mapname' => 'required|min_len,2|max_len,30']);
            $this->getBaseSystem()->getGump()->filter_rules([
                'type' => 'trim|sanitize_string',
                'protocol' => 'trim|sanitize_string',
                'mod' => 'trim|sanitize_string',
                'mapname' => 'trim|sanitize_string']);

            $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
            if($input !== false) {
                $get = $this->getBaseSystem()->getDatabase()->fetch(
                    'SELECT `id`,`enabled`,`pic_path` FROM `dzcp_server_gspics` '.
                    'WHERE `protocol` = ? AND `mod` = ? AND `type` = ? AND `mapname` = ? LIMIT 1;',
                    strtolower($input['protocol']),strtolower($input['mod']),
                    strtolower($input['type']),strtolower($input['mapname']));

                $client_hash = sha1($this->getBaseSystem()->getClientIP()['v4'].$this->getBaseSystem()->getClientIP()['v6']);

                if(is_null($get)) {
                    $this->getBaseSystem()->getDatabase()->query(' INSERT INTO `dzcp_server_gspics` SET `protocol` = ?, `mod` = ?, `type` = ?, `mapname` = ?, `searched` = 1;',
                        strtolower($input['protocol']),strtolower($input['mod']),strtolower($input['type']),strtolower($input['mapname']));

                    //Update Stats
                    $id = $this->getBaseSystem()->getDatabase()->getInsertId();

                    if(!$this->getBaseSystem()->getDatabase()->fetch('SELECT `id` FROM `dzcp_server_gspics_lock` WHERE `mapid` = ? AND `indent` = ?;',
                        (int)$id,utf8_encode($client_hash))) {
                        $this->getLogger()->debug('Event: Insert the IP-Address "'.$this->getBaseSystem()->getClientIP()['v4'].' / '.
                            $this->getBaseSystem()->getClientIP()['v6'].'" for Lock');

                        $this->getBaseSystem()->getDatabase()->query('INSERT INTO `dzcp_server_gspics_lock` SET `mapid` = ?, `indent` = ?',
                            $id, utf8_encode($client_hash));

                        $this->setContent(['results' => '','status'=>'map not found']);
                    }
                } else {
                    if(!$get['enabled'] || empty($get['pic_path'])) {
                        if(!$this->getBaseSystem()->getDatabase()->fetch('SELECT `id` FROM `dzcp_server_gspics_lock` WHERE `mapid` = ? AND `indent` = ?;',
                            $get['id'],utf8_encode($client_hash))) {
                            $this->getBaseSystem()->getDatabase()->query('UPDATE `dzcp_server_gspics` SET `searched` = (searched+1) WHERE `id` = ?;',$get['id']);
                            $this->getBaseSystem()->getDatabase()->query('INSERT INTO `dzcp_server_gspics_lock` SET `mapid` = ?, `indent` = ?',
                                $get['id'], utf8_encode($client_hash));
                        }

                        $this->setContent(['results' => '','status'=>'map not found']);
                    } else {
                        //Pic Download
                        $check = $this->getBaseSystem()->getExternalContents(STATIC_SERVER_URL.'exists.php?img=images/maps/'.$get['pic_path']);
                        if(!empty($check)) {
                            $check = json_decode($check,true);
                            if($check['images/maps/'.$get['pic_path']]) {
                                //https://static.dzcp.de/thumbgen.php?img=images/maps/source/svencoop/hl_c02_a1.jpg&width=200&height=160
                                $this->setContent(['results' => STATIC_SERVER_URL.'thumbgen.php?img=images/maps/'.$get['pic_path']]);
                            } else {
                                $this->setContent(['results' => '','status'=>'map not found']);
                            }
                        } else {
                            $this->setContent(['results' => '','status'=>'map not found']);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->getLogger()->critical('Exception in '.__CLASS__.':'.__METHOD__,(array)$e);
        }
    }

    /**
     * @return mixed
     */
    public function getProtocolsPath()
    {
        return $this->protocols_path;
    }

    /**
     * @param mixed $protocols_path
     */
    public function setProtocolsPath($protocols_path): void
    {
        $this->protocols_path = $protocols_path;
    }

    /**
     * @param array $protocolsBlacklist
     */
    public function setProtocolsBlacklist(array $protocolsBlacklist): void
    {
        $this->protocolsBlacklist = $protocolsBlacklist;
    }

    /**
     * @return array
     */
    public function getProtocolsBlacklist(): array
    {
        return $this->protocolsBlacklist;
    }

    /**
     * @return array
     */
    public function getProtocols(): array
    {
        return $this->protocols;
    }

    /**
     * @param array $protocols
     */
    public function setProtocols(array $protocols): void
    {
        $this->protocols = $protocols;
    }
}