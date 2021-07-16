
<p>With an enhanced mode tag or the <a href="api_misc.tpl#ref_parser_FillTemplate">FillTemplate()</a>
function, you may add formatting flags to a variable after a slash
character (<tt>/</tt>) to control how its text is cleaned up.  By default, the text is
unchanged, passed raw to the output.  (Note: This differs from the behavior of NBBC v1.2
and earlier.)  However, you can change this so that you do not have to manually encode
your data:  For example, using <tt>{$variable/u}</tt> instead of just <tt>{$variable}</tt>
causes this variable to be passed through <tt>urlencode()</tt>, and using <tt>{$variable/e}</tt>
causes this variable to be passed through <a href="api_misc.html#ref_parser_HTMLEncode">HTMLEncode()</a>.
The available formatting flags are:</p>

<ul>
<li style='margin-bottom:1em;'><b><tt>v</tt> - Verbatim.</b>  Do not apply any formatting to the variable; use its exact text,
	however the user provided it.  This overrides all other flags.</li>

<li><b><tt>b</tt> - Apply <tt>basename()</tt> to the variable.</b></li>
<li><b><tt>n</tt> - Apply <tt>nl2br()</tt> to the variable.</b></li>
<li><b><tt>t</tt> - Trim.</b>  This causes all initial and trailing whitespace to be trimmed (removed).</li>
<li style='margin-bottom:1em;'><b><tt>w</tt> - Clean up whitespace.</b>  This causes all non-newline whitespace, such as
	control codes and tabs, to be collapsed into individual space characters.</li>

<li><b><tt>e</tt> - Apply <a href="api_misc.html#ref_parser_HTMLEncode"><tt>BBCode::HTMLEncode()</tt></a></b> to the variable.</li>
<li><b><tt>h</tt> - Apply <tt>htmlspecialchars()</tt></b> to the variable.</li>
<li><b><tt>k</tt> - Apply <a href="api_wiki.html#ref_parser_Wikify"><tt>BBCode::Wikify()</tt></a></b> to the variable.</li>
<li><b><tt>u</tt> - Apply <tt>urlencode()</tt></b> to the variable.</li>
</ul>

<p>Note that only one of the 'e', 'h', 'k', or 'u' flags may be specified;
these four flags are mutually-exclusive.  Note also that the 'v' flag overrides
all other flags, and can effectively be used during debugging to temporarily
disable all formatting flags.</p>
