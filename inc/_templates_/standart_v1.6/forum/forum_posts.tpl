<table class="mainContent" cellspacing="1">
  <tr>
    <td class="contentHead" align="center"><a name="top"><span class="fontSites">{lang msgID="forum_head"}</span></a></td>
  </tr>
  <tr>
    <td class="contentMainTop">{$where}</td>
  </tr>
  {$admin}
  {$f_abo}
</table>
<table class="mainContent" cellspacing="1">
  <tr>
    <td class="contentHead" align="center" colspan="2">
      <table class="hperc" cellspacing="0">
        <tr>
          <td class="gray"><span class="fontSites">{$threadhead}</span></td>
          <td class="gray" style="text-align:right">{$nav}</td>
        </tr>
      </table>
    </td>
  </tr>
  <!-- 1. Post -->
  <tr>
    <td colspan="2" class="contentMainTop">
      <table class="hperc" cellspacing="0">
        <tr>
          <td style="vertical-align:middle"><a name="p{$postnr}"></a>{$titel}</td>
          <td style="text-align:right">{if $chkme >= 1 && !$closed || $permission}{$zitat}{/if}</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="commentsLeft">
      <table class="hperc">
        <tr>
          <td>{$nick}</td>
          <td align="right">{$onoff}</td>
        </tr>
        <tr>
          <td colspan="2"><span class="fontItalic">{$status}</span></td>
        </tr>
        <tr>
          <td>{$posts}</td>
        </tr>
        <tr>
          <td colspan="2" style="text-align:center;padding:6px">{$avatar}</td>
        </tr>
      </table>
    </td>
    <td {$class}>
      <table class="hperc">
        <tr>
          <td height="108">{$text}{$editedby}</td>
        </tr>
        {if $is_vote}<tr><td>{$vote}</td></tr>{/if}
        <tr>
          <td><span class="fontItalic">{$edited}&nbsp;</span>{$signatur}</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="contentMainTop" align="center">{$ip}</td>
    <td class="contentMainTop" style="text-align:left;vertical-align:middle">{$pn} {$hp}</td>
  </tr>
  <tr>
    <td style="height: 20px"></td>
  </tr>
  {$show}
  <tr>
    <td class="contentMainFirst" colspan="2">
      <table class="hperc" cellspacing="0">
        <tr>
          <td class="contentMainTop" style="vertical-align:middle">{$nav}</td>
          <td class="contentMainTop" style="text-align:right">
            {if $chkme >= 1}
              {if $closed && !$permission}<img src="{idir}/closed.png" alt="">{elseif !$closed && $permission}<a href="?action=post&amp;do=add&amp;id={$id}">
                <img src="{lang msgID="forum_button_admin_replys"}" alt="" title="{lang msgID="forum_addpost"}" class="icon" /></a>{else}<a href="?action=post&amp;do=add&amp;id={$id}">
                <img src="{lang msgID="forum_button_replys"}" alt="" title="{lang msgID="forum_addpost"}" class="icon" /></a>{/if}
            {/if}{$lpost}</td>
        </tr>
      </table>
    </td>
  </tr>
</table>