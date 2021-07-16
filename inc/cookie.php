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

final class cookie {
    private static $val = [];
    private static $expires;
    private static $dir = '/';
    private static $site = '';

    /**
     * Setzt die Werte fur ein Cookie und erstellt es.
     * @param bool $cexpires
     * @param string $cdir
     * @param string $csite
     */
    public final static function init($cexpires=false,string $cdir="",string $csite=""): void {
        self::$expires = ($cexpires ? $cexpires : (time()+cookie_expires));
        self::$dir=(empty($cdir) ? '/' : $cdir);
        self::$site=(empty($csite) ? '' : $csite);
        self::$val= [];
        self::extract();
    }

    /**
     * Extraktiert ein gespeichertes Cookie
     */
    public final static function extract(): void {
        foreach ($_COOKIE as $key => $var)
        if(!empty($var)) {
            self::$val[$key] = $var;
        }
    }

    /**
     * Liest und gibt einen Wert aus dem Cookie zuruck
     *
     * @param string $var
     * @return string
     */
    public final static function get(string $var) {
        if(!isset(self::$val) || empty(self::$val)) return false;
        if(!array_key_exists($var, self::$val)) return false;
        return self::$val[$var];
    }

    /**
     * Setzt ein neuen Key und Wert im Cookie
     * @param string $var
     * @param $value
     */
    public final static function set(string $var, $value): void {
        self::$val[$var]=$value;
        $_COOKIE[$var]=self::$val[$var];
        if(empty($value)) unset(self::$val[$var]);
    }

    /**
     * Leert das Cookie
     */
    public final static function clear()
    { self::$val= []; self::save(); }

    /**
     * Loscht einen Wert aus dem Cookie
     * @param string $var
     */
    public final static function delete(string $var): void
    { unset(self::$val[$var]); self::save(); }

    /**
     * Speichert das Cookie
     */
    public final static function save(): void {
        foreach (self::$val as $key => $var) {
            setcookie($key, $var, self::$expires, self::$dir, self::$site,true,true);
        }
    }
}