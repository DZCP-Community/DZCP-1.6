<?php
/////////// ADMINNAVI \\\\\\\\\
// Typ:       contentmenu
// Rechte:    permission('serverliste')
///////////////////////////////
if(_adminMenu != 'true') exit;

    $where = $where.': '._slist_head_admin;
    if(!permission("serverliste"))
    {
      $show = error(_error_wrong_permissions, 1);
    } else {
      $qry = db("SELECT id,ip,port,clanname,clanurl,pwd,checked
                 FROM ".$db['serverliste']."");

      while ($get = _fetch($qry))
      {
        if($get['checked'] == '1')
        {
          $selected = "selected=\"selected\"";
        } else {
          $selected = "";
        }
        $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                          "action" => "admin=serverlist&amp;do=delete",
                                                          "title" => _button_title_del,
                                                          "del" => convSpace(_confirm_del_server)));

        if(empty($get['clanurl']))
        {
          $clanname = show(_slist_clanname_without_url, array("name" => re($get['clanname'])));
        } else {
          $clanname = show(_slist_clanname_with_url, array("name" => re($get['clanname']),
                                                           "url" => re($get['clanurl'])));
        }
        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
        $show_ .= show($dir."/slist_show", array("id" => $get['id'],
                                                 "clanname" => $clanname,
                                                 "serverip" => re($get['ip']),
                                                 "serverpwd" => re($get['pwd']),
                                                 "class" => $class,
                                                 "delete" => $delete,
                                                 "selected" => $selected,
                                                 "check" => $get['check'],
                                                 "serverport" => $get['port']));
      }

      $show = show($dir."/slist", array("show" => $show_,
                                        "slisthead" => _slist_head_admin,
                                        "clan" => _profil_clan,
                                        "delete" => _deleteicon_blank,
                                        "serverip" => _slist_serverip));
    }
    if($_GET['do'] == "accept")
    {
      $qry = db("UPDATE ".$db['serverliste']."
                 SET `checked` = '".((int)$_POST['checked'])."'
                 WHERE id = '".intval($_POST['id'])."'");

      if($_POST['checked'] == "1") $show = info(_error_server_accept, "?admin=serverlist");
      else $show = info(_error_server_dont_accept, "?admin=serverlist");

    } elseif($_GET['do'] == "delete") {
      $qry = db("DELETE FROM ".$db['serverliste']."
                 WHERE id = '".intval($_GET['id'])."'");

      $show = info(_slist_server_deleted, "?admin=serverlist");
    }
?>