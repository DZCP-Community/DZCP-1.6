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

set_time_limit(30);
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set("Europe/Berlin");

## Paths ##
const HOME_DIR = '/usr/home/dzcpad';
const VENDOR_PATH = SCRIPT_PATH . '/vendor';
const LOG_PATH = HOME_DIR . '/www_logs/dzcp.de/api';
const CONFIG_PATH = HOME_DIR . '/www_config';
const SERVER_VERSION = '1.0.0';
const SERVER_MAINTENANCE = false;

## Debug ##
const DEBUG = true;
const DEBUG_TO_FILE = true;
const DEBUG_SYSTEM = true;
const DEBUG_DATABASE = true;
const DEBUG_SERIALIZER = true;

## System ##
const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36';
const DOWNLOAD_SERVER_URL = 'https://download.dzcp.de/';
const STATIC_SERVER_URL = 'https://static.dzcp.de/';

## Database ##
const SQL_DSN = 'mysql:dbname=xxxx;host=localhost';
const SQL_USERNAME = '';
const SQL_PASSWORD = '';
const SQL_PERSISTENT = false;

## Demo ##
const SQL_DEMO_DSN = 'mysql:dbname=demo;host=localhost';
const SQL_DEMO_USERNAME = '';
const SQL_DEMO_PASSWORD = '';

## Google-API ##
const GEO_API_KEY = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
const GEO_API_REFRESH_TIME = 2592000; //1 x Monat
const GEO_API_URL = 'https://maps.googleapis.com/maps/api/geocode';

## GameQ ##
const GAMEQ_BLACK_LIST = [
    'teamspeak2' => false,
    'dayzmod' => false,
    'gta5m' => false,
    'minecraft' => false,
    'minecraftpe' => false,
    'ventrilo' => false,
];