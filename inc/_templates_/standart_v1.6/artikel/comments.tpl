<!-- start comments.tpl -->
<tr>
  <td class="contentHead" align="center" colspan="2">
    <table class="hperc" cellspacing="0">
      <tr>
        <td class="gray"><a name="lastcomment"></a><span class="fontBold">{lang msgID="comments_head"}</span></td>
        <td class="gray" style="text-align:right">{$seiten}</td>
      </tr>
    </table>
  </td>
</tr>
{$show}
{if !$add}
  <tr>
    <td class="contentMainFirst" align="center" colspan="2">
      <span class="fontBold">{lang msgID="error_unregistered_nc"}</span>
    </td>
  </tr>
{else}
    {$add}
{/if}
<!-- end comments.tpl -->