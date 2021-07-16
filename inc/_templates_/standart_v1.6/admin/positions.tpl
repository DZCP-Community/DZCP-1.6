<tr>
  <td class="contentHead" colspan="3" align="center"><span class="fontBold">{lang msgID="admin_pos"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" colspan="3"><span class="fontBold">{lang msgID="description"}</span></td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="3">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="positions" />
      <input type="hidden" name="do" value="new" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="pos_new_head"}" />
    </form>
  </td>
</tr>
