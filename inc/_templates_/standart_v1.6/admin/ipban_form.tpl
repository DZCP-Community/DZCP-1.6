<form name="dlkats" action="?index=admin&amp;admin=ipban&amp;do={$do}" method="post" onsubmit="return(DZCP.submitButton())">
<tr>
  <td class="contentHead" align="center" colspan="2"><span class="fontBold">{$newhead}</span></td>
</tr>
<tr>
  <td class="contentMainTop" width="100"><span class="fontBold">{lang msgID="server_ip"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="ip" value="{$ip_set}" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';">
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="160"><span class="fontBold">{lang msgID="ipban_dis"}:</span></td>
  <td class="contentMainFirst" colspan="2" align="center">
  <input type="text"  name="info" value="{$info}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" /></td>
<tr>
  <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{$what}" class="submit"></td>
</tr>
</form>