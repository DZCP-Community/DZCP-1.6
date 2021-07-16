<form action="../search/index.php" method="get" onsubmit="return DZCP.submitButton('searchSubmit')">
  <input type="hidden" name="do" value="search" />
  <input type="hidden" name="con" value="and" />
  <input type="hidden" name="allkat" value="true" />
  <input type="hidden" name="area" value="posts" />
  <input type="hidden" name="type" value="text" />
  <table width="10" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><input type="text" id="searchpanel" name="search" value="{lang msgID="forum_search_word"}"
                 onfocus="if(this.value=='{lang msgID="forum_search_word"}')this.value=''" onblur="if(this.value=='')this.value='{lang msgID="forum_search_word"}'" /></td>
      <td style="padding-left:6px;"><input id="searchSubmit" type="submit" value="" class="searchSubmit" /></td>
    </tr>
  </table>
</form>