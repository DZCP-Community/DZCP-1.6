<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START ##
include("../inc/buffer.php");

## INCLUDES ##
include(basePath."/inc/debugger.php");
include(basePath."/inc/config.php");
include(basePath."/inc/bbcode.php");

## SETTINGS ##
$where = _site_contact;
$title = $pagetitle." - ".$where."";
$dir = "contact";
## SECTIONS ##
switch ($action):
    default:
        $index = show($dir."/contact", array("head" => _site_contact,
            "nachricht" => _contact_nachricht,
            "nick" => _nick,
            "what" => "contact",
            "security" => _register_confirm,
            "joinus" => "",
            "value" => _button_value_send,
            "why" => "",
            "pflicht" => _contact_pflichtfeld,
            "email" => _email,
            "skype" => _skypeid,
            "steam" => _steamid,
            "icq" => _icq));
        break;
    case 'do';
        if(HasDSGVO()) {
            if ($_POST['secure'] != $_SESSION['sec_contact'] || empty($_SESSION['sec_contact']))
                $index = error(_error_invalid_regcode, 1);
            elseif (empty($_POST['text']))
                $index = error(_error_empty_nachricht, 1);
            elseif (empty($_POST['email']))
                $index = error(_empty_email, 1);
            elseif (!check_email($_POST['email']))
                $index = error(_error_invalid_email, 1);
            elseif (empty($_POST['nick']))
                $index = error(_empty_nick, 1);
            else {
                $icq = preg_replace("=-=Uis", "", $_POST['icq']);
                $email = show(_email_mailto, array("email" => $_POST['email']));
                $text = show(_contact_text, array("icq" => $icq,
                    "skype" => $_POST['skype'],
                    "steam" => $_POST['steam'],
                    "email" => $email,
                    "text" => $_POST['text'],
                    "nick" => $_POST['nick']));

                $qry = db("SELECT s1.id FROM " . $db['users'] . " AS s1
                       LEFT JOIN " . $db['permissions'] . " AS s2
                       ON s1.id = s2.user
                       WHERE s2.contact = '1' AND s1.`user` != '0 GROUP BY s1.`id`'");

                $sqlAnd = '';
                while ($get = _fetch($qry)) {
                    $sqlAnd .= " AND s2.`user` != '" . (int)($get['id']) . "'";
                    $qrys = db("INSERT INTO " . $db['msg'] . "
                            SET `datum`     = '" . time() . "',
                                `von`       = '0',
                                `an`        = '" . ((int)$get['id']) . "',
                                `titel`     = '" . _contact_title . "',
                                `nachricht` = '" . up($text) . "'");
                }

                $qry = db("SELECT s2.`user` FROM " . $db['permissions'] . " AS s1
                       LEFT JOIN " . $db['userpos'] . " AS s2 ON s1.`pos` = s2.`posi`
                       WHERE s1.`contact` = '1' AND s2.`posi` != '0'" . $sqlAnd . " GROUP BY s2.`user`");

                while ($get = _fetch($qry)) {
                    $qrys = db("INSERT INTO " . $db['msg'] . "
                            SET `datum`     = '" . time() . "',
                                `von`       = '0',
                                `an`        = '" . ((int)$get['user']) . "',
                                `titel`     = '" . _contact_title . "',
                                `nachricht` = '" . up($text) . "'");
                }

                $index = info(_contact_sended, "../news/");
            }
        }
        break;
endswitch;

## INDEX OUTPUT ##
page($index, $title, $where);