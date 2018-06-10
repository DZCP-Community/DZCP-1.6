<?php
function template_switch() {
    global $tmpdir;

    //init templateswitch
    $tmpldir=""; $tmps = get_files('../inc/_templates_/',true);
    foreach ($tmps as $tmp) {
        $selt = ($tmpdir == $tmp ? 'selected="selected"' : '');
        $tmpldir .= show(_select_field, array("value" => "?tmpl_set=".$tmp,  "what" => $tmp,  "sel" => $selt));
    }

    $template_switch = show("menu/tmp_switch", array("templates" => $tmpldir));
    return $template_switch;
}