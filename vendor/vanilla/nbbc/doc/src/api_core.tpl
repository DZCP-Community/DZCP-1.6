
<a name="ref_parser_BBCode"></a>
<div class='api'>
	<div class='api_head'><b>BBCode::BBCode</b> ( )</div>
	<div class='api_descr'>This is the constructor for the BBCode object.  It assigns default values
		to all settings and creates all attached objects like the BBCodeLibrary object.</div>
	<div class='api_info'><b>Parameters:</b>  None.</div>
	<div class='api_info'><b>Return Value:</b>  None.</div>
</div>

<a name="ref_parser_Parse"></a>
<div class='api'>
	<div class='api_head'>string <b>BBCode::Parse</b> ( string $<tt>string</tt> )</div>
	<div class='api_descr'>This function is the public interface to the core BBCode
		parser; supply this with BBCode as input, and it will return that BBCode converted to HTML.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>string</i></tt>: The string to be converted from BBCode to HTML.</li>
		</ul>
	</div>
	<div class='api_info'><b>Return Value:</b>  A string containing HTML converted from the input BBCode.</div>
</div>

<a name="ref_parser_DoTag"></a>
<div class='api'>
	<div class='api_head'>string <b>BBCode::DoTag</b> ( string $<tt>action</tt> , string $<tt>tag_name</tt> ,
		string $<tt>default_value</tt> , array $<tt>params</tt> , string $<tt>contents</tt> )</div>
	<div class='api_descr'>This function is called by the parser to check tags and to convert tags into HTML.
		It is called at most twice for each tag.  Normally, it dispatches the checking/conversion job to
		other functions, like library functions and callback functions, but you can override this
		behavior by inheriting the BBCode class and overriding this function.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>action</i></tt>: One of <tt>BBCODE_CHECK</tt> or <tt>BBCODE_OUTPUT</tt>, depending
			on the task to be performed.  If this is <tt>BBCODE_CHECK</tt>, DoTag() must return
			<i>true</i> if the inputs are acceptable, or <i>false</i> if the inputs are unacceptable;
			if this is <tt>BBCODE_OUTPUT</tt>, DoTag() must return an HTML string that contains
			the converted output of this tag.</li>
		<li><tt><i>tag_name</i></tt>: The name of the tag being processed, without surrounding brackets
			or an end-tag slash (/), as in "<tt>b</tt>" or "<tt>quote</tt>".</li>
		<li><tt><i>default_value</i></tt>: The default value of the tag.  The default value is the
			value assigned to the tag name itself; for example, in <tt>[quote=John]</tt>, the default
			value is "<tt>John</tt>", but in <tt>[quote&nbsp;name=John]</tt> or <tt>[b]</tt>, the default
			value is the empty string.</li>
		<li><tt><i>params</i></tt>:  This is an array of key =&gt; value parameters given in the tag.
			For example, in the tag <tt>[tag="foo"&nbsp;bar="baz"&nbsp;chocolate="good"]</tt>, this array would be:<br />
			<xmp style='margin:0'>    array(
        "_name" => "tag",
        "_default" => "foo",
        "bar" => "baz",
        "chocolate" => "good",
    );</xmp>
			The "<tt>_name</tt>" and "<tt>_default</tt>" parameters will always be present in this
			array, and will always contain the same values as $<tt>tag_name</tt> and $<tt>default_value</tt>.</li>
		<li><tt><i>contents</i></tt>:  This parameter is <i>only</i> valid during <tt>BBCODE_OUTPUT</tt>;
			during <tt>BBCODE_CHECK</tt> it will always be the empty string.  This parameter contains
			the "contents" of the tag, the text between the start tag and its matching end tag.  For
			example, with this input:<br />
			<tt> &nbsp; &nbsp; [b]The quick brown "fox" jumps over the lazy dog.[/b]</tt><br />
			during <tt>BBCODE_OUTPUT</tt>, the $<tt>contents</tt> parameter will be set to:<br />
			<tt> &nbsp; &nbsp; The quick brown &amp;quot;fox&amp;quot; jumps over the lazy dog.</tt><br />
			This parameter is always either the empty string or fully-validated HTML contents.</li>
		</ul>
	</div>
	<div class='api_info'><b>Return Value:</b>  For <tt>BBCODE_CHECK</tt>, this must return <i>true</i> if
		the default value and parameters are acceptable, or <i>false</i> if they're malformed.  For
		<tt>BBCODE_OUTPUT</tt>, this must return clean HTML output text.</div>
	<div class='api_info'><b>Warning:</b>  This function is part of NBBC's internal parser.  It is exposed
		and documented so that you can inherit and override its behavior if you want, should
		you want exotic tag processing that NBBC does not perform by default; however,
		be aware that by changing this you are changing NBBC's internals, so be careful.</div>
</div>

<a name="ref_parser_SetDebug"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetDebug</b> ( bool $<tt>enable</tt> )</div>
	<div class='api_descr'>This function enables NBBC's built-in <i>debug mode</i>.  When in debug mode,
		NBBC will dump <i>huge</i> quantities of data to the browser to indicate what it is doing
		when it parses a given chunk of input (it's not unusual for a single line of BBCode input
		to produce several <i>pages</i> of debug output).  This is useful if you think NBBC is
		misbehaving, or if you're adding a tag of your own and trying to figure out why it is or
		is not working correctly.  You should <i>not</i> enable debug mode in production environments.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>enable</i></tt>: Whether to turn debug mode on or off; if this parameter is <i>true</i>,
			debug mode will be enabled and huge quantities of debugging information will be dumped to the
			browser; if this parameter is <i>false</i>, no debugging information will be displayed.</li>
		</ul>
	</div>
	<div class='api_info'><b>Note:</b>  Debug mode is disabled by default, and is only available if
		you use the multi-file version of NBBC (<tt>nbbc_main.php</tt>, <tt>nbbc_parse.php</tt>,
		<tt>nbbc_lex.php</tt>, etc.):  In the compressed version of NBBC (<tt>nbbc.php</tt>),
		all debugging code has been removed to make the code smaller and faster.  This function
		and GetDebug() still both exist in the compressed version, but regardless of the state
		of the debug flag, compressed NBBC will not display any debugging information.
	</div>
</div>

<a name="ref_parser_GetDebug"></a>
<div class='api'>
	<div class='api_head'>bool <b>BBCode::GetDebug</b> ( )</div>
	<div class='api_descr'>This function returns the current state of NBBC's built-in <i>debug mode</i>.
		See <a href="api_core.html#ref_parser_SetDebug">SetDebug()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  Returns <i>true</i> or <i>false</i>, depending on
		whether debug mode is enabled.</div>
</div>
