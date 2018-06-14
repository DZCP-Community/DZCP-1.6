<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(defined('_Forum')) {
    if(permission("forum"))
    {
        if($do == "mod")
        {
            if(isset($_POST['delete']))
            {
                $qryv = db("SELECT * FROM ".$db['f_threads']."
                    WHERE id = '".(int)($_GET['id'])."'");
                $getv = _fetch($qryv);

                $userPostReduction = array();
                $userPostReduction[$getv['t_reg']] = 1;

                if(!empty($getv['vote']))
                {
                    $delvote = db("DELETE FROM ".$db['votes']."
                       WHERE id = '".$getv['vote']."'");

                    $delvr = db("DELETE FROM ".$db['vote_results']."
                     WHERE vid = '".$getv['vote']."'");

                    setIpcheck("vid_".$getv['vote'],false);
                }
                $del = db("DELETE FROM ".$db['f_threads']."
                   WHERE id = '".(int)($_GET['id'])."'");

                // grab user to reduce post count
                $tmpSid = (int)($_GET['id']);
                $userPosts = db('SELECT p.`reg` FROM ' . $db['f_posts'] . ' p WHERE sid = ' . $tmpSid . ' AND p.`reg` != 0');
                while($get = _fetch($userPosts)) {
                    if(!isset($userPostReduction[$get['reg']])) {
                        $userPostReduction[$get['reg']] = 1;
                    } else {
                        $userPostReduction[$get['reg']] = $userPostReduction[$get['reg']] + 1;
                    }
                }
                foreach($userPostReduction as $key_id => $value_postDecrement) {
                    db('UPDATE ' . $db['userstats'] .
                        ' SET `forumposts` = `forumposts` - '. $value_postDecrement .
                        ' WHERE user = ' . $key_id);
                }
                db("DELETE FROM ".$db['f_posts']." WHERE sid = '" . $tmpSid . "'");
                db("DELETE FROM ".$db['f_abo']." WHERE fid = '".(int)($_GET['id'])."'");
                $index = info(_forum_admin_thread_deleted, "../forum/");
            } else {
                if(!$_POST['closed']) {
                    db("UPDATE `".$db['f_threads']."` SET `closed` = 0 WHERE id = ".(int)($_GET['id']).";");
                } elseif($_POST['closed']) {
                    db("UPDATE `".$db['f_threads']."` SET `closed` = 1 WHERE id = ".(int)($_GET['id']).";");
                }

                if(isset($_POST['sticky'])) {
                    db("UPDATE `".$db['f_threads']."` SET `sticky` = 1 WHERE `id` = ".(int)($_GET['id']).";");
                } else {
                    db("UPDATE `".$db['f_threads']."` SET `sticky` = 0 WHERE `id` = ".(int)($_GET['id']).";");
                }

                if(isset($_POST['global'])) {
                    db("UPDATE `".$db['f_threads']."` SET `global` = 1 WHERE id = ".(int)($_GET['id']).";");
                } else {
                    db("UPDATE `".$db['f_threads']."` SET `global` = 0 WHERE id = ".(int)($_GET['id']).";");
                }

                if($_POST['move'] == "lazy") {
                    $index = info(_forum_admin_modded, "?action=showthread&amp;id=".$_GET['id']."");
                } else {
                    db("UPDATE `".$db['f_threads']."` SET `kid` = ".((int)$_POST['move'])." WHERE id = ".(int)($_GET['id']).";");
                    db("UPDATE `".$db['f_posts']."` SET `kid` = '".((int)$_POST['move'])."' WHERE sid = ".(int)($_GET['id']).";");

                    $getm = db("SELECT s1.`kid`,s2.`kattopic`,s2.`id` FROM `".$db['f_threads'].
                        "` AS s1 LEFT JOIN `".$db['f_skats']."` AS s2 ON s1.`kid` = s2.`id` WHERE s1.`id` = "
                        .(int)($_GET['id']).";",false,true);

                    $i_move = show(_forum_admin_do_move, array("kat" => re($getm['kattopic'])));
                    $index = info($i_move, "?action=showthread&amp;id=".$_GET['id']."");
                }
            }
        }
    } else {
        $index = error(_error_wrong_permissions, 1);
    }
}
