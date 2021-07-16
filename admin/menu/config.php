<?php
/**
 * DZCP - deV!L`z ClanPortal - Mainpage ( dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * Diese Datei ist ein Bestandteil von dzcp.de
 * Diese Version wurde speziell von Lucas Brucksch (Codedesigns) für dzcp.de entworfen bzw. verändert.
 * Eine Weitergabe dieser Datei außerhalb von dzcp.de ist nicht gestattet.
 * Sie darf nur für die Private Nutzung (nicht kommerzielle Nutzung) verwendet werden.
 *
 * Homepage: http://www.dzcp.de
 * E-Mail: info@web-customs.com
 * E-Mail: lbrucksch@codedesigns.de
 * Copyright 2017 © CodeKing, my-STARMEDIA, Codedesigns
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._config_global_head;

if($_POST) {
    if(settings::changed(($key='upicsize'),($var=(int)($_POST['m_upicsize'])))) settings::set($key,$var);
    if(settings::changed(($key='m_artikel'),($var=(int)($_POST['m_artikel'])))) settings::set($key,$var);
    if(settings::changed(($key='m_adminartikel'),($var=(int)($_POST['m_adminartikel'])))) settings::set($key,$var);
    if(settings::changed(($key='securelogin'),($var=(int)($_POST['securelogin'])))) settings::set($key,$var);
    if(settings::changed(($key='m_userlist'),($var=(int)($_POST['m_userlist'])))) settings::set($key,$var);
    if(settings::changed(($key='m_adminnews'),($var=(int)($_POST['m_adminnews'])))) settings::set($key,$var);
    if(settings::changed(($key='m_fthreads'),($var=(int)($_POST['m_fthreads'])))) settings::set($key,$var);
    if(settings::changed(($key='m_fposts'),($var=(int)($_POST['m_fposts'])))) settings::set($key,$var);
    if(settings::changed(($key='m_news'),($var=(int)($_POST['m_news'])))) settings::set($key,$var);
    if(settings::changed(($key='m_comments'),($var=(int)($_POST['m_comments'])))) settings::set($key,$var);
    if(settings::changed(($key='m_archivnews'),($var=(int)($_POST['m_archivnews'])))) settings::set($key,$var);
    if(settings::changed(($key='maxwidth'),($var=(int)($_POST['maxwidth'])))) settings::set($key,$var);
    if(settings::changed(($key='f_forum'),($var=(int)($_POST['f_forum'])))) settings::set($key,$var);
    if(settings::changed(($key='f_artikelcom'),($var=(int)($_POST['f_artikelcom'])))) settings::set($key,$var);
    if(settings::changed(($key='f_newscom'),($var=(int)($_POST['f_newscom'])))) settings::set($key,$var);
    if(settings::changed(($key='l_newsadmin'),($var=(int)($_POST['l_newsadmin'])))) settings::set($key,$var);
    if(settings::changed(($key='l_newsarchiv'),($var=(int)($_POST['l_newsarchiv'])))) settings::set($key,$var);
    if(settings::changed(($key='l_forumtopic'),($var=(int)($_POST['l_forumtopic'])))) settings::set($key,$var);
    if(settings::changed(($key='l_forumsubtopic'),($var=(int)($_POST['l_forumsubtopic'])))) settings::set($key,$var);
    if(settings::changed(($key='m_lnews'),($var=(int)($_POST['m_lnews'])))) settings::set($key,$var);
    if(settings::changed(($key='m_lartikel'),($var=(int)($_POST['m_lartikel'])))) settings::set($key,$var);
    if(settings::changed(($key='m_events'),($var=(int)($_POST['m_events'])))) settings::set($key,$var);
    if(settings::changed(($key='m_topdl'),($var=(int)($_POST['m_topdl'])))) settings::set($key,$var);
    if(settings::changed(($key='m_ftopics'),($var=(int)($_POST['m_ftopics'])))) settings::set($key,$var);
    if(settings::changed(($key='m_lreg'),($var=(int)($_POST['m_lreg'])))) settings::set($key,$var);
    if(settings::changed(($key='l_topdl'),($var=(int)($_POST['l_topdl'])))) settings::set($key,$var);
    if(settings::changed(($key='l_ftopics'),($var=(int)($_POST['l_ftopics'])))) settings::set($key,$var);
    if(settings::changed(($key='l_lreg'),($var=(int)($_POST['l_lreg'])))) settings::set($key,$var);
    if(settings::changed(($key='l_lnews'),($var=(int)($_POST['l_lnews'])))) settings::set($key,$var);
    if(settings::changed(($key='l_lartikel'),($var=(int)($_POST['l_lartikel'])))) settings::set($key,$var);
    if(settings::changed(($key='direct_refresh'),($var=(int)($_POST['direct_refresh'])))) settings::set($key,$var);
    if(settings::changed(($key='news_feed'),($var=(int)($_POST['feed'])))) settings::set($key,$var);
    if(settings::changed(($key='clanname'),($var=stringParser::encode($_POST['clanname'])))) settings::set($key,$var);
    if(settings::changed(($key='default_pwd_encoder'),($var=stringParser::encode($_POST['pwd_encoder'])))) settings::set($key,$var);
    if(settings::changed(($key='pagetitel'),($var=stringParser::encode($_POST['pagetitel'])))) settings::set($key,$var);
    if(settings::changed(($key='badwords'),($var=stringParser::encode($_POST['badwords'])))) settings::set($key,$var);
    if(settings::changed(($key='regcode'),($var=(int)($_POST['regcode'])))) settings::set($key,$var);
    if(settings::changed(($key='forum_vote'),($var=(int)($_POST['forum_vote'])))) settings::set($key,$var);
    if(settings::changed(($key='reg_artikel'),($var=(int)($_POST['reg_artikel'])))) settings::set($key,$var);
    if(settings::changed(($key='reg_newscomments'),($var=(int)($_POST['reg_nc'])))) settings::set($key,$var);
    if(settings::changed(($key='reg_dl'),($var=(int)($_POST['reg_dl'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_reg_subj'),($var=stringParser::encode($_POST['eml_reg_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_pwd_subj'),($var=stringParser::encode($_POST['eml_pwd_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_nletter_subj'),($var=stringParser::encode($_POST['eml_nletter_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_pn_subj'),($var=stringParser::encode($_POST['eml_pn_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='double_post'),($var=(int)($_POST['double_post'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_fabo_npost_subj'),($var=stringParser::encode($_POST['eml_fabo_npost_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_fabo_tedit_subj'),($var=stringParser::encode($_POST['eml_fabo_tedit_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_fabo_pedit_subj'),($var=stringParser::encode($_POST['eml_fabo_pedit_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_akl_regist_subj'),($var=stringParser::encode($_POST['eml_akl_regist_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_reg'),($var=stringParser::encode($_POST['eml_reg'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_pwd'),($var=stringParser::encode($_POST['eml_pwd'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_nletter'),($var=stringParser::encode($_POST['eml_nletter'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_pn'),($var=stringParser::encode($_POST['eml_pn'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_fabo_npost'),($var=stringParser::encode($_POST['eml_fabo_npost'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_fabo_tedit'),($var=stringParser::encode($_POST['eml_fabo_tedit'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_fabo_pedit'),($var=stringParser::encode($_POST['eml_fabo_pedit'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_akl_register'),($var=stringParser::encode($_POST['eml_akl_register'])))) settings::set($key,$var);
    if(settings::changed(($key='mailfrom'),($var=stringParser::encode($_POST['mailfrom'])))) settings::set($key,$var);
    if(settings::changed(($key='tmpdir'),($var=stringParser::encode($_POST['tmpdir'])))) settings::set($key,$var);
    if(settings::changed(($key='wmodus'),($var=(int)($_POST['wmodus'])))) settings::set($key,$var);
    if(settings::changed(($key='mail_extension'),($var=stringParser::encode($_POST['mail_extension'])))) settings::set($key,$var);
    if(settings::changed(($key='smtp_password'),($var=session::encode($_POST['smtp_pass'])))) settings::set($key,$var);
    if(settings::changed(($key='smtp_port'),($var=(int)($_POST['smtp_port'])))) settings::set($key,$var);
    if(settings::changed(($key='smtp_hostname'),($var=stringParser::encode($_POST['smtp_host'])))) settings::set($key,$var);
    if(settings::changed(($key='smtp_username'),($var=stringParser::encode($_POST['smtp_username'])))) settings::set($key,$var);
    if(settings::changed(($key='smtp_tls_ssl'),($var=(int)($_POST['smtp_tls_ssl'])))) settings::set($key,$var);
    if(settings::changed(($key='sendmail_path'),($var=stringParser::encode($_POST['sendmail_path'])))) settings::set($key,$var);
    settings::set('urls_linked',stringParser::encode($_POST['urls_linked']));
    settings::set('eml_lpwd_key',stringParser::encode($_POST['eml_lpwd']));
    settings::set('eml_lpwd_key_subj',stringParser::encode($_POST['eml_lpwd_subj']));
    settings::set('use_akl',(int)($_POST['akl']));
    settings::set('securelogin',(int)($_POST['securelogin']));
    settings::load();
    notification::add_success(_config_set);
}

$files = common::get_files(basePath.'/inc/lang/',false,true, ['php']); $lang = '';
foreach($files as $file) {
    $lng = preg_replace("#.php#", "",$file);
    if($lng == 'global') continue;
    $text = defined('_lang_'.$lng) ? constant('_lang_'.$lng) : $lng;
    $lang .= common::select_field($lng,(stringParser::decode(settings::get('language')) == $lng),$text);
} unset($files,$file,$lng,$sel);

$tmplsel="";
$tmps = common::get_files(basePath.'/inc/_templates_/',true);
foreach ($tmps as $tmp) {
    $cache_hash = md5('templateswitch_xml_'.$tmp);
    if(!common::$cache->AutoMemExists($cache_hash) || !config::$use_system_cache) {
        if(file_exists(basePath.'/inc/_templates_/'.$tmp.'/template.xml')) {
            $xml = simplexml_load_file(basePath . '/inc/_templates_/' . $tmp . '/template.xml');
            if(config::$use_system_cache) {
                common::$cache->AutoMemSet($cache_hash, json_encode($xml), cache::TIME_TEMPLATE_XML);
            }

            if(!empty((string)$xml->permissions)) {
                if(common::permission((string)$xml->permissions) || ((int)$xml->level >= 1 && common::$chkMe >= (int)$xml->level)) {
                    $tmplsel .= common::select_field("?tmpl_set=".$tmp,(settings::get('tmpdir') == $tmp),(string)$xml->name);
                }
            } else if((int)$xml->level >= 1 && common::$chkMe >= (int)$xml->level) {
                $tmplsel .= common::select_field($tmp,(settings::get('tmpdir') == $tmp),(string)$xml->name);
            } else if (!(int)$xml->level){
                $tmplsel .= common::select_field($tmp,(settings::get('tmpdir') == $tmp),(string)$xml->name);
            }
        }
    } else {
        $data = json_decode(common::$cache->AutoMemGet($cache_hash),true);
        if(!empty($data['permissions'])) {
            if(common::permission((string)$data['permissions']) || ((int)$data['level'] >= 1 && common::$chkMe >= (int)$data['level'])) {
                $tmplsel .= common::select_field($tmp,(settings::get('tmpdir') == $tmp),(string)$data['name']);
            }
        } else if((int)$data['level'] >= 1 && common::$chkMe >= (int)$data['level']) {
            $tmplsel .= common::select_field($tmp,(settings::get('tmpdir') == $tmp),(string)$data['name']);
        } else if (!(int)$data['level']){
            $tmplsel .= common::select_field($tmp,(settings::get('tmpdir') == $tmp),(string)$data['name']);
        }
    }
}

unset($data,$tmps,$tmp,$xml);

$smarty->caching = false;
$pwde_options = $smarty->fetch('string:<option '.(!settings::get('default_pwd_encoder') ? 'selected="selected"' : '').' value="0">MD5 '._pwd_encoder_algorithm.'</option>'
    . '<option '.(settings::get('default_pwd_encoder') == 1 ? 'selected="selected"' : '').' value="1">SHA1 '.constant('_pwd_encoder_algorithm').'</option>'
    . '<option '.(settings::get('default_pwd_encoder') == 2 ? 'selected="selected"' : '').' value="2">SHA256 '.constant('_pwd_encoder_algorithm').'</option>'
    . '<option '.(settings::get('default_pwd_encoder') == 3 ? 'selected="selected"' : '').' value="3">SHA512 '.constant('_pwd_encoder_algorithm').'</option>');

$smarty->caching = false;
$mail_options = $smarty->fetch('string:<option '.(settings::get('mail_extension') == 'mail' ? 'selected="selected"' : '').' value="mail">'._default.'</option>'
    . '<option '.(settings::get('mail_extension') == 'sendmail' ? 'selected="selected"' : '').' value="sendmail">Sendmail</option>'
    . '<option '.(settings::get('mail_extension') == 'smtp' ? 'selected="selected"' : '').' value="smtp">SMTP</option>');

$smarty->caching = false;
$smtp_secure_options = $smarty->fetch('string:<option '.(!settings::get('smtp_tls_ssl') ? 'selected="selected"' : '').' value="0">'._default.'</option>'
    . '<option '.(settings::get('smtp_tls_ssl') == 1 ? 'selected="selected"' : '').' value="1">TLS</option>'
    . '<option '.(settings::get('smtp_tls_ssl') == 2 ? 'selected="selected"' : '').' value="2">SSL</option>');

$smarty->caching = false;
$smarty->assign('c_eml_reg_subj',stringParser::decode(settings::get('eml_reg_subj')));
$smarty->assign('c_eml_pwd_subj',stringParser::decode(settings::get('eml_pwd_subj')));
$smarty->assign('c_eml_nletter_subj',stringParser::decode(settings::get('eml_nletter_subj')));
$smarty->assign('c_eml_pn_subj',stringParser::decode(settings::get('eml_pn_subj')));
$smarty->assign('c_eml_fabo_npost_subj',stringParser::decode(settings::get('eml_fabo_npost_subj')));
$smarty->assign('c_eml_fabo_tedit_subj',stringParser::decode(settings::get('eml_fabo_tedit_subj')));
$smarty->assign('c_eml_fabo_pedit_subj',stringParser::decode(settings::get('eml_fabo_pedit_subj')));
$smarty->assign('c_eml_akl_regist_subj',stringParser::decode(settings::get('eml_akl_regist_subj')));
$smarty->assign('c_eml_reg',stringParser::decode(settings::get('eml_reg')));
$smarty->assign('c_eml_pwd',stringParser::decode(settings::get('eml_pwd')));
$smarty->assign('c_eml_nletter',stringParser::decode(settings::get('eml_nletter')));
$smarty->assign('c_eml_pn',stringParser::decode(settings::get('eml_pn')));
$smarty->assign('c_eml_fabo_tedit',stringParser::decode(settings::get('eml_fabo_tedit')));
$smarty->assign('c_eml_fabo_pedit',stringParser::decode(settings::get('eml_fabo_pedit')));
$smarty->assign('c_eml_fabo_nposr',stringParser::decode(settings::get('eml_fabo_nposr')));
$smarty->assign('c_eml_akl_register',stringParser::decode(settings::get('eml_akl_register')));
$smarty->assign('c_eml_lpwd_subj',stringParser::decode(settings::get('eml_lpwd_subj')));
$smarty->assign('c_eml_lpwd',stringParser::decode(settings::get('eml_lpwd')));
$smarty->assign('memcache_host',stringParser::decode(settings::get('memcache_host')));
$smarty->assign('memcache_port',(int)(settings::get('memcache_port')));
$smarty->assign('tmplsel',$tmplsel);
$smarty->assign('maxwidth',(int)(settings::get('maxwidth')));
$smarty->assign('mailfrom',stringParser::decode(settings::get('mailfrom')));
$smarty->assign('l_lreg',(int)(settings::get('l_lreg')));
$smarty->assign('m_lreg',(int)(settings::get('m_lreg')));
$smarty->assign('badwords',stringParser::decode(settings::get('badwords')));
$smarty->assign('regcode',(int)(settings::get('regcode')));
$smarty->assign('m_lnews',(int)(settings::get('m_lnews')));
$smarty->assign('m_lartikel',(int)(settings::get('m_lartikel')));
$smarty->assign('m_ftopics',(int)(settings::get('m_ftopics')));
$smarty->assign('m_events',(int)(settings::get('m_events')));
$smarty->assign('m_topdl',(int)(settings::get('m_topdl')));
$smarty->assign('m_userlist',(int)(settings::get('m_userlist')));
$smarty->assign('m_adminnews',(int)(settings::get('m_adminnews')));
$smarty->assign('m_comments',(int)(settings::get('m_comments')));
$smarty->assign('m_archivnews',(int)(settings::get('m_archivnews')));
$smarty->assign('m_fthreads',(int)(settings::get('m_fthreads')));
$smarty->assign('m_fposts',(int)(settings::get('m_fposts')));
$smarty->assign('m_news',(int)(settings::get('m_news')));
$smarty->assign('m_upicsize',(int)(settings::get('upicsize')));
$smarty->assign('f_forum',(int)(settings::get('f_forum')));
$smarty->assign('f_newscom',(int)(settings::get('f_newscom')));
$smarty->assign('m_artikel',(int)(settings::get('m_artikel')));
$smarty->assign('m_adminartikel',(int)(settings::get('m_adminartikel')));
$smarty->assign('c_wmodus',(int)(settings::get('wmodus')));
$smarty->assign('l_newsadmin',(int)(settings::get('l_newsadmin')));
$smarty->assign('l_newsarchiv',(int)(settings::get('l_newsarchiv')));
$smarty->assign('l_forumtopic',(int)(settings::get('l_forumtopic')));
$smarty->assign('l_forumsubtopic',(int)(settings::get('l_forumsubtopic')));
$smarty->assign('l_topdl',(int)(settings::get('l_topdl')));
$smarty->assign('l_ftopics',(int)(settings::get('l_ftopics')));
$smarty->assign('l_lnews',(int)(settings::get('l_lnews')));
$smarty->assign('l_lartikel',(int)(settings::get('l_lartikel')));
$smarty->assign('f_artikelcom',(int)(settings::get('f_artikelcom')));
$smarty->assign('clanname',stringParser::decode(settings::get('clanname')));
$smarty->assign('pagetitel',stringParser::decode(settings::get('pagetitel')));
$smarty->assign('smtp_host',stringParser::decode(settings::get('smtp_hostname')));
$smarty->assign('smtp_username',stringParser::decode(settings::get('smtp_username')));
$smarty->assign('smtp_pass',session::decode(settings::get('smtp_password')));
$smarty->assign('smtp_port',(int)(settings::get('smtp_port')));
$smarty->assign('sendmail_path',stringParser::decode(settings::get('sendmail_path')));
$smarty->assign('smtp_tls_ssl',$smtp_secure_options);
$smarty->assign('lang',$lang);
$smarty->assign('mail_ext_select',$mail_options);
$smarty->assign('sel_akl',(settings::get('use_akl') == 1 ? 'selected="selected"' : ''));
$smarty->assign('sel_akl_ad',(settings::get('use_akl') == 2 ? 'selected="selected"' : ''));
$smarty->assign('selyes',(settings::get('regcode') ? 'selected="selected"' : ''));
$smarty->assign('selno',(!settings::get('regcode') ? 'selected="selected"' : ''));
$smarty->assign('selwm',(settings::get('wmodus') ? 'selected="selected"' : ''));
$smarty->assign('sel_fv',(settings::get('forum_vote') ? 'selected="selected"' : ''));
$smarty->assign('sel_sl',(settings::get('securelogin') ? 'selected="selected"' : ''));
$smarty->assign('sel_dp',(settings::get('double_post') ? 'selected="selected"' : ''));
$smarty->assign('selr_nc',(settings::get('reg_newscomments') ? 'selected="selected"' : ''));
$smarty->assign('selr_forum',(settings::get('reg_forum') ? 'selected="selected"' : ''));
$smarty->assign('selr_dl',(settings::get('reg_dl') ? 'selected="selected"' : ''));
$smarty->assign('selr_artikel',(settings::get('reg_artikel') ? 'selected="selected"' : ''));
$smarty->assign('sel_url',(settings::get('urls_linked') ? 'selected="selected"' : ''));
$smarty->assign('selfeed',(settings::get('news_feed') ? 'selected="selected"' : ''));
$smarty->assign('sel_refresh',(settings::get('direct_refresh') ? 'selected="selected"' : ''));
$smarty->assign('pwde_options',$pwde_options);
$show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form_config.tpl');
$smarty->clearAllAssign();

$smarty->caching = false;
$smarty->assign('head',_config_global_head);
$smarty->assign('what',"config");
$smarty->assign('value',_button_value_config);
$smarty->assign('show',$show);
$show = $smarty->fetch('file:['.common::$tmpdir.']'.$dir.'/form.tpl');
$smarty->clearAllAssign();