<tr>
  <td class="contentHead" style="text-align:left"><span class="fontBold">{lang msgID="info"}:</span></td>
</tr>
<tr>
  <td class="contentMainFirst" align="center">
    <span class="fontBold">{$msg}</span>
    <div align="right">
      <form action="{$url}" method="get" onsubmit="return(DZCP.submitButton('infoSubmit'))">
        {$parts}
        <input type="submit" id="infoSubmit" class="submit" value="{lang msgID="weiter"}" />
      </form>
    </div>
      {if $timeout >= 1}
      <script language="javascript" type="text/javascript">
          window.setTimeout("DZCP.goTo('{$rawurl}');", {$timeout}000);
      </script>
      <noscript><meta http-equiv="refresh" content="{$timeout};url={$rawurl}"></noscript>
      {/if}
  </td>
</tr>