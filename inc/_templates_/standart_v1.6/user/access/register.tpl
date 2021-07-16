<tr>
  <td class="contentHead" colspan="2" align="center"><span class="fontBold">{lang msgID="register_head"}</span></td>
</tr>
{if !$lock}
{$notification_page}
<tr>
<td>
<form name="reg" action="?action=register&amp;do=add" method="post" onsubmit="return(DZCP.submitButton())">
<table class="hperc" cellspacing="1">
<tr>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="loginname"}:</span> <span class="fontRed">*</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="user" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" value="{$r_name}" />
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="nick"}:</span> <span class="fontRed">*</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="nick" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" value="{$r_nick}" />
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="email"}:</span> <span class="fontRed">*</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="email" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" value="{$r_email}" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="pwd"}:</span></td>
  <td class="contentMainFirst" align="center">
	<input type="password" name="pwd" class="inputField_dis"
	onfocus="this.className='inputField_en';"
	onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="pwd2"}:</span></td>
  <td class="contentMainFirst" align="center">
	<input type="password" name="pwd2" class="inputField_dis"
	onfocus="this.className='inputField_en';"
	onblur="this.className='inputField_dis';" />
  </td>
</tr>
{$regcode}
<tr>
  <td class="contentMainTop" colspan="2" align="center">{lang msgID="contact_pflichtfeld"}</td>
</tr>
<tr>
  <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{lang msgID="button_value_reg"}" class="submit" name="send" /></td>
</tr>
</table>
</form>
</td>
</tr>
{else}
    <tr>
        <td>
            <div align="center">
                <br><p><span style="font-size: 13px;">Diese Funktion steht nur zur Verfügung, wenn du die Datenschutz-Grundverordnung (EU-DSGVO) akzeptiert hast.</span>
                <p><span style="font-size: 13px;">Diese findest du hier: <a href="sdfsdfsdfsd" target="_parent">{$dsgvo_url}</a></span><br><br>
            </div>
        </td>
    </tr>
{/if}