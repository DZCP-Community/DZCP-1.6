<!-- start comments.tpl -->
{if !$add and !$permissions}
<table id="comments" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="contentMainFirst" align="center" colspan="2">
      <span class="fontBold">{lang msgID="error_unregistered_nc"}</span>
    </td>
  </tr>
</table>
{elseif !$add}
{$notification}
{else}
{$add}
{/if}
<table class="mainContent" cellspacing="0" style="margin-top: 15px;">
  <tr>
    <td>
      <table class="hperc" cellspacing="1">
        <tr>
          <td class="contentHead" align="center" colspan="2">
            <span class="fontBold">{lang msgID="comments_head"}</span>
          </td>
        </tr>
        <tr>
          <td id="comments" class="contentMainFirst" align="center" colspan="2">
            {$show}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<table id="comments" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2">
      <a name="lastcomment"></a>
      <div style="float: right;">{$seiten}</div>
    </td>
  </tr>
</table>
<!-- end comments.tpl -->