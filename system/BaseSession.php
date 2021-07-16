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

class BaseSession {
    /**
     * @var BaseSystemAbstract
     */
    private BaseSystemAbstract $baseSystem;

    /**
     * @var array
     */
    private array $session;

    /**
     * @var string
     */
    private string $session_id;

    /**
     * @var int
     */
    private int $updated;

    /**
     * @var int
     */
    private int $gc_period;

    /**
     * BaseSession constructor.
     * @param BaseSystemAbstract $bsys
     */
    public function __construct(BaseSystemAbstract $bsys) {
        $this->baseSystem = $bsys;
        $this->session = [];
        $this->session_id = '';
        $this->updated = 0;
        $this->gc_period = 1800;
    }

    /**
     * @return BaseSystem
     */
    public function getBaseSystem(): BaseSystem {
        return $this->baseSystem;
    }

    /**
     * @throws Exception
     */
    public function start(): void {
        $this->getBaseSystem()->getGump()->validation_rules(['session' => 'min_len,32']);
        $this->getBaseSystem()->getGump()->filter_rules(['session' => 'trim']);

        $session_input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
        if($session_input !== false && array_key_exists('session',$session_input)) {
            $this->session_id = strip_tags(strtolower($session_input['session']));
            $this->sql_get_session(true);
            return;
        }

        $this->session_id = md5($this->getBaseSystem()->mkPWD(32,false));
        $this->sql_get_session(false);
    }

    public function delete(): void {
        $this->sql_delete_session();
        $this->session = [];
        $this->session_id = '';
    }

    public function save(): void {
        if(count($this->session) >= 1 && !empty($this->session_id)) {
            $this->sql_set_session();
        }
    }

    //Call on Cronjob
    public function gc(): void {
        $rows = $this->getBaseSystem()->getDatabase()->fetchAll(
            'SELECT `id` FROM `dzcp_server_session` WHERE `update` = 0 OR `update` <= ?;',
            (time() - $this->gc_period));
        $this->getBaseSystem()->getDatabaseLogger()->debug(
            $this->getBaseSystem()->getDatabase()->getLastQueryString());
        $sql = ''; $first = false;
        foreach ($rows as $row) {
            if($first)
                $sql .= ' OR ';

            $sql .= '`id` = '.$row->id;
            $first = true;
        }

        if(!empty($sql)) {
            $debug = $result = $this->getBaseSystem()->getDatabase()->query('DELETE FROM `dzcp_server_session` WHERE '.$sql.';');
            $this->getBaseSystem()->getDatabaseLogger()->debug($debug->getQueryString());
            $this->getBaseSystem()->getDatabaseLogger()->info('"'.$result->getRowCount().'" sessions deleted');
        }

        //TRUNCATE TABLE
        if(!$this->getBaseSystem()->getDatabase()->query('SELECT `id` FROM `dzcp_server_session`;')->getRowCount()) {
            $debug = $this->getBaseSystem()->getDatabase()->query('TRUNCATE TABLE `dzcp_server_session`;');
            $this->getBaseSystem()->getDatabaseLogger()->debug($debug->getQueryString());
        }
    }

    /**
     * @return string
     */
    public function getSessionId(): string {
        return $this->session_id;
    }

    /**
     * @return int
     */
    public function getUpdated(): int {
        return $this->updated;
    }

    /**
     * @param int $updated
     */
    public function setUpdated(int $updated): void {
        $this->updated = $updated;
    }

    /**
     * @param int $gc_period
     */
    public function setGcPeriod(int $gc_period): void {
        $this->gc_period = $gc_period;
    }

    /**
     * @param string $key
     * @param $var
     */
    public function set(string $key,$var): void {
        $this->session[$key] = $var;
    }

    /**
     * @param string $key
     * @param string $default
     * @return mixed|string|null
     */
    public function get(string $key, string $default='') {
        if($this->exists($key)) {
            return $this->session[$key];
        }

        return !empty($default) ? $default : null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool {
        return array_key_exists($key,$this->session);
    }

    /**
     * @param bool $fromInput
     */
    private function sql_get_session(bool $fromInput = false) {
        $session = $this->getBaseSystem()->getDatabase()->fetch('SELECT * FROM `dzcp_server_session` WHERE `ssid` = ?',
            utf8_encode($this->session_id));
        $this->getBaseSystem()->getDatabaseLogger()->debug($this->getBaseSystem()->getDatabase()->getLastQueryString());
        if(is_null($session)) {
            $this->session = [];

            if($fromInput)
                $this->session_id = md5($this->getBaseSystem()->mkPWD(32,false));

            $debug = $this->getBaseSystem()->getDatabase()->query('INSERT INTO `dzcp_server_session` ?', [
                'ssid' => utf8_encode($this->session_id),
                'data' => json_encode($this->session),
                'update' => time(),
            ]);

            $this->getBaseSystem()->getDatabaseLogger()->debug($debug->getQueryString());
            return;
        }

        $this->session = json_decode($session->data,true);
        $this->session_id = utf8_decode($session->ssid);
        $this->updated = intval($session->update);
    }

    private function sql_set_session() {
        $session = $this->getBaseSystem()->getDatabase()->fetch('SELECT `id` FROM `dzcp_server_session` WHERE `ssid` = ?',
            utf8_encode($this->session_id));
        if(!is_null($session)) {
            $this->updated = time();
            $debug = $this->getBaseSystem()->getDatabase()->query('UPDATE `dzcp_server_session` SET', [
                'data' => json_encode($this->session),
                'update' => $this->updated,
            ], 'WHERE `id` = ?', $session->id);

            $this->getBaseSystem()->getDatabaseLogger()->debug($debug->getQueryString());
        }
    }

    private function sql_delete_session() {
        $session = $this->getBaseSystem()->getDatabase()->fetch('SELECT `id` FROM `dzcp_server_session` WHERE `ssid` = ?',
            utf8_encode($this->session_id));
        if(!is_null($session)) {
            $this->updated = time();
            $debug = $this->getBaseSystem()->getDatabase()->query('DELETE FROM `dzcp_server_session` WHERE `id` = ?;', $session->id);
            $this->getBaseSystem()->getDatabaseLogger()->debug($debug->getQueryString());
        }
    }
}