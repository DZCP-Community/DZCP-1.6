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

if(defined('_UserMenu')) {
    $where = _site_msg;
    if(!common::$chkMe) {
        $index = common::error(_error_have_to_be_logged, 1);
    } else {
        switch (common::$do) {
            case 'show':
                $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_messages}` WHERE `id` = ? LIMIT 1;", [(int)($_GET['id'])]);
                if(common::$sql['default']->rowCount() && ($get['von'] == common::$userid || $get['an'] == common::$userid)) {
                    common::$sql['default']->update("UPDATE `{prefix_messages}` SET `readed` = 1 WHERE `id` = ?;", [$get['id']]);

                    //delete icon
                    $smarty->caching = false;
                    $smarty->assign('id',$get['id']);
                    $delete = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_delete.tpl');
                    $smarty->clearAllAssign();

                    if(!$get['von']) {
                        //message from MsgBot
                        $smarty->caching = false;
                        $smarty->assign('nick','MsgBot');
                        $answermsg = $smarty->fetch('string:'._msg_answer_msg);
                        $smarty->clearAllAssign();
                        $answer = "";
                    } else {
                        //message von ..
                        $smarty->caching = false;
                        $smarty->assign('nick',common::autor($get['von']));
                        $answermsg = $smarty->fetch('string:'._msg_answer_msg);
                        $smarty->clearAllAssign();

                        //answer form
                        $smarty->caching = false;
                        $smarty->assign('id',$get['id']);
                        $answer = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_answer.tpl');
                        $smarty->clearAllAssign();
                    }

                    $smarty->caching = false;
                    $smarty->assign('answermsg',$answermsg);
                    $smarty->assign('titel',stringParser::decode($get['titel']));
                    $smarty->assign('nachricht',BBCode::parse_html((string)$get['nachricht']));
                    $smarty->assign('answer',$answer);
                    $smarty->assign('delete',$delete);
                    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_show.tpl');
                    $smarty->clearAllAssign();
                }
            break;
            case 'showsended':
                $get = common::$sql['default']->fetch("SELECT `id`,`von`,`an`,`titel`,`nachricht` "
                                        . "FROM `{prefix_messages}` "
                                        . "WHERE `id` = ? LIMIT 1;", [(int)$_GET['id']]);
                if(common::$sql['default']->rowCount() && ($get['von'] == common::$userid || $get['an'] == common::$userid)) {
                    $smarty->caching = false;
                    $smarty->assign('nick',common::autor($get['an']));
                    $answermsg = $smarty->fetch('string:'._msg_sended_msg);
                    $smarty->clearAllAssign();
                    $answer = _back;

                    //delete icon
                    $smarty->caching = false;
                    $smarty->assign('id',$get['id']);
                    $delete = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_delete.tpl');
                    $smarty->clearAllAssign();

                    $smarty->caching = false;
                    $smarty->assign('answermsg',$answermsg);
                    $smarty->assign('titel',stringParser::decode($get['titel']));
                    $smarty->assign('nachricht',BBCode::parse_html((string)$get['nachricht']));
                    $smarty->assign('answer',$answer);
                    $smarty->assign('delete',$delete);
                    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_show.tpl');
                    $smarty->clearAllAssign();
                }
            break;
            case 'answer':
                $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_messages}` WHERE `id` = ? LIMIT 1;", [(int)$_GET['id']]);
                if(common::$sql['default']->rowCount() && ($get['von'] == common::$userid || $get['an'] == common::$userid)) {
                    $titel = (preg_match("#RE:#is",stringParser::decode($get['titel'])) ? stringParser::decode($get['titel']) : "RE: ".stringParser::decode($get['titel']));
                    $smarty->caching = false;
                    $smarty->assign('von',common::$userid);
                    $smarty->assign('an',$get['von']);
                    $smarty->assign('titel',$titel);
                    $smarty->assign('nick',common::autor($get['von']));
                    $smarty->assign('zitat',BBCode::zitat(common::autor($get['von']),stringParser::decode($get['nachricht'])));
                    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/answer.tpl');
                    $smarty->clearAllAssign();
                }
            break;
            case 'pn':
                $uid = (isset($_GET['id']) && !empty($_GET['id']) ? (int)($_GET['id']) : common::$userid);
                if (!common::$chkMe) {
                    $index = common::error(_error_have_to_be_logged);
                } elseif ($uid == common::$userid) {
                    $index = common::error(_error_msg_self, 1);
                } else {
                    $smarty->caching = false;
                    $smarty->assign('nick',stringParser::decode(common::data("nick")));
                    $titel = $smarty->fetch('string:'._msg_from_nick);
                    $smarty->clearAllAssign();

                    //index
                    $smarty->caching = false;
                    $smarty->assign('von',common::$userid);
                    $smarty->assign('an',$uid);
                    $smarty->assign('titel',$titel);
                    $smarty->assign('nick', common::autor($uid));
                    $smarty->assign('zitat','');
                    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/answer.tpl');
                    $smarty->clearAllAssign();
                }
            break;
            case 'sendanswer':
                if(empty($_POST['titel'])) {
                    $index = common::error(_empty_titel, 1);
                } elseif(empty($_POST['eintrag'])) {
                    $index = common::error(_empty_eintrag, 1);
                } else {
                    common::$sql['default']->insert("INSERT INTO `{prefix_messages}` "
                               . "SET `datum`  = ".time().","
                               . "`von`        = ?,"
                               . "`an`         = ?,"
                               . "`titel`      = ?,"
                               . "`nachricht`  = ?,"
                               . "`see`        = 1;",
                    [common::$userid,(int)$_POST['an'],stringParser::encode($_POST['titel']),stringParser::encode($_POST['eintrag'])]);
                    common::userstats_increase('writtenmsg');

                    //benachrichtigungs email senden
                    if(common::data('pnmail',(int)$_POST['an'])) {
                        //E-Mail an empfänger senden
                        $smarty->caching = false;
                        $smarty->assign('nick',stringParser::decode(common::data('nick',(int)$_POST['an'])));
                        $smarty->assign('titel',stringParser::encode($_POST['titel']));
                        $smarty->assign('clan',common::$pagetitle);
                        $message = $smarty->fetch('string:'.BBCode::bbcode_email(stringParser::decode(settings::get('eml_pn'))));
                        $smarty->clearAllAssign();

                        //subj
                        $smarty->caching = false;
                        $smarty->assign('domain',common::$httphost);
                        $subj = $smarty->fetch('string:'.stringParser::decode(settings::get('eml_pn_subj')));
                        $smarty->clearAllAssign();

                        //send e-mail
                        common::sendMail(stringParser::decode(common::data('email',(int)$_POST['an'])), $subj, $message);
                    }

                    $index = common::info(_msg_answer_done, "?action=msg", 5, false);
                }
            break;
            case 'delete':
                if(!empty($_POST)) {
                    foreach ($_POST as $key => $id) {
                        if(strpos($key, 'posteingang_') !== false) {
                            $get = common::$sql['default']->fetch("SELECT `id`,`see` FROM `{prefix_messages}` WHERE `id` = ? LIMIT 1;", [(int)($id)]);
                            if(!$get['see']) {
                                common::$sql['default']->delete("DELETE FROM `{prefix_messages}` WHERE `id` = ?;", [$get['id']]);
                            } else {
                                common::$sql['default']->update("UPDATE `{prefix_messages}` SET `see_u` = 1 WHERE `id` = ?;", [$get['id']]);
                            }
                        }
                    }
                }
                header("Location: ?action=msg");
            break;
            case 'deletethis':
                $get = common::$sql['default']->fetch("SELECT `id`,`see` FROM `{prefix_messages}` WHERE `id` = ? LIMIT 1;", [(int)($_GET['id'])]);
                if(common::$sql['default']->rowCount()) {
                    if(!$get['see']) {
                        common::$sql['default']->delete("DELETE FROM `{prefix_messages}` WHERE `id` = ?;", [$get['id']]);
                    } else {
                        common::$sql['default']->update("UPDATE `{prefix_messages}` SET `see_u` = 1 WHERE `id` = ?;", [$get['id']]);
                    }
                }
                
                $index = common::info(_msg_deleted, "?action=msg");
            break;
            case 'deletesended':
                if(!empty($_POST)) {
                    foreach ($_POST as $key => $id) {
                        if(strpos($key, 'postausgang_') !== false) {
                            common::$sql['default']->delete("DELETE FROM `{prefix_messages}` WHERE `id` = ?;", [(int)($id)]);
                        }
                    }
                }
                header("Location: ?action=msg");
            break;
            case 'new':
                //To users *list
                $qry = common::$sql['default']->select("SELECT `id`,`nick` "
                                  . "FROM `{prefix_users}` "
                                  . "WHERE `id` != ? "
                                  . "ORDER BY `nick`;", [common::$userid]);
                $users = '';
                foreach($qry as $get) {
                    $smarty->caching = false;
                    $smarty->assign('id',$get['id']);
                    $smarty->assign('selected','');
                    $smarty->assign('nick',stringParser::decode($get['nick']));
                    $users .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_option_users.tpl');
                    $smarty->clearAllAssign();
                }

                //To buddy *list
                $qry = common::$sql['default']->select("SELECT userbuddy.`buddy`,user.`nick` "
                                  . "FROM `{prefix_user_buddys}` AS `userbuddy` "
                                  . "LEFT JOIN `{prefix_users}` AS `user` "
                                  . "ON (user.`id` = userbuddy.`buddy`) "
                                  . "WHERE userbuddy.`user` = ? "
                                  . "ORDER BY userbuddy.`user`;", [common::$userid]);
                $buddys = '';
                foreach($qry as $get) {
                    $smarty->caching = false;
                    $smarty->assign('id',$get['buddy']);
                    $smarty->assign('selected','');
                    $smarty->assign('nick',stringParser::decode($get['nick']));
                    $buddys .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_option_users.tpl');
                    $smarty->clearAllAssign();
                }

                //index
                $smarty->caching = false;
                $smarty->assign('buddys',$buddys);
                $smarty->assign('users',$users);
                $smarty->assign('posttitel','');
                $smarty->assign('posteintrag','');
                $smarty->assign('notification_page','');
                $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_new.tpl');
                $smarty->clearAllAssign();
            break;
            case 'send':
                if(empty($_POST['titel']) || empty($_POST['eintrag']) || $_POST['buddys'] == "-" && $_POST['users'] == "-" || $_POST['buddys'] != "-"
                   && $_POST['users'] != "-" || $_POST['users'] == common::$userid || $_POST['buddys'] == common::$userid) {
                    if (empty($_POST['titel'])) {
                        notification::add_error(_empty_titel);
                    } elseif (empty($_POST['eintrag'])) {
                        notification::add_error(_empty_eintrag);
                    } elseif ($_POST['buddys'] == "-" && $_POST['users'] == "-") {
                        notification::add_error(_empty_to);
                    } elseif ($_POST['buddys'] != "-" && $_POST['users'] != "-") {
                        notification::add_error(_msg_to_just_1);
                    } elseif ($_POST['buddys'] == common::$userid || $_POST['users'] == common::$userid) {
                        notification::add_error(_msg_not_to_me);
                    }

                    //To users *list
                    $qry = common::$sql['default']->select("SELECT `id`,`nick` "
                                      . "FROM `{prefix_users}` "
                                      . "WHERE `id` != ? "
                                      . "ORDER BY `nick`;", [common::$userid]);
                    $users = '';
                    foreach($qry as $get) {
                        $selected = isset($_POST['users']) && $get['id'] == $_POST['users'] ? 'selected="selected"' : '';
                        $smarty->caching = false;
                        $smarty->assign('id',$get['id']);
                        $smarty->assign('selected',$selected);
                        $smarty->assign('nick',stringParser::decode($get['nick']));
                        $users .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_option_users.tpl');
                        $smarty->clearAllAssign();
                    }

                    //To buddy *list
                    $qry = common::$sql['default']->select("SELECT userbuddy.`buddy`,user.`nick` "
                            . "FROM `{prefix_user_buddys}` AS `userbuddy` "
                            . "LEFT JOIN `{prefix_users}` AS `user` "
                            . "ON (user.`id` = userbuddy.`buddy`) "
                            . "WHERE userbuddy.`user` = ? "
                            . "ORDER BY userbuddy.`user`;", [common::$userid]);
                    $buddys = '';
                    foreach($qry as $get) {
                        $selected = isset($_POST['buddys']) && $get['buddy'] == $_POST['buddys'] ? 'selected="selected"' : '';
                        $smarty->caching = false;
                        $smarty->assign('id',$get['buddy']);
                        $smarty->assign('selected',$selected);
                        $smarty->assign('nick',stringParser::decode($get['nick']));
                        $buddys .= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_option_users.tpl');
                        $smarty->clearAllAssign();
                    }

                    //index
                    $smarty->caching = false;
                    $smarty->assign('buddys',$buddys);
                    $smarty->assign('users',$users);
                    $smarty->assign('posttitel',stringParser::decode($_POST['titel']));
                    $smarty->assign('posteintrag',stringParser::decode($_POST['eintrag']));
                    $smarty->assign('notification_page',notification::get());
                    $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_new.tpl');
                    $smarty->clearAllAssign();
                } else {
                    $to = ($_POST['buddys'] == "-" ? $_POST['users'] : $_POST['buddys']);
                    common::$sql['default']->insert("INSERT INTO `{prefix_messages}` "
                               . "SET `datum` = ".time().", "
                               . "`von` = ?, "
                               . "`an` = ?, "
                               . "`titel` = ?, "
                               . "`nachricht` = ?,"
                               . "`see` = 1;", [common::$userid,(int)$to,stringParser::encode($_POST['titel']),stringParser::encode($_POST['eintrag'])]);

                    //benachrichtigungs email senden
                    if(common::data('pnmail',(int)$to)) {
                        //E-Mail an empfänger senden
                        $smarty->caching = false;
                        $smarty->assign('nick',stringParser::decode(common::data('nick',(int)$to)));
                        $smarty->assign('titel',stringParser::encode($_POST['titel']));
                        $smarty->assign('clan',common::$pagetitle);
                        $message = $smarty->fetch('string:'.BBCode::bbcode_email(stringParser::decode(settings::get('eml_pn'))));
                        $smarty->clearAllAssign();

                        //subj
                        $smarty->caching = false;
                        $smarty->assign('domain',common::$httphost);
                        $subj = $smarty->fetch('string:'.stringParser::decode(settings::get('eml_pn_subj')));
                        $smarty->clearAllAssign();

                        //send e-mail
                        common::sendMail(stringParser::decode(common::data('email',(int)$to)), $subj, $message);
                    }

                    common::userstats_increase('writtenmsg');
                    $index = common::info(_msg_answer_done, "?action=msg");
                }
            break;
            default:
                //-> Post Eingang
                $qry = common::$sql['default']->select("SELECT `von`,`titel`,`datum`,`readed`,`see_u`,`id` "
                                  . "FROM `{prefix_messages}` "
                                  . "WHERE `an` = ? AND `see_u` = 0 "
                                  . "ORDER BY datum DESC;", [common::$userid]);
                $posteingang = "";
                if(common::$sql['default']->rowCount()) {
                    foreach($qry as $get) {
                        $absender = !$get['von'] ? _msg_bot : common::autor($get['von']);
                        $smarty->caching = false;
                        $smarty->assign('titel',stringParser::decode($get['titel']));
                        $smarty->assign('id',$get['id']);
                        $titel = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_in_title.tpl');
                        $smarty->clearAllAssign();

                        $date = date("d.m.Y H:i", $get['datum'])._uhr;
                        $new = !$get['readed'] && !$get['see_u'] ? _newicon : '';

                        //posteingang
                        $smarty->caching = false;
                        $smarty->assign('titel',$titel);
                        $smarty->assign('absender',$absender);
                        $smarty->assign('datum',$date);
                        $smarty->assign('color',$color);
                        $smarty->assign('new',$new);
                        $smarty->assign('id',$get['id']);
                        $posteingang.= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/posteingang.tpl');
                        $smarty->clearAllAssign(); $color++;
                    }
                }
                
                if(empty($posteingang)) {
                    $smarty->caching = false;
                    $smarty->assign('colspan',5);
                    $posteingang = $smarty->fetch('string:'._no_entrys_yet);
                    $smarty->clearAllAssign();
                }
                
                $qry = common::$sql['default']->select("SELECT `titel`,`datum`,`readed`,`an`,`id` "
                                  . "FROM `{prefix_messages}` "
                                  . "WHERE `von` = ? AND `see` = 1 "
                                  . "ORDER BY datum DESC;", [common::$userid]);
                $postausgang = "";
                foreach($qry as $get) {
                    $smarty->caching = false;
                    $smarty->assign('titel',stringParser::decode($get['titel']));
                    $smarty->assign('id',$get['id']);
                    $titel = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg_out_title.tpl');
                    $smarty->clearAllAssign();

                    $date = date("d.m.Y H:i", $get['datum'])._uhr;
                    $readed = !$get['readed'] ? _noicon : _yesicon;

                    //postausgang
                    $smarty->caching = false;
                    $smarty->assign('titel',$titel);
                    $smarty->assign('empfaenger',common::autor($get['an']));
                    $smarty->assign('datum',$date);
                    $smarty->assign('color',$color);
                    $smarty->assign('readed',$readed);
                    $smarty->assign('id',$get['id']);
                    $postausgang.= $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/postausgang.tpl');
                    $smarty->clearAllAssign(); $color++;
                }

                if (empty($postausgang)) {
                    $smarty->caching = false;
                    $smarty->assign('colspan',5);
                    $postausgang = $smarty->fetch('string:'._no_entrys_yet);
                    $smarty->clearAllAssign();
                }

                $smarty->caching = false;
                $smarty->assign('nick',common::autor(common::$userid));
                $msghead = $smarty->fetch('string:'._msghead);
                $smarty->clearAllAssign();

                //index
                $smarty->caching = false;
                $smarty->assign('msghead',$msghead);
                $smarty->assign('showincoming',$posteingang);
                $smarty->assign('showsended',$postausgang);
                $index = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/msg/msg.tpl');
                $smarty->clearAllAssign();
            break;
        }
    }
}