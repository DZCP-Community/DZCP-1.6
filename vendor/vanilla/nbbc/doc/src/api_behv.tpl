
<a name="ref_parser_SetTagMarker"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetTagMarker</b> ( string $<tt>marker</tt> )</div>
	<div class='api_descr'>This function changes NBBC's <i>tag marker</i>, which is the character
		used to separate tags from content.  You may set this to any of '<tt>[</tt>',
		'<tt>&lt;</tt>', '<tt>{</tt>', or '<tt>(</tt>'.  NBBC allows you to change the tag
		marker so that you can use it in more environments, and you could even use it as an HTML
		validator.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>marker</i></tt>: The type of tag marker to use; this must be one of '<tt>[</tt>',
			'<tt>&lt;</tt>', '<tt>{</tt>', or '<tt>(</tt>'.  Other values <i>must not</i> be
			used with the current version of NBBC.</li>
		</ul>
	</div>
	<div class='api_info'><b>Note:</b>  The default tag marker is obviously '<tt>[</tt>' since
		NBBC is designed to process BBCode.  If you intend to use NBBC to process HTML, though,
		you should not only change the tag marker, but you should also allow ampersands with
		the <a href="#ref_parser_SetAllowAmpersand">SetAllowAmpersand()</a> function:  If you
		forget to allow ampersands, your users will be unable to display &lt; and &gt; characters
		because the initial &amp; used in HTML to represent them (as in the case of <tt>&amp;lt;</tt>
		and <tt>&amp;gt;</tt>) will be converted to an <tt>&amp;amp;</tt> in the output.<br /><br />
		
		<b>Very important note: The tag marker cannot be changed during a parse!</b>
		This function must not be called by a tag callback function; any changes to the
		tag marker during a <tt>Parse()</tt> will be ignored until the next time <tt>Parse()</tt>
		is called.
	</div>
</div>

<a name="ref_parser_GetTagMarker"></a>
<div class='api'>
	<div class='api_head'>string <b>BBCode::GetTagMarker</b> ( )</div>
	<div class='api_descr'>This function returns the current tag marker.
		See <a href="api_behv.html#ref_parser_SetTagMarker">SetTagMarker()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  Returns one of '<tt>[</tt>',
		'<tt>&lt;</tt>', '<tt>{</tt>', or '<tt>(</tt>'.</div>
</div>

<a name="ref_parser_SetAllowAmpersand"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetAllowAmpersand</b> ( bool $<tt>allow</tt> )</div>
	<div class='api_descr'>This function determines whether ampersands (the '&amp;' character)
		are converted to <tt>&amp;amp;</tt> in the output or sent to the output verbatim.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>allow</i></tt>:  If set to false, all '&amp;' characters are converted to
			"<tt>&amp;amp;</tt>"; this is the default behavior.  If set to true, all '&amp;'
			characters are left unchanged in the output.</li>
		</ul>
	</div>
	<div class='api_info'><b>Note:</b>  This function exists so that you can allow the user
		to type HTML entities like '&amp;lt;' directly.  By default, any time the user types an '&amp;'
		character, it will be turned into '&amp;amp;'; but if you allow ampersands to pass
		through unchanged, the user can type '&amp;eacute;' and get '&amp;eacute;' in the
		output, thus displaying as '&eacute;'.  This is effectively a pass-through, allowing
		the user to do HTML things that would otherwise be unsafe; but it's very important
		when the tag marker is set to '&lt;', because otherwise the user would have no
		way to display '&lt;' or '&gt;' without them being converted to tags.<br /><br />
		
		Note also that in tags marked <tt>BBCODE_VERBATIM</tt>, this is temporarily
		disabled:  The tag wants its data verbatim, so it's getting its data verbatim.<br /><br />
	</div>
</div>

<a name="ref_parser_GetAllowAmpersand"></a>
<div class='api'>
	<div class='api_head'>bool <b>BBCode::GetAllowAmpersand</b> ( )</div>
	<div class='api_descr'>This function returns the current allow-ampersand state.
		See <a href="api_behv.html#ref_parser_SetAllowAmpersand">SetAllowAmpersand()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  Returns <tt>true</tt> or <tt>false</tt>, depending
		on whether ampersands are passed verbatim to the output.</div>
</div>

<a name="ref_parser_SetIgnoreNewlines"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetIgnoreNewlines</b> ( bool $<tt>ignore</tt> )</div>
	<div class='api_descr'>This function determines whether newlines
		are converted to <tt>&lt;br&nbsp;/&gt;</tt> in the output or sent to the output verbatim;
		in effect, it allows you to "turn off" the normal way that BBCode handles newlines
		and have a fully free-formatted language.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>ignore</i></tt>:  If set to false, all newlines (be they Un*x-style "\n", or
			Windows/DOS-style "\r\n" or "\n\r", or Mac-style "\r") are converted to
			"<tt>&lt;br&nbsp;/&gt;</tt>"; this is the default behavior.  If set to true, all
			newlines are converted to Un*x-style newlines ("\n") and passed to the output
			the same as any other whitespace.</li>
		</ul>
	</div>
</div>

<a name="ref_parser_GetIgnoreNewlines"></a>
<div class='api'>
	<div class='api_head'>bool <b>BBCode::GetIgnoreNewlines</b> ( )</div>
	<div class='api_descr'>This function returns the current ignore-newline state.
		See <a href="api_behv.html#ref_parser_SetIgnoreNewlines">SetIgnoreNewlines()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  Returns <tt>true</tt> or <tt>false</tt>, depending
		on whether newlines are treated as paragraph breaks (<tt>false</tt>) or whether they're treated
		as generic whitespace (<tt>true</tt>).</div>
</div>

<a name="ref_parser_SetPlainMode"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetPlainMode</b> ( bool $<tt>enable</tt> )</div>
	<div class='api_descr'>This function turns on "plain mode."   When NBBC is in "plain mode",
		it outputs very lightweight HTML content, using the tags as a guide, but not actually calling
		any of them directly.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>enable</i></tt>:  If set to true, all plain text will be copied verbatim
			to the output, and tags will be mostly ignored, using only their '<tt>plain_start</tt>'
			and '<tt>plain_end</tt>' and '<tt>plain_content</tt>' members as a guideline for
			generating the output.  If set to false (the default), full BBCode-and-HTML formatting
			logic will be used for all tags.</li>
		</ul>
	</div>
	<div class='api_info_block'><b>Note:</b><br />
		<br />
		Plain mode is useful for generating a non-HTML or minimal-HTML but still-readable
		representation of the input text quickly, without having to resort to brute-force
		solutions like <tt>strip_tags()</tt>.  The only HTML tags generated in plain mode
		will be &lt;b&gt;, &lt;i&gt;, &lt;u&gt;, and &lt;a&nbsp;href="..."&gt; tags.  Newlines
		will be retained in the output, so if you want to display them, a quick call to
		<tt>nl2br()</tt> will do the job.  Alternatively, a few quick calls to <tt>preg_replace()</tt>
		can turn your plain-jane HTML into simple, readable plain text:
		
		<div class='code_header'>Code:</div>
		<xmp class='code'>// Convert the input to plain HTML with line breaks intact.
$bbcode->SetPlainMode(true);
$output = $bbcode->Parse($input);
$output = $bbcode->nl2br($output);
print $output;</xmp>

		<div class='code_header'>Code:</div>
		<xmp class='code'>// Convert the input to "plain HTML"...
$bbcode->SetPlainMode(true);
$output = $bbcode->Parse($input);

// ...and convert to plain text by stripping the <b>, <i>, <u>, and
// <a> tags.  Just for the heck of it, we do word-wrap too to make it
// look nice on text displays and line printers and similar ancient
// output devices.
$output = wordwrap($bbcode->UnHTMLEncode(strip_tags($output)));

print $output;</xmp>
	</div>
</div>

<a name="ref_parser_GetPlainMode"></a>
<div class='api'>
	<div class='api_head'>bool <b>BBCode::GetPlainMode</b> ( )</div>
	<div class='api_descr'>This function returns the current plain-mode state.
		See <a href="api_behv.html#ref_parser_SetPlainMode">SetPlainMode()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  Returns <tt>true</tt> or <tt>false</tt>, depending
		on whether plain-jane HTML is generated as output (<tt>true</tt>) or whether HTML is generated
		as output (<tt>false</tt>).</div>
</div>

<a name="ref_parser_SetLimit"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetLimit</b> ( int $<tt>limit</tt> )</div>
	<div class='api_descr'>This function enables output-limiting.  When output-limiting is
		turned on, no more than <tt>$limit</tt> plain-text characters will be generated in
		the output (tags like &lt;b&gt; are not counted, but their content is), and the
		resulting output will be always be broken at whitespace or before/after a tag, never
		in the middle of a word.  Even if output is limited, correct formatting logic will
		be applied; the output will still be XHTML 1.0 Strict-compliant.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>limit</i></tt>:  If set to nonzero, no more than this many plain-text
			characters will be generated in the output.  If set to zero, there is no limit.</li>
		</ul>
	</div>
	<div class='api_info_block'><b>Note:</b><br /><br />
		The purpose of this function is to allow you to generate <i>excerpts</i> of your
		text, short initial chunks of the text suitable for use as titles or as blurbs in a
		news box or RSS feed.  It does <i>not</i> exist to simply chop off the output in
		an exacting fashion; rather, it tries to generate something relatively short, near
		your size limit, while still maintaining formatting.  It works very well when used
		in conjunction with <a href="#ref_parser_SetPlainMode">SetPlainMode()</a>, which
		together can output a short, HTML-free excerpt of any input text.
	</div>
</div>

<a name="ref_parser_GetLimit"></a>
<div class='api'>
	<div class='api_head'>int <b>BBCode::GetLimit</b> ( )</div>
	<div class='api_descr'>This function returns the current output-limit length.
		See <a href="api_behv.html#ref_parser_SetLimit">SetLimit()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  Returns the number of characters to which
		the output is limited, or zero (0) if there is no limit.</div>
</div>

<a name="ref_parser_SetLimitPrecision"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetLimitPrecision</b> ( float $<tt>prec</tt> )</div>
	<div class='api_descr'>When limiting text, it's often desirable to allow text that's
		slightly longer than your limit to pass through:  For example, cutting one word
		off an entire paragraph is wasteful and silly.  This function lets you control
		how accurate the limiting is, whether it will chop things exactly where you specify,
		or whether you're willing to let some text slip through to get a better appearance.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>prec</i></tt>:  What amount over your limit you're willing to accept.
			For example, if you're willing to accept up to 10% more than your limit,
			$<tt>prec</tt> would be <tt>0.1</tt>; 25% more would be <tt>0.25</tt>; and so
			on.  The default is <tt>0.15</tt>, or up to 15% more than your limit.  If
			you want the text chopped very exactingly at your limit, use a $<tt>prec</tt>
			value of <tt>0</tt>.</li>
		</ul>
	</div>
</div>

<a name="ref_parser_GetLimitPrecision"></a>
<div class='api'>
	<div class='api_head'>float <b>BBCode::GetLimitPrecision</b> ( )</div>
	<div class='api_descr'>This function returns the current output-limit precision.
		See <a href="api_behv.html#ref_parser_SetLimitPrecision">SetLimitPrecision()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  Returns the current output-limit precision.</div>
</div>

<a name="ref_parser_SetLimitTail"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetLimitTail</b> ( string $<tt>tail</tt> )</div>
	<div class='api_descr'>When limiting text, it's often desirable to append some kind
		of text to the end of a string that gets cut off.  For example, you may want to
		add "<tt>...</tt>" to a string to show that it has been cut.  This function lets
		you specify what gets added to a chopped-off string.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>tail</i></tt>:  If your input gets truncated, this will be appended
			to the end of the output, before any closing tags and whitespace.  The
			default value is "<tt>...</tt>", but other common values are "<tt>&nbsp;[more]</tt>"
			and "<tt>&nbsp;[cont'd]</tt>".  The tail must be <i>HTML</i>, not plain-text
			or BBCode.</li>
		</ul>
	</div>
</div>

<a name="ref_parser_GetLimitTail"></a>
<div class='api'>
	<div class='api_head'>string <b>BBCode::GetLimitTail</b> ( )</div>
	<div class='api_descr'>This function returns the current output-limit tail string.
		See <a href="api_behv.html#ref_parser_SetLimitTail">SetLimitTail()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  Returns the current output-limit tail HTML.</div>
</div>

<a name="ref_parser_WasLimited"></a>
<div class='api'>
	<div class='api_head'>bool <b>BBCode::WasLimited</b> ( )</div>
	<div class='api_descr'>This function returns true if the most-recently-generated output
		from <a href="api_core.html#ref_parser_Parse">Parse()</a> was cut off as the result
		of an output limit.  This provides you with a way to determine whether or not the
		limit was applied to the input or was unneeded.</div>
	<div class='api_info'><b>Return values:</b>  Returns <tt>true</tt> if the output was
		truncated, or <tt>false</tt> if the output was complete.</div>
</div>
