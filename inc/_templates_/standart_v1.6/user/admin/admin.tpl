<tr>
<td>
<form name="edituser" action="?action=admin&amp;do=update&amp;user={$user}" method="post" onsubmit="return(DZCP.submitButton())" autocomplete="off">
<table class="hperc" cellspacing="1">
<tr>
  <td class="contentHead" align="center" colspan="3"><span class="fontBold">{lang msgID="admin_user_edithead"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" align="center" colspan="3"><span class="fontBold">{lang msgID="admin_user_personalhead"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" width="150"><span class="fontBold">{lang msgID="nick"}:</span></td>
  <td class="contentMainFirst" colspan="2" align="center">
    <input type="text" name="nick" value="{$enick}" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="150"><span class="fontBold">{lang msgID="email"}:</span></td>
  <td class="contentMainFirst" colspan="2" align="center">
    <input type="text" name="email" value="{$email}" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="150"></td>
  <td class="contentMainFirst" colspan="2" align="center"></td>
</tr>
<tr>
  <td class="contentMainTop" width="150"><span class="fontBold">{lang msgID="loginname"}:</span></td>
  <td class="contentMainFirst" colspan="2" align="center">
    <input type="text" name="loginname" value="{$loginname}" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
{$editpwd}
<tr>
  <td class="contentMainTop" width="150" valign="top"><span class="fontBold">{lang msgID="admin_user_identitat"}:</span></td>
  <td class="contentMainFirst" colspan="2" align="center">{$get}</td>
</tr>
<tr>
  <td class="contentMainTop" align="center" colspan="3"><span class="fontBold">{lang msgID="admin_user_squadhead"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" width="150" valign="top"><span class="fontBold">{lang msgID="member_admin_squad"}/{lang msgID="profil_position"}:</span></td>
  <td class="contentMainFirst" colspan="2">
    <table width="100%">
      {$squad}
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop" align="center" colspan="3"><span class="fontBold">{lang msgID="admin_user_clanhead"}</span><br />
    <small>{lang msgID="admin_user_clanhead_info"}</small></td>
</tr>
<tr>
  <td class="contentMainTop" width="150"><span class="fontBold">{lang msgID="admin_user_level"}:</span></td>
  <td class="contentMainFirst" colspan="2" align="center">
    <select name="level" class="selectpicker">
        {$elevel}
    </select>
  </td>
</tr>
</table>
<table id="hideLvl" class="mainContent" cellspacing="1" style="margin:0;margin-top:-1px">
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
  <td class="contentBottom" colspan="3"><input id="contentSubmit" type="submit" value="{lang msgID="button_value_edit"}" class="submit" /></td>
</tr>
</table>
</form>
</td>
</tr>