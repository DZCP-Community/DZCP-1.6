<tr>
  <td>
    <form name="admin_self" action="?action=admin&amp;do=updateme" method="post" onsubmit="return(DZCP.submitButton())">
      <table class="hperc" cellspacing="1">
        <tr>
          <td class="contentHead" align="center" colspan="3"><span class="fontBold">{lang msgID="admin_user_squadhead"}</span></td>
        </tr>
        <tr>
          <td class="contentMainTop" width="150"><span class="fontBold">{lang msgID="member_admin_squad"}:</span></td>
          <td class="contentMainFirst" colspan="2">
            <table width="100%">
              {$esquad}
            </table>
          </td>
        </tr>
        <tr>
          <td class="contentBottom" colspan="3"><input id="contentSubmit" type="submit" value="{lang msgID="button_value_edit"}" class="submit"></td>
        </tr>
      </table>
    </form>
  </td>
</tr>
