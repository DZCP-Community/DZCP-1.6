<tr>
  <td class="contentMainFirst">
    <form name="formular" action="?action=show&amp;kid={$kid}&amp;id={$id}" method="post" onsubmit="return(DZCP.submitButton())">
    <table class="hperc" cellspacing="0">
      <tr>
        <td style="vertical-align:middle"><span class="fontBold">{lang msgID="forum_head_skat_search"}:</span></td>
        <td style="text-align:center">
          <input type="text" name="suche" value="{$suchwort}" class="inputFieldFsearch_dis"
          onfocus="this.className='inputFieldFsearch_en';" 
          onblur="this.className='inputFieldFsearch_dis';" />
        </td>
        <td style="text-align:center"><input id="contentSubmit" type="submit" value="search" class="submit" /></td>
      </tr>
    </table>
    </form>
  </td>
</tr>