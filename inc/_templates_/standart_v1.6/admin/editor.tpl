<tr>
  <td class="contentHead" colspan="3" align="center"><span class="fontBold">{lang msgID="editor_head"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" colspan="3"><span class="fontBold">{lang msgID="editor_name"}</span></td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="3">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="editor" />
      <input type="hidden" name="do" value="add" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="editor_add_head"}" />
    </form>
  </td>
</tr>