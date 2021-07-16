<tr>
  <td class="contentHead" colspan="7" align="center"><span class="fontBold">{lang msgID="config_startpage"}</span></td>
</tr>
<tr>
  <td width="20%" class="contentMainTop"><span class="fontBold">ModID</span></td>
  <td width="30%" class="contentMainTop"><span class="fontBold">Name</span></td>
  <td class="contentMainTop"><span class="fontBold">Version</span></td>
  <td class="contentMainTop"><span class="fontBold">Ziel-Edition</span></td>
  <td class="contentMainTop"><span class="fontBold">Ziel-Version</span></td>
  <td class="contentMainTop" colspan="2"></td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="7">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="startpage" />
      <input type="hidden" name="do" value="new" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="admin_startpage_add"}" />
    </form>
  </td>
</tr>