
<a name="ref_lib_container_code"></a>
<p class='bblib'><b><tt>[code]...[/code]</tt></b><br />
	The <tt>[code]</tt> tag is designed for representing content from various programming
	languages without requiring use of special codes to display it.  Every character between
	<tt>[code]</tt> and <tt>[/code]</tt> will be copied directly to the output, with whitespace
	including newlines and tabs, retained and preformatted.  The only exception to this is that
	the first newline after the <tt>[code]</tt> tag will be removed, and the last newline before
	<tt>[/code]</tt> will be removed, just to keep the output from having wasted blank lines in it.
	The ending <tt>[/code]</tt> tag is required; without it, the start <tt>[code]</tt> tag will
	be ignored.</p>

<a name="ref_lib_container_quote"></a>
<p class='bblib' style='margin-bottom:0'><b><tt>[quote]...[/quote]</tt></b></p>
<p class='bblib' style='margin-bottom:0;margin-top:0'><b><tt>[quote="John"]...[/quote]</tt></b></p>
<p class='bblib' style='margin-bottom:0;margin-top:0'><b><tt>[quote name="John"]...[/quote]</tt></b></p>
<p class='bblib' style='margin-bottom:0;margin-top:0'><b><tt>[quote name="John" date="July 4, 2008" url="http://www.foo.com"]...[/quote]</tt></b>
	The <tt>[quote]</tt> tag lets you copy someone else's BBCode and attribtue it to them.
	The <tt>[quote]</tt> tag comes in several different flavors, depending on how you want to
	quote them.  In the first form, it attributes the quote to no-one specific:</p>

	<div style='margin-left:2em;'>
	<div class='code_header'>Code:</div>
	<xmp class='code'>[quote]A rolling stone gathers no moss.[/quote]</xmp>
	<div class='output_header'>Output:</div>
	<div class='output'>
	<div class="bbcode_quote">
	<div class="bbcode_quote_head">Quote:</div>
	<div class="bbcode_quote_body">A rolling stone gathers no moss.</div>
	</div>
	</div>
	</div>

	<p style='margin-left:2em;'>In its second form, <tt>[quote]</tt> lets you identify who said something:</p>

	<div style='margin-left:2em;'>
	<div class='code_header'>Code:</div>
	<xmp class='code'>[quote=Thomas Jefferson]We hold these truths to be self-evident:
That all men are created equal.[/quote]</xmp>
	<div class='output_header'>Output:</div>
	<div class='output'>
	<div class="bbcode_quote">
	<div class="bbcode_quote_head">Thomas Jefferson wrote:</div>
	<div class="bbcode_quote_body">We hold these truths to be self-evident:<br />
	That all men are created equal.</div>
	</div>
	</div>
	</div>

	<p style='margin-left:2em;'>In its third form, <tt>[quote]</tt> lets you identify who said something,
		as well as optionally including the date they said it and a URL to where it can be found:</p>

	<div style='margin-left:2em;'>
	<div class='code_header'>Code:</div>
	<xmp class='code'>[quote name="Thomas Jefferson" date="July 4, 1776"
	url="http://www.ushistory.org/Declaration/document/index.htm"]
We hold these truths to be self-evident:
That all men are created equal.[/quote]</xmp>
	<div class='output_header'>Output:</div>
	<div class='output'>
	<div class="bbcode_quote">
	<div class="bbcode_quote_head"><a href="http://www.ushistory.org/Declaration/document/index.htm">Thomas Jefferson wrote on July 4, 1776:</a></div>
	<div class="bbcode_quote_body">We hold these truths to be self-evident:<br />
	That all men are created equal.</div>
	</div>
	</div>
	</div>

	<p style='margin-left:2em;'>The ending <tt>[/quote]</tt> tag is required.</p>
