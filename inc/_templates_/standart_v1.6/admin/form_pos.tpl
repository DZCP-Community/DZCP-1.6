<tr>
<td>
<form name="positions" action="?admin=positions&amp;do={$do}" method="post" onsubmit="return(DZCP.submitButton())">
<table class="hperc" cellspacing="1">
<tr>
  <td class="contentHead" align="center" colspan="2"><span class="fontBold">{$newhead}</span></td>
</tr>
<tr>
  <td class="contentMainTop" width="135"><span class="fontBold">{lang msgID="description"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="kat" value="{$kat}" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="config_positions_rights"}:</span></td>
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
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="position"}:</span></td>
  <td class="contentMainFirst" align="center">
    <select name="pos" class="selectpicker">
      {lang msgID="nothing"}
      <option value="1">als erstes</option>
      {$positions}
    </select>
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="color"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="color" id="colorpicker" value="{$color}" class="inputField_dis_mid"
    onfocus="this.className='inputField_en_mid';"
    onblur="this.className='inputField_dis_mid';" />
  </td>
</tr>
<tr>
  <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{$what}" class="submit" /></td>
</tr>
</table>
</form>
</td>
</tr>