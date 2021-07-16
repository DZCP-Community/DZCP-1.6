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

ob_start();
/**
 * VERSIONS
 */
if(isset($_GET['version'])) {
    switch ($_GET['version']) {
        case 'society':
            $post = [
                'event' => 'version',
                'version' => '0.0.0.1',
                'edition' => 'society',
                'build' => '0000.00.01',
                'release' => '01.01.2020',
            ];

            header('Content-Type: text/plain; charset=utf-8');
            $version = send($post,600);
            if(!empty($version) && is_array($version) && !$version['error'] && !$version['maintenance']) {
                echo utf8_encode($version['results']['version']);
            } else {
                echo utf8_encode('0.0.0.1');
            }
            break;
        default:
            $post = [
                'event' => 'version',
                'version' => '1.6.0.4',
                'edition' => 'final',
                'build' => '1604.01.20',
                'release' => '30.09.2017',
            ];

            header('Content-Type: text/plain; charset=utf-8');
            $version = send($post,600);
            if(!empty($version) && is_array($version) && !$version['error'] && !$version['maintenance']) {
                echo utf8_encode($version['results']['version']);
            } else {
                echo utf8_encode('0.0.0.0');
            }
    }
}
/**
 * NEWS
 */
if(isset($_GET['news'])) {
    header('Content-Type: text/html; charset=utf-8');
    $news = send(['event' => 'news', 'old_news' => 1],60);
    if(!empty($news) && !$news['error'] && !$version['maintenance']) {
        echo utf8_encode($news['results']['news']);
    } else {
        echo utf8_encode('Die DZCP.de News sind zur Zeit nicht verf&uuml;gbar.');
    }
}
ob_end_flush();

/**
 * @param array $input
 * @param int $ttl
 * @return bool|mixed
 */
function send($input=[],$ttl=30) {
    $input += ['event' => 'null'];
    $input += ['format' => 'json'];
    $input += ['reload' => false];
    $input += ['language' => false];
    $input += ['compress' => false];
    $input += ['session' => false];
    $input += ['serialize' => ''];

    $post = []; $serialize = [];
    foreach($input as $key => $var) {
        if(is_array($var)) {
            $post[$key] = utf8_encode(json_encode($var,JSON_HEX_TAG));
            $serialize += [$key=>true];
        } else {
            if(is_integer($var) || is_bool($var)) {
                $post[$key] = $var;
            } else {
                $post[$key] = utf8_encode($var);
            }
        }
    }

    $post['serialize'] = bin2hex(gzcompress(serialize($serialize)));

    $cache = md5(serialize($post));
    $result = apcu_fetch($cache);
    if ($result === false)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.dzcp.de");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, "dzcp");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        if($post && count($post) >= 1)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

        $output = curl_exec($curl);
        curl_close($curl);
        if (!empty($output) && $output) {
            $output = @json_decode(utf8_decode($output),true);
            if(json_last_error()) {
                return false;
            }

            //Uncompress
            if(array_key_exists('compress',$output['results'])) {
                unset($output['results']['compress']);
                foreach ($output['results'] as $key => $result) {
                    $output['results'][$key] = json_decode(gzuncompress(hex2bin($result)),true);
                }
            }

            $output['results'] = (array)$output['results'] ;
            $output['code'] = intval($output['code']);
            $output['error'] = boolval($output['error']);
            $output['status'] = strval($output['status']);

            apcu_store($cache, $output, $ttl);
            return $output;
        }
    }

    return $result;
}