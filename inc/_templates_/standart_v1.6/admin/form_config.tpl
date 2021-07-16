<tr>
  <td class="contentMainTop info" colspan="2" onclick="DZCP.toggle(1)" style="cursor:pointer" onmouseover="DZCP.showInfo('{lang msgID="main_info"}');this.className='contentMainFirst info'" onmouseout="DZCP.hideInfo();this.className='contentMainTop info'">
    <table class="hperc" cellspacing="0">
      <tr>
        <td style="width:1%"><img id="img1" src="{idir}/expand.gif" alt="" /></td>
        <td><span class="fontBold">{lang msgID="config_c_main"}</span></td>
      </tr>
    </table>
  </td>
</tr>
<tr id="more1" style="display:none">
  <td>
    <table class="hperc" cellspacing="0">
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="wartungsmodus_head"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="wmodus" class="selectpicker">
            <option value="0">{lang msgID="off"}</option>
            <option value="1" {$selwm}>{lang msgID="on"}</option>
          </select>
          <br />{lang msgID="wartungsmodus_info"}
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_clanname"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="clanname" value="{$clanname}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';"/>
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_pagetitel"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="pagetitel" value="{$pagetitel}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_mailfrom"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="mailfrom" value="{$mailfrom}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" />
          <br />{lang msgID="config_mailfrom_info"}
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_language"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="language" class="selectpicker">
            {$lang}
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_tmpdir"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="tmpdir" class="selectpicker">
            {$tmplsel}
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_regcode"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="regcode" class="selectpicker">
            <option value="1" {$selyes}>{lang msgID="show"}</option>
            <option value="0" {$selno}>{lang msgID="dont_show"}</option>
          </select>
          <br />{lang msgID="config_c_regcode_what"}
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_upicsize"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="m_upicsize" value="{$m_upicsize}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
          <br />{lang msgID="config_c_upicsize_what"}
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_maxwidth"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="maxwidth" value="{$maxwidth}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" /> px
          <br />{lang msgID="config_maxwidth_info"}
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_seclogin"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="securelogin" class="selectpicker">
            <option value="0">{lang msgID="off"}</option>
            <option value="1" {$sel_sl}>{lang msgID="on"}</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="akl"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="akl" class="selectpicker">
            <option value="0">{lang msgID="off"}</option>
            <option value="1" {$sel_akl}>{lang msgID="on"}</option>
            <option value="2" {$sel_akl_ad}>{lang msgID="akl_only_admin"}</option>
          </select>
          <br />{lang msgID="akl_info"}
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_double_post"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="double_post" class="selectpicker">
            <option value="0">{lang msgID="off"}</option>
            <option value="1" {$sel_dp}>{lang msgID="on"}</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_fotum_vote"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="forum_vote" class="selectpicker">
            <option value="0">{lang msgID="off"}</option>
            <option value="1" {$sel_fv}>{lang msgID="on"}</option>
          </select>
                    <br />{lang msgID="config_fotum_vote_info"}
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_direct_refresh"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="direct_refresh" class="selectpicker">
            <option value="0">{lang msgID="off"}</option>
            <option value="1" {$sel_refresh}>{lang msgID="on"}</option>
          </select>
          <br />{lang msgID="config_direct_refresh_info"}
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_url_linked_head"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="urls_linked" class="selectpicker">
            <option value="0">{lang msgID="off"}</option>
            <option value="1" {$sel_url}>{lang msgID="on"}</option>
          </select>
          <br />{lang msgID="urls_linked_info"}
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="feeds"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="feed" class="selectpicker">
            <option value="0">{lang msgID="off"}</option>
            <option value="1" {$selfeed}>{lang msgID="on"}</option>
          </select>
          <br />{lang msgID="feeds_info"}
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="pwd_encoder"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="pwd_encoder" class="selectpicker">
              {$pwde_options}
          </select>
          <br />{lang msgID="pwd_encoder_info"}
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop info" colspan="2" onclick="DZCP.toggle(2)" style="cursor:pointer" onmouseover="DZCP.showInfo('{lang msgID="admin_config_badword_info"}');this.className='contentMainFirst info'" onmouseout="DZCP.hideInfo();this.className='contentMainTop info'">
    <table class="hperc" cellspacing="1">
      <tr>
        <td style="width:1%"><img id="img2" src="{idir}/expand.gif" alt="" /></td>
        <td><span class="fontBold">{lang msgID="admin_config_badword"}</span></td>
      </tr>
    </table>
  </td>
</tr>
<tr id="more2" style="display:none">
  <td>
    <table class="hperc" cellspacing="0">
      <tr>
        <td class="contentMainFirst info" align="center" colspan="2">
          <textarea name="badwords" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" rows="auto" cols="auto">{$badwords}</textarea>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop info" colspan="2" onclick="DZCP.toggle(9)" style="cursor:pointer">
    <table class="hperc" cellspacing="1">
      <tr>
        <td style="width:1%"><img id="img9" src="{idir}/expand.gif" alt="" /></td>
        <td><span class="fontBold">{lang msgID="admin_eml_config_head"}</span></td>
      </tr>
    </table>
  </td>
</tr>
<tr id="more9" style="display:none">
  <td><table class="hperc" cellspacing="0">
    <tr>
      <td class="contentMainTop" width="180"><span class="fontBold">{lang msgID="admin_eml_config_ext"}:</span></td>
      <td class="contentMainFirst info" align="center">
          <select name="mail_extension" id="mail_extension">
            {$mail_ext_select}
          </select>
      </td>
    </tr>
          <tr>
        <td colspan="2" class="contentMainTop"><hr /></td>
      </tr>
  </table>
    <table class="hperc" cellspacing="0">
      <tr>
        <td width="180" class="contentMainTop"><span class="fontBold">{lang msgID="smtp_host"}:</span></td>
        <td class="contentMainFirst info" align="center"><input type="text" name="smtp_host" value="{$smtp_host}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" id="smtp_host" /></td>
      </tr>
      <tr>
        <td width="180" class="contentMainTop"><span class="fontBold">{lang msgID="smtp_port"}:</span></td>
        <td class="contentMainFirst info" align="center"><input type="text" name="smtp_port" value="{$smtp_port}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" id="smtp_port" /></td>
      </tr>
      <tr>
        <td width="180" class="contentMainTop"><span class="fontBold">{lang msgID="smtp_username"}:</span></td>
        <td class="contentMainFirst info" align="center"><input type="text" name="smtp_username" value="{$smtp_username}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" id="smtp_username" /></td>
      </tr>
      <tr>
        <td width="180" class="contentMainTop"><span class="fontBold">{lang msgID="smtp_passwort"}:</span></td>
        <td class="contentMainFirst info" align="center"><input type="password" name="smtp_pass" value="{$smtp_pass}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" id="smtp_pass" /></td>
      </tr>
      <tr>
        <td width="180" class="contentMainTop"><span class="fontBold">{lang msgID="smtp_tls_ssl"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="smtp_tls_ssl" class="selectpicker">{$smtp_tls_ssl}</select></td>
      </tr>
      <tr>
        <td colspan="2" class="contentMainTop"><hr /></td>
      </tr>
    </table>
    <table class="hperc" cellspacing="0">
      <tr>
        <td width="180" class="contentMainTop"><span class="fontBold">{lang msgID="smtp_sendmail_path"}:</span></td>
        <td class="contentMainFirst info" align="center"><input type="text" name="sendmail_path" value="{$sendmail_path}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" id="sendmail_path" /></td>
      </tr>
      </table>
    </td>
</tr>
<tr>
  <td class="contentMainTop info" colspan="2" onclick="DZCP.toggle(7)" style="cursor:pointer" onmouseover="DZCP.showInfo('{lang msgID="admin_eml_info"}');this.className='contentMainFirst info'" onmouseout="DZCP.hideInfo();this.className='contentMainTop info'">
    <table class="hperc" cellspacing="1">
      <tr>
        <td style="width:1%"><img id="img7" src="{idir}/expand.gif" alt="" /></td>
        <td><span class="fontBold">{lang msgID="admin_eml_head"}</span></td>
      </tr>
    </table>
  </td>
</tr>
<tr id="more7" style="display:none">
  <td>
    <table class="hperc" cellspacing="0">
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="admin_reg_subj"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="eml_reg_subj" value="{$c_eml_reg_subj}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{lang msgID="admin_reg"}</span></td>
      </tr>
      <tr>
        <td class="contentMainFirst info" align="center" colspan="2">
          <textarea id="eml_reg" name="eml_reg" cols="0" rows="0" class="editorStyleWord" style="width:98%">{$c_eml_reg}</textarea>
        </td>
      </tr>
      <tr><td height="20">&nbsp;</td></tr>
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="admin_pwd_subj"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="eml_pwd_subj" value="{$c_eml_pwd_subj}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{lang msgID="admin_pwd"}</span></td>
      </tr>
      <tr>
        <td class="contentMainFirst info" align="center" colspan="2">
          <textarea id="eml_pwd" name="eml_pwd" cols="0" rows="0" class="editorStyleWord" style="width:98%">{$c_eml_pwd}</textarea>
        </td>
      </tr>
      <tr><td height="20">&nbsp;</td></tr>
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="admin_akl_regist_subj"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="eml_akl_regist_subj" value="{$c_eml_akl_regist_subj}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{lang msgID="admin_akl_regist"}</span></td>
      </tr>
      <tr>
        <td class="contentMainFirst info" align="center" colspan="2">
          <textarea id="eml_akl_register" name="eml_akl_register" cols="0" rows="0" class="editorStyleWord" style="width:98%">{$c_eml_akl_register}</textarea>
        </td>
      </tr>
      <tr><td height="20">&nbsp;</td></tr>
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="admin_fabo_npost_subj"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="eml_fabo_npost_subj" value="{$c_eml_fabo_npost_subj}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{lang msgID="admin_fabo_npost"}</span></td>
      </tr>
      <tr>
        <td class="contentMainFirst info" align="center" colspan="2">
          <textarea id="eml_fabo_npost" name="eml_fabo_npost" cols="0" rows="0" class="editorStyleWord" style="width:98%">{$c_eml_fabo_nposr}</textarea>
        </td>
      </tr>
      <tr><td height="20">&nbsp;</td></tr>
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="admin_fabo_tedit_subj"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="eml_fabo_tedit_subj" value="{$c_eml_fabo_tedit_subj}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{lang msgID="admin_fabo_tedit"}</span></td>
      </tr>
      <tr>
        <td class="contentMainFirst info" align="center" colspan="2">
          <textarea id="eml_fabo_tedit" name="eml_fabo_tedit" cols="0" rows="0" class="editorStyleWord" style="width:98%">{$c_eml_fabo_tedit}</textarea>
        </td>
      </tr>
      <tr><td height="20">&nbsp;</td></tr>
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="admin_fabo_pedit_subj"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="eml_fabo_pedit_subj" value="{$c_eml_fabo_pedit_subj}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{lang msgID="admin_fabo_pedit"}</span></td>
      </tr>
      <tr>
        <td class="contentMainFirst info" align="center" colspan="2">
          <textarea id="eml_fabo_pedit" name="eml_fabo_pedit" cols="0" rows="0" class="editorStyleWord" style="width:98%">{$c_eml_fabo_pedit}</textarea>
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="admin_pn_subj"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="eml_pn_subj" value="{$c_eml_pn_subj}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{lang msgID="admin_pn"}</span></td>
      </tr>
      <tr>
        <td class="contentMainFirst info" align="center" colspan="2">
          <textarea id="eml_pn" name="eml_pn" cols="0" rows="0" class="editorStyleWord" style="width:98%">{$c_eml_pn}</textarea>
        </td>
      </tr>
      <tr><td height="20">&nbsp;</td></tr>
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="admin_nletter_subj"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="eml_nletter_subj" value="{$c_eml_nletter_subj}" class="inputField_dis"
          onfocus="this.className='inputField_en';" onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{lang msgID="admin_nletter"}</span></td>
      </tr>
      <tr>
        <td class="contentMainFirst info" align="center" colspan="2">
          <textarea id="eml_nletter" name="eml_nletter" cols="0" rows="0" class="editorStyleWord" style="width:98%">{$c_eml_nletter}</textarea>
        </td>
      </tr>
      <tr><td height="20">&nbsp;</td></tr>
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="admin_lpwd_subj"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text" name="eml_lpwd_subj" value="{$c_eml_lpwd_subj}" class="inputField_dis"
                 onfocus="this.className='inputField_en';" onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{lang msgID="admin_lpwd"}</span></td>
      </tr>
      <tr>
        <td class="contentMainFirst info" align="center" colspan="2">
          <textarea id="eml_lpwd" name="eml_lpwd" cols="0" rows="0" class="editorStyleWord" style="width:98%">{$c_eml_lpwd}</textarea>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop info" colspan="2" onclick="DZCP.toggle(3)" style="cursor:pointer" onmouseover="DZCP.showInfo('{lang msgID="admin_reg_info"}');this.className='contentMainFirst info'" onmouseout="DZCP.hideInfo();this.className='contentMainTop info'">
    <table class="hperc" cellspacing="1">
      <tr>
        <td style="width:1%"><img id="img3" src="{idir}/expand.gif" alt="" /></td>
        <td><span class="fontBold">{lang msgID="admin_reg_head"}</span></td>
      </tr>
    </table>
  </td>
</tr>
<tr id="more3" style="display:none">
  <td>
    <table class="hperc" cellspacing="0">
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="admin_nc"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="reg_nc" class="selectpicker">
            <option value="0">{lang msgID="no"}</option>
            <option value="1" {$selr_nc}>{lang msgID="yes"}</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="dl"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="reg_dl" class="selectpicker">
            <option value="0">{lang msgID="no"}</option>
            <option value="1" {$selr_dl}>{lang msgID="yes"}</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="reg_artikel"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <select name="reg_artikel" class="selectpicker">
            <option value="0">{lang msgID="no"}</option>
            <option value="1" {$selr_artikel}>{lang msgID="yes"}</option>
          </select>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop info" colspan="2" onclick="DZCP.toggle(4)" style="cursor:pointer" onmouseover="DZCP.showInfo('{lang msgID="config_c_limits_what"}');this.className='contentMainFirst info'" onmouseout="DZCP.hideInfo();this.className='contentMainTop info'">
    <table class="hperc" cellspacing="1">
      <tr>
        <td style="width:1%"><img id="img4" src="{idir}/expand.gif" alt="" /></td>
        <td><span class="fontBold">{lang msgID="config_c_limits"}</span></td>
      </tr>
    </table>
  </td>
</tr>
<tr id="more4" style="display:none">
  <td>
    <table class="hperc" cellspacing="0">
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="config_c_lnews"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_lnews" value="{$m_lnews}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="config_c_lartikel"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_lartikel" value="{$m_lartikel}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_ftopics"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_ftopics" value="{$m_ftopics}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_topdl"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_topdl" value="{$m_topdl}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_events"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_events" value="{$m_events}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_lreg"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_lreg" value="{$m_lreg}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_martikel"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_artikel" value="{$m_artikel}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_madminartikel"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_adminartikel" value="{$m_adminartikel}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_news"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_news" value="{$m_news}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_comments"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_comments" value="{$m_comments}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_archivnews"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_archivnews" value="{$m_archivnews}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_adminnews"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_adminnews" value="{$m_adminnews}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_fthreads"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_fthreads" value="{$m_fthreads}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_fposts"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_fposts" value="{$m_fposts}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_userlist"}:</span></td>
        <td class="contentMainFirst info" align="center">max.
          <input type="text"  name="m_userlist" value="{$m_userlist}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop info" colspan="2" onclick="DZCP.toggle(5)" style="cursor:pointer" onmouseover="DZCP.showInfo('{lang msgID="config_c_floods_what"}');this.className='contentMainFirst info'" onmouseout="DZCP.hideInfo();this.className='contentMainTop info'">
    <table class="hperc" cellspacing="1">
      <tr>
        <td style="width:1%"><img id="img5" src="{idir}/expand.gif" alt="" /></td>
        <td><span class="fontBold">{lang msgID="config_c_floods"}</span></td>
      </tr>
    </table>
  </td>
</tr>
<tr id="more5" style="display:none">
  <td>
    <table class="hperc" cellspacing="0">
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="config_c_forum"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text"  name="f_forum" value="{$f_forum}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />{lang msgID="seconds"}
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_comments"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text"  name="f_newscom" value="{$f_newscom}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />{lang msgID="seconds"}
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="reg_artikel"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text"  name="f_artikelcom" value="{$f_artikelcom}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />{lang msgID="seconds"}
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop info" colspan="2" onclick="DZCP.toggle(6)" style="cursor:pointer" onmouseover="DZCP.showInfo('{lang msgID="config_c_length_what"}');this.className='contentMainFirst info'" onmouseout="DZCP.hideInfo();this.className='contentMainTop info'">
    <table class="hperc" cellspacing="1">
      <tr>
        <td style="width:1%"><img id="img6" src="{idir}/expand.gif" alt="" /></td>
        <td><span class="fontBold">{lang msgID="config_c_length"}</span></td>
      </tr>
    </table>
  </td>
</tr>
<tr id="more6" style="display:none">
  <td>
    <table class="hperc" cellspacing="0">
      <tr>
        <td class="contentMainTop" style="width:180px"><span class="fontBold">{lang msgID="config_c_topdl"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text"  name="l_topdl" value="{$l_topdl}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_lnews"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text"  name="l_lnews" value="{$l_lnews}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_lartikel"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text"  name="l_lartikel" value="{$l_lartikel}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_ftopics"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text"  name="l_ftopics" value="{$l_ftopics}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_lreg"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text"  name="l_lreg" value="{$l_lreg}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_newsadmin"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text"  name="l_newsadmin" value="{$l_newsadmin}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_newsarchiv"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text"  name="l_newsarchiv" value="{$l_newsarchiv}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_forumtopic"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text"  name="l_forumtopic" value="{$l_forumtopic}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="config_c_forumsubtopic"}:</span></td>
        <td class="contentMainFirst info" align="center">
          <input type="text"  name="l_forumsubtopic" value="{$l_forumsubtopic}" class="inputField_dis_mid"
          onfocus="this.className='inputField_en_mid';"
          onblur="this.className='inputField_dis_mid';" />
        </td>
      </tr>
    </table>
  </td>
</tr>