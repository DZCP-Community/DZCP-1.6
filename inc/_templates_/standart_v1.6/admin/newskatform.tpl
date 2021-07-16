<tr>
  <td class="contentHead" colspan="2" align="center"><span class="fontBold">{$head}</span></td>
</tr>
<tr>
<td>
<form name="newskat" enctype="multipart/form-data" action="?admin=news&amp;do={$do}" method="post" onsubmit="return(DZCP.submitButton())"> 
<table class="hperc" cellspacing="1">
<tr>
  <td class="contentMainTop" width="150"><span class="fontBold">{lang msgID="config_katname"}:</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="kat" value="{$kat}" class="inputField_dis"
    onfocus="this.className='inputField_en';"
    onblur="this.className='inputField_dis';" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="config_newskats_katbild"}:</span></td>
  <td class="contentMainFirst" align="center">
    <select name="img" class="selectpicker">
      {$img}
    </select>
    <br />{$upload}
  </td>
</tr>
  <tr>
    <td class="contentMainTop"><span class="fontBold">{lang msgID="color_title"}:</span></td>
    <td class="contentMainFirst" align="center">
      <input type="text" name="color" id="colorpicker" value="{$color}" class="inputField_dis_mid"
             onfocus="this.className='inputField_en_mid';"
             onblur="this.className='inputField_dis_mid';" />
    </td>
  </tr>
<tr>
  <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{$value}" class="submit" /> | <input type="button" value="{lang msgID="paginator_previous"}" class="submit" onclick="DZCP.goTo('../admin/?admin=news')" tabindex="6"></td>
</tr>
</table>
</form>
</td>
</tr>