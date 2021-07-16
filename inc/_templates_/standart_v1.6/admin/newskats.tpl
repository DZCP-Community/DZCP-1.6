<tr>
  <td class="contentHead" colspan="3"><span class="fontBold">{lang msgID="config_newskats_head"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" colspan="3"><span class="fontBold">{lang msgID="config_newskats_kat"}</span></td>
</tr>
{$kats}
<tr>
  <td class="contentBottom" colspan="3">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="news" />
      <input type="hidden" name="do" value="add" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="config_newskats_add_head"}" />
    </form>
  </td>
</tr>