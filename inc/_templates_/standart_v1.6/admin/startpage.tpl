<tr>
  <td class="contentHead" colspan="4" align="center"><span class="fontBold">{lang msgID="config_startpage"}</span></td>
</tr>
<tr>
  <td width="30%" class="contentMainTop"><span class="fontBold">{lang msgID="admin_bezeichnung"}</span></td>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="admin_startpage_url"}</span></td>
  <td class="contentMainTop" colspan="2"></td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="4">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="startpage" />
      <input type="hidden" name="do" value="new" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="admin_startpage_add"}" />
    </form>
  </td>
</tr>