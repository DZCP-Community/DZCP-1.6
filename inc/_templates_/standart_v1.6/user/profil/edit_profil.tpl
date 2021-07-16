<tr>
<td>
<form id="editprofil" name="editprofil" action="?action=editprofile" method="post" onsubmit="return(DZCP.submitButton())">
<table class="hperc" cellspacing="1">
<tr>
  <td colspan="2" class="contentHead"><span class="fontBold">{lang msgID="profil_pic"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" width="30%"><span class="fontBold">{lang msgID="profil_ppic"}:</span></td>
  <td class="contentMainFirst" style="text-align:center">
    {$pic}<br />{lang msgID="profil_edit_pic"} {$deletepic}
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="profil_avatar"}:</span></td>
  <td class="contentMainFirst" style="text-align:center">{$ava}<br />
      {lang msgID="profil_edit_ava"} {$deleteava}
  </td>
</tr>
<tr><td height="20"></td></tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="profil_del_account"}:</span></td>
    <td class="contentMainFirst" align="center">{$delete}</td>
</tr>
<tr><td height="20"></td></tr>
<tr>
  <td class="contentHead" colspan="2" align="center"><span class="fontBold">{lang msgID="profil_hp"}</span></td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="loginname"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="user" value="{$name}" class="inputField_dis_profil"
    onfocus="this.className='inputField_en_profil';"
    onblur="this.className='inputField_dis_profil';" />
  </td>
</tr>
<tr>
    <td class="contentMainTop"><span class="fontBold">{lang msgID="nick"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="nick" value="{$nick}" class="inputField_dis_profil"
    onfocus="this.className='inputField_en_profil';"
    onblur="this.className='inputField_dis_profil';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="new_pwd"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="password" autocomplete="off" name="pwd" class="inputField_dis_profil"
    onfocus="this.className='inputField_en_profil';"
    onblur="this.className='inputField_dis_profil';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="pwd2"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="password" autocomplete="off" name="cpwd" class="inputField_dis_profil"
    onfocus="this.className='inputField_en_profil';"
    onblur="this.className='inputField_dis_profil';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="profil_nletter"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input class="checkbox" type="checkbox" name="nletter" value="1" {$pnl} />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="profil_pnmail"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input class="checkbox" type="checkbox" name="pnmail" value="1" {$pnm} />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="email"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="email" value="{$email}" class="inputField_dis_profil"
    onfocus="this.className='inputField_en_profil';"
    onblur="this.className='inputField_dis_profil';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="hp"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="hp" value="{$hp}" class="inputField_dis_profil"
    onfocus="this.className='inputField_en_profil';"
    onblur="this.className='inputField_dis_profil';" />
  </td>
</tr>
<tr>
  <td class="contentHead" colspan="2"><span class="fontBold">{lang msgID="pedit_visibility"}</span></td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="pedit_visibility_profile"}:</span></td>
  <td class="contentMainFirst" style="text-align:center">
    <select name="visibility_profile" class="selectpicker">
      {$visibility_profile}
    </select>
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="profil_startpage"}:</span></td>
  <td class="contentMainFirst" align="center">
  <select id="startpage" name="startpage" class="selectpicker">{$startpage}</select>
  </td>
</tr>
<tr>
  <td class="contentHead" colspan="2"><span class="fontBold">{lang msgID="profil_about"}</span></td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold"><span class="fontBold">{lang msgID="profil_real"}:</span></span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="rlname" value="{$rlname}" class="inputField_dis_profil"
    onfocus="this.className='inputField_en_profil';"
    onblur="this.className='inputField_dis_profil';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="profil_sex"}:</span></td>
  <td class="contentMainFirst" align="center">
    <select name="sex" class="selectpicker">{$sex}</select>
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="profil_bday"}:</span></td>
  <td class="contentMainFirst" align="center">
    {$dropdown_age}
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="profil_country"}:</span></td>
  <td class="contentMainFirst" align="center">
    {$country}
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold"><span class="fontBold">{lang msgID="profil_city"}:</span></span></td>
  <td class="contentMainFirst" align="center">
    <input id="city" type="text" name="city" value="{$city}" class="inputField_dis_profil"
    onfocus="this.className='inputField_en_profil';"
    onblur="this.className='inputField_dis_profil';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop" colspan="2"><span class="fontBold">{lang msgID="profil_ich"}</span></td>
</tr>
<tr>
  <td class="contentMainFirst" colspan="2" align="center">
    <textarea id="ich" name="ich" cols="0" rows="0" class="editorStyleMini">{$ich}</textarea>
  </td>
</tr>
<tr>
  <td class="contentHead" colspan="2"><span class="fontBold">{lang msgID="profil_sig"}</span></td>
</tr>
<tr>
  <td class="contentMainFirst" align="center" colspan="2">
    <textarea id="sig" name="sig" cols="0" rows="0" class="editorStyle">{$sig}</textarea>
  </td>
</tr>
<tr>
  <td class="contentBottom" colspan="2"><input id="contentSubmit" class="submit" type="submit" value="{lang msgID="button_value_edit"}" /></td>
</tr>
</table>
</form>
</td>
</tr>