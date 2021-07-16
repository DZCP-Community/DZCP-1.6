<tr>
  <td class="contentHead" style="text-align:left"><span class="fontBold">{lang msgID="error"}:</span></td>
</tr>
<tr>
  <td class="contentMainFirst" align="center">
    <span class="fontBold">{$error}</span>
      {if $back >= 1}<div align="right">
        <input type="button" class="submit" value="{lang msgID="error_back"}" onclick="history.go(-{$back})" />
      </div>{/if}
  </td>
</tr>