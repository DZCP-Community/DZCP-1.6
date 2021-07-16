<tr>
  <td class="contentHead" colspan="4" align="center"><span class="fontBold">{lang msgID="kalender_admin_head"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" width="120" nowrap="nowrap"><span class="fontBold">{lang msgID="datum"}&nbsp;<a href="{$order_date}"><img src="{idir}/order.gif" alt="" class="icon" /></a></span></td>
  <td class="contentMainTop" colspan="3"><span class="fontBold">{lang msgID="kalender_event"}&nbsp;<a href="{$order_titel}"><img src="{idir}/order.gif" alt="" class="icon" /></a></span></td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="4">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="kalender" />
      <input type="hidden" name="do" value="add" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="kalender_admin_head_add"}" />
    </form>
  </td>
</tr>
