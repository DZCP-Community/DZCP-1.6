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

if(defined('_Forum')) {
    if(common::$userid && common::$chkMe >= 1) {
        switch (common::$do) {
            case 'edit':
                /*
                 * ########################################################
                 * POST
                 * ########################################################
                 */

                if (array_key_exists('eintrag', $_POST)) {
                    //validation
                    common::$gump->validation_rules([
                        'topic' => 'required|max_len,150|min_len,1',
                        'eintrag' => 'required|min_len,1'
                    ]);

                    //filter
                    common::$gump->filter_rules([
                        'topic' => 'trim',
                        'subtopic' => 'trim',
                        'sticky' => 'sanitize_numbers',
                        'global' => 'sanitize_numbers',
                        'eintrag' => 'trim'
                    ]);

                    $validated_post_data = common::$gump->run($_POST);
                    if ($validated_post_data !== false && (int)($_GET['id']) >= 1) {
                        $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_forum_threads}` WHERE `id` = ?;", [(int)($_GET['id'])]);

                        //#####################
                        //Update Forum-Vote
                        //#####################
                        common::$gump->validation_rules([
                            'question' => 'required|alpha_numeric|min_len,1',
                            'answer_1' => 'required|alpha_numeric|min_len,1',
                            'answer_2' => 'required|alpha_numeric|min_len,1'
                        ]);

                        $validated_vote_data = common::$gump->run($validated_post_data);
                        if ($validated_vote_data !== false) {
                            //Delete forum vote
                            if (array_key_exists('vote_del', $validated_vote_data) && $get['vote'] >= 1) {
                                common::$sql['default']->delete("DELETE FROM `{prefix_votes}` WHERE `id` = ?;", [$get['vote']]);
                                common::$sql['default']->delete("DELETE FROM `{prefix_vote_results}` WHERE `vid` = ?;", [$get['vote']]);
                                common::setIpcheck("vid_" . $get['vote'], false);
                                $get['vote'] = 0; //Set to null
                            } else {
                                //Update forum vote
                                $fgetvote = common::$sql['default']->fetch("SELECT s1.`intern`,s2.`id` "
                                    . "FROM `{prefix_forum_kats}` AS s1 "
                                    . "LEFT JOIN `{prefix_forum_sub_kats}` AS s2 "
                                    . "ON s2.`sid` = s1.`id` "
                                    . "WHERE s2.`id` = ?;", [$_SESSION['kid']]);

                                $validated_vote_data['closed'] = (array_key_exists($validated_vote_data,'closed') ? $validated_vote_data['closed'] : 0);
                                if (common::$sql['default']->rows("SELECT `id` FROM `{prefix_votes}` WHERE `id` = ?;", [$get['vote']])) {
                                    common::$sql['default']->update("UPDATE `{prefix_votes}` SET `titel`  = ?," .
                                        ($fgetvote['intern'] ? " `intern` = 1," : " `intern` = " . (int)($validated_vote_data['intern']) . ",") . " `closed` = ? WHERE `id` = ?;",
                                        [stringParser::encode(strip_tags($validated_vote_data['question'])), (int)($validated_vote_data['closed']), $get['vote']]);
                                } else {
                                    common::$sql['default']->insert("INSERT INTO `{prefix_votes}` SET `datum`  = ?, `titel`  = ?," .
                                        ($fgetvote['intern'] ? " `intern` = 1," : " `intern` = " . (int)($validated_vote_data['intern']) . ",") . " `forum`  = 1, `von` = ?",
                                        [time(), stringParser::encode(strip_tags($validated_vote_data['question'])), $get['t_reg']]);
                                    unset($fgetvote);

                                    $get['vote'] = common::$sql['default']->lastInsertId();
                                }
                                unset($fgetvote);

                                //Loop answers
                                $answers = [];
                                for ($x = 1; $x <= 10; $x++) {
                                    if ($get['vote'] >= 1) {
                                        $answers[] = ['answer' => common::voteanswer("a" . $x, $get['vote']), 'key' => 'answer_' . $x];
                                    } else {
                                        $answers[] = ['answer' => isset($_POST['answer_' . $x]) ? $_POST['answer_' . $x] : '', 'key' => 'answer_' . $x];
                                    }
                                }

                                //Insert/Update/Delete vote answers
                                $i = 1;
                                foreach ($answers as $answer) {
                                    if (isset($validated_vote_data[$answer['key']]) && !empty($validated_vote_data[$answer['key']]) && $get['vote'] >= 1) {
                                        if (common::$sql['default']->rows("SELECT `id` FROM `{prefix_vote_results}` WHERE `vid` = ? AND `what` = ?;", [$get['vote'], 'a' . $i])) {
                                            common::$sql['default']->update("UPDATE `{prefix_vote_results}` SET `sel` = ? WHERE `vid` = ? AND `what` = ?;",
                                                [stringParser::encode(strip_tags($validated_vote_data[$answer['key']])), $get['vote'], 'a' . $i]);
                                        } else {
                                            common::$sql['default']->insert("INSERT INTO `{prefix_vote_results}` SET `vid` = ?, `what` = ?, `sel` = ?;",
                                                [$get['vote'], 'a' . $i, stringParser::encode(strip_tags($validated_vote_data[$answer['key']]))]);
                                        }
                                    } else if ($get['vote'] >= 1) {
                                        if (common::$sql['default']->rows("SELECT `id` FROM `{prefix_vote_results}` WHERE `vid` = ? AND `what` = ?;", [$get['vote'], 'a' . $i])) {
                                            common::$sql['default']->delete("DELETE FROM `{prefix_vote_results}` WHERE `vid` = ? AND `what` = ?;",
                                                [$get['vote'], 'a' . $i]);
                                        }
                                    }

                                    $i++;
                                }

                                foreach ($answers as $answer) {
                                    unset($validated_post_data[$answer['key']]);
                                }

                                unset($validated_post_data['question'], $validated_post_data['intern']);
                                unset($i, $answer, $validated_vote_data);
                            }
                        }//end vote

                        //Editby Text
                        $editedby = "";
                        if (array_key_exists('editby', $validated_vote_data)) {
                            $smarty->caching = false;
                            $smarty->assign('autor', common::autor(common::$userid));
                            $smarty->assign('time', date("d.m.Y H:i", time()));
                            $editedby = $smarty->fetch('string:' . _edited_by);
                            $smarty->clearAllAssign();
                        }

                        $validated_post_data['sticky'] = array_key_exists('sticky', $validated_post_data) ? $validated_post_data['sticky'] : 0;
                        $validated_post_data['global'] = array_key_exists('global', $validated_post_data) ? $validated_post_data['global'] : 0;
                        $validated_post_data['subtopic'] = array_key_exists('subtopic', $validated_post_data) ? $validated_post_data['subtopic'] : '';

                        //Update thread
                        common::$sql['default']->update("UPDATE `{prefix_forum_threads}` SET `topic` = ?, `subtopic` = ?, `t_text` = ?," .
                            "`sticky` = ?, `global` = ?, `vote` = ?, `edited` = ? WHERE `id` = ?;",
                            [stringParser::encode($validated_post_data['topic']), stringParser::encode($validated_post_data['subtopic']),
                                stringParser::encode($validated_post_data['eintrag']), $validated_post_data['sticky'], $validated_post_data['global'], $get['vote'],
                                stringParser::encode($editedby), $get['id']]);

                        send_forum_abo(true, $get['id'], $validated_post_data['eintrag']);

                        $index = common::info(_forum_editthread_successful, "?action=showthread&amp;id=" . $get['id']);
                        unset($validated_post_data);
                    } else {
                        DebugConsole::insert_info('forum/case_thread.php', common::$gump->get_readable_errors(true));
                        //Errors
                        if (!isset($_POST['eintrag']) || empty($_POST['eintrag'])) {
                            notification::add_error(_empty_eintrag);
                        } else {
                            notification::add_error(_empty_topic);
                        }
                    }
                }

                /*
                 * ########################################################
                 * EDITOR
                 * ########################################################
                 */

                if (empty($index)) {
                    $get = common::$sql['default']->fetch("SELECT * FROM `{prefix_forum_threads}` WHERE `id` = ?;", [(int)($_GET['id'])]);
                    if ($get['t_reg'] == common::$userid || common::permission("forum")) {
                        //Admin Options
                        $admin = "";
                        if (common::permission("forum")) {
                            $smarty->caching = false;
                            $smarty->assign('is_sticky', $get['sticky'] ? ' checked' : '');
                            $smarty->assign('is_global', $get['global'] ? ' checked' : '');
                            $smarty->assign('is_editby', common::permission('editby'));
                            $admin = $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/admin/form_admin.tpl');
                            $smarty->clearAllAssign();
                        }

                        $getv = common::$sql['default']->fetch("SELECT `id`,`closed`,`titel` FROM `{prefix_votes}` WHERE `id` = ?;", [$get['vote']]);
                        $fget = common::$sql['default']->fetch("SELECT s1.`intern`,s2.`id` FROM `{prefix_forum_kats}` AS s1 LEFT JOIN `{prefix_forum_sub_kats}` AS s2 ON s2.`sid` = s1.`id` WHERE s2.`id` = ?;", [$get['kid']]);

                        $vote = '';
                        if(is_array($getv)) {
                            //Loop answers
                            $answers = [];
                            for ($x = 1; $x <= 10; $x++) {
                                if ($getv['id'] >= 1) {
                                    $answers[] = ['answer' => common::voteanswer("a" . $x, $getv['id']), 'key' => 'answer_' . $x];
                                } else {
                                    $answers[] = ['answer' => isset($_POST['answer_' . $x]) ? $_POST['answer_' . $x] : '', 'key' => 'answer_' . $x];
                                }
                            }

                            //Forum Vote Form
                            $smarty->caching = false;
                            $smarty->assign('edit', 1);
                            $smarty->assign('forum_answer', fvote_question($answers));
                            $smarty->assign('closed', ($getv['closed'] ? 'checked="checked"' : ''));
                            $smarty->assign('question', stringParser::decode($getv['titel']));
                            $smarty->assign('expand', $getv['id'] && !$getv['closed'] ? 'collapse' : 'expand');
                            $smarty->assign('br1', $getv['id'] ? "" : "<!--");
                            $smarty->assign('br2', $getv['id'] ? "" : "-->");
                            $smarty->assign('display', !empty($getv['titel']) && !$getv['closed'] ? '' : 'none');
                            $smarty->assign('intern_kat', ($fget['intern'] && !$getv['closed'] ? 'style="display:none"' : ''));
                            $smarty->assign('intern', ($fget['intern'] ? 'checked="checked"' : ''));
                            $vote = $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/vote/form_vote.tpl');
                            $smarty->clearAllAssign();
                        }

                        //Forum thread
                        $smarty->caching = false;
                        $smarty->assign('is_edit', true);
                        $smarty->assign('kid', $_SESSION['kid']);
                        $smarty->assign('id', (int)($_GET['id']));
                        $smarty->assign('form', common::editor_is_reg($get));
                        $smarty->assign('posttopic', stringParser::decode($get['topic']));
                        $smarty->assign('postsubtopic', stringParser::decode($get['subtopic']));
                        $smarty->assign('admin', $admin);
                        $smarty->assign('vote', $vote);
                        $smarty->assign('posteintrag', stringParser::decode($get['t_text']));
                        $smarty->assign('notification', notification::get('global', true));
                        $index = $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/thread.tpl');
                        $smarty->clearAllAssign();
                    } else {
                        $index = common::error(_error_wrong_permissions);
                    }
                }
                break;
            case 'add':
                //Loop answers
                $answers = [];
                for ($x = 1; $x <= 10; $x++) {
                    $answers[] = ['answer' => isset($_POST['answer_' . $x]) ? $_POST['answer_' . $x] : '', 'key' => 'answer_' . $x];
                }

                /*
                 * ########################################################
                 * POST
                 * ########################################################
                 */
                if (array_key_exists('eintrag', $_POST)) {
                    //validation
                    common::$gump->validation_rules([
                        'topic' => 'required|alpha_numeric|max_len,150|min_len,1',
                        'eintrag' => 'required|min_len,1'
                    ]);

                    //filter
                    common::$gump->filter_rules([
                        'topic' => 'trim',
                        'subtopic' => 'trim',
                        'sticky' => 'sanitize_numbers',
                        'global' => 'sanitize_numbers',
                        'eintrag' => 'trim'
                    ]);

                    $validated_post_data = common::$gump->run($_POST);
                    if ($validated_post_data !== false) {
                        //#####################
                        //Insert Forum-Vote
                        //#####################
                        $vid = 0; //Set 0 for forum votes
                        common::$gump->validation_rules([
                            'question' => 'required|alpha_numeric|min_len,1',
                            'answer_1' => 'required|alpha_numeric|min_len,1',
                            'answer_2' => 'required|alpha_numeric|min_len,1'
                        ]);

                        $validated_vote_data = common::$gump->run($validated_post_data);
                        if ($validated_vote_data !== false) {
                            $fgetvote = common::$sql['default']->fetch("SELECT s1.`intern`,s2.`id` "
                                . "FROM `{prefix_forum_kats}` AS s1 "
                                . "LEFT JOIN `{prefix_forum_sub_kats}` AS s2 "
                                . "ON s2.`sid` = s1.`id` "
                                . "WHERE s2.`id` = ?;", [$_SESSION['kid']]);

                            common::$sql['default']->insert("INSERT INTO `{prefix_votes}` SET `datum`  = ?, `titel`  = ?," .
                                ($fgetvote['intern'] ? " `intern` = 1," : " `intern` = " . (int)($validated_vote_data['intern']) . ",") . " `forum`  = 1, `von` = ?",
                                [time(), stringParser::encode(strip_tags($validated_vote_data['question'])), common::$userid]);
                            unset($fgetvote);

                            $vid = common::$sql['default']->lastInsertId();

                            //Insert vote answers
                            $i = 1;
                            foreach ($answers as $answer) {
                                if (isset($validated_vote_data[$answer['key']]) && !empty($validated_vote_data[$answer['key']]) && $vid >= 1) {
                                    common::$sql['default']->insert("INSERT INTO `{prefix_vote_results}` SET `vid` = ?, `what` = ?, `sel` = ?;",
                                        [(int)($vid), 'a' . $i, stringParser::encode(strip_tags($validated_vote_data[$answer['key']]))]);
                                    $i++;
                                }
                            }

                            foreach ($answers as $answer) {
                                unset($validated_post_data[$answer['key']]);
                            }

                            unset($validated_post_data['question'], $validated_post_data['intern']);
                            unset($i, $answer, $validated_vote_data);
                        } //end vote

                        $validated_post_data['sticky'] = array_key_exists('sticky', $validated_post_data) ? $validated_post_data['sticky'] : 0;
                        $validated_post_data['global'] = array_key_exists('global', $validated_post_data) ? $validated_post_data['global'] : 0;
                        $validated_post_data['subtopic'] = array_key_exists('subtopic', $validated_post_data) ? $validated_post_data['subtopic'] : '';

                        //Insert thread
                        common::$sql['default']->insert("INSERT INTO `{prefix_forum_threads}` SET `kid` = ?, `t_date` = ?,`topic` = ?, `subtopic` = ?, `t_reg` = ?, `t_text` = ?," .
                            "`sticky` = ?, `global` = ?, `ipv4` = ?, `vote` = ?, `first` = 1;",
                            [$_SESSION['kid'], time(), stringParser::encode($validated_post_data['topic']), stringParser::encode($validated_post_data['subtopic']),
                                common::$userid, stringParser::encode($validated_post_data['eintrag']), $validated_post_data['sticky'], $validated_post_data['global'],
                                common::$userip['v4'], $vid]);

                        $thisFID = common::$sql['default']->lastInsertId(); //Get new thread-id
                        common::setIpcheck("fid(" . $_SESSION['kid'] . ")");
                        common::userstats_increase('forumthreads');

                        $index = common::info(_forum_newthread_successful, "?action=showthread&amp;id=" . $thisFID . "#p1");
                        unset($thisFID, $validated_post_data);
                    } else {
                        DebugConsole::insert_info('forum/case_thread.php', common::$gump->get_readable_errors(true));
                        //Errors
                        if (!isset($_POST['eintrag']) || empty($_POST['eintrag'])) {
                            notification::add_error(_empty_eintrag);
                        } else {
                            notification::add_error(_empty_topic);
                        }
                    }
                }

                /*
                 * ########################################################
                 * EDITOR
                 * ########################################################
                 */

                if (empty($index)) {
                    if (!common::ipcheck("fid(" . $_SESSION['kid'] . ")", settings::get('f_forum'))) {
                        //Admin Options
                        $admin = "";
                        if (common::permission("forum")) {
                            $smarty->caching = false;
                            $smarty->assign('is_sticky', isset($_POST['sticky']) ? ' checked' : '');
                            $smarty->assign('is_global', isset($_POST['global']) ? ' checked' : '');
                            $smarty->assign('is_editby', false);
                            $admin = $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/admin/form_admin.tpl');
                            $smarty->clearAllAssign();
                        }

                        //Forum Vote Public/Intern
                        $fget = common::$sql['default']->fetch("SELECT s1.intern,s2.id FROM `{prefix_forum_kats}` AS s1 LEFT JOIN `{prefix_forum_sub_kats}` AS s2 ON s2.`sid` = s1.id WHERE s2.`id` = ?;", [$_SESSION['kid']]);

                        //Forum Vote Form
                        $smarty->caching = false;
                        $smarty->assign('edit', 0);
                        $smarty->assign('forum_answer', fvote_question($answers));
                        $smarty->assign('closed', (isset($_POST['closed']) ? 'checked="checked"' : ''));
                        $smarty->assign('question', stringParser::decode((isset($_POST['question']) && !empty($_POST['question']) ? $_POST['question'] : '')));
                        $smarty->assign('expand', 'expand');
                        $smarty->assign('br1', "<!--");
                        $smarty->assign('br2', "-->");
                        $smarty->assign('display', (isset($_POST['question']) && !empty($_POST['question']) ? '' : 'none'));
                        $smarty->assign('intern_kat', ($fget['intern'] ? 'style="display:none"' : ''));
                        $smarty->assign('intern', ($fget['intern'] || isset($_POST['intern']) ? 'checked="checked"' : ''));
                        $vote = $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/vote/form_vote.tpl');
                        $smarty->clearAllAssign();

                        //Forum thread
                        $smarty->caching = false;
                        $smarty->assign('is_edit', false);
                        $smarty->assign('id', 0);
                        $smarty->assign('kid', $_SESSION['kid']);
                        $smarty->assign('form', common::editor_is_reg(['reg' => common::$userid]));
                        $smarty->assign('posttopic', isset($_POST['topic']) ? $_POST['topic'] : '');
                        $smarty->assign('postsubtopic', isset($_POST['subtopic']) ? $_POST['subtopic'] : '');
                        $smarty->assign('admin', $admin);
                        $smarty->assign('vote', $vote);
                        $smarty->assign('posteintrag', isset($_POST['eintrag']) ? $_POST['eintrag'] : '');
                        $smarty->assign('notification', notification::get('global', true));
                        $index = $smarty->fetch('file:[' . common::$tmpdir . ']' . $dir . '/thread.tpl');
                        $smarty->clearAllAssign();
                    } else {
                        //flood error
                        $smarty->caching = false;
                        $smarty->assign('sek', settings::get('f_forum'));
                        $error = $smarty->fetch('string:' . _error_flood_post);
                        $smarty->clearAllAssign();
                        $index = common::error($error);
                        unset($error);
                    }
                }
                break;
        }
    } else {
        $index = common::error(_error_have_to_be_logged);
    }
}