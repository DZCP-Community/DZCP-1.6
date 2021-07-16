<!-- start comments.tpl -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  {if !$add}
    <tr>
      <td class="contentMainFirst" align="center" colspan="2">
        <span class="fontBold">{lang msgID="error_unregistered_nc"}</span>
      </td>
    </tr>
  {else}
    {$add}
  {/if}
  {$show}
  <tr>
    <td class="contentHead" align="center" colspan="2">
      <table class="hperc" cellspacing="0">
        <tr>
          <td colspan="2" class="gray"><a name="lastcomment"></a><span class="fontBold">{lang msgID="comments_head"}</span></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <div style="float: right;">{$seiten}</div>
    </td>
  </tr>
</table>
<!-- end comments.tpl -->