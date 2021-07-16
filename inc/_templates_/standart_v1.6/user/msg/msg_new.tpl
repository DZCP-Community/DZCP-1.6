{$notification_page}
<tr>
  <td>
    <form name="sendmsg" action="?action=msg&amp;do=send" method="post" onsubmit="return(DZCP.submitButton())">
    <table class="hperc" cellspacing="1">
      <tr>
        <td class="contentHead" colspan="3" align="center"><span class="fontBold">{lang msgID="msg_titel"}</span></td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="to"}:</span></td>
        <td class="contentMainFirst" align="center" colspan="2">
          <select name="buddys" style="width: 100px;" class="selectpicker">
            <option value="-" class="selectpicker">- Buddy - </option>
            {$buddys}
          </select> {lang msgID="or"}
          <select name="users" style="width: 100px;" class="selectpicker">
            <option value="-" class="selectpicker">- User -</option>
            {$users}
          </select>
        </td>
      </tr>
      <tr> 
        <td class="contentMainTop"><span class="fontBold">{lang msgID="titel"}:</span></td>
        <td class="contentMainFirst" align="center" colspan="2">
          <input type="text" name="titel" value="{$posttitel}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainFirst" align="center" colspan="3">
          <textarea id="eintrag" name="eintrag" cols="0" rows="0" class="editorStyle">{$posteintrag}</textarea>
        </td>
      </tr>
      <tr>
      <td class="contentBottom" colspan="3"><input id="contentSubmit" type="submit" value="{lang msgID="button_value_msg"}" class="submit" name="send" /></td>
      </tr>
    </table>
    </form>
  </td>
</tr>