<tr>
  <td class="contentHead" colspan="2" align="center"><span class="fontBold">{lang msgID="member_admin_edit_header"}</span></td>
</tr>
<tr>
<td>
<form enctype="multipart/form-data" action="?admin=gruppen&amp;do=edit&amp;id={$id}" method="post" onsubmit="return(DZCP.submitButton())">
<table class="hperc" cellspacing="1">
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="member_admin_squad"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="group" value="{$sgroup}" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="dl_besch"}:</span></td>
  <td class="contentMainFirst" align="center">
    <textarea id="beschreibung" name="beschreibung" cols="0" rows="0" class="editorStyleMini">{$beschreibung}</textarea>
  </td>
</tr>
<tr>
  <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{lang msgID="button_value_edit"}" class="submit" /></td>
</tr>
</table>
</form>
</td>
</tr>
