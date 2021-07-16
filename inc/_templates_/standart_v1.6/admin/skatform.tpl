<tr>
  <td class="contentHead" colspan="2" align="center"><span class="fontBold">{$head}</span></td>
</tr>
<tr>
  <td>
    <form enctype="multipart/form-data" action="?admin=forum&amp;do={$what}&amp;sid={$sid}&amp;id={$id}" method="post" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="sid" value="{$sid}">
      <table class="hperc" cellspacing="1">
        <tr>
          <td class="contentMainTop" width="130"><span class="fontBold">{lang msgID="config_forum_skatname"}:</span></td>
          <td class="contentMainFirst" align="center">
            <input type="text" name="skat" value="{$skat}" class="inputField_dis"
                   onfocus="this.className='inputField_en';"
                   onblur="this.className='inputField_dis';" />
          </td>
        </tr>
        <tr>
          <td class="contentMainTop" width="130"><span class="fontBold">{lang msgID="config_forum_stopic"}:</span></td>
          <td class="contentMainFirst" align="center">
            <input type="text" name="stopic" value="{$stopic}" class="inputField_dis"
                   onfocus="this.className='inputField_en';"
                   onblur="this.className='inputField_dis';" />
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="position"}:</span></td>
          <td class="contentMainFirst" align="center">
            <select name="order" class="selectpicker show-tick" data-size="10">
              {lang msgID="nothing"}
              <option value="1">als erstes</option>
              {$position}
            </select>
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="language"}:</span></td>
          <td class="contentMainFirst" align="center">
            <select name="flags" class="selectpicker show-tick" data-size="10">
              {$flags}
            </select>
          </td>
        </tr>
        <tr>
          <td class="contentBottom" colspan="2"><input id="contentSubmit"  type="submit" value="{$value}" class="submit" /></td>
        </tr>
      </table>
    </form>
  </td>
</tr>