<tr>
  <td class="contentHead" colspan="2" align="center"><span class="fontBold">{$head}</span></td>
</tr>
<tr>
	<td>
	<form name="linksadmin" action="?admin=links&amp;do={$do}" method="post" onsubmit="return(DZCP.submitButton())">
		<table class="hperc" cellspacing="1">
		<tr>
		  <td class="contentMainTop"><span class="fontBold">{lang msgID="links_link"}:</span></td>
		  <td class="contentMainFirst" align="center">
			<input type="text" name="link" value="{$llink}" class="inputField_dis"
			onfocus="this.className='inputField_en'"
			onblur="this.className='inputField_dis'" />
		  </td>
		</tr>
		<tr>
		  <td class="contentMainTop"><span class="fontBold">{lang msgID="links_beschreibung"}:</span></td>
		  <td class="contentMainFirst" align="center">
			<textarea id="beschreibung" name="beschreibung" cols="0" rows="0" class="editorStyleMini">{$lbeschreibung}</textarea>
		  </td>
		</tr>
		<tr>
		  <td class="contentMainTop"><span class="fontBold">{lang msgID="links_art"}:</span></td>
		  <td class="contentMainFirst" align="center">
			<table class="hperc" cellspacing="0">
			  <tr>
				<td style="width:20px"><input id="banner" class="checkbox" type="checkbox" name="banner" value="1" {$bchecked} onchange="DZCP.toggle('hideBanner')" /></td>
				<td>{lang msgID="links_admin_bannerlink"}</td>
			  </tr>
			</table>   
		  </td>
		</tr>
		<tr id="morehideBanner" style="{$bnone}">
		  <td class="contentMainTop"><span class="fontBold">{lang msgID="links_text"}:</span></td>
		  <td class="contentMainFirst" align="center">
			<input type="text" name="text" value="{$ltext}" class="inputField_dis"
			onfocus="this.className='inputField_en'"
			onblur="this.className='inputField_dis'" />
		  </td>
		</tr>
		<tr>
		  <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{$what}" class="submit" /></td>
		</tr>
		</table>
	</form>
	</td>
</tr>