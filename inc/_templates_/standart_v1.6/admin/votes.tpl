<tr>
  <td class="contentHead" colspan="6" align="center"><span class="fontBold">{lang msgID="votes_head"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" style="width:70px"><span class="fontBold">{lang msgID="datum"}</span></td>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="titel"}</span></td>
  <td class="contentMainTop" colspan="6"><span class="fontBold">{lang msgID="autor"}</span></td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="6">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="votes" />
      <input type="hidden" name="do" value="new" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="votes_admin_head"}" />
    </form>
  </td>
</tr>
</table>
<table class="mainContent" cellspacing="1">
<tr>
  <td class="contentHead" colspan="6" style="text-align:left"><span class="fontBold">{lang msgID="legende"}:</span></td>
</tr>
<tr>
  <td class="contentMainFirst" colspan="6" align="center"><img src="../inc/images/yesno.gif" alt="" class="icon" /> = <span class="fontItalic">{lang msgID="vote_legendemenu"}</span></td>
</tr>