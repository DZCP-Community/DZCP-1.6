
<a name="ref_parser_SetPreTrim"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetPreTrim</b> ( string $<tt>how</tt> )</div>
	<div class='api_descr'>This function controls how NBBC removes whitespace from the
		start of the document.  The default behavior is to remove no whitespace; however,
		it can be convenient to have NBBC remove any initial spaces or newlines for you.
		You supply a whitespace-removal string in $<tt>how</tt> using the same syntax
		as in the <a href="app_rule.html">whitespace-removal parameters for a rule</a>.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul>
		<li><i>how</i>: This string describes what kinds of whitespace to remove.  It is
			a simple pattern expression, constructed from the following elements:
			<ul>
			<li><tt>s</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove any non-newline whitespace found.</li>
			<li><tt>n</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove a single newline, if one exists.</li>
			<li><tt>a</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove as many spaces and newlines as are found.</li>
			</ul>
			The pattern provided is matched <i>forward</i> from the start of the input.
			The default is the empty string, which means no whitespace or newlines
			are to be removed.
		</li>
		</ul>
	</div>
	<div class='api_info'><b>Return Value:</b>  None.</div>
	<div class='api_info'><b>Common Examples:</b><br />
		&nbsp; &nbsp; &nbsp; <tt>'s'&nbsp;&nbsp;</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove all non-newline whitespace<br />
		&nbsp; &nbsp; &nbsp; <tt>'sn'&nbsp;</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove all non-newline whitespace, then a single newline if it exists.<br />
		&nbsp; &nbsp; &nbsp; <tt>'ns'&nbsp;</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove a single newline if it exists, then all non-newline whitespace.<br />
		&nbsp; &nbsp; &nbsp; <tt>'sns'</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove all non-newline whitespace, then a single newline if it exists,<br />
		&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; then any non-newline whitespace found past that newline.
	</div>
</div>

<a name="ref_parser_GetPreTrim"></a>
<div class='api'>
	<div class='api_head'>string <b>BBCode::GetPreTrim</b> ( )</div>
	<div class='api_descr'>This function returns the current whitespace pre-trimming pattern.
		See <a href="api_trim.html#ref_parser_SetPreTrim">SetPreTrim()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  The current whitespace pre-trimming pattern.</div>
</div>

<a name="ref_parser_SetPostTrim"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetPostTrim</b> ( string $<tt>how</tt> )</div>
	<div class='api_descr'>This function controls how NBBC removes whitespace from the
		end of the document.  The default behavior is to remove no whitespace; however,
		it can be convenient to have NBBC remove any trailing spaces or newlines for you.
		You supply a whitespace-removal string in $<tt>how</tt> using the same syntax
		as in the <a href="app_rule.html">whitespace-removal parameters for a rule</a>.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul>
		<li><i>how</i>: This string describes what kinds of whitespace to remove.  It is
			a simple pattern expression, constructed from the following elements:
			<ul>
			<li><tt>s</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove any non-newline whitespace found.</li>
			<li><tt>n</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove a single newline, if one exists.</li>
			<li><tt>a</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove as many spaces and newlines as are found.</li>
			</ul>
			The pattern provided is matched <i>backward</i> from the very end of the input.
			The default is the empty string, which means no whitespace or newlines
			are to be removed.
		</li>
		</ul>
	</div>
	<div class='api_info'><b>Return Value:</b>  None.</div>
	<div class='api_info'><b>Common Examples:</b><br />
		&nbsp; &nbsp; &nbsp; <tt>'s'&nbsp;&nbsp;</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove all non-newline whitespace<br />
		&nbsp; &nbsp; &nbsp; <tt>'sn'&nbsp;</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove all non-newline whitespace, then a single newline if it exists.<br />
		&nbsp; &nbsp; &nbsp; <tt>'ns'&nbsp;</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove a single newline if it exists, then all non-newline whitespace.<br />
		&nbsp; &nbsp; &nbsp; <tt>'sns'</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove all non-newline whitespace, then a single newline if it exists,<br />
		&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; then any non-newline whitespace found past that newline.
	</div>
</div>

<a name="ref_parser_GetPostTrim"></a>
<div class='api'>
	<div class='api_head'>string <b>BBCode::GetPostTrim</b> ( )</div>
	<div class='api_descr'>This function returns the current whitespace post-trimming pattern.
		See <a href="api_trim.html#ref_parser_SetPostTrim">SetPostTrim()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  The current whitespace post-trimming pattern.</div>
</div>
