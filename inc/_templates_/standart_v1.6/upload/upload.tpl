
<tr>
  <td>
    <form enctype="multipart/form-data" action="{$action}" method="post" onsubmit="return(DZCP.submitButton())"> 
    <table class="hperc" cellspacing="1">
      <tr>
       <td class="contentHead" colspan="2" align="center"><span class="fontBold">{$uploadhead}</span></td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="upload_file"}:</span></td>
        <td class="contentMainFirst" align="center"><input type="file" name="{$name}" /></td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="upload_info"}:</span></td>
        <td class="contentMainFirst" align="center">{$infos}</td>
      </tr>
      <tr>
        <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{lang msgID="button_value_upload"}" class="submit" /></td>
      </tr>
    </table>
    </form>
  </td>
</tr>