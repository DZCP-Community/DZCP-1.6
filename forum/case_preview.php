<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if (defined('_Forum')) {
    header("Content-type: text/html; charset=utf-8");
    if ($_GET['what'] == 'thread') {
        if ($do == 'editthread') {
            $qry = db("SELECT * FROM " . $db['f_threads'] . " WHERE id = '" . (int)($_GET['id']) . "'");
            $get = _fetch($qry);

            $get_datum = $get['t_date'];

            if ($get['t_reg'] == 0) $guestCheck = false;
            else {
                $guestCheck = true;
                $pUId = $get['t_reg'];
            }
            $editedby = show(_edited_by, array("autor" => cleanautor($userid),
                "time" => date("d.m.Y H:i", time()) . _uhr));
            $tID = $get['id'];
        } else {
            $get_datum = time();

            if (!$chkMe) $guestCheck = false;
            else {
                $guestCheck = true;
                $pUId = $userid;
            }
            $tID = $_GET['kid'];
        }

        $titel = show(_eintrag_titel_forum, array("postid" => "1",
            "datum" => date("d.m.Y", $get_datum),
            "zeit" => date("H:i", $get_datum) . _uhr,
            "url" => '#',
            "edit" => "",
            "delete" => ""));
        if ($guestCheck) {
            $qryu = db("SELECT nick,hp,email FROM " . $db['users'] . "
                  WHERE id = '" . $pUId . "'");
            $getu = _fetch($qryu);

            $email = show(_emailicon_forum, array("email" => eMailAddr(re($getu['email']))));
            $pn = _forum_pn_preview;

            if (empty($getu['hp'])) $hp = "";
            else $hp = show(_hpicon_forum, array("hp" => $getu['hp']));
            if (data("signatur", $pUId)) $sig = _sig . bbcode(data("signatur", $pUId));
            else $sig = "";
            $onoff = onlinecheck($userid);
            $userposts = show(_forum_user_posts, array("posts" => (userstats("forumposts", $pUId) + 1)));
        } else {
            $pn = "";
            $email = show(_emailicon_forum, array("email" => eMailAddr(re($_POST['email'], true))));
            if (empty($_POST['hp'])) $hp = "";
            else $hp = show(_hpicon_forum, array("hp" => links(re($_POST['hp'], true))));
        }


        $qryw = db("SELECT s1.kid,s1.topic,s2.kattopic,s2.sid
                FROM " . $db['f_threads'] . " AS s1
                LEFT JOIN " . $db['f_skats'] . " AS s2
                ON s1.kid = s2.id
                WHERE s1.id = '" . (int)($tID) . "'");
        $getw = _fetch($qryw);

        $qrykat = db("SELECT name FROM " . $db['f_kats'] . "
                  WHERE id = '" . $getw['sid'] . "'");
        $kat = _fetch($qrykat);

        $wheres = show(_forum_post_where_preview, array("wherepost" => re($_POST['topic']),
            "wherekat" => re($getw['kattopic']),
            "mainkat" => re($kat['name']),
            "tid" => $_GET['id'],
            "kid" => $getw['kid']));

        if (empty($get['vote'])) $vote = "";
        else $vote = '<tr><td>' . fvote($get['vote']) . '</td></tr>';

        if (!empty($_POST['question '])) $vote = _forum_vote_preview;
        else $vote = "";

        $index = show($dir . "/forum_posts", array("head" => _forum_head,
            "where" => $wheres,
            "admin" => "",
            "class" => 'class="commentsRight"',
            "nick" => cleanautor($pUId, '', re($_POST['nick'], true), re($_POST['email'], true)),
            "threadhead" => re($_POST['topic']),
            "titel" => $titel,
            "postnr" => "1",
            "pn" => $pn,
            "hp" => $hp,
            "email" => $email,
            "posts" => $userposts,
            "text" => bbcode(re($_POST['eintrag'], true), true) . $editedby,
            "status" => getrank($pUId),
            "avatar" => useravatar($pUId),
            "edited" => $get['edited'],
            "signatur" => $sig,
            "date" => _posted_by . date("d.m.y H:i", time()) . _uhr,
            "zitat" => _forum_zitat_preview,
            "onoff" => $onoff,
            "ip" => $userip . '<br />' . _only_for_admins,
            "top" => _topicon,
            "lpost" => $lpost,
            "lp" => "",
            "add" => "",
            "nav" => "",
            "vote" => $vote,
            "f_abo" => "",
            "show" => $show));

        echo utf8_encode('<table class="mainContent" cellspacing="1" style="margin-top:17px">' . $index . '</table>');

        if (!mysqli_persistconns)
            $mysql->close(); //MySQL

        exit();
    } else {
        if ($do == 'editpost') {
            $qry = db("SELECT * FROM " . $db['f_posts'] . "
                 WHERE id = '" . (int)($_GET['id']) . "'");
            $get = _fetch($qry);

            $get_datum = $get['date'];

            if ($get['reg'] == 0) $guestCheck = false;
            else {
                $guestCheck = true;
                $pUId = $get['reg'];
            }
            $editedby = show(_edited_by, array("autor" => cleanautor($userid),
                "time" => date("d.m.Y H:i", time()) . _uhr));
            $tID = $get['sid'];
            $cnt = "?";
        } else {
            $get_datum = time();

            if (!$chkMe) $guestCheck = false;
            else {
                $guestCheck = true;
                $pUId = $userid;
            }
            $tID = $_GET['id'];
            $cnt = cnt($db['f_posts'], " WHERE sid = '" . (int)($_GET['id']) . "'") + 2;
        }

        $titel = show(_eintrag_titel_forum, array("postid" => $cnt,
            "datum" => date("d.m.Y", $get_datum),
            "zeit" => date("H:i", $get_datum) . _uhr,
            "url" => '#',
            "edit" => "",
            "delete" => ""));
        if ($guestCheck) {
            $qryu = db("SELECT nick,hp,email FROM " . $db['users'] . "
                  WHERE id = '" . (int)($pUId) . "'");
            $getu = _fetch($qryu);

            $email = show(_emailicon_forum, array("email" => eMailAddr(re($getu['email']))));
            $pn = _forum_pn_preview;

            if (empty($getu['hp'])) $hp = "";
            else $hp = show(_hpicon_forum, array("hp" => $getu['hp']));
            if (data("signatur", $pUId)) $sig = _sig . bbcode(data("signatur", $pUId));
            else $sig = "";
        } else {
            $pn = "";
            $email = show(_emailicon_forum, array("email" => eMailAddr(re($_POST['email'], true))));
            if (empty($_POST['hp'])) $hp = "";
            else $hp = show(_hpicon_forum, array("hp" => links(re($_POST['hp'], true))));
        }

        $index = show($dir . "/forum_posts_show", array(
            "nick" => cleanautor((int)$pUId, '', re($_POST['nick'], true), re($_POST['email'], true)),
            "postnr" => "#" . ($i + ($page - 1) * config('m_fposts')),
            "p" => ($i + ($page - 1) * config('m_fposts')),
            "class" => 'class="commentsRight"',
            "text" => bbcode(up($_POST['eintrag'])) . $editedby,
            "pn" => $pn,
            "hp" => $hp,
            "email" => $email,
            "status" => getrank($pUId),
            "avatar" => useravatar($pUId),
            "ip" => $userip . '<br />' . _only_for_admins,
            "edited" => "",
            "posts" => $userposts,
            "titel" => $titel,
            "signatur" => $sig,
            "zitat" => _forum_zitat_preview,
            "onoff" => $onoff));

        echo utf8_encode('<table class="mainContent" cellspacing="1" style="margin-top:17px">' . $index . '</table>');

        if (!mysqli_persistconns)
            $mysql->close(); //MySQL

        exit();
    }
}