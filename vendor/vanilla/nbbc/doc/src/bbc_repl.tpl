
<a name="ref_lib_replaced_img"></a>
<p class='bblib'><b><tt>[img]...[/img]</tt></b><br />
	This inserts an image into your document.  You should provide a URL to the image between the start and
	end <tt>[img]</tt> tags, like this:<br />
	<br />
	&nbsp; &nbsp; &nbsp; &nbsp; <tt>Google's logo: [img]http://www.google.com/intl/en_ALL/images/logo.gif[/img]</tt><br />
	&nbsp; &nbsp; &nbsp; &nbsp; --&gt; Google's logo: <img src="http://www.google.com/intl/en_ALL/images/logo.gif" align='middle' /><br />
	Alternatively, if you instead use a partial filename (which <i>must</i> start with an alphanumeric
	character or an underscore), the <tt>[img]</tt> tag will locate the file relative to the defined
	<a href="api_repl.html#ref_parser_SetLocalImgDir">local image directory</a>, and will (A) determine that the
	image file actually exists on the local server and (B) insert correct width/height for it as well,
	which can speed page loading.  For example, if you run a webcomic site, it may be beneficial to
	set your local image directory to <tt>/comics</tt>, so that you can then include individual
	comics just by writing <tt>[img]20080704.jpg[/img]</tt>.  Any filename which starts with a <tt>.</tt>
	or a <tt>/</tt> will <i>not</i> be considered a local image file.</p>

<a name="ref_lib_replaced_rule"></a>
<p class='bblib'><b><tt>[rule]</tt></b><br />
	This inserts a horizontal rule (separator bar) into your document.  When you use the shorthand <tt>-----</tt> form,
	NBBC converts it into a <tt>[rule]</tt> tag.  The default behavior of the <tt>[rule]</tt> tag is to generate
	a <tt>&lt;hr&nbsp;/&gt;</tt> HTML element as its output; this behavior can be changed with the
	<a href="api_rule.html#ref_parser_SetRuleHTML">SetRuleHTML</a> function.  The <tt>[rule]</tt> tag does not have an end tag.</p>

<a name="ref_lib_replaced_br"></a>
<p class='bblib'><b><tt>[br]</tt></b><br />
	This inserts a break (<tt>&lt;br /&gt;</tt>) into your document; it is equivalent to
	a carriage return, but unlike a carriage return, it can't be "eaten" by a nearby block tag like
	<tt>[center]</tt>.  The <tt>[br]</tt> tag does not have an end tag.</p>
