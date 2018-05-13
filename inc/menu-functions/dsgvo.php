<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 * Menu: Last Articles
 */
function dsgvo() {
    global $db;
    $qry = db("SELECT * FROM `".$db['dsgvo']."` WHERE `show` = 1 ORDER BY `sort` ASC;");
    $dsgvo_texts = ''; $dsgvo_texts_lock = '';
    if(_rows($qry)) { $i = 0;
        while ($get = _fetch($qry)) {
            $ph = array();
            $ph['organisation'] = ''; $ph['first_name'] = '';
            $ph['last_name'] = ''; $ph['address'] = '';
            $ph['zip_code'] = ''; $ph['place'] = '';
            $ph['country'] = ''; $ph['e-mail'] = '';
            $ph['phone'] = ''; $ph['website'] = '';
            $ph['titel'] = '';
            $ph['clanname'] = re(settings('clanname'));
            $ph['lang'] = language_short_tag();

            if($get['persid']) {
                $pers = db("SELECT * FROM `".$db['dsgvo_pers']."` WHERE `id` = " . $get['persid'] . ";", false, true);
                $ph['organisation'] = re($pers['organisation']);
                $ph['titel'] = re($pers['titel']);
                $ph['first_name'] = re($pers['first_name']);
                $ph['last_name'] = re($pers['last_name']);
                $ph['address'] = re($pers['address']);
                $ph['zip_code'] = re($pers['zip_code']);
                $ph['place'] = re($pers['place']);
                $ph['country'] = re($pers['country']);
                $ph['e-mail'] = re($pers['e-mail']);
                $ph['phone'] = re($pers['phone']);
                $ph['website'] = re($pers['website']);
                unset($pers);
            }

            if(!empty($get['title']) && defined($get['title']) && $get['id'] != 1) {
                $title = constant($get['title']);
                if(!empty($title) && $title != '')
                    $i++;
            }

            //Output
            $text = !empty($get['text_tag']) && defined(re($get['text_tag'])) ? show(constant(re($get['text_tag'])),$ph) : '';
            $title = !empty($get['title']) && defined(re($get['title'])) ? show(constant(re($get['title'])),array('count' => strval($i))) : '';
            $dsgvo_texts .= show("menu/dsgvo_texts", array('title' => bbcode_html($title), 'text' => bbcode_html($text)));
            unset($ph);
        }

        if($_SESSION['user_has_dsgvo_lock']) {
            $qry = db("SELECT * FROM `".$db['dsgvo']."` WHERE `lock_show` = 1 ORDER BY `sort` ASC;");
            while ($get = _fetch($qry)) {
                $ph = array();
                $ph['organisation'] = ''; $ph['first_name'] = '';
                $ph['last_name'] = ''; $ph['address'] = '';
                $ph['zip_code'] = ''; $ph['place'] = '';
                $ph['country'] = ''; $ph['e-mail'] = '';
                $ph['phone'] = ''; $ph['website'] = '';
                $ph['titel'] = '';
                $ph['clanname'] = re(settings('clanname'));
                $ph['lang'] = language_short_tag();

                if($get['persid']) {
                    $pers = db("SELECT * FROM `".$db['dsgvo_pers']."` WHERE `id` = " . $get['persid'] . ";", false, true);
                    $ph['organisation'] = re($pers['organisation']);
                    $ph['titel'] = re($pers['titel']);
                    $ph['first_name'] = re($pers['first_name']);
                    $ph['last_name'] = re($pers['last_name']);
                    $ph['address'] = re($pers['address']);
                    $ph['zip_code'] = re($pers['zip_code']);
                    $ph['place'] = re($pers['place']);
                    $ph['country'] = re($pers['country']);
                    $ph['e-mail'] = re($pers['e-mail']);
                    $ph['phone'] = re($pers['phone']);
                    $ph['website'] = re($pers['website']);
                    unset($pers);
                }

                if(!empty($get['title']) && defined($get['title']) && $get['id'] != 1) {
                    $title = constant($get['title']);
                    if(!empty($title) && $title != '')
                        $i++;
                }

                //Output
                $text = !empty($get['text_tag']) && defined(re($get['text_tag'])) ? show(constant(re($get['text_tag'])),$ph) : '';
                $title = !empty($get['title']) && defined(re($get['title'])) ? show(constant(re($get['title'])),array('count' => strval($i))) : '';
                $dsgvo_texts_lock .= show("menu/dsgvo_texts", array('title' => bbcode_html($title), 'text' => bbcode_html($text)));
                unset($ph);
            }
        }
    }

    return show("menu/dsgvo", array("content" => $dsgvo_texts, "texts_lock" => $dsgvo_texts_lock,
        "dsgvo_base_lock_title" => _dsgvo_base_lock_title, "dsgvo_base_title" => _dsgvo_base_title,
        "b1" => $_SESSION['user_has_dsgvo_lock'] ? '' : '<!--', "b2" => $_SESSION['user_has_dsgvo_lock'] ? '' : '-->'));
}