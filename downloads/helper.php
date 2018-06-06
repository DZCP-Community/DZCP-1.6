<?php
//--> Downloadbilder verkleinern
function img_download($img)
{
    $pic = "<a href=\"../".$img."\" rel=\"lightbox[l_".intval($img)."]\"><img src=\"../thumbgendownloads.php?img=".$img."\" alt=\"\" /></a>";
    return $pic;
}

function latest_download() {
    global $db,$dir;

    $shownewest = '';
    $qrynew = db("SELECT `id`,`download`,`hits` FROM `".$db['downloads']."` ORDER BY `hits` DESC LIMIT 6;");
    while($getnew = _fetch($qrynew)) {
        $shownewest .= show($dir."/newest_show", [
            "id" => strval($getnew['id']),
            "titel" => cut(re($getnew['download']),25),
            "hits" => strval($getnew['hits'])]);
    }

    return show($dir."/newest", ["id" => $getnew['id'], "shownewest" => $shownewest]);
}

function hottest_download() {
    global $db,$dir,$picformat;
    $get = db("SELECT `id`,`beschreibung`,`download`,`hits` FROM `".$db['downloads']."` ORDER BY `hits` DESC LIMIT 1;",false,true);

    $hotpic = '../inc/images/noimg.png';
    foreach($picformat as $endung) {
        if(file_exists(basePath."/inc/images/downloads/".$get['id'].".".$endung)) {
            $hotpic = '../inc/images/downloads/'.$get['id'].'.'.$endung;
            break;
        }
    }

    $showhottest = show($dir."/hottest_show", [
        "id" => strval($get['id']),
        "pic" => $hotpic,
        "besch" => cut(bbcode($get['beschreibung']),160),
        "titel" => cut(re($get['download']),25),
        "hits" => strval($get['hits'])]);

    return show($dir."/hottest", array("id" => strval($get['id']), "showhottest" => $showhottest));
}

define('_downloads_link_new' , '<img src="../inc/images/download.gif" alt="" class="icon" /> [download]');
define('_downloads_picture' , 'Hauptscreenshot');
define('_downloads_picturea' , 'Screenshot 1');
define('_downloads_pictureb' , 'Screenshot 2');
define('_downloads_picturec' , 'Screenshot 3');
define('_download_klickinfo' , 'Auf das Bild klicken f&uuml;r vergr&ouml;sserte Ansicht');
define('_download_hottest_name' , 'Name');
define('_download_hottest_beschreibung' , 'Beschreibung');
define('_download_hottest_downloads' , 'Downloads');
define('_download_hottest_downloaded' , 'mal heruntergeladen');
define('_download_newest_head' , 'Neueste Downloads');
define('_downloads_hottest_head' , 'Beliebtester Download');
define('_downloads_pictures_head' , 'Screenshots');
define('_downloads_infos_head' , 'Downloadinfos');
define('_downloads_picvorschau' , '<img src="[picvorschau]" alt="" style="height:140px; width:170px;" />');
define('_downloads_pica' , '<div class="panel" title="Panel 1">[pica]</div>');
define('_downloads_picb' , '<div class="panel" title="Panel 1">[picb]</div>');
define('_downloads_picc' , '<div class="panel" title="Panel 1">[picc]</div>');
define('_downloads_picd' , '<div class="panel" title="Panel 1">[picd]</div>');
define('_downloads_pica_link' , '<div><a href="#1" class="cross-link active-thumb"><img src="[thumba]" style="width:60px; height:40px;" border="0" alt="" class="nav-thumb" alt="temp-thumb" /></a></div>');
define('_downloads_picb_link' , '<div><a href="#2" class="cross-link"><img src="[thumbb]" style="width:60px; height:40px;" border="0" alt=""  class="nav-thumb" alt="temp-thumb" /></a></div>');
define('_downloads_picc_link' , '<div><a href="#3" class="cross-link"><img src="[thumbc]" style="width:60px; height:40px;" border="0" alt=""  class="nav-thumb" alt="temp-thumb" /></a></div>');
define('_downloads_picd_link' , '<div><a href="#4" class="cross-link"><img src="[thumbd]" style="width:60px; height:40px;" border="0" alt="" class="nav-thumb" alt="temp-thumb" /></a></div>');
