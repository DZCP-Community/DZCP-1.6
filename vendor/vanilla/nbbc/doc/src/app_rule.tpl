
<p>Tag rules are arrays that may contain any of the following members to describe
how the tag is to be processed.  Most members are optional, and default values will
usually be assumed if no member is given.</p>

<ul>
<li><b><tt>'mode'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>BBCODE_MODE_SIMPLE</tt>)<br />
	Must be exactly one of the following identifiers:<br /><br />
	<ul>
	<li><tt>BBCODE_MODE_SIMPLE</tt>:  Replaces the tag with the text defined in <tt>'simple_start'</tt>
		and <tt>'simple_end'</tt>.</li>
	<li><tt>BBCODE_MODE_CALLBACK</tt>:  Calls a callback function or method to format the tag.
		The formatting function is specified in the <tt>'method'</tt> parameter.</li>
	<li><tt>BBCODE_MODE_INTERNAL</tt>:  Calls an internal formatting function in the BBCode parser.
		The formatting function is specified in the <tt>'method'</tt> parameter, which must be a string.</li>
	<li><tt>BBCODE_MODE_LIBRARY</tt>:  Calls a formatting function that's part of the Standard BBCode Library (the BBCodeLibrary object).
		The formatting function is specified in the <tt>'method'</tt> parameter, which must be a string.</li>
	</ul><br />
	</li>

<li><b><tt>'allow_params'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>true<tt>)<br />
	Sets flag on whether tag can accept parameters or not. Set to false on common tags
	such as [b], [u], [i], [s], [sup], and [sub].<br /><br />
	</li>

<li><b><tt>'simple_start'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>""</tt>)<br />
	This parameter stores a text string that, in <tt>BBCODE_MODE_SIMPLE</tt>, describes what
	HTML to use in place of the start tag.<br /><br />
	</li>

<li><b><tt>'simple_end'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>""</tt>)<br />
	This parameter stores a text string that, in <tt>BBCODE_MODE_SIMPLE</tt>, describes what
	HTML to use in place of the end tag.  Note that if the rule's <tt>'content'</tt>
	member is set to <tt>BBCODE_PROHIBIT</tt>, the <tt>'simple_end'</tt> string will be
	concatenated to <tt>'simple_start'</tt> and the two of them together will be used in
	place of the start tag (the start tag, effectively, behaves as both the start and end
	tags if content is prohibited).<br /><br />
	</li>

<li><b><tt>'method'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>""</tt>)<br />
	This member is used with <tt>BBCODE_MODE_CALLBACK</tt>, <tt>BBCODE_MODE_INTERNAL</tt>, and
	in <tt>BBCODE_MODE_LIBRARY</tt> to determine what function to call:<br /><br />
	<ul>
	<li><tt>BBCODE_MODE_CALLBACK</tt>:  This may be a string, for calling a function,
		or an array, for calling a method of a class.  See PHP's
		documentation on <tt>call_user_func()</tt> for more details on how to
		define a callback function.  See the section on <a href="usage_call.html">callback tags</a> for more details
		on how to set up a <tt>BBCODE_MODE_CALLBACK</tt> tag.</li>
	<li><tt>BBCODE_MODE_INTERNAL</tt>:  This is a string, the name of an internal
		method of an instance of class <a href="api.html"><tt>BBCode</tt></a>.</li>
	<li><tt>BBCODE_MODE_LIBRARY</tt>:  This is a string, the name of a method of an
		instance of class <tt>BBCodeLibrary</tt>.</li>
	</ul><br />
	Note: Unlike most members, this member <i>must</i> be defined if <tt>BBCODE_MODE_CALLBACK</tt>,
	<tt>BBCODE_MODE_INTERNAL</tt>, or <tt>BBCODE_MODE_LIBRARY</tt> is used.  If you fail to
	define this member, NBBC may produce error messages or warnings, and will <i>definitely</i>
	not format your content correctly!<br /><br />
	</li>

<li><b><tt>'content'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>BBCODE_OPTIONAL</tt>)<br />
	Must be exactly one of the following identifiers:<br /><br />
	<ul>
	<li><tt>BBCODE_REQUIRED</tt>:  Non-empty content must be provided for this tag to
		be properly converted.  If empty content is provided by the user,
		this tag will be rendered verbatim rather than formatted.</li>
	<li><tt>BBCODE_OPTIONAL</tt>:  (The most common setting.)  Content is optional, and
		may be provided by the user between the start tag and the end
		tag.</li>
	<li><tt>BBCODE_PROHIBIT</tt>:  Content is disallowed.  Generally, this is used in
		conjunction with the <tt>'end_tag'</tt> member (see below) when end tags are prohibited
		as well as content.</li>
	<li><tt>BBCODE_VERBATIM</tt>:  Content is optional, but any tags or special formatting
		contained within it will be ignored:  The content is treated as
		plain text, and is sent to the formatting function exactly as
		given.  If this is used in conjunction with <tt>BBCODE_MODE_SIMPLE</tt>,
		the content <i>will</i> be passed through safety routines like
		<tt>htmlspecialchars()</tt>, so you can safely use this in combination
		with elements like <tt>&lt;pre&gt;</tt>, but not with elements like <tt>&lt;xmp&gt;</tt>.  If this
		is used with a formatting function, the formatting function will
		be responsible for applying <tt>htmlspecialchars()</tt> or any other form
		of cleanup necessary:  The formatting function will receive the
		exact text given by the user, whitespace included.  This setting is generally
		intended to be combined with the <tt>white-space:pre</tt> CSS rule applied to
		the block that contains the verbatim content.</li>
	</ul>
	<br /></li>

<li><b><tt>'before_tag'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>""</tt>)</li>
<li><b><tt>'after_tag'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>""</tt>)</li>
<li><b><tt>'before_endtag'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>""</tt>)</li>
<li><b><tt>'after_endtag'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>""</tt>)<br />
	These settings collectively control how (non-newline) whitespace and
	newlines are treated when placed in proximity to this tag or to its
	end tag.  For each setting, you use a simple pattern comprised of
	the following characters to describe what to remove:<br /><br />
	<ul>
	<li><tt>s</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove any non-newline whitespace found.</li>
	<li><tt>n</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove a single newline, if one exists.</li>
	<li><tt>a</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove as many spaces and newlines as are found.</li>
	</ul><br />
	For <tt>'before'</tt> removal, the pattern is matched <i>backward</i> from the tag;
	for 'after' removal, the pattern is matched <i>forward</i> from the tag.
	The default is the empty string, which means no whitespace or newlines
	are to be removed.<br /><br />
	
	<u>Common examples:</u><br />
	&nbsp; &nbsp; &nbsp; <tt>'s'&nbsp;&nbsp;</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove all non-newline whitespace<br />
	&nbsp; &nbsp; &nbsp; <tt>'sn'&nbsp;</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove all non-newline whitespace, then a single newline if it exists.<br />
	&nbsp; &nbsp; &nbsp; <tt>'ns'&nbsp;</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove a single newline if it exists, then all non-newline whitespace.<br />
	&nbsp; &nbsp; &nbsp; <tt>'sns'</tt> &nbsp; &nbsp; &nbsp; &nbsp; Remove all non-newline whitespace, then a single newline if it exists,<br />
		&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; then any non-newline whitespace found past that newline.<br />
	<br /></li>

<li><b><tt>'end_tag'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>BBCODE_REQUIRED</tt>)</li>
Must be exactly one of the following identifiers:<br /><br />
<ul>
<li><tt>BBCODE_REQUIRED</tt>:  (The most common setting.)  An ending <tt>[/tag]</tt> must be
	provided if the starting <tt>[tag]</tt> is used; if no end tag is given by
	the user, the start tag will be ignored and formatted verbatim.</li>
<li><tt>BBCODE_OPTIONAL</tt>:  An end tag is optional, and the content of the start
	tag extends until the end of this node of the document tree, or
	to the end of the document, whichever comes first.</li>
<li><tt>BBCODE_PROHIBIT</tt>:  An end tag is disallowed.  The start tag has no
	content, and is formatted as an isolated tag.  If an end tag is
	provided, it will be formatted verbatim.  This is most useful
	for tags like <tt>[rule]</tt> and <tt>[wiki]</tt> that have no content.</li>
</ul>
<br /></li>

<li><b><tt>'allow_in'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>array('block')</tt>)</li>
	This is an array that lists which other classes this tag may be
	placed inside.  See the description of <a href="app_class.html">content classes</a> below
	to understand how this is used.<br /><br /></li>

<li><b><tt>'class'</tt></b> &nbsp; &nbsp; &nbsp; &nbsp; (<i>Default value:</i>  <tt>'block'</tt>)</li>
	This describes what content-class this tag belongs to.  There is one
	special class, "<tt>block</tt>", which is used by the invisible "root tag" of the
	BBCode tree, but may be used for other tags as well.  See the description of
	<a href="app_class.html">content classes</a> below to understand how this is used.<br /><br /></li>
	
</ul>
