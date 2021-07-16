<tr>
  <td colspan="4" class="contentHead" align="center"><span class="fontBold">{lang msgID="tutorials"} - {lang msgID="tutorials_kategorie"}</span></td>
</tr>
<tr>
  <td width="85%" class="contentMainTop"><span class="fontBold">{lang msgID="tutorials_bezeichnung"}</span></td>
  <td width="15%" class="contentMainTop" align="center"><span class="fontBold">{lang msgID="tutorials_anzahl_tutorials"}</span></td>
  <td colspan="2" class="contentMainTop"></td>	
</tr>
{$kats}
<tr>
  <td colspan="4" class="contentBottom" align="center">
    <form action="index.php" method="get" onsubmit="return sendMe()">
      <input type="hidden" name="admin" value="tutorials" />
      <input type="hidden" name="do" value="kat_new" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="tutorials_new_kat"}" />
    </form>
  </td>		
</tr>