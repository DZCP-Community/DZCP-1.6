<tr>
  <td>
    <script language="javascript" type="text/javascript">
     <!--
      var prevURL = '../admin/?admin=nletter&do=preview';
     //-->
    </script>
    <form id="nletterForm" name="nletterForm" action="?admin=nletter&amp;do=send" method="post" onsubmit="return(DZCP.submitButton())">
    <table class="hperc" cellspacing="1">
      <tr>
        <td class="contentHead" colspan="3" align="center"><span class="fontBold">{$titel}</span></td>
      </tr>
      {$error}
      <tr>
        <td class="contentMainTop"><span class="fontBold">{$an}:</span></td>
        <td class="contentMainFirst" align="center" colspan="2">
          <select name="to" class="selectpicker">
            <option class="selectpicker" value="-">- {$who} - </option>
            <option value="reg" {$selr}>-> {$reg}</option>
            <option value="member" {$selm}>-> {$allmembers}</option>
            <option class="selectpicker" value="-">{$squad}</option>
            {$squads}
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentMainFirst" align="center" colspan="3">
          <textarea id="eintrag" name="eintrag" cols="0" rows="0" class="editorStyleNewsletter">{$posteintrag}</textarea>
        </td>
      </tr>
      <tr>
        <td class="contentBottom" colspan="3"><input id="contentSubmit" type="submit" value="{$value}" class="submit" /> <input type="button" value="{$preview}" class="submit" onclick="DZCP.ajaxPreview('nletterForm')" /></td>
      </tr>
    </table>
    </form>
  </td>
</tr>
</table>
<table class="hperc" cellspacing="0">
  <tr>
    <td>
      <div id="previewDIV"></div>
    </td>
  </tr>