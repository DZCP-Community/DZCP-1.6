<table class="mainContent" cellspacing="1">
    <tr>
      <td class="contentHead" colspan="5" align="center"><span class="fontBold">{lang msgID="forum_head"}: {$title}</span></td>
    </tr>
    <tr>
        <td class="body_forum" colspan="5" align="center">
            <table width="100%" class="forumBG" cellspacing="0" cellpadding="0">
                <tbody>
                    <tr>
                      <td class="forumTD warp">{$where}{$search}</td>
                    </tr>
                </tbody>
            </table>
            {$show}
            <table class="mainContent" width="100%" border="1" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="contentHead" colspan="5"><span class="fontBold">{lang msgID="forum_who_is_online"}</span></td>
                </tr>
            </table>
            <table class="body_forum" height="100%" cellspacing="1">
            {$online}
            </table>
        </td>
    </tr>
</table>