<table width="100%">
<tr>
    <td>
      <form name="contentLogin" action="../user/?action=login&amp;do=yes" method="post" onsubmit="return(DZCP.submitButton())">
        <table class="mainContent" cellspacing="1">
          <tr>
            <td class="contentHead" colspan="2" align="center"><span class="fontBold">{lang msgID="login_head_admin"}</span></td>
          </tr>
          <tr>
            <td class="contentMainTop"><span class="fontBold">{lang msgID="loginname"}:</span></td>
            <td class="contentMainFirst">
              <input type=text name="user" class="inputField_dis"
              onfocus="this.className='inputField_en';"
              onblur="this.className='inputField_dis';" />
            </td>
          </tr>
          <tr>
            <td class="contentMainTop"><span class="fontBold">{lang msgID="pwd"}:</span></td>
            <td class="contentMainFirst">
              <input type=password name="pwd" class="inputField_dis"
              onfocus="this.className='inputField_en';"
              onblur="this.className='inputField_dis';" required><br /><br />
            </td>
          </tr>
          {$secure}
          <tr>
            <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{lang msgID="login"}" class="submit" /></td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>