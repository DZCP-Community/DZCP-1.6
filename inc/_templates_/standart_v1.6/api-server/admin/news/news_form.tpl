<tr>
  <td class="contentHead" align="center" colspan="2"><span class="fontBold">{lang msgID="news_admin_head"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" colspan="4">
    <script language="javascript" type="text/javascript">
      var prevURL = '../admin/?admin=api_server&module=news&action=preview';
    </script>
    <div style="margin-right: 10px;margin-left: 10px;">
      <form name="news" action="" method="post" onsubmit="return(DZCP.submitButton())">
        <table class="hperc" cellspacing="1">
          <tr>
            <td class="contentHead" colspan="2" align="center"><span class="fontBold">{$head}</span></td>
          </tr>
          <tr>
            <td class="contentMainTop"><span class="fontBold">Version:</span></td>
            <td class="contentMainFirst" align="center">
              <select name="version" class="selectpicker">
                {$version}
              </select>
            </td>
          </tr>
          <tr>
            <td class="contentMainTop"><span class="fontBold">{lang msgID="news_userimage"}:</span></td>
            <td class="contentMainFirst" align="center">
              {$n_newspic}{$delnewspic}
              <input type="file" name="newspic">
            </td>
          </tr>
          <tr>
            <td class="contentMainTop"><span class="fontBold">{lang msgID="titel"}:</span></td>
            <td class="contentMainFirst" align="center">
              <input type="text" name="titel" value="{$titel}" class="inputField_dis"
                     onfocus="this.className='inputField_en';"
                     onblur="this.className='inputField_dis';" />
            </td>
          </tr>
          <tr>
            <td class="contentMainFirst" align="center" colspan="2">
              <textarea id="text" name="text" cols="0" rows="0" class="editorStyleWord">{$text}</textarea>
            </td>
          </tr>
          <tr>
            <td class="contentMainTop"><span class="fontBold">{lang msgID="url"}:</span></td>
            <td class="contentMainFirst" align="center">
              <input type="text" name="url" value="{$url}" class="inputField_dis"
                     onfocus="this.className='inputField_en';"
                     onblur="this.className='inputField_dis';" />
            </td>
          </tr>
          <tr>
            <td class="contentBottom" colspan="2">
              <input name="submit" id="contentSubmit"  type="submit" value="{$what}" class="submit" />
              <input type="button" value="{lang msgID="preview"}" class="submit" onclick="DZCP.ajaxPreview('newsAPIForm')" /></td>
          </tr>
        </table>
      </form>
    </div>
  </td>
</tr>
<tr>
  <td>
    <div id="previewDIV"></div>
  </td>
</tr>