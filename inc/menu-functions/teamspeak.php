<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 * Menu: Teamspeak
 * @param bool $js
 * @return bool|mixed|null|string|string[]
 * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
 */
function teamspeak($js = false) {
    global $language, $cache;

    header('Content-Type: text/html; charset=utf-8');
    if(!fsockopen_support()) return _fopen;

    if(empty($js)) {
        $teamspeak = '
          <div id="navTeamspeakServer">
            <div style="width:100%;padding:10px 0;text-align:center"><img src="../inc/images/ajax_loading.gif" alt="" /></div>
            <script language="javascript" type="text/javascript">DZCP.initTeamspeakServer();</script>
          </div>';

    } else {
        $teamspeak = '';
        $ts_ip = settings('ts_ip');
        $ts_sport = settings('ts_sport');
        $ts_port = settings('ts_port');
        if(!empty($ts_ip) && !empty($ts_sport) && !empty($ts_port)) {
            $CachedString = $cache->getItem('nav_teamspeak_'.$language);
            if(is_null($CachedString->get())) {
                $teamspeak = teamspeakViewer();
                $CachedString->set(base64_encode($teamspeak))->expiresAfter(config('cache_teamspeak'));
                $cache->save($CachedString);
            } else {
                $teamspeak = base64_decode($CachedString->get());
            }
        } else {
            $teamspeak = '<br /><center>'._no_ts.'</center><br />';
        }
    }

    return $teamspeak;
}