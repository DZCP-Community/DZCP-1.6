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
include(basePath."/downloads/helper.php");

## SETTINGS ##
$where = _site_dl;
$title = $pagetitle." - ".$where;
$dir = "downloads";

## SECTIONS ##
$wpublic = " WHERE `public` = 1";
$apublic = " AND `public` = 1";

$intern = '';
if($chkMe == "unlogged" || $chkMe < "1")
    $intern = 'AND `intern` = 0';
if($chkMe == 1)
    $intern = 'AND `intern` < 2';
if($chkMe == 2)
    $intern = 'AND `intern` < 3';
if($chkMe == 3)
    $intern = 'AND `intern` < 4';

switch ($action):
    default:
        $qry = db("SELECT * FROM `".$db['dl_kat']."` ORDER BY `id`;");
        $t = 1; $cnt = 0; $tr1 = ''; $tr2 = ''; $kats = '';
        while($get = _fetch($qry)) {
            if($t == 0 || $t == 1)
                $tr1 = "<tr>";

            if($t == 2) {
                $tr2 = "</tr>";
                $t = 0;
            }

            $subkats = '';
            $qrys = db("SELECT `name`,`id` FROM `".$db['dl_subkat']."` WHERE `kid` = ".$get['id']." ORDER BY `name` ASC;");
            while($gets = _fetch($qrys)) {
                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                $subkats .= show($dir."/download_subkats", array("class" => $class, "subkat" => re($gets['name']),  "kid" => $gets['id']));
            }

            $kats .= show($dir."/download_kats", array("kat" => re($get['name']), "kid" => $get['id'], "tr1" => $tr1,
                "tr2" => $tr2, "subkats" => $subkats, "show" => $show));
            $cnt++; $t++;
        }

        if(is_float($cnt/2)) {
            $end = '<td style="width:50%">&nbsp;</td></tr>';
        }

        // Downloads Top
        $qry = db("SELECT `id`,`download`,`kat`,`beschreibung` FROM `".$db['downloads'].
            "` WHERE `top` = 1 ".$apublic." ".$intern." ORDER BY `date` DESC LIMIT 2;");
        $dl_top = '';
        while($get = _fetch($qry)) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $dl_top .= show($dir."/dl_top", array("titel" => re($get['download']),
                "desc" => cut(bbcode($get['beschreibung']),190),
                "kat" => dlkat($get['kat']),
                "class" => $class,
                "id" => $get['id']));
        }

        // Top Downloads
        $qry = db("SELECT * FROM ".$db['downloads']." ".$wpublic." ".$intern." ORDER BY hits DESC LIMIT 5");
        $i = 1; $top_dl = '';
        while($get = _fetch($qry)) {
            $top_dl .= show($dir."/top_dl", array("id" => $get['id'],
                "titel" => cut(re($get['download']),18),
                "fulltitel" => re($get['download']),
                "size" => get_filesize(re($get['url'])),
                "hl" => $get['id'],
                "i" => $i,
                "hits" => $get['hits']));
            $i++;
        }

// New Downloads
        $qry = db("SELECT * FROM ".$db['downloads']." ".$wpublic." ".$intern." ORDER BY date DESC LIMIT 5");
        $new_dl = '';
        while($get = _fetch($qry))
        {
            $new_dl .= show($dir."/new_dl", array("id" => $get['id'],
                "titel" => cut(re($get['download']),18),
                "fulltitel" => re($get['download']),
                "size" => get_filesize(re($get['url'])),
                "hl" => $get['id'],
                "i" => $i,
                "time" => time_difference($get['date'])));
        }

        $index = show($dir."/downloads", array("kats" => $kats, "end" => $end, "new_dl" => $new_dl, "top_dl" => $top_dl, "dl_top" => $dl_top));
        break;
    case 'download';
        if(settings("reg_dl") == 1 && $chkMe == "unlogged")
        {
            $index = error(_error_unregistered);
        } else {

            $qry = db("SELECT * FROM ".$db['downloads']."
							 WHERE id = '".intval($_GET['id'])."'
				 			 ".$intern.";");
            $get = _fetch($qry);
            if($chkMe > $get['intern'] || $chkMe == $get['intern'] || $chkMe == "4")
            {
                $file = preg_replace("#added...#Uis", "files/", $get['url']);
                if(strpos($get['url'],"../") != 0) $rawfile = @basename($file);
                else                                   $rawfile = re($get['download']);

                $size = @filesize($file);
                $size_mb = @round($size/1048576,2);
                $size_kb = @round($size/1024,2);

                $speed_modem = @round(($size/1024)/(56/8)/60,2);
                $speed_isdn = @round(($size/1024)/(128/8)/60,2);
                $speed_dsl256 = @round(($size/1024)/(256/8)/60,2);
                $speed_dsl512 = @round(($size/1024)/(512/8)/60,2);
                $speed_dsl1024 = @round(($size/1024)/(1024/8)/60,2);
                $speed_dsl2048 = @round(($size/1024)/(2048/8)/60,2);
                $speed_dsl3072 = @round(($size/1024)/(3072/8)/60,2);
                $speed_dsl6016 = @round(($size/1024)/(6016/8)/60,2);
                $speed_dsl16128 = @round(($size/1024)/(16128/8)/60,2);

                if(strlen(@round(($size/1048576)*$get['hits'],0)) >= 4)
                    $traffic = @round(($size/1073741824)*$get['hits'],2).' GB';
                else $traffic = @round(($size/1048576)*$get['hits'],2).' MB';

                $getfile = show(_dl_getfile, array("file" => $rawfile));

                if($size == false)
                {
                    $dlsize = $traffic = 'n/a';
                    $br1 = '<!--';
                    $br2 = '-->';
                } else {
                    $br1 = '';
                    $br2 = '';
                }
                if(empty($get['date']))
                {
                    if($size == false) $date = 'n/a';
                    else $date = date("d.m.Y H:i",@filemtime($file))._uhr;
                } else $date = date("d.m.Y H:i",$get['date'])._uhr;
                $lastdate = date("d.m.Y H:i",@fileatime($file))._uhr;

                $dlsize = get_filesize($file);
                $show = show($dir."/info", array("head" => _dl_info,
                    "headd" => _dl_info2,
                    "getfile" => $getfile,
                    "dl_file" => _dl_file,
                    "dl_besch" => _dl_besch,
                    "dl_size" => _dl_size,
                    "dl_speed" => _dl_speed,
                    "dl_traffic" => _dl_traffic,
                    "dl_loaded" => _dl_loaded,
                    "dl_date" => _dl_date,
                    "last_date" => _download_last_date,
                    "br1" => $br1,
                    "br2" => $br2,
                    "date" => $date,
                    "lastdate" => $lastdate,
                    "id" => $_GET['id'],
                    "dlname" => re($get['download']),
                    "loaded" => $get['hits'],
                    "traffic" => $traffic,
                    "speed_modem" => $speed_modem,
                    "speed_isdn" => $speed_isdn,
                    "speed_dsl256" => $speed_dsl256,
                    "speed_dsl512" => $speed_dsl512,
                    "speed_dsl1024" => $speed_dsl1024,
                    "speed_dsl2048" => $speed_dsl2048,
                    "speed_dsl3072" => $speed_dsl3072,
                    "speed_dsl6016" => $speed_dsl6016,
                    "speed_dsl16128" => $speed_dsl16128,
                    "size" => $dlsize,
                    "besch" => bbcode($get['beschreibung']),
                    "file" => $rawfile));
/////////////////////////////////////////
                $qry = db("SELECT * FROM ".$db['dl_kat']."
             ORDER BY id");
                $t = 1;
                $cnt = 0;
                while($get = _fetch($qry))
                {
                    unset($subkats);
                    unset($tr1); unset($tr2);

                    if($t == 0 || $t == 1) $tr1 = "<tr>";
                    if($t == 2)
                    {
                        $tr2 = "</tr>";
                        $t = 0;
                    }

                    $qrys = db("SELECT * FROM ".$sql_prefix."dl_subkat
                WHERE kid = '".$get['id']."'
                ORDER BY name ASC");
                    while($gets = _fetch($qrys))
                    {
                        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                        $subkats .= show($dir."/download_subkats", array("subkat" => re($gets['name']),
                            "class" => $class,
                            "kid" => $gets['id']));
                    }

                    $kats .= show($dir."/download_kats", array("kat" => re($get['name']),
                        "kid" => $get['id'],
                        "tr1" => $tr1,
                        "tr2" => $tr2,
                        "subkats" => $subkats,
                        "show" => $show));
                    $cnt++;
                    $t++;
                }
                if(is_float($cnt/2)) $end = '<td  style="width:50%">&nbsp;</td></tr>';
// Downloads Top
                $qry = db("SELECT * FROM ".$db['downloads']."
             WHERE top = 1
             ".$apublic."
						 ".$intern."
             ORDER BY date DESC
             LIMIT 2");
                while($get = _fetch($qry))
                {
                    $dl_top .= show($dir."/dl_top", array("titel" => re($get['download']),
                        "desc" => cut(bbcode($get['beschreibung']),190),
                        "kat" => dlkat($get['kat']),
                        "id" => $get['id']));
                }

                $index = show($dir."/downloads_more", array("data_head" => _downloads_data,
                    "top_head" => _downloads_top,
                    "kat_head" => _downloads_kats,
                    "kats" => $kats,
                    "end" => $end,
                    "svalue" => re($_GET['dl']),
                    "show" => $show,
                    "dl_top" => "<tr>".$dl_top."</tr>"));
            } else {
                $index = error(_error_nodl,1);
            }
        }
        break;
    case 'searchdownload';
        $qry = db("SELECT * FROM ".$db['downloads']."
               WHERE (download LIKE '%".re($_GET['dl'])."%' 
               OR beschreibung LIKE '%".re($_GET['dl'])."%')
               ".$apublic."
							 ".$intern."
               ORDER BY date DESC");
        unset($files);
        while($get = _fetch($qry))
        {
            if(dlkat($get['kat']) != $lastKat) {
                $color = 0;
                $lastKat = dlkat($get['kat']);
                $files .= '<tr><td>&nbsp;</td></tr>
                   <tr>
                     <td class="contentHead" colspan="2"><b>'.dlkat($get['kat']).'</b></td>
                   </tr>
                   <tr>
                     <td class="contentMainTop" colspan="2"><b>File</b></td>
                   </tr>';
            }

            $class = ($color % 2) ? "contentMainFirst" : "contentMainSecond"; $color++;
            $files .= show($dir."/downloads_showkat_show", array("date" => date("d.m.y",$get['date']),
                "id" => $get['id'],
                "file" => re($get['download']),
                "class" => $class));
        }

        $show = show($dir."/downloads_search", array("search_head" => _downloads_search,
            "svalue" => re($_GET['dl']),
            "show" => $files));

/////////////
        $qry = db("SELECT * FROM ".$db['dl_kat']."
             ORDER BY id");
        $t = 1;
        $cnt = 0;
        while($get = _fetch($qry))
        {
            unset($subkats);
            unset($tr1); unset($tr2);

            if($t == 0 || $t == 1) $tr1 = "<tr>";
            if($t == 2)
            {
                $tr2 = "</tr>";
                $t = 0;
            }

            $qrys = db("SELECT * FROM ".$sql_prefix."dl_subkat
                WHERE kid = '".$get['id']."'
                ORDER BY name ASC");
            while($gets = _fetch($qrys))
            {
                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                $subkats .= show($dir."/download_subkats", array("subkat" => re($gets['name']),
                    "class" => $class,
                    "kid" => $gets['id']));
            }

            $kats .= show($dir."/download_kats", array("kat" => re($get['name']),
                "kid" => $get['id'],
                "tr1" => $tr1,
                "tr2" => $tr2,
                "subkats" => $subkats,
                "show" => $show));
            $cnt++;
            $t++;
        }
        if(is_float($cnt/2)) $end = '<td  style="width:50%">&nbsp;</td></tr>';
// Downloads Top
        $qry = db("SELECT * FROM ".$db['downloads']."
             WHERE top = 1
             ".$apublic."
						 ".$intern."
             ORDER BY date DESC
             LIMIT 2");
        while($get = _fetch($qry))
        {
            $dl_top .= show($dir."/dl_top", array("titel" => re($get['download']),
                "desc" => cut(bbcode($get['beschreibung']),190),
                "kat" => dlkat($get['kat']),
                "id" => $get['id']));
        }

        $index = show($dir."/downloads_more", array("data_head" => _downloads_data,
            "top_head" => _downloads_top,
            "kat_head" => _downloads_kats,
            "kats" => $kats,
            "end" => $end,
            "svalue" => re($_GET['dl']),
            "show" => $show,
            "dl_top" => "<tr>".$dl_top."</tr>"));
        break;
    case 'show';
        $qry = db("SELECT * FROM ".$db['downloads']."
             WHERE kat = '".intval($_GET['id'])."'
             ".$apublic."
						 ".$intern."
             ORDER BY date DESC");
        unset($files);
        while($get = _fetch($qry))
        {
            $class = ($color % 2) ? "contentMainFirst" : "contentMainSecond"; $color++;
            $files .= show($dir."/downloads_showkat_show", array("date" => date("d.m.y",$get['date']),
                "id" => $get['id'],
                "file" => re($get['download']),
                "class" => $class));
        }

        $show = show($dir."/downloads_showkat", array("head" => dlkat(intval($_GET['id'])),
            "show" => $files));

        $qry = db("SELECT * FROM ".$db['dl_kat']."
             ORDER BY id");
        $t = 1;
        $cnt = 0;
        while($get = _fetch($qry))
        {
            unset($subkats);
            unset($tr1); unset($tr2);

            if($t == 0 || $t == 1) $tr1 = "<tr>";
            if($t == 2)
            {
                $tr2 = "</tr>";
                $t = 0;
            }

            $qrys = db("SELECT * FROM ".$sql_prefix."dl_subkat
                WHERE kid = '".$get['id']."'
                ORDER BY name ASC");
            while($gets = _fetch($qrys))
            {
                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                $subkats .= show($dir."/download_subkats", array("subkat" => re($gets['name']),
                    "class" => $class,
                    "kid" => $gets['id']));
            }

            $kats .= show($dir."/download_kats", array("kat" => re($get['name']),
                "kid" => $get['id'],
                "tr1" => $tr1,
                "tr2" => $tr2,
                "subkats" => $subkats,
                "show" => $show));
            $cnt++;
            $t++;
        }
        if(is_float($cnt/2)) $end = '<td  style="width:50%">&nbsp;</td></tr>';
// Downloads Top
        $qry = db("SELECT * FROM ".$db['downloads']."
             WHERE top = 1
             ".$apublic."
						 ".$intern."
             ORDER BY date DESC
             LIMIT 2");
        while($get = _fetch($qry))
        {
            $dl_top .= show($dir."/dl_top", array("titel" => re($get['download']),
                "desc" => cut(bbcode($get['beschreibung']),190),
                "kat" => dlkat($get['kat']),
                "id" => $get['id']));
        }

        $index = show($dir."/downloads_more", array("data_head" => _downloads_data,
            "top_head" => _downloads_top,
            "kat_head" => _downloads_kats,
            "kats" => $kats,
            "end" => $end,
            "svalue" => re($_GET['dl']),
            "show" => $show,
            "dl_top" => "<tr>".$dl_top."</tr>"));
        break;
    case 'getfile';
        if(settings("reg_dl") == "1" && $chkMe == "unlogged")
        {
            $index = error(_error_unregistered,1);
        } else {

            if($chkMe == "1") $intern = 'AND intern < 2';
            if($chkMe == "2") $intern = 'AND intern < 3';
            if($chkMe == "3") $intern = 'AND intern < 4';
            if($chkMe == "4") $intern = '';

            $qry = db("SELECT * FROM ".$db['downloads']."
               WHERE id = '".intval($_GET['id'])."' ".$intern.";");
            $get = _fetch($qry);

            if($chkMe > $get['intern'] || $chkMe == $get['intern'] || $chkMe == "4")
            {

                $file = preg_replace("#added...#Uis", "", $get['url']);

                if(preg_match("=added...=Uis",$get['url']) != FALSE)
                    $dlFile = "files/".$file;
                else $dlFile = $get['url'];

                db("UPDATE ".$db['downloads']." SET `hits` = hits+1 WHERE id = '".intval($_GET['id'])."'");
                //download file
                header("Location: ".$dlFile);
            } else {
                $index = error(_error_nodl,1);
            }
        }
        break;
endswitch;

## INDEX OUTPUT ##
page($index, $title, $where, '','downloads');