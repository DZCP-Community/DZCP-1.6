<tr>
  <td class="contentHead" colspan="2" align="center"><span class="fontBold">{lang msgID="useradd_head"}</span></td>
</tr>
<tr>
<td>
<form id="adduser" name="adduser" enctype="multipart/form-data" action="?admin=adduser" method="post" onsubmit="return(DZCP.submitButton())">
<table class="hperc" cellspacing="1">
<tr>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="loginname"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="user" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="nick"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="nick" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="email"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="email" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="pwd"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="password" name="pwd" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentHead" colspan="2"><span class="fontBold">{lang msgID="useradd_about"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="profil_ppic"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="file" name="file" />
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="profil_avatar"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="file" name="file_avatar" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold"><span class="fontBold">{lang msgID="profil_real"}:</span></span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="rlname" value="" class="inputField_dis_profil"
    onfocus="this.className='inputField_en_profil';"
    onblur="this.className='inputField_dis_profil';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="profil_sex"}:</span></td>
  <td class="contentMainFirst" align="center">
    <select name="sex" class="selectpicker">{lang msgID="pedit_male"}</select>
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="profil_bday"}:</span></td>
  <td class="contentMainFirst" align="center">
	{$dropdown_age}
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="profil_city"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" id="city" name="city" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="profil_country"}:</span></td>
  <td class="contentMainFirst" align="center">{$country}</td>
</tr>
<tr>
  <td class="contentMainTop" align="center" colspan="3"><span class="fontBold">{lang msgID="config_positions_rights"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" width="150" valign="top"><span class="fontBold">{lang msgID="member_admin_squad"}:</span></td>
  <td class="contentMainFirst" colspan="2">
    <table width="100%">
      {$groups}
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="150"><span class="fontBold">{lang msgID="admin_user_level"}:</span></td>
  <td class="contentMainFirst" colspan="2" align="center">
    <select name="level" class="selectpicker" onchange="hidelevels(this.value)">
      <option value="banned" >{lang msgID="admin_level_banned"}</option>
      <option value="1" selected="selected">{lang msgID="status_user"}</option>
      <option value="4" >{lang msgID="status_admin"}</option>
    </select>
  </td>
</tr>
</table>
<table class="mainContent" cellspacing="1" style="margin:0;margin-top:-1px">
<tr>
  <td class="contentMainTop" width="150"><span class="fontBold">{lang msgID="config_positions_rights"}:</span></td>
  <td class="contentMainFirst" align="center">
    <div class="permissions">
      {$getpermissions}
    </div>
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="config_positions_boardrights"}:</span></td>
  <td class="contentMainFirst" align="center">
    <div class="permissions">
      {$getboardpermissions}
    </div>
  </td>
</tr>
</table>
<table class="mainContent" cellspacing="1" style="margin:0;margin-top:-1px">
<tr>
  <td class="contentBottom" colspan="3"><input id="contentSubmit" type="submit" value="{lang msgID="button_value_reg"}" onclick="return(getCord())" class="submit" /></td>
</tr>
</table>
</form>
</td>
</tr>