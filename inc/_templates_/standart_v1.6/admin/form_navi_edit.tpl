<tr>
<td>
<form name="navi" method="post" action="?admin=navi&amp;do=editlink&amp;id={$data.id}" onsubmit="return(DZCP.submitButton())">
<table class="hperc" cellspacing="1">
<tr>
  <td class="contentHead" colspan="2" align="center"><span class="fontBold">{lang msgID="navi_edit_head"}</span></td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="navi_name"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="name" value="{$name}" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="navi_url_to"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="url" value="{$url}" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="posi"}:</span></td>
  <td class="contentMainFirst" align="center">
    <select name="pos" class="selectpicker">
      {$position}
    </select>
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="navi_wichtig"}:</span></td>
  <td class="contentMainFirst" align="center">
    <select name="wichtig" class="selectpicker">
      <option value="0">{lang msgID="no"}</option>
      <option value="1"{if $data.wichtig} selected="selected"{/if}>{lang msgID="yes"}</option>
    </select>
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="config_forum_intern"}:</span></td>
  <td class="contentMainFirst" align="center">
    <select name="internal" class="selectpicker">
      <option value="0">{lang msgID="no"}</option>
      <option value="1"{if $data.internal} selected="selected"{/if}>{lang msgID="yes"}</option>
    </select>
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="navi_shown"}:</span></td>
  <td class="contentMainFirst" align="center">
    <select name="sichtbar" class="selectpicker">
      <option value="0">{lang msgID="no"}</option>
      <option value="1"{if $data.shown} selected="selected"{/if}>{lang msgID="yes"}</option>
    </select>
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="target"}:</span></td>
  <td class="contentMainFirst" align="center">
    <select name="target" class="selectpicker">
      <option value="0">{lang msgID="no"}</option>
      <option value="1"{if $data.target} selected="selected"{/if}>{lang msgID="yes"}</option>
    </select>
  </td>
</tr>
<tr>
  <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{lang msgID="button_value_edit"}" class="submit" /></td>
</tr>
</table>
</form>
</td>
</tr>