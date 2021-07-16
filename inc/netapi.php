<?php
/**
 * DZCP - deV!L`z ClanPortal - Mainpage ( dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * Diese Datei ist ein Bestandteil von dzcp.de
 * Diese Version wurde speziell von Lucas Brucksch (Codedesigns) für dzcp.de entworfen bzw. verändert.
 * Eine Weitergabe dieser Datei außerhalb von dzcp.de ist nicht gestattet.
 * Sie darf nur für die Private Nutzung (nicht kommerzielle Nutzung) verwendet werden.
 *
 * Homepage: http://www.dzcp.de
 * E-Mail: info@web-customs.com
 * E-Mail: lbrucksch@codedesigns.de
 * Copyright 2017 © CodeKing, my-STARMEDIA, Codedesigns
 */

class dzcp_network_api extends common
{
    /**
     * @var bool
     */
    var bool $debug = false;

    /**
     * @var array
     */
    var array $data = [];

    /**
     * @var string
     */
    var string $error;

    /**
     * @var string
     */
    var string $api_version;

    /**
     * @var string
     */
    var string $session_id;

    public function __construct()
    {
        $this->debug = true;
        $this->error = '';
        $this->api_version = '0.1';
        $this->session_id = '';
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function handshake(): bool
    {
        $input = ['event'=>'handshake'];
        $handshake = $this->run($input,false,0, false);

        //var_dump($handshake);

        if(!$handshake['error']) {
            if ((int)str_replace('.', '', $handshake['api_version']) >= (int)str_replace('.', '', $this->api_version)) {
                $this->session_id = $handshake['ssid'];
                return !empty($this->session_id);
            }
        }

        return false;
    }

    /**
     * @param int $id
     * @param bool $cache
     * @param int $cache_time
     * @return array
     * @throws Exception
     */
    public function get_download_key(int $id,bool $cache=true,int $cache_time=600): array {
        $input = ['event'=>'mainpage',
            'call'=>'download',
            'action'=>'get_download_key',
            'id'=>$id
        ];

        return $this->run($input,$cache,$cache_time);
    }

    /**
     * @param bool $cache
     * @param int $cache_time
     * @return array
     * @throws Exception
     */
    public function news(bool $cache=false,int $cache_time=30): array
    {
        $input = ['event'=>'news',
            'version'=>_version,
            'edition'=>_edition];
        return $this->run($input,$cache,$cache_time);
    }

    /**
     * @param array $data
     * @param bool $cache
     * @param int $cache_time
     * @param bool $UseSID
     * @param bool $Rechache
     * @return array
     * @throws Exception
     */
    private final function run(array $data=[], bool $cache=false, int $cache_time=30, bool $UseSID=true, bool $Rechache=false): array {
        if($UseSID && empty($this->session_id)) {
            $this->handshake(); //Get SessionID
        }

        $input = ['type'=>'json','cert'=>config::$dzcp_api_indent];
        if($Rechache)
            $data['reload'] = true;

        $input = array_merge($input,$data);

        //In Cache
        $cache_hash = md5(serialize($input));
        if($cache && !$Rechache && self::$cache->AutoExists($cache_hash)) {
            $item =  self::$cache->AutoGet($cache_hash);
            if(!empty($item))
                return (array)array_merge(['error'=>false],unserialize($item));
        }

        //Get from API-Server
        if($this->call($input)) {
            if($cache && !$this->data['error'] && !empty($this->data)) //Set to Cache
                self::$cache->AutoSet($cache_hash,serialize($this->data),$cache_time);

            return $this->data;
        }

        return (array)array_merge(['error'=>true],[]);
    }

    /**
     * @param array $options
     * @return bool
     * @throws Exception
     */
    private final function call(array $options): bool
    {
        if(!count($options) || !array_key_exists('event',$options))
            return false;

        $stream = self::get_external_contents(config::$dzcp_api_srv_add,$options,false,true);
        if($stream === false || empty($stream)) //Null Check
            return false;

        unset($options);

        $api_data = $this->json_validate($stream);

        if($api_data['code'] != 200)
            return false;

        //Hash Check
        if($api_data['crc32'] != crc32(serialize($api_data['results'])))
            return false;

        unset($api_data['crc32'],$api_data['code']);

        $this->data = $api_data;
        if(!is_object($this->data))
            $this->error = self::$gump->get_readable_errors(true);

        unset($api_data,$data,$key);
        return true;
    }

    private function json_validate($string)
    {
        // decode the JSON data
        $result = @json_decode($string,true);

        // switch and check possible JSON errors
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = ''; // JSON is valid // No error has occurred
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
            // PHP >= 5.3.3
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occured.';
                break;
        }

        if ($error !== '') {
            // throw the Exception or exit // or whatever :)
            exit($error);
        }

        // everything is OK
        return $result;
    }
}