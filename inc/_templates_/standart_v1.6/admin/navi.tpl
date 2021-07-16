<tr>
  <td class="contentHead" colspan="5" align="center"><span class="fontBold">{lang msgID="menu_kats_head"}</span></td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="config_newskats_kat"}</span></td>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="placeholder"}</span></td>
  <td class="contentMainTop" colspan="2">&nbsp;</td>
</tr>
{$show_kats}
<tr>
  <td class="contentBottom" colspan="5">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="navi" />
      <input type="hidden" name="do" value="addkat" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="menu_add_kat"}" />
    </form>
  </td>
</tr>
</table>

<table class="mainContent" cellspacing="1">
<tr>
  <td class="contentHead" colspan="8" align="center"><span class="fontBold">{lang msgID="menu_kats_head"}</span></td>
</tr>
<tr>
  <td class="contentMainFirst" colspan="8" align="center">{lang msgID="navi_info"}</td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="navi_name"}</span></td>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="config_newskats_kat"}</span></td>
  <td class="contentMainTop"><span class="fontBold">{lang msgID="navi_url"}</span></td>
  <td class="contentMainTop" style="width:1%"><span class="fontBold">{lang msgID="navi_shown"}</span></td>
  <td class="contentMainTop" colspan="2">&nbsp;</td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="8">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="navi" />
      <input type="hidden" name="do" value="add" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="navi_add_head"}" />
    </form>
  </td>
</tr>