<tr>
  <td class="contentHead" colspan="5" align="center"><span class="fontBold">{$head}</span></td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{$mainkat}</span></td>
  <td class="contentMainTop" width="1%"><span class="fontBold">{$status}</span></td>
  <td class="contentMainTop" colspan="3" width="1%"><span class="fontBold">{$skats}</span></td>
</tr>
{$kats}
<tr>
  <td class="contentBottom" colspan="5">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="forum" />
      <input type="hidden" name="do" value="newkat" />
      <input id="contentSubmit" type="submit" class="submit" value="{$add}" />
    </form>
  </td>
</tr>