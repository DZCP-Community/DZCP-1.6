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

//-> Codiert Text zur Speicherung
final class stringParser {
    /**
     * Codiert Text in das UTF8 Charset.
     * @param string $txt
     * @return string
     */
    public static function encode(?string $txt='',bool $htmlentities=true): string {
        $txt = str_replace(["\r\n", "\r", "\n"], ["[nr]","[r]","[n]"], $txt);
        $txt = ($htmlentities ? htmlentities($txt, ENT_COMPAT, 'UTF-8') : $txt);
        return utf8_encode($txt);
    }

    /**
     * Decodiert UTF8 Text in das aktuelle Charset der Seite.
     * @param utf8 string $txt
     */
    public static function decode(?string $txt=''): string {
        $txt = utf8_decode($txt);
        $txt = html_entity_decode($txt, ENT_COMPAT, 'UTF-8');
        return str_replace(['[nr]','[r]','[n]'],["\r\n","\r","\n"], $txt);
    }
}