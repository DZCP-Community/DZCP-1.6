<tr>
  <td class="contentHead" colspan="4" align="center"><span class="fontBold">{lang msgID="protocol"}</span></td>
</tr>
<tr>
  <td class="contentMainFirst" colspan="4">
    <table class="hperc" cellspacing="0">
      <tr>
        <td style="text-align:center;vertical-align:middle">{$nav}</td>
        <td style="width:1%" nowrap="nowrap">
          <form id="protocol" action="" method="get" onsubmit="return DZCP.submitButton('searchip')">
            <input type="hidden" name="admin" value="protocol" />
            <input class="inputField_dis_mid" type="text" name="sip" value="{$search}" 
              onfocus="this.className='inputField_en_mid'" 
              onblur="this.className='inputField_dis_mid'" />
            &nbsp;&nbsp;
            <input id="searchip" type="submit" class="submit" value="{lang msgID="button_value_search"}" />
          </form>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop" style="width:130px"><span class="fontBold">{lang msgID="datum"}</span></td>
  <td class="contentMainTop" style="width:100px"><span class="fontBold">{lang msgID="info_ip"}</span></td>
  <td class="contentMainTop" colspan="2"><span class="fontBold">{lang msgID="protocol_action"}</span></td>
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="4">
    <form action="?admin=protocol" method="post">
      <input type="hidden" name="run" value="delete" />
      <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="button_title_del_protocol"}" />
    </form>
  </td>
</tr>
