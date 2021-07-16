<tr>
  <td class="contentHead" colspan="2" align="center"><span class="fontBold">{$head}</span></td>
</tr>
<tr>
<td>
<form enctype="multipart/form-data" name="sponsors" action="?admin=sponsors&amp;do={$do}" method="post" onsubmit="return(DZCP.submitButton())">
<table class="hperc" cellspacing="1">
{$error}
<tr>
  <td class="contentMainTop"><span class="fontBold">{$name}:</span> <span class="fontWichtig">*</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="name" value="{$sname}" class="inputField_dis"
    onfocus="this.className='inputField_en'"
    onblur="this.className='inputField_dis'" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{$link}:</span> <span class="fontWichtig">*</span></td>
  <td class="contentMainFirst" align="center">
    <input type="text" name="link" value="{$slink}" class="inputField_dis"
    onfocus="this.className='inputField_en'"
    onblur="this.className='inputField_dis'" />
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{$beschreibung}:</span> <span class="fontWichtig">*</span></td>
  <td class="contentMainFirst" align="center">
    <textarea id="beschreibung" name="beschreibung" cols="0" rows="0" class="editorStyleMini">{$sbeschreibung}</textarea>
  </td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{$pos}:</span></td>
  <td class="contentMainFirst" align="center">
    <select name="position" class="selectpicker">
      <option value="1">{$first}</option>
      {$positions}
    </select>
  </td>
</tr>
<tr>
  <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{$site}</span></td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{$addsite}:</span></td>
  <td class="contentMainFirst" align="center">
    <table class="hperc" cellspacing="0">
      <tr>
        <td style="width:20px"><input id="site" class="checkbox" type="checkbox" name="site" value="1" {$schecked} onchange="DZCP.toggle('site')" /></td>
        <td><label for="site">{$add_site}</label></td>
      </tr>
    </table>   
  </td>
</tr>
<tr id="moresite" style="display:{$snone}">
  <td colspan="2">
    <table class="hperc" cellspacing="0">
	  <tr>
	    <td class="contentMainTop"><span class="fontBold">{$upload}:</span></td>
	    <td class="contentMainFirst" align="center">
		  <input type="file" name="sdata" /><br />{$sitepic}
	    </td>
	  </tr>
	  <tr>
	    <td class="contentMainTop"><span class="fontBold">{$url}</span></td>
	    <td class="contentMainFirst" align="center">
		  <input type="text" name="slink" value="{$site_link}" class="inputField_dis"
		  onfocus="this.className='inputField_en'"
		  onblur="this.className='inputField_dis'" />
	    </td>
	  </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{$banner}</span></td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{$addbanner}:</span></td>
  <td class="contentMainFirst" align="center">
    <table class="hperc" cellspacing="0">
      <tr>
        <td style="width:20px"><input id="banner" class="checkbox" type="checkbox" name="banner" value="1" {$bchecked} onchange="DZCP.toggle('banner')" /></td>
        <td><label for="banner">{$add_banner}</label></td>
      </tr>
    </table>   
  </td>
</tr>
<tr id="morebanner" style="display:{$bnone}">
  <td colspan="2">
    <table class="hperc" cellspacing="0">
	  <tr>
	    <td class="contentMainTop"><span class="fontBold">{$upload}:</span></td>
	    <td class="contentMainFirst" align="center">
		  <input type="file" name="bdata" /><br />{$bannerpic}
	    </td>
	  </tr>
	  <tr>
	    <td class="contentMainTop"><span class="fontBold">{$url}</span></td>
	    <td class="contentMainFirst" align="center">
		  <input type="text" name="blink" value="{$banner_link}" class="inputField_dis"
		  onfocus="this.className='inputField_en'"
		  onblur="this.className='inputField_dis'" />
	    </td>
	  </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentMainTop" colspan="2" align="center"><span class="fontBold">{$box}</span></td>
</tr>
<tr>
  <td class="contentMainTop"><span class="fontBold">{$addbox}:</span></td>
  <td class="contentMainFirst" align="center">
    <table class="hperc" cellspacing="0">
      <tr>
        <td style="width:20px"><input id="box" class="checkbox" type="checkbox" name="box" value="1" {$xchecked} onchange="DZCP.toggle('box')" /></td>
        <td><label for="box">{$add_box}</label></td>
      </tr>
    </table>   
  </td>
</tr>
<tr id="morebox" style="display:{$xnone}">
  <td colspan="2">
    <table class="hperc" cellspacing="0">
	  <tr>
	    <td class="contentMainTop"><span class="fontBold">{$upload}:</span></td>
	    <td class="contentMainFirst" align="center">
		  <input type="file" name="xdata" /><br />{$boxpic}
	    </td>
	  </tr>
	  <tr>
	    <td class="contentMainTop"><span class="fontBold">{$url}</span></td>
	    <td class="contentMainFirst" align="center">
		  <input type="text" name="xlink" value="{$box_link}" class="inputField_dis"
		  onfocus="this.className='inputField_en'"
		  onblur="this.className='inputField_dis'" />
	    </td>
	  </tr>
    </table>
  </td>
</tr>
<tr>
  <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" value="{$what}" class="submit" /></td>
</tr>
</table>
<input type="hidden" name="posname" value="{$posname}"  />
</form>
</td>
</tr>