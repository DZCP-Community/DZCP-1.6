<?php
function template_switch() {
    global $tmpdir,$chkMe;

    //init templateswitch
    $tmpldir=""; $tmps = get_files('../inc/_templates_/',true);
    foreach ($tmps as $tmp) {
        if (file_exists(basePath . '/inc/_templates_/' . $tmp . '/template.xml')) {
            $xml = simplexml_load_file(basePath . '/inc/_templates_/' . $tmp . '/template.xml');
            $selt = ($tmpdir == $tmp ? 'selected="selected"' : '');
            if (!empty((string)$xml->permissions)) {
                if (permission((string)$xml->permissions) || ((int)$xml->level >= 1 && $chkMe >= (int)$xml->level)) {
                    $tmpldir .= show(_select_field, array("value" => "?tmpl_set=" . $tmp, "what" => (string)$xml->name, "sel" => $selt));
                }
            } else if ((int)$xml->level >= 1 && $chkMe >= (int)$xml->level) {
                $tmpldir .= show(_select_field, array("value" => "?tmpl_set=" . $tmp, "what" => (string)$xml->name, "sel" => $selt));
            } else if (!(int)$xml->level) {
                $tmpldir .= show(_select_field, array("value" => "?tmpl_set=" . $tmp, "what" => (string)$xml->name, "sel" => $selt));
            }
        }
    }

    return show("menu/tmp_switch", array("templates" => $tmpldir));
}