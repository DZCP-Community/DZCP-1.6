<table class="navContent" cellspacing="0">
  <tr>
    <td class="loginLeft"></td>
    <td>
      <form name="login" action="../user/?action=login&amp;do=yes&amp;from=menu" method="post" onsubmit="return DZCP.submitButton('navLogin')">
        <table class="navContent">
          <tr>
            <td>
              <input type="text" name="user" class="loginName_dis" 
               onblur="this.className='loginName_dis';if(this.value=='')this.value='{lang msgID="loginname"}'"
               onfocus="this.className='loginName_en';if(this.value=='{lang msgID="loginname"}') this.value='';" value="{lang msgID="loginname"}"/>
            </td>
          </tr>
          <tr>
            <td>
              <input type="password" name="pwd" class="loginPwd_dis"
              onblur="this.className='loginPwd_dis';if(this.value=='')this.value='{lang msgID="pwd"}'"
              onfocus="this.className='loginPwd_en';if(this.value=='{lang msgID="pwd"}') this.value='';" value="{lang msgID="pwd"}"/>
            </td>
          </tr>
          <tr>
            <td><input class="checkbox" type="checkbox" id="permanent" name="permanent" value="1" /><label for="permanent"> {lang msgID="login_permanent"}</label></td>
          </tr>
          {$secure}
          <tr>
            <td align="center"><input id="navLogin" type="submit" value="{lang msgID="login"}" class="loginSubmit" name="send" /></td>
          </tr>
          <tr>
            <td>&raquo; <a class="navLostPwd" href="../user/?action=register" title="{lang msgID="login_signup"}">{lang msgID="login_signup"}</a></td>
          </tr>
          <tr>
            <td>&raquo; <a class="navLostPwd" href="../user/?action=lostpwd" title="{lang msgID="login_lostpwd"}">{lang msgID="login_lostpwd"}</a></td>
          </tr>
        </table>
      </form>
    </td>
    <td class="loginRight"></td>
  </tr>
</table>

