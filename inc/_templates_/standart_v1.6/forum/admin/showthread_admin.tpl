</table>
<table class="mainContent" cellspacing="0">
  <tr>
    <td>
      <form name="formular" action="?action=admin&amp;do=mod&amp;id={$id}" method="post" onsubmit="return(DZCP.submitButton())">
      <table class="hperc" cellspacing="1">
        <tr>
          <td class="contentHead" colspan="3" ><span class="fontBold">{lang msgID="admin"}</span></td>
        </tr>
        
        <tr>
          <td class="contentMainTop"><input class="checkbox" type="radio" id="closed1" name="closed" value="0" {$opened} /><label for="closed1">  {lang msgID="forum_admin_open"}</label></td>
          <td class="contentMainTop"><input class="checkbox" type="checkbox" id="sticky" name="sticky" value="1" {$sticky} /><label for="sticky">  {lang msgID="forum_admin_addsticky"}</label></td>
          <td class="contentMainTop" rowspan="3" style="width:1%;vertical-align:middle"><input id="contentSubmit" type="submit" value="{lang msgID="button_value_save"}" class="submit" /></td>
        </tr>
        
        <tr>
          <td class="contentMainTop"><input class="checkbox" type="radio" id="closed2" name="closed" value="1" {$closed} /><label for="closed2"> {lang msgID="forum_admin_close"}</label></td>
          <td class="contentMainTop"><input class="checkbox" type="checkbox" id="global" name="global" value="1" {$global} /><label for="global"> {lang msgID="forum_admin_global"}</label></td>
        </tr>
        
        <tr>
          <td class="contentMainTop"><select name="move" class="selectpicker"><option value="lazy">{lang msgID="forum_admin_moveto"}</option>{$move}</select></td>
          <td class="contentMainTop"><input class="checkbox" type="checkbox" id="delete" name="delete" value="1" /><label for="delete"> {lang msgID="forum_admin_delete"}</label></td>
        </tr>
        
      </table>
      </form>
    </td>
  </tr>