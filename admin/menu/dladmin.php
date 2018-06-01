<?php
/////////// ADMINNAVI \\\\\\\\\
// Typ:       contentmenu
// Rechte:    permission('downloads')
///////////////////////////////
if(_adminMenu != 'true') exit;

    $where = $where.': '._dl;
    if(!permission("downloads"))
    {
      $show = error(_error_wrong_permissions, 1);
    } else {
      if($_GET['do'] == "new")
      {
        $qry = db("SELECT s1.*,s2.name AS kat FROM ".$sql_prefix."dl_subkat AS s1
                   LEFT JOIN ".$db['dl_kat']." AS s2
                   ON s1.kid = s2.id
                   ORDER BY s2.name,s1.name");
        while($get = _fetch($qry))
        {
          $kats .= show(_select_field, array("value" => $get['id'],
                                             "what" => re($get['kat']).' | '.re($get['name']),
                                             "sel" => ""));
        }

        $files = get_files('../downloads/files/');
        for($i=0; $i<count($files); $i++)
        {
          $dl .= show(_downloads_files_exists, array("dl" => $files[$i],
                                                     "sel" => ""));
        }

        $show = show($dir."/form_dl", array("admin_head" => _downloads_admin_head,
                                            "ddownload" => "",
                                             "durl" => "",
                                             "oder" => _or,
                                             "lang" => $language,
                                             "file" => $dl,
																						 "dsize" => "",
																						 "size" => _dl_file_size,
                                             "nothing" => "",
																						 "interna" => _dl_admin_intern,
																						 "no" => _no,
																						 "ruser" => _status." "._status_user,
																						 "trial" => _status." "._status_trial,
																						 "member" => _status." "._status_member,
																						 "admin" => _status." "._status_admin,
																						 "selt" => "",
                                             "selm" => "",
					     															 "selu" => "",
                                             "sela" => "",
                                             "nofile" => _downloads_nofile,
                                             "lokal" => _downloads_lokal,
                                             "what" => _button_value_add,
                                             "do" => "add",
                                             "exist" => _downloads_exist,
                                             "dbeschreibung" => "",
                                             "kat" => _downloads_kat,
                                             "kats" => $kats,
                                             "url" => _downloads_url,
                                             "beschreibung" => _beschreibung,
                                             "download" => _downloads_name));
      } elseif($_GET['do'] == "add") {
        if(empty($_POST['download']) || empty($_POST['url']))
        {
          if(empty($_POST['download'])) $show = error(_downloads_empty_download, 1);
          elseif(empty($_POST['url']))  $show = error(_downloads_empty_url, 1);
        } else {
          
          if(preg_match("#^www#i",$_POST['url'])) $dl = links($_POST['url']);
          else                                    $dl = up($_POST['url']);

          $qry = db("INSERT INTO ".$db['downloads']."
                     SET `download`     = '".up($_POST['download'])."',
                         `url`          = '".$dl."',
                         `size`         = '".str_replace(',','.',up($_POST['size']))."',
												 `date`         = '".((int)time())."',
												 `intern`       = '".((int)$_POST['intern'])."',
                         `beschreibung` = '".up($_POST['beschreibung'],1)."',
                         `kat`          = '".((int)$_POST['kat'])."'");

          $show = info(_downloads_added, "?admin=dladmin");
        }
      } elseif($_GET['do'] == "edit") {
        $qry  = db("SELECT * FROM ".$db['downloads']."
                    WHERE id = '".intval($_GET['id'])."'");
        $get = _fetch($qry);

        $qryk = db("SELECT s1.*,s2.name AS kat FROM ".$sql_prefix."dl_subkat AS s1
                   LEFT JOIN ".$db['dl_kat']." AS s2
                   ON s1.kid = s2.id
                   ORDER BY s2.name,s1.name");
        while($getk = _fetch($qryk))
        {
          if($getk['id'] == $get['kat']) $sel = "selected=\"selected\"";
          else $sel = "";

          $kats .= show(_select_field, array("value" => $getk['id'],
                                             "what" => re($getk['kat']).' | '.re($getk['name']),
                                             "sel" => $sel));
        }

				if($get['intern'] == 0)     $sel  = "selected=\"selected\"";
				elseif($get['intern'] == 1) $selu = "selected=\"selected\"";
        elseif($get['intern'] == 2) $selt = "selected=\"selected\"";
        elseif($get['intern'] == 3) $selm = "selected=\"selected\"";
        elseif($get['intern'] == 4) $sela = "selected=\"selected\"";

        $show = show($dir."/form_dl", array("admin_head" => _downloads_admin_head_edit,
                                            "ddownload" => re($get['download']),
                                            "durl" => re($get['url']),
                                            "file" => $dl,
																						"dsize" => $get['size'],
																						"size" => _dl_file_size,
                                            "lokal" => _downloads_lokal,
                                            "exist" => _downloads_exist,
                                            "nothing" => _nothing,
																						"interna" => _dl_admin_intern,
																						"no" => _no,
																						"ruser" => _status." "._status_user,
																						"trial" => _status." "._status_trial,
																						"member" => _status." "._status_member,
																						"admin" => _status." "._status_admin,
																						"sel" => $sel,
																						"selt" => $selt,
                                            "selm" => $selm,
					    															"selu" => $selu,
                                            "sela" => $sela,
                                            "nofile" => _downloads_nofile,
                                            "oder" => _or,
                                            "dbeschreibung" => re_bbcode($get['beschreibung']),
                                            "kat" => _downloads_kat,
                                            "what" => _button_value_edit,
                                            "do" => "editdl&amp;id=".$_GET['id']."",
                                            "kats" => $kats,
                                            "url" => _downloads_url,
                                            "beschreibung" => _beschreibung,
                                            "download" => _downloads_name));
      } elseif($_GET['do'] == "editdl") {
        if(empty($_POST['download']) || empty($_POST['url']))
        {
          if(empty($_POST['download'])) $show = error(_downloads_empty_download, 1);
          elseif(empty($_POST['url']))  $show = error(_downloads_empty_url, 1);
        } else {
          if(preg_match("#^www#i",$_POST['url'])) $dl = links($_POST['url']);
          else                                    $dl = up($_POST['url']);

          $qry = db("UPDATE ".$db['downloads']."
                     SET `download`     = '".up($_POST['download'])."',
                         `url`          = '".$dl."',
                         `beschreibung` = '".up($_POST['beschreibung'],1)."',
						 						 `intern`       = '".((int)$_POST['intern'])."',
                         `size`         = '".str_replace(',','.',up($_POST['size']))."',
												 `date`         = '".((int)time())."',
                         `kat`          = '".((int)$_POST['kat'])."'
                     WHERE id = '".intval($_GET['id'])."'");

          $show = info(_downloads_edited, "?admin=dladmin");
        }
      } elseif($_GET['do'] == "delete") {
        $qry = db("DELETE FROM ".$db['downloads']."
                   WHERE id = '".intval($_GET['id'])."'");

        $show = info(_downloads_deleted, "?admin=dladmin");
      } elseif($_GET['do'] == 'top') {
        if($_GET['what'] == 'set')
        {
          $upd = db("UPDATE ".$db['downloads']."
                     SET `top` = '1'
                     WHERE id = '".intval($_GET['id'])."'");
        } elseif($_GET['what'] == 'unset') {
          $upd = db("UPDATE ".$db['downloads']."
                     SET `top` = '0'
                     WHERE id = '".intval($_GET['id'])."'");
        }

        header("Location: ?admin=dladmin");
      } elseif($_GET['do'] == 'public') {
        if($_GET['what'] == 'set')
        {
          $upd = db("UPDATE ".$db['downloads']."
                     SET `public` = '1'
                     WHERE id = '".intval($_GET['id'])."'");
        } elseif($_GET['what'] == 'unset') {
          $upd = db("UPDATE ".$db['downloads']."
                     SET `public` = '0'
                     WHERE id = '".intval($_GET['id'])."'");
        }

        header("Location: ?admin=dladmin");
      } else {
        $qry = db("SELECT * FROM ".$db['downloads']."
                   ORDER BY id");
        while($get = _fetch($qry))
        {
          $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                        "action" => "admin=dladmin&amp;do=edit",
                                                        "title" => _button_title_edit));
          $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                            "action" => "admin=dladmin&amp;do=delete",
                                                            "title" => _button_title_del,
                                                            "del" => convSpace(_confirm_del_dl)));

          $top = ($get['top'] == 1)
               ? '<a href="?admin=dladmin&amp;do=top&amp;id='.$get['id'].'&amp;what=unset"><img src="../inc/images/yes.gif" alt="" title="'._top_unset.'" /></a>'
               : '<a href="?admin=dladmin&amp;do=top&amp;id='.$get['id'].'&amp;what=set"><img src="../inc/images/no.gif" alt="" title="'._top_set.'" /></a>';
          $public = ($get['public'] == 1)
               ? '<a href="?admin=dladmin&amp;do=public&amp;id='.$get['id'].'&amp;what=unset"><img src="../inc/images/public.gif" alt="" title="'._public_unset.'" /></a>'
               : '<a href="?admin=dladmin&amp;do=public&amp;id='.$get['id'].'&amp;what=set"><img src="../inc/images/nonpublic.gif" alt="" title="'._public_set.'" /></a>';


          $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
          $show_ .= show($dir."/downloads_show", array("id" => $get['id'],
                                                       "dl" => re($get['download']),
                                                       "class" => $class,
                                                       "top" => $top,
                                                       "public" => $public,
																											 "edit" => $edit,
                                                       "delete" => $delete
                                                       ));
        }

        $show = show($dir."/downloads", array("head" => _dl,
                                              "date" => _datum,
                                              "titel" => _dl_file,
                                              "add" => _downloads_admin_head,
                                              "show" => $show_
                                              ));
      }
    }
?>