<tr>
  <td>
    <script language="javascript" type="text/javascript">
      var prevURL = '../news/?action=preview';
    </script>
    <form enctype="multipart/form-data" id="newsForm" name="newsForm" action="?admin=newsadmin&amp;do={$do}" method="post" onsubmit="return(DZCP.submitButton())">
      <table class="hperc" cellspacing="1">
        <tr>
          <td class="contentHead" colspan="2" align="center"><span class="fontBold">{$head}</span></td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="autor"}:</span></td>
          <td class="contentMainFirst" align="center">{$autor}</td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="news_admin_kat"}:</span></td>
          <td class="contentMainFirst" align="center">
              <select{$all_disabled} name="kat" class="selectpicker">
              {$kat}
            </select>
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="news_userimage"}:</span></td>
          <td class="contentMainFirst" align="center">
            {$n_newspic}{$delnewspic}
      	 	<input type="file"{$all_disabled} name="newspic">
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="titel"}:</span></td>
          <td class="contentMainFirst" align="center">
            <input type="text" name="titel"{$all_disabled} value="{$titel}" class="inputField_dis"
            onfocus="this.className='inputField_en';"
            onblur="this.className='inputField_dis';" />
          </td>
        </tr>
        <tr>
          <td class="contentMainFirst" align="center" colspan="2">
            <textarea id="newstext"{$all_disabled} name="newstext" cols="0" rows="0" class="editorStyleWord">{$newstext}</textarea>
          </td>
        </tr>
        <tr>
          <td class="contentMainFirst" align="center" colspan="2">
             <textarea id="morenews" name="morenews" cols="0" rows="0" class="editorStyleWord">{$morenews}</textarea>
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="linkname"} 1:</span></td>
          <td class="contentMainFirst" align="center">
            <input type="text"{$all_disabled} name="link1" value="{$link1}" class="inputField_dis"
            onfocus="this.className='inputField_en';"
            onblur="this.className='inputField_dis';" />
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="url"} 1:</span></td>
          <td class="contentMainFirst" align="center">
            <input type="text"{$all_disabled} name="url1" value="{$url1}" class="inputField_dis"
            onfocus="this.className='inputField_en';"
            onblur="this.className='inputField_dis';" />
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="linkname"} 2:</span></td>
          <td class="contentMainFirst" align="center">
            <input type="text"{$all_disabled} name="link2" value="{$link2}" class="inputField_dis"
            onfocus="this.className='inputField_en';"
            onblur="this.className='inputField_dis';" />
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="url"} 2:</span></td>
          <td class="contentMainFirst" align="center">
            <input type="text"{$all_disabled} name="url2" value="{$url2}" class="inputField_dis"
            onfocus="this.className='inputField_en';"
            onblur="this.className='inputField_dis';" />
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="linkname"} 3:</span></td>
          <td class="contentMainFirst" align="center">
            <input type="text"{$all_disabled} name="link3" value="{$link3}" class="inputField_dis"
            onfocus="this.className='inputField_en';"
            onblur="this.className='inputField_dis';" />
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="url"} 3:</span></td>
          <td class="contentMainFirst" align="center">
            <input type="text"{$all_disabled} name="url3" value="{$url3}" class="inputField_dis"
            onfocus="this.className='inputField_en';"
            onblur="this.className='inputField_dis';" />
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="news_admin_intern"}:</span></td>
          <td class="contentMainFirst" align="center">
            <input class="checkbox"{$all_disabled} type="checkbox" value="1" name="intern" {$intern} />
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="news_get_timeshift"}:</span></td>
          <td class="contentMainFirst" align="center">
            <div align="left"><input class="checkbox"{$all_disabled} type="checkbox" value="1" name="timeshift" {$timeshift} /> {lang msgID="news_timeshift_from"}</div>
            {$timeshift_date}&nbsp;&nbsp;{$timeshift_time}
          </td>
        </tr>
        <tr>
          <td class="contentMainTop"><span class="fontBold">{lang msgID="news_get_sticky"}:</span></td>
          <td class="contentMainFirst" align="center">
            <div align="left"><input class="checkbox"{$all_disabled} type="checkbox" value="1" name="sticky" {$sticky} /> {lang msgID="news_sticky_till"}</div>
            {$dropdown_date}&nbsp;&nbsp;{$dropdown_time}
          </td>
        </tr>
        <tr>
          <td class="contentBottom" colspan="2"><input name="submit" id="contentSubmit"  type="submit" value="{$button}" class="submit" /> <input type="button" value="{lang msgID="preview"}" class="submit" onclick="DZCP.ajaxPreview('newsForm')" /></td>
        </tr>
      </table>
    </form>
  </td>
</tr>
</table>
<table class="hperc" cellspacing="0">
<tr>
  <td>
    <div id="previewDIV"></div>
  </td>
</tr>