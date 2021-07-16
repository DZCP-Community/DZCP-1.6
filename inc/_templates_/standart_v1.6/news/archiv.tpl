<!-- start archiv.tpl -->
<tr>
  <td class="contentHead" colspan="5">
    <table class="hperc" cellspacing="0">
      <tr>
        <td class="gray"><span class="fontBold">{lang msgID="news_archiv_head"}</span></td>
        <td class="gray" style="text-align:right">{$nav}</td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop" width="10%"><span class="fontBold">{lang msgID="datum"}&nbsp;<a href="{$order_date}"><img src="{idir}/order.gif" alt="" class="icon" /></a></span></td>
  <td class="contentMainTop" width="35%"><span class="fontBold">{lang msgID="titel"}&nbsp;<a href="{$order_titel}"><img src="{idir}/order.gif" alt="" class="icon" /></a></span></td>
  <td class="contentMainTop" width="35%"><span class="fontBold">{lang msgID="news_admin_kat"}&nbsp;<a href="{$order_kat}"><img src="{idir}/order.gif" alt="" class="icon" /></a></span></td>
  <td class="contentMainTop" width="29%"><span class="fontBold">{lang msgID="autor"}&nbsp;<a href="{$order_autor}"><img src="{idir}/order.gif" alt="" class="icon" /></a></span></td>
  <td class="contentMainTop" align="center" width="1%"><span class="fontBold">#</span></td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="5">&nbsp;</td>
</tr>
<!-- end archiv.tpl -->