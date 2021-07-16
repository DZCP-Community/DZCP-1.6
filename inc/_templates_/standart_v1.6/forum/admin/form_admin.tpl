<tr> 
  <td class="contentMainTop" valign="top"><span class="fontBold">{lang msgID="forum_admin_head"}:</span></td>
  <td class="contentMainFirst" align="center" colspan="2">
    <table class="hperc">
      <tr>
        <td><input class="checkbox" type="checkbox" name="sticky" value="1" {$is_sticky}/> {lang msgID="forum_admin_addsticky"}</td>
      </tr>
      <tr>
        <td><input class="checkbox" type="checkbox" name="global" value="1" {$is_global}/> {lang msgID="forum_admin_addglobal"}</td>
      </tr>
      {if $is_editby}
      <tr>
        <td><input class="checkbox" type="checkbox" name="editby" value="1" checked/> {lang msgID="forum_admin_editby"}</td>
      </tr>
      {/if}
    </table>
  </td>
</tr>