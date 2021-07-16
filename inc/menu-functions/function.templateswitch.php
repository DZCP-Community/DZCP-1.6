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

function smarty_function_templateswitch($params,Smarty_Internal_Template &$smarty) {
    $tmpldir = "";
    $tmps = common::get_files(basePath . '/inc/_templates_/', true);
    foreach ($tmps as $tmp) {
        $cache_hash = md5('templateswitch_xml_' . $tmp);
        if (!common::$cache->AutoMemExists($cache_hash) || !config::$use_system_cache) {
            if (file_exists(basePath . '/inc/_templates_/' . $tmp . '/template.xml')) {
                $xml = simplexml_load_file(basePath . '/inc/_templates_/' . $tmp . '/template.xml');
                if (config::$use_system_cache) {
                    common::$cache->AutoMemSet($cache_hash, json_encode($xml), cache::TIME_TEMPLATE_XML);
                }

                if (!empty((string)$xml->permissions)) {
                    if (common::permission((string)$xml->permissions) || ((int)$xml->level >= 1 && common::$chkMe >= (int)$xml->level)) {
                        $tmpldir .= common::select_field_bootstrap("?tmpl_set=" . $tmp, (string)$xml->name, (common::$tmpdir == $tmp), ['icon'=>'far fa-newspaper']);
                    }
                } else if ((int)$xml->level >= 1 && common::$chkMe >= (int)$xml->level) {
                    $tmpldir .= common::select_field_bootstrap("?tmpl_set=" . $tmp, (string)$xml->name, (common::$tmpdir == $tmp), ['icon'=>'far fa-newspaper']);
                } else if (!(int)$xml->level) {
                    $tmpldir .= common::select_field_bootstrap("?tmpl_set=" . $tmp, (string)$xml->name, (common::$tmpdir == $tmp), ['icon'=>'far fa-newspaper']);
                }
            }
        } else {
            $data = json_decode(common::$cache->AutoMemGet($cache_hash), true);
            if (!empty($data['permissions'])) {
                if (common::permission((string)$data['permissions']) || ((int)$data['level'] >= 1 && common::$chkMe >= (int)$data['level'])) {
                    $tmpldir .= common::select_field_bootstrap("?tmpl_set=" . $tmp, (string)$data['name'], (common::$tmpdir == $tmp), ['icon'=>'far fa-newspaper']);
                }
            } else if ((int)$data['level'] >= 1 && common::$chkMe >= (int)$data['level']) {
                $tmpldir .= common::select_field_bootstrap("?tmpl_set=" . $tmp, (string)$data['name'], (common::$tmpdir == $tmp), ['icon'=>'far fa-newspaper']);
            } else if (!(int)$data['level']) {
                $tmpldir .= common::select_field_bootstrap("?tmpl_set=" . $tmp, (string)$data['name'], (common::$tmpdir == $tmp), ['icon'=>'far fa-newspaper']);
            }
        }
    }

    $smarty->assign('templates', $tmpldir);
    return $smarty->fetch('file:[' . common::$tmpdir . ']page/template_switch.tpl');
}