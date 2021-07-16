<tr>
  <td class="contentHead" colspan="3" align="center"><span class="fontBold">{lang msgID="msg_titel_answer"}</span></td>
</tr>
<tr>
<td>
<form name="msganswer" action="?action=msg&amp;do=sendanswer" method="post" onsubmit="return(DZCP.submitButton())">
  <input type="hidden" name="von" value="{$von}" />
  <input type="hidden" name="an" value="{$an}" />
<table class="mainContent" style="margin-top: 0px;" cellspacing="1">
<tr> 
  <td class="contentMainTop"><span class="fontBold">{lang msgID="titel"}:</span></td>
  <td class="contentMainFirst" align="center" colspan="2">
    <input type="text" name="titel" value="{$titel}" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr> 
  <td class="contentMainTop"><span class="fontBold">{lang msgID="to"}:</span></td>
  <td class="contentMainFirst" align="center" colspan="2"><span class="fontBold">{$nick}</span></td>
</tr>
<tr>
  <td class="contentMainFirst" align="center" colspan="3">
    <textarea id="eintrag" name="eintrag" cols="0" rows="0" class="editorStyle">{$zitat}</textarea>
  </td>
</tr>
<tr>
  <td class="contentBottom" colspan="3"><input id="contentSubmit" type="submit" value="{lang msgID="button_value_msg"}" class="submit" /></td>
</tr>
</table>
</form>
</td>
</tr>