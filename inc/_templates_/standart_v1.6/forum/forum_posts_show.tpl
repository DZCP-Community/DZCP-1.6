<tr>
  <td colspan="2" class="contentMainTop">
    <table class="hperc">
      <tr>
        <td style="vertical-align:middle"><a name="p{$p}"></a>{$titel}</td>
        <td style="text-align:right">{if $chkme >= 1 && !$closed || $permission}{$zitat}{/if}</td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="commentsLeft">
    <table class="hperc">
      <tr>
        <td>{$nick}</td>
        <td align="right">{$onoff}</td>
      </tr>
      <tr>
        <td colspan="2"><span class="fontItalic">{$status}</span></td>
      </tr>
      <tr>
        <td>{$posts}</td>
      </tr>
      <tr>
        <td colspan="2" style="text-align:center;padding:11px">{$avatar}</td>
      </tr>
    </table>
  </td>
  <td {$class}>
    <table class="hperc">
      <tr>
        <td height="108">{$text}</td>
      </tr>
      <tr>
        <td><span class="fontItalic">{$edited}&nbsp;</span>{$signatur}</td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop" align="center">{$ip}</td>
  <td class="contentMainTop" style="text-align:left;vertical-align:middle">{$pn} {$hp}</td>
</tr>
<tr>
  <td style="height: 20px"></td>
</tr>