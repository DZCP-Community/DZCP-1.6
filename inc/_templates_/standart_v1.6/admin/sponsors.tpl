<tr>
  <td class="contentHead" colspan="4" align="center"><span class="fontBold">{$head}</span></td>
</tr>
<tr>
  <td class="contentMainTop" colspan="1"><span class="fontBold">{$sname}</span></td>
  <td class="contentMainTop" colspan="3"><span class="fontBold">{$slink}</span></td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="4">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="sponsors" />
      <input type="hidden" name="do" value="new" />
      <input id="contentSubmit" type="submit" class="submit" value="{$add}" />
    </form>
  </td>
</tr>
