<table class="mainContent" width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
      <td class="contentHead" colspan="5" align="center"><span class="fontBold">{lang msgID="forum_head"}</span></td>
    </tr>
    <tr>
        <td class="body_forum" colspan="5" align="center">
            <table width="100%" class="forumBG" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="contentBottom" colspan="5" align="center">{lang msgID="forum_searchlink"}</td>
                </tr>
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
            <table class="mainContent" width="100%" border="1" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="contentHead" colspan="5"><span class="fontBold">{lang msgID="forum_stats_top5"}</span></td>
                </tr>
            </table>
            <table width="100%" class="body_forum" height="100%" cellspacing="1">
                {$stats}
            </table>
        </td>
    </tr>
</table>