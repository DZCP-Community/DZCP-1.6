
<a name="ref_lib_columns_columns"></a>
<p class='bblib'><b><tt>[columns]...[nextcol]...[nextcol]...[/columns]</tt></b><br />
	The <tt>[columns]</tt> tag begins a section where separate chunks of text are placed
	visually side-by-side, like with newspaper columns.  The <tt>[nextcol]</tt> tag works
	with the <tt>[columns]</tt> tag to separate individual columns from one another.
	Usage of the <tt>[columns]</tt> tag is best demonstrated by example:</p>
	
	<div style='margin-left:2em;'>
	<div class='code_header'>Code:</div>
	<xmp class='code'>This text goes before the columns.
[columns]
This text is in the first column.
This is a second line in the first column.
[nextcol]
This is the second column,
with a second line.
[nextcol]
This is a third column.
Are we having fun yet?
[/columns]
This text goes after the columns.</xmp>
	<div class='output_header'>Output:</div>
	<div class='output'>
		This text goes before the columns.
		<table class="bbcode_columns"><tbody><tr><td class="bbcode_column bbcode_firstcolumn">
		This text is in the first column.<br />
		This is a second line in the first column.
		</td><td class="bbcode_column">
		This is the second column,<br />
		with a second line.
		</td><td class="bbcode_column">
		This is a third column.<br />
		Are we having fun yet?
		</td></tr></table>
		This text goes after the columns.
	</div>
	</div>

	<p style='margin-left:2em;'>The ending <tt>[/columns]</tt> tag is required.</p>
