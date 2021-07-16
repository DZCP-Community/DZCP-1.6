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

require_once SCRIPT_PATH."/events/EventProxy/models/Location.php";

/**
 * Class GoogleGeocoding
 */
class GoogleGeocoding
{
    /**
     * Time for cache
     */
    const CACHE_TIME = 120;

    /**
     * @var BaseSystem
     */
    private $baseSystem;

    /**
     * @var string
     */
    private $address;

    /**
     * @var Location
     */
    private $location;

    /**
     * @var string
     */
    private $cache_id;

    /**
     * @var array
     */
    private $json;

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * Google Geocoding constructor.
     * @param BaseEventAbstract $event
     */
    public function __construct(BaseEventAbstract $event) {
        $this->baseSystem = $event->getBaseSystem();
        $this->logger = $event->getLogger();
        $this->location = new Location();
        $this->cache_id = '';
        $this->address = '';
        $this->json = [];
    }

    /**
     * Call G-Maps API
     */
    public function run() {
        if(empty($this->address))
            return;

        $this->cache_id = md5($this->address);
        $cache = $this->baseSystem->getCacheInstance()->getItem($this->cache_id);

        if (is_null($cache->get())) {
            $row = $this->baseSystem->getDatabase()->fetch('SELECT * FROM `dzcp_server_geolocation` WHERE `hash` = ?', $this->cache_id);
            if (!is_null($row) && $row->count()) { //In Database, Check for update & send
                $this->logger->info('Load location from database', [$this->address, $this->cache_id]);
                if ((!$row->offsetGet('updated') || (time() - $row->offsetGet('updated')) >= GEO_API_REFRESH_TIME) && $row->offsetGet('updated') != -1) {
                    $this->logger->info('Location update from Google', [$this->address, $this->cache_id]);
                    $fallback = false;
                    $post = ['address' => urlencode($this->address), 'key' => GEO_API_KEY, 'language' => 'de'];
                    $json_string = $this->baseSystem->getExternalContents(GEO_API_URL . '/json?' . http_build_query($post));
                    if (empty($json_string)) { //Google is offline, get from database
                        $this->logger->notice('Location update from Google faild! ( Offline )', [
                            $this->address,
                            $this->cache_id,
                            GEO_API_URL . '/json?' . http_build_query($post)
                        ]);
                        $json_string = $row->offsetGet('data');
                        $fallback = true;
                    }

                    $this->json = json_decode($json_string, true);
                    unset($json_string);

                    if ($this->json['status'] == 'OK' && !$fallback) {
                        $this->logger->debug('Location update to database', [$this->address, $this->cache_id]);
                        $this->update();
                    }

                    if ($this->json['status'] == 'ZERO_RESULTS') {
                        $this->logger->info('Location update delivered "ZERO_RESULTS" delete from database.',
                            [$this->address, $this->cache_id]);

                        $this->remove();
                        $this->baseSystem->getCacheInstance()->deleteItem($this->cache_id);
                        return;
                    }

                    if ($this->json['status'] == 'REQUEST_DENIED') {
                        $this->logger->critical('Google-API not reachable -> "REQUEST_DENIED"',
                            [$this->json['error_message'], $this->address, $this->cache_id]);
                    }

                    $cache->set($this->json)->expiresAfter(self::CACHE_TIME);
                    $this->baseSystem->getCacheInstance()->save($cache);

                    $this->location->setLat(floatval($cache->get()['results'][0]['geometry']['location']['lat']));
                    $this->location->setLng(floatval($cache->get()['results'][0]['geometry']['location']['lng']));

                } else { //Up to date
                    if (!empty($row->offsetGet('data'))) {
                        $this->json = json_decode($row->offsetGet('data'), true);

                        $cache->set($this->json)->expiresAfter(self::CACHE_TIME);
                        $this->baseSystem->getCacheInstance()->save($cache);

                        $this->location->setLat(floatval($cache->get()['results'][0]['geometry']['location']['lat']));
                        $this->location->setLng(floatval($cache->get()['results'][0]['geometry']['location']['lng']));
                    }
                }
            } else { //Not in Database, load from google api
                $post = ['address' => urlencode($this->address), 'key' => GEO_API_KEY, 'language' => 'de'];
                $json_string = $this->baseSystem->getExternalContents(GEO_API_URL . '/json?' . http_build_query($post));
                if (empty($json_string)) { //Google is offline, get from database
                    $this->logger->alert('Location update from Google faild! ( Offline )', [
                        $this->address,
                        $this->cache_id,
                        GEO_API_URL . '/json?' . http_build_query($post)
                    ]);
                    return;
                }

                $this->json = json_decode($json_string, true);
                unset($json_string);

                if ($this->json['status'] == 'OK') {
                    $this->logger->debug('Location insert to database', [$this->address, $this->cache_id]);
                    $this->insert(); //Insert to DB
                    $cache->set($this->json)->expiresAfter(self::CACHE_TIME);
                    $this->location->setLat(floatval($cache->get()['results'][0]['geometry']['location']['lat']));
                    $this->location->setLng(floatval($cache->get()['results'][0]['geometry']['location']['lng']));
                    $this->baseSystem->getCacheInstance()->save($cache);
                }

                if ($this->json['status'] == 'ZERO_RESULTS') {
                    $this->logger->debug('Location not found! "ZERO_RESULTS"',
                        [$this->address, $this->cache_id]);
                }

                if ($this->json['status'] == 'REQUEST_DENIED') {
                    $this->logger->critical('Google-API not reachable -> "REQUEST_DENIED"',
                        [$this->json['error_message'], $this->address, $this->cache_id]);
                }
            }
        } else {
            $this->logger->debug('Location found in cache!', [$this->address, $this->cache_id]);
            $this->location->setLat(floatval($cache->get()['results'][0]['geometry']['location']['lat']));
            $this->location->setLng(floatval($cache->get()['results'][0]['geometry']['location']['lng']));
        }
    }

    private function update() {
        $this->baseSystem->getDatabase()->query('UPDATE `dzcp_server_geolocation` SET `data` = ?, `updated` = ? WHERE `hash` = ?',
            json_encode($this->json), time(), $this->cache_id);
    }

    private function insert() {
        $this->baseSystem->getDatabase()->query('INSERT INTO `dzcp_server_geolocation` SET `hash` = ?, `data` = ?, `created` = ?, `updated` = ?;',
            $this->cache_id, json_encode($this->json), time(), time());
    }

    private function remove() {
        $this->baseSystem->getDatabase()->query('DELETE FROM `dzcp_server_geolocation` WHERE `hash` = ?;', $this->cache_id);
    }

    /**
     * @return string
     */
    public function getAddress(): string {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address) {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getLocation(): Location {
        return $this->location;
    }
}