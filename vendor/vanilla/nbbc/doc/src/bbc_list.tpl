
<a name="ref_lib_list_list"></a>
<p class='bblib' style='margin-bottom:0'><b><tt>[list]...[*]...[*]...[/list]</tt></b></p>
<p class='bblib' style='margin-bottom:0;margin-top:0'><b><tt>[list=type]...[*]...[*]...[/list]</tt></b><br />
	The <tt>[list]</tt> tag is designed for creating lists of things.  In its default form,
	where no type is given, it creates a simple bullet-point list, like this:</p>

	<table style='border-collapse:collapse;margin-left:2em;'><tbody><tr><td style='padding-right:2em;vertical-align:top;'>
		<div class='code_header'>Code:</div>
		<xmp class='code' style='width:10em;'>[list]
[*]John
[*]Mary
[*]Bill
[*]Sue
[/list]</xmp>
	</td><td style='vertical-align:top;'>
		<div class='output_header'>Output:</div>
		<div class='output' style='width:10em;'>
			<ul style='margin-top:0;margin-bottom:0'>
			<li>John</li>
			<li>Mary</li>
			<li>Bill</li>
			<li>Sue</li>
			</ul>
		</div>
	</td></tr></tbody></table>

	<p style='margin-left:2em;'>Notice that while you do need to have an ending <tt>[/list]</tt> tag,
	you don't need to have an ending <tt>[/*]</tt> tag for each	<tt>[*]</tt> tag; this makes creating
	lists somewhat simpler.</p>

	<p style='margin-left:2em;'>You can also create numbered lists by specifying what type
	of list you want; for example, if you want a numbered list, use type "<tt>1</tt>", or for an
	alphabetic list, use type "<tt>A</tt>", or for Roman numerals, use type "<tt>i</tt>".  Two
	examples are shown below:</p>

	<table style='border-collapse:collapse;margin-left:2em;'><tbody><tr><td style='padding-right:2em;vertical-align:top;'>
		<div class='code_header'>Code:</div>
		<xmp class='code' style='width:10em;'>[list=1]
[*]John
[*]Mary
[*]Bill
[*]Sue
[/list]</xmp>
	</td><td style='vertical-align:top;'>
		<div class='output_header'>Output:</div>
		<div class='output' style='width:10em;'>
			<ol style='margin-top:0;margin-bottom:0'>
			<li>John</li>
			<li>Mary</li>
			<li>Bill</li>
			<li>Sue</li>
			</ol>
		</div>
	</td></tr></tbody></table>

	<table style='border-collapse:collapse;margin-left:2em;'><tbody><tr><td style='padding-right:2em;vertical-align:top;'>
		<div class='code_header'>Code:</div>
		<xmp class='code' style='width:10em;'>[list=A]
[*]John
[*]Mary
[*]Bill
[*]Sue
[/list]</xmp>
	</td><td style='vertical-align:top;'>
		<div class='output_header'>Output:</div>
		<div class='output' style='width:10em;'>
			<ol style='margin-top:0;margin-bottom:0;list-style-type:upper-alpha;'>
			<li>John</li>
			<li>Mary</li>
			<li>Bill</li>
			<li>Sue</li>
			</ol>
		</div>
	</td></tr></tbody></table>
	
	<p style='margin-left:2em;'>NBBC supports a wide variety of different kinds of list types, and
	matches capabilities, more-or-less, with the various list styles defined in the CSS 2.1 standard.
	A complete list of supported types is given below:
	
<div align='center'>
<table class='list_table'>
<thead>
<tr><th>Type</th><th>Description</th><th>Example</th><th>CSS equivalent</th></tr>
</thead>
<tbody>
<tr><th>1</th><td>Ordered list using Arabic numerals</td><td>1, 2, 3...</td><td><tt>decimal</tt></td></tr>
<tr><th>01</th><td>Ordered list using numbers with a leading zero</td><td>01, 02, 03..., 10, 11...</td><td><tt>decimal-leading-zero</tt></td></tr>
<tr><th>A</th><td>Ordered list using capital letters</td><td>A, B, C...</td><td><tt>upper-alpha</tt></td></tr>
<tr><th>a</th><td>Ordered list using lowercase letters</td><td>a, b, c...</td><td><tt>lower-alpha</tt></td></tr>
<tr><th>I</th><td>Ordered list using capital Roman numerals</td><td>I, II, III, IV...</td><td><tt>upper-roman</tt></td></tr>
<tr><th>i</th><td>Ordered list using lowercase Roman numerals</td><td>i, ii, iii, iv...</td><td><tt>lower-roman</tt></td></tr>
<tr><th>greek</th><td>Ordered list using lowercase Greek letters</td><td>&#945;, &#946;, &#947;...</td><td><tt>lower-greek</tt></td></tr>
<tr><th>armenian</th><td>Ordered list using Armenian numbering</td><td></td><td><tt>armenian</tt></td></tr>
<tr><th>georgian</th><td>Ordered list using Georgian numbering</td><td>an, ban, gan...</td><td><tt>georgian</tt></td></tr>
<tr><th>circle</th><td>Unordered list using circle-bullets</td><td><ul style='margin-top:0;margin-bottom:0;list-style-type:circle;'><li>&nbsp;</li></ul></td><td><tt>circle</tt></td></tr>
<tr><th>disc</th><td>Unordered list using disc-bullets</td><td><ul style='margin-top:0;margin-bottom:0;list-style-type:disc;'><li>&nbsp;</li></ul></td><td><tt>disc</tt></td></tr>
<tr><th>square</th><td>Unordered list using square-bullets</td><td><ul style='margin-top:0;margin-bottom:0;list-style-type:square;'><li>&nbsp;</li></ul></td><td><tt>square</tt></td></tr>
</tbody>
</table>
</div>
