<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 * Menu: Globale Suche & Forum Suche
 */
function search() {
    global $dir,$search_forum;
    if($dir == 'forum' || $search_forum) {
        return show("menu/search_forum", array("submit" => _button_value_search, "search" => _search_word));
    }

    return show("menu/search", array("submit" => _button_value_search, "searchword" => (empty($_GET['searchword']) ? _search_word : up($_GET['searchword']))));
}