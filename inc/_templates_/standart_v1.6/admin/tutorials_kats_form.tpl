<form name="tutorialkats" enctype="multipart/form-data" action="?admin=tutorials&amp;do={$do}" method="post">
  <tr>
    <td colspan="2" class="contentHead" align="center"><span class="fontBold">{lang msgID="tutorials"} - {lang msgID="tutorials_new_kat"}</span></td>
  </tr>
  <tr>
    <td class="contentMainTop" width="150"><span class="fontBold">{lang msgID="tutorials_bezeichnung"}:</span> <span class="fontWichtig">*</span></td>
    <td class="contentMainFirst" align="center">
      <input type="text" name="bezeichnung" maxlength="150" value="{$v_bezeichnung}" class="inputField_dis"
      onfocus="this.className='inputField_en';"
      onblur="this.className='inputField_dis';" />
    </td>
  </tr>
  <tr>
    <td class="contentMainTop"><span class="fontBold">{lang msgID="tutorials_position"}:</span></td>
    <td class="contentMainFirst" align="center">
      <select name="position" class="selectpicker">
        {$positions}
      </select>
    </td>
  </tr>
  <tr>
    <td class="contentMainTop"><span class="fontBold">{lang msgID="tutorials_level"}:</span></td>
    <td class="contentMainFirst" align="center">
      <select name="level" class="selectpicker">
        <option value="0">{lang msgID="status_unregged"}</option>
        <option value="1" {$v_user}>{lang msgID="status_user"}</option>
        <option value="2" {$v_trial}>{lang msgID="status_trial"}</option>
        <option value="3" {$v_member}>{lang msgID="status_member"}</option>
        <option value="4" {$v_admin}>{lang msgID="status_admin"}</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="contentMainTop" width="150"><span class="fontBold">{lang msgID="tutorials_pic"}:</span> {lang msgID="tutorials_pflichtfeld"}</td>
    <td class="contentMainFirst" align="center">
      {$v_pic}
      <input type="file" name="pic" /><br />{lang msgID="tutorials_katpic_info"}
    </td>
  </tr>
  <tr>
    <td class="contentMainTop"><span class="fontBold">{lang msgID="tutorials_beschreibung"}:</span></td>
    <td class="contentMainFirst" align="center">
      <textarea name="beschreibung" cols="0" rows="0" class="editorStyle" style="width: 99%">{$v_beschreibung}</textarea>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="contentMainTop" align="center">{lang msgID="tutorials_pflichtfeld"}</td>
  </tr>	
  <tr>
    <td class="contentBottom" align="center" colspan="2">
      <input id="contentSubmit" type="submit" value="{lang msgID="tutorials_new_kat"}" class="submit" />
    </td>
  </tr>         
</form>