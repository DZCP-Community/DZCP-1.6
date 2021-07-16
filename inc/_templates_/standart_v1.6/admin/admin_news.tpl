<tr>
  <td class="contentHead" colspan="6" align="center"><span class="fontBold">{lang msgID="news_admin_head"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" colspan="6" style="text-align:center">{$nav}</td>
</tr>
<tr>
  <td class="contentMainTop" width="120"><span class="fontBold">{lang msgID="datum"}&nbsp;<a href="{$order_date}"><img src="{idir}/order.gif" alt="" class="icon" /></a></span></td>
  <td class="contentMainTop" width="200"><span class="fontBold">{lang msgID="titel"}&nbsp;<a href="{$order_titel}"><img src="{idir}/order.gif" alt="" class="icon" /></a></span></td>
  <td class="contentMainTop" colspan="4"><span class="fontBold">{lang msgID="autor"}&nbsp;<a href="{$order_autor}"><img src="{idir}/order.gif" alt="" class="icon" /></a></span></td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="6">
    <form action="?admin=newsadmin&do=add" method="post">
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="admin_news_head"}" />
    </form>
  </td>
</tr>
