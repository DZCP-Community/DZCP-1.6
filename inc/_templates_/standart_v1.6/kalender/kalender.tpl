<tr>
  <td class="contentHead" align="center" colspan="7"><span class="fontBold">{lang msgID="kalender_head"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" colspan="7" align="center">
  <form name="kalender" action="" method="post" onsubmit="return(DZCP.submitButton())">
    <select name="monat" class="selectpicker">{$monate}</select>
    <select name="jahr" class="selectpicker">{$jahr}</select>
    <input id="contentSubmit" type="submit" value="{lang msgID="button_value_show"}" class="submit" />
  </form>
  </td>
</tr>
<tr>
  <td class="contentMainTop" align="center"><span class="fontBold">{lang msgID="montag"}</span></td>
  <td class="contentMainTop" align="center"><span class="fontBold">{lang msgID="dienstag"}</span></td>
  <td class="contentMainTop" align="center"><span class="fontBold">{lang msgID="mittwoch"}</span></td>
  <td class="contentMainTop" align="center"><span class="fontBold">{lang msgID="donnerstag"}</span></td>
  <td class="contentMainTop" align="center"><span class="fontBold">{lang msgID="freitag"}</span></td>
  <td class="contentMainTop" align="center"><span class="fontBold">{lang msgID="samstag"}</span></td>
  <td class="contentMainTop" align="center"><span class="fontBold">{lang msgID="sonntag"}</span></td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="7">&nbsp;</td>
</tr>