<tr>
  <td>
    <script language="javascript" type="text/javascript">
     <!--
      var prevURL = '../sites/?action=preview';
     //-->
    </script>
    <form id="editorForm" name="editorForm" action="?admin=editor&amp;do={$do}" method="post" onsubmit="return(DZCP.submitButton())">
    <table class="hperc" cellspacing="1">
      <tr>
        <td class="contentHead" align="center" colspan="2"><span class="fontBold">{$head}</span></td>
      </tr>
      {$error}
      <tr>
        <td class="contentMainTop"><span class="fontBold">{$name}:</span></td>
        <td class="contentMainFirst" align="center">
          <input type="text" name="name" value="{$n_name}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{$pos}:</span></td>
        <td class="contentMainFirst" align="center">
          <select name="pos" class="selectpicker">
            {$position}
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{$allow_html}:</span></td>
        <td class="contentMainFirst" align="center">
          <input class="checkbox" type="checkbox" name="html" value="1" {$checked} />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{lang msgID="editor_allow_php"}:</span></td>
        <td class="contentMainFirst" align="center">
          <input {$disabled_php} class="checkbox" type="checkbox" name="php" value="1" {$checked_php} />
        </td>
      </tr>
      <tr>
        <td class="contentMainTop"><span class="fontBold">{$titel}:</span></td>
        <td class="contentMainFirst" align="center">
          <input type="text" name="titel" value="{$e_titel}" class="inputField_dis"
          onfocus="this.className='inputField_en';"
          onblur="this.className='inputField_dis';" />
        </td>
      </tr>
      <tr>
        <td class="contentMainFirst" align="center" colspan="3">
        <textarea id="inhalt" name="inhalt" cols="0" rows="0" class="editorStyleWord">{$e_inhalt}</textarea>
        </td>
      </tr>
      <tr>
        <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{$what}" class="submit" /> <input type="button"  value="{$preview}" class="submit" onclick="DZCP.ajaxPreview('editorForm')" /></td>
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