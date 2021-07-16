<tr>
<td>
<form name="navi" method="post" action="?admin=navi&amp;do={$do}" onsubmit="return(DZCP.submitButton())">
<table class="hperc" cellspacing="1">
<tr>
  <td class="contentHead" colspan="2" align="center"><span class="fontBold">{if $is_edit}{lang msgID="menu_edit_kat"}{else}{lang msgID="menu_add_kat"}{/if}</span></td>
</tr>
<tr>
  <td class="contentMainTop" colspan="2" align="center">{lang msgID="menu_kat_info"}</td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="sponsors_admin_name"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="name" value="{$name}" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="placeholder"}:</span></td>
  <td class="contentMainFirst" align="center">
      {literal}{$nav_ <input type="text" name="placeholder" value="{/literal}{$placeholder} {literal}" class="inputField_dis" style="width:100px" /> }{/literal}
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="menu_visible"}:</span></td>
  <td class="contentMainFirst" align="center">
    <select name="level" class="selectpicker">
      <option value="0">{lang msgID="status_unregged"}</option>
      <option {if $level_user == 1}selected="selected"{/if} value="1">{lang msgID="status_user"}</option>
      <option {if $level_user == 2}selected="selected"{/if} value="2">{lang msgID="status_trial"}</option>
      <option {if $level_user == 3}selected="selected"{/if} value="3">{lang msgID="status_member"}</option>
      <option {if $level_user == 4}selected="selected"{/if} value="4">{lang msgID="status_admin"}</option>
    </select>
  </td>
</tr>
<tr>
  <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{if $is_edit}{lang msgID="menu_edit_kat"}{else}{lang msgID="menu_add_kat"}{/if}" class="submit" /></td>
</tr>
</table>
</form>
</td>
</tr>