<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if (defined('_News')) {
    header("Content-type: text/html; charset=utf-8");
    if ($do == 'edit') {
        $get = db("SELECT * FROM `" . $db['newscomments'] . "` WHERE `id` = " . (int)($_GET['cid']) . ";", false, true);
        $get_id = '?';
        $get_userid = $get['reg'];
        $get_date = $get['datum'];
        $regCheck = !$get['reg'] ? false : true;
        $editedby = show(_edited_by, array("autor" => cleanautor($userid), "time" => date("d.m.Y H:i", time()) . _uhr));
    } else {
        $get_id = cnt($db['newscomments'], " WHERE `news` = " . (int)($_GET['id'])) + 1;
        $get_userid = $userid;
        $get_date = time();
        $regCheck = $chkMe >= 1 ? true : false;
        $editedby = '';
    }

    $email = "";
    $hp = "";
    if (!$regCheck) {
        $get_hp = isset($_POST['hp']) ? re($_POST['hp'], true) : '';
        $get_email = isset($_POST['email']) ? re($_POST['email'], true) : '';
        $get_nick = isset($_POST['nick']) ? re($_POST['nick'], true) : '';

        if (!empty($get_hp))
            $hp = show(_hpicon_forum, array("hp" => links(re($get_hp, true))));

        if (!empty($get_email))
            $email = '<br />' . show(_emailicon_forum, array("email" => eMailAddr($get_email)));

        $onoff = "";
        $avatar = "";
        $nick = show(_link_mailto, array("nick" => re($get_nick), "email" => $get_email));
    } else {
        $onoff = onlinecheck((int)$get_userid);
        $nick = cleanautor((int)$get_userid);
    }

    $titel = show(_eintrag_titel, array("postid" => $get_id,
        "datum" => date("d.m.Y", $get_date),
        "zeit" => date("H:i", $get_date) . _uhr,
        "edit" => '',
        "delete" => ''));

    $email = ($chkMe >= 1 ? $email : '');
    $index = show("page/comments_show", array("titel" => $titel,
        "comment" => bbcode(re($_POST['comment'], true), true),
        "nick" => $nick,
        "editby" => bbcode($editedby, true),
        "email" => $email,
        "hp" => $hp,
        "avatar" => useravatar($get_userid),
        "onoff" => $onoff,
        "rank" => getrank($get_userid),
        "ip" => $userip . _only_for_admins));

    echo utf8_encode('<table class="mainContent" cellspacing="1">' . $index . '</table>');

    if (!mysqli_persistconns)
        $mysql->close(); //MySQL

    exit();
}