<tr>
  <td class="contentHead" colspan="5" align="center"><span class="fontBold">{lang msgID="member_admin_header"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" colspan="3"><span class="fontBold">{lang msgID="member_admin_squad"}</span></td>
</tr>
{$groups}
<tr>
  <td class="contentBottom" colspan="5">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="gruppen" />
      <input type="hidden" name="do" value="add" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="member_admin_add_header"}" />
    </form>
  </td>
</tr>