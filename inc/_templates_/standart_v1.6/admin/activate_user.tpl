<tr>
  <td colspan="13" class="contentHead" align="center"><span class="fontBold">{lang msgID="config_activate_user"}</span></td>
</tr>
<form name="akl_from" id="akl_from" method="post" action="">
<input name="do" type="hidden" value="" />
<tr>
  <td colspan="3" class="contentMainTop"><span class="fontBold">{lang msgID="nick"}</span></td>
  <td align="center" class="contentMainTop"><span class="fontBold">{lang msgID="admin_akl_activated"}</span></td>
  <td width="1%" class="contentMainTop"><span class="fontBold">{lang msgID="emailicon_blank"}</span></td>
  <td width="1%" colspan="3" class="contentMainTop"><span class="fontBold" style="text-align: center;">{lang msgID="aktion"}</span></td>
  <td width="1%" class="contentMainTop"><span class="fontBold"><input onclick="DZCP.check_all('userid[]',this)" type="checkbox"></span></td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="13">
      <div align="right">
        <button id="rerun">Auswahl</button>
        <button id="select">Action w&auml;hlen</button>
      </div>
      <ul >
        <li><a href="#" onClick="DZCP.sendFrom('do','send-all','akl_from');">{lang msgID="akl_send"}</a></li>
        <li><a href="#" onClick="DZCP.sendFrom('do','enable-all','akl_from');">{lang msgID="button_activate_user"}</a></li>
        <li><a href="#" onClick="DZCP.sendFrom('do','delete-all','akl_from');">{lang msgID="button_del_user"}</a></li>
      </ul>
</td>
</tr>
</form>