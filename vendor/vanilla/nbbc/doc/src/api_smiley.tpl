<a name="ref_parser_AddSmiley"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::AddSmiley</b> ( string $<tt>name</tt> , string $<tt>image</tt> )</div>
	<div class='api_descr'>This function adds a new smiley.  Smileys, or emoticons, are character
		symbols like <tt>:-)</tt> that get automatically converted to images like
		<img src="../smileys/smile.gif" width='16' height='16' alt=":-)" />.  To add a smiley, call
		AddSmiley() and provide the text of the smiley you wish to add or replace, and the name
		of an image that is to be displayed in its place.  Any old smiley defined for that string
		will be removed by this function, so this can also replace smileys as well as adding new smileys.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul>
		<li><i>name</i>: The name of a smiley you wish to add or replace, like "<tt>:-)</tt>" or
			"<tt>:3</tt>" or "<tt>:frog:</tt>".  Nearly any name is allowed; however, beyond
			common sequences, you are encouraged to use names surrounded by <tt>:</tt>colons<tt>:</tt>
			so that they are not accidentally mistaken for non-smiley text.</li>
		<li><i>image</i>:  The name of an image located in the current smileys directory that
			you wish to have displayed in place of the text symbol.</li>
		</ul>
	</div>
	<div class='api_info'><b>Return Value:</b>  None.</div>
	<div class='api_info_block'>
		(The user's manual, section <a href="usage_smiley.html">III.D</a> as well as
		<a href="app_smiley.html">appendix A</a>, contains additional documentation on
		using this function, so that information will not be repeated here.)
	</div>
</div>

<a name="ref_parser_RemoveSmiley"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::RemoveSmiley</b> ( string $<tt>name</tt> )</div>
	<div class='api_descr'>This function un-defines the given smiley --- in short,
		this causes NBBC to stop recognizing the given smiley and converting it to an image.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul>
		<li><i>name</i>: The text form of a smiley to remove from the ruleset.</li>
		</ul>
	</div>
	<div class='api_info'><b>Return Value:</b>  None.</div>
</div>

<a name="ref_parser_GetSmiley"></a>
<div class='api'>
	<div class='api_head'>string <b>BBCode::GetSmiley</b> ( string $<tt>name</tt> )</div>
	<div class='api_descr'>This function returns the image for a given smiley,
		by name, the same array as was most recently given to <a href="api_smiley.html#ref_parser_AddSmiley">AddSmiley()</a>
		for that same smiley.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul>
		<li><i>name</i>: The text form of the smiley whose image name you'd like to retrieve.</li>
		</ul>
	</div>
	<div class='api_info'><b>Return values:</b>  The image for the given smiley, if the smiley
		has been defined; if it has not, the return value is <tt>false</tt>.</div>
</div>

<a name="ref_parser_ClearSmileys"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::ClearSmileys</b> ( )</div>
	<div class='api_descr'>This function removes <i>all</i> smileys currently associated with this
		BBCode object, after which the BBCode object will have no smileys defined at all.  This
		is useful if you want to replace the default smileys entirely with your own custom smileys.</div>
	<div class='api_info'><b>Parameters:</b>  None.</div>
	<div class='api_info'><b>Return Value:</b>  None.</div>
</div>

<a name="ref_parser_GetDefaultSmiley"></a>
<div class='api'>
	<div class='api_head'>array <b>BBCode::GetDefaultSmiley</b> ( string $<tt>name</tt> )</div>
	<div class='api_descr'>This function returns the default smiley image name
		for a given chunk of text, the same image name that would be used by a newly-constructed BBCode object.
		This provides you with an easy way to look up a smiley provided by the <a href="app_smiley.html">Standard Smileys</a>.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul>
		<li><i>name</i>: The text form of the smiley whose image name you'd like to retrieve.</li>
		</ul>
	</div>
	<div class='api_info'><b>Return values:</b>  The Standard Smiley's image name for the
		given text, if the text has been defined as a smiley; if it has not, the return value is <tt>false</tt>.</div>
</div>

<a name="ref_parser_SetDefaultSmiley"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetDefaultSmiley</b> ( string $<tt>name</tt> )</div>
	<div class='api_descr'>This function changes the smiley
		for a given text string to the same image name that would be used by a newly-constructed BBCode object,
		the same image name that would be returned by <a href="#ref_parser_GetDefaultSmiley">GetDefaultSmiley()</a>.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul>
		<li><i>name</i>: The text form of the smiley whose image name you'd like to revert to its default setting.</li>
		</ul>
	</div>
</div>

<a name="ref_parser_GetDefaultSmileys"></a>
<div class='api'>
	<div class='api_head'>array <b>BBCode::GetDefaultSmileys</b> ( )</div>
	<div class='api_descr'>This function returns a key =&gt; value array that contains <i>all</i>
		of the default smileys --- all of the smileys in the set of <a href="app_smiley.html">Standard Smileys</a>.  Each key
		is a text string, and each value is the name of an image.
		This is exactly the same as calling <a href="#ref_parser_GetDefaultSmiley">GetDefaultSmiley()</a>
		many times, once for each text string in the Standard Smileys, only much, much faster.</div>
	<div class='api_info'><b>Parameters:</b>  None.</div>
	<div class='api_info'><b>Return Value:</b>  An array containing all of the default smileys.</div>
</div>

<a name="ref_parser_SetDefaultSmileys"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetDefaultSmileys</b> ( )</div>
	<div class='api_descr'>This function causes the BBCode object's entire current set of smileys to be
		replaced with the default smileys --- in other words, to be changed back to the smileys
		that it had just after it was newly-created --- in other words, to the ruleset for the
		<a href="app_smiley.html">Standard Smileys</a>.  All of the existing smiley definitions in this
		object will be deleted, and the new, default smiley definitions will be installed instead.</div>
	<div class='api_info'><b>Parameters:</b>  None.</div>
	<div class='api_info'><b>Return Value:</b>  None.</div>
</div>

<a name="ref_parser_SetSmileyDir"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetSmileyDir</b> ( string $<tt>fullpath</tt> )</div>
	<div class='api_descr'>This function tells NBBC where smiley images are found,
		as a pathname relative to the root of the host's filesystem (an absolute pathname).
		You should usually use a full absolute pathname for this, like "<tt>/home/larry/web/smileys</tt>",
		and you should <i>not</i> include a trailing slash on the path, as one will be appended
		automatically.  Using relative pathnames can work, but may produce problems on some
		web servers.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>fullpath</i></tt>: The filesystem path to your smiley directory.</li>
		</ul>
	</div>
</div>

<a name="ref_parser_GetSmileyDir"></a>
<div class='api'>
	<div class='api_head'>string <b>BBCode::GetSmileyDir</b> ( )</div>
	<div class='api_descr'>This function returns the current smiley directory.
		See <a href="api_smiley.html#ref_parser_SetSmileyDir">SetSmileyDir()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  Returns the current smiley directory.
		If no smiley directory has been set, this returns simply "smileys".</div>
</div>

<a name="ref_parser_GetDefaultSmileyDir"></a>
<div class='api'>
	<div class='api_head'>string <b>BBCode::GetDefaultSmileyDir</b> ( )</div>
	<div class='api_descr'>This function returns the default local image directory.</div>
	<div class='api_info'><b>Return values:</b>  Always returns "smileys".</div>
</div>

<a name="ref_parser_SetSmileyURL"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetSmileyURL</b> ( string $<tt>url</tt> )</div>
	<div class='api_descr'>This function tells the browser where smileys are found,
		as an absolute URL.  You should usually use a full absolute URL for this, like
		"<tt>http://larry.example.com/smileys</tt>", and you should <i>not</i> include a
		trailing slash on the path, as one will be appended automatically.  Using relative
		(short) URLs can work, but may produce problems with some web browsers if not
		used carefully.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>url</i></tt>: The full URL to your smiley directory.</li>
		</ul>
	</div>
</div>

<a name="ref_parser_GetSmileyURL"></a>
<div class='api'>
	<div class='api_head'>string <b>BBCode::GetSmileyURL</b> ( )</div>
	<div class='api_descr'>This function returns the current smiley URL.
		See <a href="api_smiley.html#ref_parser_SetSmileyURL">SetSmileyURL()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  Returns the current smiley URL.
		If no smiley URL has been set, this returns simply "smileys".</div>
</div>

<a name="ref_parser_GetDefaultSmileyURL"></a>
<div class='api'>
	<div class='api_head'>string <b>BBCode::GetDefaultSmileyURL</b> ( )</div>
	<div class='api_descr'>This function returns the default smiley URL.</div>
	<div class='api_info'><b>Return values:</b>  Always returns "smileys".</div>
</div>

<a name="ref_parser_SetEnableSmileys"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetEnableSmileys</b> ( bool $<tt>enable</tt> )</div>
	<div class='api_descr'>This function determines whether smileys like <tt>:-)</tt> are
		converted to images or whether they are ignored and treated as plain text.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>enable</i></tt>:  If set to <tt>true</tt>, all defined smileys are converted to
			their matching images; this is the default behavior.  If set to <tt>false</tt>, all
			smileys are treated the same as any other text and passed to the output unchanged.</li>
		</ul>
	</div>
</div>

<a name="ref_parser_GetEnableSmileys"></a>
<div class='api'>
	<div class='api_head'>bool <b>BBCode::GetEnableSmileys</b> ( )</div>
	<div class='api_descr'>This function returns the current enable-smileys state.
		See <a href="api_smiley.html#ref_parser_SetEnableSmileys">SetEnableSmileys()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  Returns <tt>true</tt> or <tt>false</tt>, depending
		on whether smileys are being converted to images.</div>
</div>

<a name="ref_parser_SetMaxSmileys"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetMaxSmileys</b> ( int $<tt>count</tt> )</div>
	<div class='api_descr'>This function sets the maximum number of smileys that may be used in a single
		parsed string.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul class='api_params'>
		<li><tt><i>count</i></tt>:  The maximum number of smileys that may be used in a parsed string.
			Set to <tt>-1</tt> to make unlimited, which is the default.</li>
		</ul>
	</div>
</div>

<a name="ref_parser_GetMaxSmileys"></a>
<div class='api'>
	<div class='api_head'>int <b>BBCode::GetMaxSmileys</b> ( )</div>
	<div class='api_descr'>This function returns the current maximum number of smileys
		that may be parsed in a single parse.</div>
	<div class='api_info'><b>Return values:</b>  Returns the maximum number of smilies per parsed string. 
		<tt>-1</tt> indicates an unlimited number of smileys.
	</div>
</div>

