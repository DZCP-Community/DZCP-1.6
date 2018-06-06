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
        $qry = db("SELECT * FROM ".$db['dl_kat']."
                   ORDER BY name");
        while($get = _fetch($qry))
        {
          $kats .= show(_select_field, array("value" => $get['id'],
                                             "what" => re($get['name']),
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
                                             "nothing" => "",
                                             "nofile" => _downloads_nofile,
                                             "lokal" => _downloads_lokal,
                                             "what" => _button_value_add,
                                             "do" => "add",
                                             "exist" => _downloads_exist,
											 "picture" => _downloads_picture,
											 "picturea" => _downloads_picturea,
											 "pictureb" => _downloads_pictureb,
											 "picturec" => _downloads_picturec,
											 "picvorschau" => "",
											 "picvorschaua" => "",
										     "picvorschaub" => "",
											 "picvorschauc" => "",
											 "picvorschau" => "",
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
                         `date`         = '".((int)time())."',
                         `beschreibung` = '".up($_POST['beschreibung'],1)."',
                         `kat`          = '".((int)$_POST['kat'])."'");

          $tmp1 = $_FILES['dlscreen']['tmp_name'];
          $type1 = $_FILES['dlscreen']['type'];
          $end1 = explode(".", $_FILES['dlscreen']['name']);
          $end1 = strtolower($end1[count($end1)-1]);
          $end1 = strtolower($end1[count($end1)-2]);
          
          if(!empty($tmp1))
          {
            $img1 = @getimagesize($tmp1);
						if($img1[0])
            {
              @copy($tmp1, basePath."/inc/images/downloads/".mysql_insert_id().".png");
              @unlink($tmp1);
            }
          }	
          /* Multiimage Uploadmod by HellBZ */
        foreach ($_FILES['image']['name'] as $i => $name) {
       
        if ($_FILES['image']['error'][$i] == 4) {
            continue; 
        }
       
        if ($_FILES['image']['error'][$i] == 0) {
           
             if ($_FILES['image']['size'][$i] > 99439443) {
               // $message[] = "$name exceeded file limit.";
                continue;  
             }
                    $end1 = explode(".", $_FILES['dlscreen']['name']);
          $end = strtolower($end1[count($end1)-1]);
          $start = strtolower($end1[count($end1)-2]);
           
           $img = $_FILES['image']['tmp_name'][$i];
            //Get Image size info
           #
            $imgInfo = getimagesize($img);
           #
            switch ($imgInfo[2]) {
           #
             case 1: 
             
             $im = imagecreatefromgif($img); 
             imagepng($im,basePath."/inc/images/downloads/".mysql_insert_id().'_'.$start.".png");
             break;
           #
             case 2: 
             
             $im = imagecreatefromjpeg($img);
             imagepng($im,basePath."/inc/images/downloads/".mysql_insert_id().'_'.$start.".png");  
             break;
           #
             case 3: 
             
             @copy($_FILES['image']['tmp_name'][$i], basePath."/inc/images/downloads/".mysql_insert_id().'_'.strtolower($_FILES['image']['name'][$i])); 
             break;
           #
             default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
           #
            }
              
          @unlink($_FILES['image']['tmp_name'][$i]);// copy($_FILES['image']['tmp_name'][$i],$_FILES['image']['name'][$i]);
           //@unlink($_FILES['image']['tmp_name'][$i]);
            
           //  $uploaded++;
        }
        }
        /* Multiimage Uploadmod by HellBZ */
        
          $show = info(_downloads_added, "?admin=dladmin");
        }
      } elseif($_GET['do'] == "edit") {
        $qry  = db("SELECT * FROM ".$db['downloads']."
                    WHERE id = '".intval($_GET['id'])."'");
        $get = _fetch($qry);

        $qryk = db("SELECT * FROM ".$db['dl_kat']."
                    ORDER BY name");
        while($getk = _fetch($qryk))
        {
          if($getk['id'] == $get['kat']) $sel = "selected=\"selected\"";
          else $sel = "";

          $kats .= show(_select_field, array("value" => $getk['id'],
                                             "what" => re($getk['name']),
                                             "sel" => $sel));
        }
       foreach($picformat as $endung)
  		{
  			if(file_exists(basePath."/inc/images/downloads/".$get['id'].".png"))
  			{
  				$pic = show(_downloads_picvorschau, array ("picvorschau" => '../inc/images/downloads/'.$get['id'].'.png'));
  				break;
  			}	else {
  				$pic = "Kein Bild hinterlegt";
  			}
  		}
		foreach($picformat as $endung)
  		{
  			if(file_exists(basePath."/inc/images/downloads/".$get['id']."-1.png"))
  			{
  				$pica = show(_downloads_picvorschau, array ("picvorschau" => '../inc/images/downloads/'.$get['id'].'-1.png'));
  				break;
  			}	else {
  				$pica = "Kein Bild hinterlegt";
  			}
  		}
		foreach($picformat as $endung)
  		{
  			if(file_exists(basePath."/inc/images/downloads/".$get['id']."-2.png"))
  			{
  				$picb = show(_downloads_picvorschau, array ("picvorschau" => '../inc/images/downloads/'.$get['id'].'-2.png'));
  				break;
  			}	else {
  				$picb = "Kein Bild hinterlegt";
  			}
  		}
		foreach($picformat as $endung)
  		{
  			if(file_exists(basePath."/inc/images/downloads/".$get['id']."-3.png"))
  			{
  				$picc = show(_downloads_picvorschau, array ("picvorschau" => '../inc/images/downloads/'.$get['id'].'-3.png'));
  				break;
  			}	else {
  				$picc = "Kein Bild hinterlegt";
  			}
  		}
        $show = show($dir."/form_dl", array("admin_head" => _downloads_admin_head_edit,
                                            "ddownload" => re($get['download']),
                                            "durl" => re($get['url']),
                                            "file" => $dl,
                                            "lokal" => _downloads_lokal,
                                            "exist" => _downloads_exist,
											"picture" => _downloads_picture,
											"picturea" => _downloads_picturea,
											"pictureb" => _downloads_pictureb,
											"picturec" => _downloads_picturec,
											"picvorschau" => $pic,
											"picvorschaua" => $pica,
											"picvorschaub" => $picb,
											"picvorschauc" => $picc,
                                            "nothing" => _nothing,
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
                         `date`         = '".((int)time())."',
                         `kat`          = '".((int)$_POST['kat'])."'
                     WHERE id = '".intval($_GET['id'])."'");
          $tmp = $_FILES['dlscreen']['tmp_name'];
          $type = $_FILES['dlscreen']['type'];
          $end = explode(".", $_FILES['dlscreen']['name']);
          $end = strtolower($end[count($end)-1]);
          
          if(!empty($tmp))
          {
            $img = @getimagesize($tmp);
						foreach($picformat AS $end1)
            {
              if(file_exists(basePath.'/inc/images/downloads/'.intval($_GET['id']).'.png'))
              {
                @unlink(basePath.'/inc/images/downloads/'.intval($_GET['id']).'.png');
                break;
              }
            }
            if($img[0])
            {
              copy($tmp, basePath."/inc/images/downloads/".intval($_GET['id']).".png");
              @unlink($tmp);
            }
          }
		  $tmp = $_FILES['dlscreena']['tmp_name'];
          $type = $_FILES['dlscreena']['type'];
          $end = explode(".", $_FILES['dlscreena']['name']);
          $end = strtolower($end[count($end)-1]);
          
          if(!empty($tmp))
          {
            $img = @getimagesize($tmp);
						foreach($picformat AS $end1)
            {
              if(file_exists(basePath.'/inc/images/downloads/'.intval($_GET['id']).'-1.png'))
              {
                @unlink(basePath.'/inc/images/downloads/'.intval($_GET['id']).'-1.png');
                break;
              }
            }
            if($img[0])
            {
              copy($tmp, basePath."/inc/images/downloads/".intval($_GET['id'])."-1.png");
              @unlink($tmp);
            }
          }
		  $tmp = $_FILES['dlscreenb']['tmp_name'];
          $type = $_FILES['dlscreenb']['type'];
          $end = explode(".", $_FILES['dlscreenb']['name']);
          $end = strtolower($end[count($end)-1]);
          
          if(!empty($tmp))
          {
            $img = @getimagesize($tmp);
						foreach($picformat AS $end1)
            {
              if(file_exists(basePath.'/inc/images/downloads/'.intval($_GET['id']).'-2.png'))
              {
                @unlink(basePath.'/inc/images/downloads/'.intval($_GET['id']).'-2.png');
                break;
              }
            }
            if($img[0])
            {
              copy($tmp, basePath."/inc/images/downloads/".intval($_GET['id'])."-2.png");
              @unlink($tmp);
            }
          }
		  $tmp = $_FILES['dlscreenc']['tmp_name'];
          $type = $_FILES['dlscreenc']['type'];
          $end = explode(".", $_FILES['dlscreenc']['name']);
          $end = strtolower($end[count($end)-1]);
          
          if(!empty($tmp))
          {
            $img = @getimagesize($tmp);
						foreach($picformat AS $end1)
            {
              if(file_exists(basePath.'/inc/images/downloads/'.intval($_GET['id']).'-3.png'))
              {
                @unlink(basePath.'/inc/images/downloads/'.intval($_GET['id']).'-3.png');
                break;
              }
            }
            if($img[0])
            {
              copy($tmp, basePath."/inc/images/downloads/".intval($_GET['id'])."-3.png");
              @unlink($tmp);
            }
          }
          $show = info(_downloads_edited, "?admin=dladmin");
        }
      } elseif($_GET['do'] == "delete") {
        $qry = db("DELETE FROM ".$db['downloads']."
                   WHERE id = '".intval($_GET['id'])."'");

        $show = info(_downloads_deleted, "?admin=dladmin");
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

          $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
          $show_ .= show($dir."/downloads_show", array("id" => $get['id'],
                                                       "dl" => re($get['download']),
                                                       "class" => $class,
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