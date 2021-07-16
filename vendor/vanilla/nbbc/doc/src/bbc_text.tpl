
<a name="ref_lib_style_b"></a>
<p class='bblib'><b><tt>[b]...[/b]</tt></b><br />
	The <tt>[b]</tt> tag formats its contents using <b>bold text</b>.  The ending <tt>[/b]</tt> tag is required.</p>

<a name="ref_lib_style_i"></a>
<p class='bblib'><b><tt>[i]...[/i]</tt></b><br />
	The <tt>[i]</tt> tag formats its contents using <i>italic text</i>.  The ending <tt>[/i]</tt> tag is required.</p>

<a name="ref_lib_style_u"></a>
<p class='bblib'><b><tt>[u]...[/u]</tt></b><br />
	The <tt>[u]</tt> tag adds an <u>underline</u> to its contents.  The ending <tt>[/u]</tt> tag is required.</p>

<a name="ref_lib_style_s"></a>
<p class='bblib'><b><tt>[s]...[/s]</tt></b><br />
	The <tt>[s]</tt> tag adds a <strike>strikeout</strike> to its contents.  The ending <tt>[/s]</tt> tag is required.</p>

<a name="ref_lib_style_sup"></a>
<p class='bblib'><b><tt>[sup]...[/sup]</tt></b><br />
	The <tt>[sup]</tt> tag formats its contents as <sup>superscript</sup>.  The ending <tt>[/sup]</tt> tag is required.</p>

<a name="ref_lib_style_sub"></a>
<p class='bblib'><b><tt>[sub]...[/sub]</tt></b><br />
	The <tt>[sub]</tt> tag formats its contents as <sub>subscript</sub>.  The ending <tt>[/sub]</tt> tag is required.</p>

<a name="ref_lib_style_spoiler"></a>
<p class='bblib'><b><tt>[spoiler]...[/spoiler]</tt></b><br />
	The <tt>[spoiler]</tt> tag formats its contents as a <span style='color:#000;background-color:#000'>spoiler</span> (select this text with your mouse to see the spoiler).  The ending <tt>[/spoiler]</tt> tag is required.</p>

<a name="ref_lib_style_acronym"></a>
<p class='bblib'><b><tt>[acronym="description"]keyword[/acronym]</tt></b><br />
	The <tt>[acronym]</tt> tag provides a helpful mouseover for acronyms and other words where popup text
	can be helpful, like this:  <span class='acronym' title="description">keyword</span>.  The ending <tt>[/acronym]</tt> tag is required.</p>

<a name="ref_lib_style_size"></a>
<p class='bblib'><b><tt>[size=n]...[/size]</tt></b><br />
	The <tt>[size]</tt> tag lets you control the size of your text:</p>
	<div style='margin-left:4em;'>
	<div style='font-size: 0.5em'>This text is size 0 (tiny).</div>
	<div style='font-size: 0.67em'>This text is size 1 (very small).</div>
	<div style='font-size: 0.83em'>This text is size 2 (small).</div>
	<div style='font-size: 1.0em'>This text is size 3 (normal).</div>
	<div style='font-size: 1.17em'>This text is size 4 (large).</div>
	<div style='font-size: 1.5em'>This text is size 5 (very large).</div>
	<div style='font-size: 2.0em'>This text is size 6 (huge).</div>
	<div style='font-size: 2.5em'>This text is size 7 (ridiculous).</div>
	</div>
	<div style='margin-left: 2em;margin-top:0.5em;'>The ending <tt>[/size]</tt> tag is required, and the size given must be a number from 0 to 7.</div>

<a name="ref_lib_style_color"></a>
<p class='bblib'><b><tt>[color=n]...[/color]</tt></b><br />
	The <tt>[color]</tt> tag allows you to control the text color.  You can specify the
	color as either a three-digit hex code, like <tt>#069</tt>, or as a six-digit hex code,
	like <tt>#E34715</tt>, or as a standard HTML color name, like <tt>red</tt>.  For example,
	<tt>[color=goldenrod]Shiny&nbsp;gold[/color]</tt> will appear as <span style='color:goldenrod;'>Shiny&nbsp;gold</span>,
	and <tt>[color=#069]True&nbsp;blue![/color]</tt> will appear as <span style='color:#069;'>True&nbsp;blue!</span>
	</p>

<a name="ref_lib_style_font"></a>
<p class='bblib'><b><tt>[font=n]...[/font]</tt></b><br />
	The <tt>[font]</tt> tag allows you to alter the text's typeface.  You may use specific font names, like
	<span style='font-family:"Times New Roman";'>Times New Roman</span>, or generic font names, like <span style='font-family:cursive;'>cursive</span>.
	Note that if you want to use a name that contains spaces, like <span style='font-family:"Times New Roman";'>Times New Roman</span>,
	you should surround it in quotation marks, like this:  <tt>[font="Times New Roman"]...[/font]</tt>  NBBC will
	almost always get it right even if you don't use quotes, but quotes guarantee it'll be correct.<br /><br />
	Note that the [font] tag allows you to separate font names with commas, just like CSS does, so that
	if a given font is not available, your choice of fallback fonts can be used instead:  <tt>[font=Arial,Helv,Helvetica,sans]...[/font]</tt><br /><br />
	The [font] tag also recognizes the five standard CSS "generic" font names:</p>
	<ul style='margin-left:3em;'>
	<li><tt>serif</tt> - A serifed font, like Times or Roman</li>
	<li><tt>sans-serif</tt> - A sans-serif font, like Helvetica or Arial (also <tt>sansserif</tt>, <tt>sans serif</tt>, and just <tt>sans</tt>)</li>
	<li><tt>cursive</tt> - A cursive font, like Zapf-Chancery</li>
	<li><tt>fantasy</tt> - A "fantasy" font, like Western</li>
	<li><tt>monospace</tt> - A fixed-width font, like Courier (also <tt>mono</tt>)
	</ul>
