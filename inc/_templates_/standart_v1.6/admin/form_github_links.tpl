<tr>
  <td class="contentHead" colspan="2" align="center"><span class="fontBold">{$head}</span></td>
</tr>
<tr>
	<td>
	<form name="linksadmin" action="?admin=github&do={$do}" method="post" onsubmit="return(DZCP.submitButton())">
		<table class="hperc" cellspacing="1">
            <tr>
              <td class="contentMainTop"><span class="fontBold">{lang msgID="profil_real"}:</span></td>
              <td class="contentMainFirst info" align="center">
                <input type="text" name="name" value="{$name}" class="inputField_dis"
                onfocus="this.className='inputField_en'"
                onblur="this.className='inputField_dis'" />
              </td>
            </tr>
			<tr>
				<td class="contentMainTop"><span class="fontBold">{lang msgID="links_link"}:</span></td>
				<td class="contentMainFirst info" align="center">
					<input type="text" name="link" value="{$link}" class="inputField_dis"
						   onfocus="this.className='inputField_en'"
						   onblur="this.className='inputField_dis'" />
				</td>
			</tr>
            <tr>
                <td class="contentMainTop"><span class="fontBold">{lang msgID="github_link_on_off"}:</span></td>
                <td class="contentMainFirst info" align="center">
                    <select name="enabled" class="selectpicker">
                        <option value="0">{lang msgID="off"}</option>
                        <option value="1" {$selected}>{lang msgID="on"}</option>
                    </select>
                </td>
            </tr>
		<tr>
		  <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{$what}" class="submit" /></td>
		</tr>
		</table>
	</form>
	</td>
</tr>