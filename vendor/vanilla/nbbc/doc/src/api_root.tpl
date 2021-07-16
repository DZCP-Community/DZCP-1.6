
<a name="ref_parser_SetRootInline"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetRootInline</b> ( )</div>
	<div class='api_descr'>This function tells NBBC that the BBCode content to be formatted
		is <i>inline</i> content --- it's part of another paragraph --- which means it cannot
		contain block formatting tags such as <tt>[center]</tt> and <tt>[columns]</tt> and
		<tt>[quote]</tt> and <tt>[code]</tt>.  Any attempts to use those tags will result
		in them appearing verbatim, as if they were not registered rules or tags at all.
		This <i>inline mode</i> is most useful when you have content that must be joined to
		other pre-existing content.<br />
		<br />
		As always, whether in <i>inline mode</i> or in <i>block mode</i>, NBBC does <i>not</i>
		wrap the output in an HTML element of any kind:  Generally, you will want to wrap
		the output in a &lt;span&gt; element (when inline mode is active), but you do not
		have to if that doesn't fit your needs.</div>
	<div class='api_info'><b>Parameters:</b>  None.</div>
	<div class='api_info'><b>Return Value:</b>  None.</div>
	<div class='api_info_block'>
		<div class='api_info_block_header'>Notes:</div>
		
		The best way to understand the difference between the root class being "<tt>inline</tt>"
		and the root class being "<tt>block</tt>" is to see an example.  Consider the BBCode
		below:
		<div style='margin-left: 2em;'>
			<div class='code_header'>Code:</div>
			<xmp class='code'>[center]This is a [i]test[/i].[/center]</xmp>
		</div>
		In block mode, this BBCode would be converted to:
		<div style='margin-left: 2em;'>
			<div class='output_header'>Output:</div>
			<div class='output'>&lt;div style="text-align:center"&gt;This is a &lt;i&gt;test&lt;/i&gt;.&lt;/div&gt;</div>
		</div>
		But in inline mode, this same BBCode would be converted to this instead:
		<div style='margin-left: 2em;'>
			<div class='output_header'>Output:</div>
			<div class='output'>[center]This is a &lt;i&gt;test&lt;/i&gt;.[/center]</div>
		</div>
		Notice that in inline mode, NBBC refuses to generate &lt;div&gt; tags in the output;
		this is because inline mode means that you intend to put the content inside a
		&lt;span&gt; or equivalent element, and &lt;div&gt; is illegal inside &lt;span&gt;,
		so all tags that might require &lt;div&gt; or similar block layouts become forbidden:
		In short, only text-style tags like [i] and [b] and [font], and link tags like
		[url] and [wiki], and [img] tags (and smileys!) --- tags that affect the current
		line of text but that don't start a new one --- are allowed in inline mode, while
		all tags are allowed in block mode.
	</div>
</div>

<a name="ref_parser_SetRootBlock"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetRootBlock</b> ( )</div>
	<div class='api_descr'>This function tells NBBC that the BBCode content to be formatted
		is <i>block</i> content --- it's <i>not</i> part of another paragraph, and likely
		will be wrapped in its own &lt;div&gt; element by the caller.  In <i>block mode</i>,
		all BBCode tags are legal (contrast this with <i><a href="#ref_parser_SetRootInline">inline mode</a></i>
		where only text-style tags, link tags, and images are legal).<br />
		<br />
		Note that even in block mode, you cannot (should not) wrap the output inside an
		HTML &lt;p&gt; element, because &lt;p&gt; is actually very restricted in the kinds of
		content it can contain:  For example, the &lt;table&gt; element used by NBBC to
		create columns is illegal inside a &lt;p&gt; element.  So if you need to wrap your
		output in a single HTML element, you should use a &lt;div&gt; element.<br />
		<br />
		As always, whether in <i>inline mode</i> or in <i>block mode</i>, NBBC does <i>not</i>
		wrap the output in an HTML element of any kind:  Generally, you will want to wrap
		the output in a &lt;div&gt; element (when block mode is active), but you do not
		have to if that doesn't fit your needs.</div>
	<div class='api_info'><b>Parameters:</b>  None.</div>
	<div class='api_info'><b>Return Value:</b>  None.</div>
</div>

<a name="ref_parser_SetRoot"></a>
<div class='api'>
	<div class='api_head'>void <b>BBCode::SetRoot</b> ( string $<tt>class</tt> )</div>
	<div class='api_descr'>This function controls the root class NBBC uses.  The classes
		NBBC normally uses for root classes are "<tt>block</tt>" and "<tt>inline</tt>",
		but if you create new classes, you can tell NBBC to start with one of those
		instead using this function.</div>
	<div class='api_info'><b>Parameters:</b>
		<ul>
		<li><i>class</i>:  A classname to use as the root class.  See the appendix on
			<a href="app_class.html">content classes</a> for more details.</li>
		</ul>
	</div>
	<div class='api_info'><b>Return Value:</b>  None.</div>
</div>

<a name="ref_parser_GetRoot"></a>
<div class='api'>
	<div class='api_head'>string <b>BBCode::GetRoot</b> ( )</div>
	<div class='api_descr'>This function returns the currently-selected root class, usually
		either "<tt>block</tt>" or "<tt>inline</tt>".  See
		<a href="api_root.html#ref_parser_SetRootInline">SetRootInline()</a>,
		<a href="api_root.html#ref_parser_SetRootBlock">SetRootBlock()</a>,
		or <a href="api_root.html#ref_parser_SetRoot">SetRoot()</a> for more details.</div>
	<div class='api_info'><b>Return values:</b>  The currently-selected root class.</div>
</div>
