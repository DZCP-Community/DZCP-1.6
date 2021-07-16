<tr>
  <td class="contentHead" colspan="3" {$onclick}>
    <table class="hperc" cellspacing="0">
      <tr>
        <td style="width:1%">{$img}</td>
        <td style="text-align:center"><span class="fontBold">{lang msgID="forum_search_head"}</span></td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop" colspan="3" id="more1" {$style}>
    <form id="search" name="search" action="" method="get" onsubmit="return(DZCP.submitButton())">
    <input type="hidden" name="do" value="search" />
    <table class="hperc" cellspacing="2">
      <tr>
        <td><span class="fontBold">{lang msgID="search_word"}:</span></td>
        <td style="text-align:center">
          <input class="inputField_dis" type="text" name="search" value="{$search}" 
          onfocus="this.className='inputField_en'" 
          onblur="this.className='inputField_dis'" />
        </td>
      </tr>
      <tr>
        <td></td>
        <td style="text-align:center">
          <select name="con" class="selectpicker">
            <option value="and">{lang msgID="search_con_and"}</option>
            <option value="or" {$chkcon}>{lang msgID="search_con_or"}</option>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <table class="hperc" cellspacing="0">
            <tr>
              <td style="width:12px"></td>
              <td style="text-align:center">
                <ul class="search">
                  <li><label class="searchKat" style="border-bottom:1px solid #000" for="allkat"><input type="checkbox" class="chksearch" name="allkat" id="allkat" value="true" {$all_board} onchange="DZCP.hideForumAll()" />&nbsp;&nbsp;{lang msgID="search_forum_all"}</label></li>
                  {$fkats}
                </ul>
              </td>
              <td style="width:12px"></td>
              <td style="width:45%">
                <table class="hperc" cellspacing="1">
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>
                      <fieldset class="search">
								        <legend class="search"><span class="fontBold">{lang msgID="search_for_area"}</span></legend>
									      <input type="radio" name="area" class="checkbox" value="posts" {$acheck1} />
                          {lang msgID="search_type_full"}<br />
      									<input type="radio" name="area" class="checkbox" value="topic" {$acheck2} />
                          {lang msgID="search_type_title"}
								      </fieldset>
                    </td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>
                      <fieldset class="search">
								        <legend class="search"><span class="fontBold">{lang msgID="search_type"}</span></legend>
									      <input type="radio" name="type" class="checkbox" value="text" {$tcheck1} />
                          {lang msgID="search_type_text"}<br />
      									<input type="radio" name="type" class="checkbox" value="autor" {$tcheck2} />
                          {lang msgID="search_type_autor"}
								      </fieldset>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td style="text-align:center" colspan="2"><input id="contentSubmit" type="submit" class="submit" value="{lang msgID="button_value_search1"}" /></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
    </form>
  </td>
</tr>
{$show}