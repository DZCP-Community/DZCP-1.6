<?php
/////////// ADMINNAVI \\\\\\\\\
// Typ:       settingsmenu
// Rechte:    permission('downloads')
///////////////////////////////
if(_adminMenu != 'true') exit;

    $where = $where.': '._admin_dlkat;
    if(!permission("downloads"))
    {
      $show = error(_error_wrong_permissions, 1);
    } else {
      $qry = db("SELECT * FROM ".$sql_prefix."dl_subkat
                 ORDER BY name");
      while($get = _fetch($qry))
      {
        $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                      "action" => "admin=dl_subkats&amp;do=edit",
                                                      "title" => _button_title_edit));
        $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                          "action" => "admin=dl_subkats&amp;do=delete",
                                                          "title" => _button_title_del,
                                                          "del" => convSpace(_confirm_del_kat)));
        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;

  	    $qrykat = db("SELECT name FROM ".$db['dl_kat']." WHERE id='".$get['kid']."'");
	   		$getkat = _fetch($qrykat);   

        $show_ .= show($dir."/dl_subkats_show", array("edit" => $edit,
                                                    	"name" => re($get['name']),
													 														"kat" => re($getkat['name']),
                                                     	"class" => $class,
                                                     	"delete" => $delete));
      }

      $show = show($dir."/dl_subkats", array("head" => _dl_subkat_head,
                                             "show" => $show_,
                                             "add" => _dl_subkat_add,
                                             "whatkat" => 'dl_subkats',
                                             "download" => _admin_download_kat,
																						 "mainkat" => _dl_subkat_mainkat,
                                             "edit" => _editicon_blank,
                                             "delete" => _deleteicon_blank));

      if($_GET['do'] == "edit")
      {
        $qry = db("SELECT * FROM ".$sql_prefix."dl_subkat
                   WHERE id = '".intval($_GET['id'])."'");
        $get = _fetch($qry);
        
        $qryk = db("SELECT id,name FROM ".$db['dl_kat']."
                   ORDER BY name");
        while($getk = _fetch($qryk))
        {
          if($get['kid'] == $getk['id']) $sel = "selected=\"selected\"";
          else $sel = "";

          $kats .= show(_select_field, array("value" => $getk['id'],
                                             "what" => re($getk['name']),
                                             "sel" => $sel));
        }
        $show = show($dir."/dl_subkats_form", array("newhead" => _dl_subkat_edit,
                                                    "do" => "editkat&amp;id=".$_GET['id']."",
                                                    "subkat" => re($get['name']),
                                                    "kats" => $kats,
                                                    "mainkat" => _dl_subkat_mainkat,
																										"subkats" => _dl_subkat,
																										"what" => _button_value_edit,
                                                    "dlkat" => _dl_subkat));
      } elseif($_GET['do'] == "editkat") {
        if(empty($_POST['subkat']))
        {
          $show = error(_dl_empty_subkat,1);
        } else {
          $qry = db("UPDATE ".$sql_prefix."dl_subkat
                     SET `name` = '".up($_POST['subkat'])."',
                         `kid` = '".up($_POST['kat'])."'
                     WHERE id = '".intval($_GET['id'])."'");

          $show = info(_dl_subkat_edited, "?admin=dl_subkats");
        }
      } elseif($_GET['do'] == "delete") {
        $qry = db("DELETE FROM ".$sql_prefix."dl_subkat
                   WHERE id = '".intval($_GET['id'])."'");

        $show = info(_dl_subkat_deleted, "?admin=dl_subkats");

      } elseif($_GET['do'] == "new") {
      
        $qryk = db("SELECT id,name FROM ".$db['dl_kat']."
                   ORDER BY name");
        while($getk = _fetch($qryk))
        {
          if($get['kid'] == $getk['id']) $sel = "selected=\"selected\"";
          else $sel = "";

         $kats .= show(_select_field, array("value" => $getk['id'],
                                             "what" => re($getk['name']),
                                             "sel" => $sel));
        }     
        $show = show($dir."/dl_subkats_form", array("newhead" => _dl_subkat_add,
                                                    "do" => "add",
                                                    "subkat" => "",
                                                    "kats" => $kats,
																										"mainkat" => _dl_subkat_mainkat,
																										"subkats" => _dl_subkat,
                                                    "what" => _button_value_add,
                                                    "dlkat" => _dl_subkat));
      } elseif($_GET['do'] == "add") {
        if(empty($_POST['subkat']))
        {
          $show = error(_dl_empty_subkat,1);
        } else {
          $qry = db("INSERT INTO ".$sql_prefix."dl_subkat
                     SET `name` = '".up($_POST['subkat'])."',
                     `kid` = '".up($_POST['kat'])."'");

          $show = info(_dl_subkat_addet, "?admin=dl_subkats");
        }
      }
    }
?>