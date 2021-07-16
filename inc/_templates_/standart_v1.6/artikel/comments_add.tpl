<!-- start comments_add.tpl -->
</table>
<table class="mainContent" cellspacing="0">
<tr>
  <td>
    <script language="javascript" type="text/javascript">var prevURL = '{$prevurl}';</script>
    <form id="comForm" name="comForm" action="{$action}" method="post" onsubmit="return(DZCP.submitButton('contentSubmit'))">
    <table class="hperc" cellspacing="1">
      <tr>
        <td class="contentHead" colspan="2" align="center"><a name="eintragen"></a><span class="fontBold">{lang msgID="artikel_comments_write_head"}</span></td>
      </tr>
      {$notification}
      <tr>
        <td class="contentMainFirst" align="center" colspan="2">
          <textarea id="comment" name="comment" cols="0" rows="0" class="editorStyle">{$posteintrag}</textarea>
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" colspan="2" align="center">{lang msgID="iplog_info"}</td>
      </tr>
      <tr>
        <td colspan="2" class="contentBottom"><input id="contentSubmit" type="submit" value="{lang msgID="button_value_add"}" class="submit" name="send" />  <input type="button"  value="{lang msgID="preview"}" class="submit" onclick="DZCP.ajaxPreview('comForm')" /></td>
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
<!-- end comments_add.tpl -->