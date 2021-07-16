<tr>
<td>
<table class="mainContent" cellspacing="1">
<tr>
  <td class="contentHead" colspan="5"><span class="fontBold">{lang msgID="posteingang"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" width="3%">{lang msgID="newicon"}</td>
  <td class="contentMainTop" width="37%"><span class="fontBold">{lang msgID="msg_title"}</span></td>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="msg_absender"}</span></td>
  <td class="contentMainTop" width="30%"><span class="fontBold">{lang msgID="datum"}</span></td>
  <td class="contentMainTop" align="center"><input type="checkbox" onchange="DZCP.checkbox_switch(this,'posteingang');"/></td>
</tr>
<tr>
  <td colspan="5">
  <form name="peingang" action="?action=msg&amp;do=delete" method="post" onsubmit="return(DZCP.submitButton('submitIncoming'))">
    <table class="mainContent" style="margin-top: 0px;" cellspacing="1">
    {$showincoming}
    <tr>
      <td class="contentBottom" colspan="5" style="text-align:right"><input id="submitIncoming" type="submit" value="{lang msgID="msg_del"}" class="submit" /></td>
    </tr>
    </table>
    </form>
  </td>
</tr>
</table>
<table class="mainContent" cellspacing="1">
<tr>
  <td class="contentHead" colspan="5"><span class="fontBold">{lang msgID="postausgang"}</span></td>
</tr>
<tr>
  <td class="contentMainTop" width="3%"><img src="../inc/images/yesno.gif" alt="" class="icon" /></td>
  <td class="contentMainTop" width="37%"><span class="fontBold">{lang msgID="msg_title"}</span></td>
  <td class="contentMainTop" width="25%"><span class="fontBold">{lang msgID="msg_empfaenger"}</span></td>
  <td class="contentMainTop" width="30%"><span class="fontBold">{lang msgID="datum"}</span></td>
  <td class="contentMainTop" align="center"><input type="checkbox" onclick="DZCP.checkbox_switch(this,'postausgang');"/></td>
</tr>
<tr>
  <td colspan="5">
  <form name="pausgang" action="?action=msg&amp;do=deletesended" method="post" onsubmit="return(DZCP.submitButton('submitOutgoing'))">
    <table class="mainContent" style="margin-top: 0px;" cellspacing="1">
    {$showsended}
    <tr>
      <td class="contentBottom" colspan="5" style="text-align:right"><input id="submitOutgoing" type="submit" value="{lang msgID="msg_del"}" class="submit" /></td>
    </tr>
    </table>
    </form>
  </td>
</tr>
</table>
<table class="hperc" cellspacing="1" style="margin-top:17px">
<tr>
  <td style="text-align:center">
    <form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="action" value="msg" />
      <input type="hidden" name="do" value="new" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="msg_new"}" />
    </form>
  </td>
</tr>
</table>
<table class="mainContent" cellspacing="1">
<tr>
  <td class="contentHead" colspan="5" style="text-align:left"><span class="fontBold">{lang msgID="legende"}:</span></td>
</tr>
<tr>
  <td class="contentMainFirst" colspan="5" align="center">{lang msgID="newicon"} = <span class="fontItalic">{lang msgID="legende_msg"}</span>
    <span class="fontBold">|</span> <img src="../inc/images/yesno.gif" alt="" class="icon" /> = <span class="fontItalic">{lang msgID="legende_readed"}</span></td>
</tr>
</table>
</td>
</tr>