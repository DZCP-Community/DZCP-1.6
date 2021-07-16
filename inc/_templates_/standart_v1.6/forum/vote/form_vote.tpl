<tr>
  <td class="contentMainTop" colspan="2" onclick="DZCP.toggle(1)" style="cursor:pointer">
    <table class="hperc" cellspacing="0">
      <tr>
        <td style="width:1%"><img id="img1" src="../inc/images/{$expand}.gif" alt="" /></td>
        <td><span class="fontBold">Vote</span></td>
      </tr>
    </table>
  </td>
</tr>
<tr id="more1" style="display:{$display}">
  <td colspan="2">
    <table class="hperc" cellspacing="0">
	<tr>
	  <td class="contentHead" colspan="2" align="center"><span class="fontBold">{if $edit}{lang msgID="votes_admin_edit_head"}{else}{lang msgID="votes_admin_head"}{/if}</span></td>
	</tr>
	<tr>
	  <td class="contentMainTop" width="160"><span class="fontBold">{lang msgID="votes_admin_question"}:</span></td>
	  <td class="contentMainFirst" align="center">
		<input type="text" name="question" value="{$question}" class="inputField_dis"
		onfocus="this.className='inputField_en';"
		onblur="this.className='inputField_dis';" />
	  </td>
	</tr>
		{$forum_answer}
	<tr {$intern_kat}>
	  <td class="contentMainTop" width="160"><span class="fontBold">{lang msgID="votes_admin_intern"}</span></td>
	  <td class="contentMainFirst" align="center"><input class="checkbox" type="checkbox" value="1" name="intern" {$intern} /></td>
	</tr>	
	{$br1}
	<tr>
	  <td class="contentMainTop" width="160"><span class="fontBold">{lang msgID="forum_admin_closed"}</span></td>
	  <td class="contentMainFirst" align="center"><input class="checkbox" type="checkbox" value="1" name="closed" {$closed} /></td>
	</tr>
	<tr>
	  <td class="contentMainTop" width="160"><span class="fontBold">{lang msgID="forum_vote_del"}</span></td>
	  <td class="contentMainFirst" align="center"><input class="checkbox" type="checkbox" value="1" name="vote_del" /></td>
	</tr>
	{$br2}
	</table>
  </td>
</tr>