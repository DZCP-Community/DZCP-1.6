<tr>
<td>
<form name="partnerbuttons" action="?admin=partners&amp;do={$do}" method="post" onsubmit="return(DZCP.submitButton())">
<table class="hperc" cellspacing="1">
<tr>
  <td class="contentHead" align="center" colspan="2"><span class="fontBold">{$head}</span></td>
</tr>
<tr>
  <td class="contentMainTop" width="100"><span class="fontBold">{$link}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="link" value="{$e_link}" class="inputField_dis"
    onfocus="this.className='inputField_en';" onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{$banner}:</span></td>
  <td class="contentMainFirst" align="center">
    <select name="banner" class="selectpicker">
      {lang msgID="nothing"}
      {$banners}
    </select>
    <a href="../upload/?action=partners">upload</a>
  </td>
</tr>
<tr>
  <td class="contentMainTop" colspan="2" style="text-align:center"><span class="fontBold">{$or}</span></td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{$textlink}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="textlink" value="{$e_textlink}" class="inputField_dis"
    onfocus="this.className='inputField_en';" onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{$what}" class="submit" /></td>
</tr>
</table>
</form>
</td>
</tr>