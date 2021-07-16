<div style="margin-top:20px;"></div>
<script language="javascript" type="text/javascript">
    <!-- Preview -->
    var prevURL = '../forum/?action=preview&what=post&do={if $is_edit}editpost{else}addpost{/if}{if $id >= 1}&id={$id}{/if}';
</script>
<table class="mainContent" cellspacing="0" style="margin-top:0">
<tr>
  <td>
    <form id="fpostForm" name="fpostForm" action="?action=post&amp;do={if $is_edit}edit{else}add{/if}&amp;id={$id}" method="post" onsubmit="return(DZCP.submitButton())">
    <table class="hperc" cellspacing="1">
      <tr>
        <td class="contentHead" colspan="2" align="center"><span class="fontBold">{if $is_edit}{lang msgID="forum_edit_post_head"}{else}{lang msgID="forum_new_post_head"}{/if}</span></td>
      </tr>
      {$notification}
      {$from}
      <tr>
        <td class="contentMainFirst" align="center" colspan="2">
          <textarea id="eintrag" name="eintrag" cols="0" rows="0" class="editorStyle" >{$zitat}{$posteintrag}</textarea>
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" colspan="2" align="center">{lang msgID="iplog_info"}</td>
      </tr>
      <tr>
        <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{if $is_edit}{lang msgID="button_value_edit"}{else}{lang msgID="button_value_add"}{/if}" class="submit" name="send" />
          <input type="button"  value="{lang msgID="preview"}" class="submit" onclick="DZCP.ajaxPreview('fpostForm')" /></td>
      </tr>
    </table>
    </form>
  </td>
</tr>
</table>
<table class="hperc" cellspacing="0">
  <tr>
    <td>
      <div id="previewDIV"></div>
    </td>
  </tr>
</table>
{if $br}
<table class="mainContent" cellspacing="1">
  <tr>
    <td class="contentHead" colspan="2" align="center"><span class="fontBold">{lang msgID="forum_lp_head"}</span></td>
  </tr>
  {$lastpost}
</table>
{/if}