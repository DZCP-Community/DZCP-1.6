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

//-> Verbertet wichtige Informationen zwischen JS und PHP
class javascript {
    private static $data_array = [];

    public static function set(string $key='',$var=''): void {
        self::$data_array[$key] = utf8_encode($var);
    }

    public static function setArray(string $key='',array $array): void {
        self::$data_array[$key] = $array;
    }

    public static function getArray(string $key=''): array {
        if(!array_key_exists($key,self::$data_array)) {
            return [];
        }

        return self::$data_array[$key];
    }

    public static function remove(string $key='') {
        unset(self::$data_array[$key]);
    }

    public static function get(string $key='') {
        return utf8_decode(self::$data_array[$key]);
    }

    public static function encode() {
        return json_encode(self::$data_array);
    }
}