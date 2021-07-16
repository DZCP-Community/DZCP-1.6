<tr>
  <td class="contentHead" align="center" colspan="2"><span class="fontBold">{$head}</span></td>
</tr>
<tr>
  <td class="contentMainTop" colspan="4">
    <div style="margin-right: 10px;margin-left: 10px;">
      <form name="dlkats" action="?admin=api_server&module=downloads_kats&sub=1&action={$do}" method="post" onsubmit="return(DZCP.submitButton())">
        <table class="hperc" cellspacing="1">
          <tr>
            <td class="contentMainTop" width="100"><span class="fontBold">Bezeichung:</span></td>
            <td class="contentMainFirst" align="center">
              <input type="text" name="kat" value="{$kat}" class="inputField_dis"
                     onfocus="this.className='inputField_en';"
                     onblur="this.className='inputField_dis';" />
            </td>
          </tr>
          <tr>
            <td class="contentMainTop" width="100"><span class="fontBold">{lang msgID="dl_dlkat"}:</span></td>
            <td class="contentMainFirst" align="center">
              <label>
                <select name="kid" size="1" class="selectpicker show-tick" data-style="btn-secondary" data-width="300px">
                  {$options}
                </select>
              </label>
            </td>
          </tr>
          <tr>
            <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" class="submit btn" value="{$what}" /></td>
          </tr>
        </table>
      </form>
    </div>
  </td>
</tr>