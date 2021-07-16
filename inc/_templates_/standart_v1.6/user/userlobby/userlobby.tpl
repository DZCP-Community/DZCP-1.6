<div style="margin-top:20px;"></div>
<div class="row">
  <div class="col">
    <table class="hperc mainContent" cellspacing="1">
      <tr>
        <td class="contentHead" colspan="2" align="center"><span class="fontBold">{lang msgID="userlobby"}</span></td>
      </tr>
      <tr>
        <td colspan="2" id="uInfo" class="contentMainSecond">
          <table class="hperc" cellspacing="1">
            <tr>
              <td style="width:1%">{useravatar}</td>
              <td style="width:12px"></td>
              <td>
                <table class="hperc" cellspacing="1">
                  <tr>
                    <td><span class="fontBold">{lang msgID="user"}:</span></td>
                    <td>{autor}</td>
                  </tr>
                  <tr>
                    <td><span class="fontBold">{lang msgID="admin_user_level"}/{lang msgID="profil_position"}:</span></td>
                    <td>{$mylevel} / {getrank}</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class="fontBold">{lang msgID="profil_logins"}:</span></td>
                    <td>{$mylogins}</td>
                  </tr>
                  <tr>
                    <td><span class="fontBold">{lang msgID="profil_pagehits"}:</span></td>
                    <td>{$myhits}</td>
                  </tr>
                  <tr>
                    <td><span class="fontBold">{lang msgID="profil_forenposts"}:</span></td>
                    <td>{$myposts}</td>
                  </tr>
                  <tr>
                    <td colspan="2" style="text-align:right;padding-right:9px">{$mymsg}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{lang msgID="lobby_new"}</span></td>
      </tr>
      <tr>
        <td class="contentMainTop" width="25%" valign="top"><span class="fontBold">{lang msgID="lobby_news"}:</span></td>
        <td class="contentMainFirst" width="75%">{$news}</td>
      </tr>
      <tr>
        <td class="contentMainTop" width="25%" valign="top"><span class="fontBold">{lang msgID="lobby_newsc"}:</span></td>
        <td class="contentMainFirst" width="75%">{$newsc}</td>
      </tr>
      <tr>
        <td class="contentMainTop" width="25%" valign="top"><span class="fontBold">{lang msgID="artikel"}:</span></td>
        <td class="contentMainFirst" width="75%">{$art}</td>
      </tr>
      <tr>
        <td class="contentMainTop" width="25%" valign="top"><span class="fontBold">{lang msgID="lobby_artikelc"}:</span></td>
        <td class="contentMainFirst" width="75%">{$artc}</td>
      </tr>
      <tr>
        <td class="contentMainTop" width="25%" valign="top"><span class="fontBold">{lang msgID="lobby_forum"}:</span></td>
        <td class="contentMainFirst" width="75%">{$forum}</td>
      </tr>
      <tr>
        <td class="contentMainTop" width="25%" valign="top"><span class="fontBold">{lang msgID="lobby_votes"}:</span></td>
        <td class="contentMainFirst" width="75%">{$votes}</td>
      </tr>
      <tr>
        <td class="contentMainTop" width="25%" valign="top"><span class="fontBold">{lang msgID="kalender"}:</span></td>
        <td class="contentMainFirst" width="75%">{$kal}</td>
      </tr>
      <tr>
        <td class="contentMainTop" width="25%" valign="top"><span class="fontBold">{lang msgID="lobby_user"}:</span></td>
        <td class="contentMainFirst" width="75%">{$user}</td>
      </tr>
      <tr>
        <td class="contentMainTop" align="center" colspan="2">{$erase}</td>
      </tr>
      <tr>
        <td class="contentHead" colspan="2" align="center"><span class="fontBold">{lang msgID="last_forum"}</span></td>
      </tr>
      <tr>
        <td class="contentMainTop" width="25%" align="center"><span class="fontBold">{lang msgID="forum"}</span></td>
        <td class="contentMainTop" width="75%" align="center"><span class="fontBold">{lang msgID="forum_thread"}</span></td>
      </tr>
      {$ftopics}
      <tr>
        <td class="contentBottom" colspan="2"></td>
      </tr>
    </table>
  </div>
</div>