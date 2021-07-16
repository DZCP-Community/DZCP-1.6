  <tr>
    <td>
      <form name="contentLogin" action="?action=login&amp;do=yes" method="post">
        <table class="mainContent" cellspacing="1">
          <tr>
            <td class="contentHead" colspan="2" align="center"><span class="fontBold">{lang msgID="login_head"}</span></td>
          </tr>
          <tr>
            <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="loginname"}:</span></td>
            <td class="contentMainFirst">
              <input type=text name="user" class="inputField_dis"
                onfocus="this.className='inputField_en';"
                onblur="this.className='inputField_dis';"/>
            </td>
          </tr>
          <tr>
            <td class="contentMainTop"><span class="fontBold">{lang msgID="pwd"}:</span></td>
            <td class="contentMainFirst">
              <input type=password name="pwd" class="inputField_dis"
              onfocus="this.className='inputField_en';"
              onblur="this.className='inputField_dis';" required><br /><br />
              <input class="checkbox" type="checkbox" name="permanent" value="1"> {lang msgID="login_permanent"}<br />
              <a href="../?action=lostpwd">{lang msgID="login_lostpwd"}?</a>
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