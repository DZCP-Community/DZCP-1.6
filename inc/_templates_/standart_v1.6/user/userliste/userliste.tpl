<tr>
  <td class="contentHead" colspan="13" align="center"><span class="fontBold">{lang msgID="userlist"}</span> [ {$cnt} ]</td>
</tr>
<tr>
  <td class="contentMainTop" colspan="13" align="center">{$nav}</td>
</tr>
<tr>
  <td class="contentHead" colspan="13" align="center">
    <table class="hperc" cellspacing="0">
      <tr>
        <td style="text-align:center">
          <form name="formChange" action="">
            <select name="changeme" onchange="DZCP.switchuser()"  class="selectpicker">
              <option selected="selected" value="" class="selectpicker">{lang msgID="ulist_sort"}</option>
              <option value="?action=userlist">-> {lang msgID="ulist_normal"}</option>
              <option value="?action=userlist&amp;show=online">-> {lang msgID="ulist_online"}</option>
              <option value="?action=userlist&amp;show=lastreg">-> {lang msgID="ulist_lastreg"}</option>
              <option value="?action=userlist&amp;show=country">-> {lang msgID="ulist_country"}</option>
              <option value="?action=userlist&amp;show=sex">-> {lang msgID="ulist_sex"}</option>
              <option value="?action=userlist&amp;show=lastlogin">-> {lang msgID="ulist_last_login"}</option>
              <option value="?action=userlist&amp;show=banned">-> {lang msgID="ulist_acc_banned"}</option>
            </select>
          </form>
        </td>
        <td style="text-align:center;width:50%">
          <form id="userlist" action="" method="get" onsubmit="return(DZCP.submitButton())">
            <input type="hidden" name="action" value="userlist" />
            <input type="hidden" name="show" value="search" />
            <input class="inputField_dis_mid" type="text" name="search" value="{$search}"
              onfocus="this.className='inputField_en_mid';if(this.value=='{$search}')this.value=''"
              onblur="this.className='inputField_dis_mid';if(this.value=='')this.value='{$search}'" />
            &nbsp;&nbsp;
            <input id="contentSubmit" type="submit" class="submit" value="{lang msgID="button_value_search"}" />
          </form>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td colspan="2" class="contentMainTop"><span class="fontBold">{lang msgID="nick"}&nbsp;<a href="{$order_nick}"><img src="{idir}/order.gif" alt="" class="icon" /></a></span></td>
  <td colspan="2" class="contentMainTop"><span class="fontBold">{lang msgID="status"}</span></td>
  <td width="1%" class="contentMainTop"><span class="fontBold">{lang msgID="hpicon_blank"}</span></td>
  <td width="1%" class="contentMainTop"><span class="fontBold">{lang msgID="mficon_blank"}</span></td>
  <td width="55" class="contentMainTop"><span class="fontBold">{lang msgID="profil_age"}&nbsp;<a href="{$order_age}"><img src="{idir}/order.gif" alt="" class="icon" /></a></span></td>
{$edel}
</tr>
{$show}
<tr>
  <td class="contentBottom" colspan="13"></td>
</tr>