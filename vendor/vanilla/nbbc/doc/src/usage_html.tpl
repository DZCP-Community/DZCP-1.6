
<a name="overview"></a>
<h4>Overview</h4>

<p>So you say that you don't really want BBCode, and just want a safer version of HTML instead?
Maybe you just prefer the syntax and structure of HTML, or maybe you want to use a Javascript
WYSIWYG HTML editor on your site?  Believe it or not, NBBC can do HTML syntax too.  In this
section, we'll discuss what it takes to implement a "safe HTML" using NBBC, and look at some
of the issues and caveats involved.</p>

<p>BBCode is not, in general, that different from HTML.  They both use "tags" to represent
document structure, and although their tags aren't the same, there is some overlap.  BBCode
tends to focus on presentation, while HTML tends to focus on structure, but some tags, like
<tt>[b]</tt> and <tt>&lt;b&gt;</tt> are nearly identical in behavior.  These are the major
differences between BBCode and HTML:</p>

<ol>
<li>BBCode uses [brackets] while HTML uses &lt;angle brackets&gt;.</li>
<li>BBCode treats a newline as a paragraph break; HTML ignores it.</li>
<li>HTML allows the use of <i>entities</i>, character codes produced by the "&amp;" character, such
	as <tt>&amp;lt;</tt> and <tt>&amp;amp;</tt> and <tt>&amp;eacute;</tt>.</li>
<li>BBCode has mostly presentation tags, while HTML has mostly structural tags, leaving presentation to CSS.</li>
</ol>

<p>NBBC has specific features to address points 1, 2, and 3 above; point 4, the issue of
translating input pseudo-HTML tags into valid output HTML entities (i.e., replacing the
Standard BBCode Library) is left up to you (although in future versions of NBBC, we may
add a Standard HTML Library if enough people demand it).</p>

<p>Let's tackle each of points 1, 2, and 3 separately, and then put them all together
at the end.</p>

<a name="tagmarkers"></a>
<h4>Switching Tag Markers</h4>

<p>NBBC lets you use any of [brackets], &lt;angle brackets&gt;, {curly braces}, or (parentheses)
to delineate your tags.  (Most likely, you'll want either [brackets] or &lt;angle brackets&gt;,
but the other two are offered in case you need them.)</p>

<p>Switching from using [brackets] to using &lt;angle brackets&gt; is easy:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>$htmlparser = new BBCode;
$htmlparser->SetTagMarker('<');
...
$output = $htmlparser->Parse($input);</xmp>

<p>The <a href="api_core.html#ref_parser_SetTagMarker">SetTagMarker</a> function changes the
current tag marker to your desired marker.  Note that NBBC still behaves otherwise the same:
It simply uses a different character for marking the start and end of tags.
<a href="usage_wiki.html">[[Wiki-links]]</a> are fully supported no matter what tag marker
you use, and always use the current tag marker; for example, if the tag marker is '&lt;',
a valid wiki-link might look like this:  &lt;&lt;keyword&gt;&gt;</p>

<p>The default tag marker is '[', and you can determine the current tag marker by calling
<a href="api_core.html#ref_parser_GetTagMarker">GetTagMarker</a>.</p>

<a name="newlines"></a>
<h4>Disabling Newline Breaks</h4>

<p>Normally, NBBC treats a newline as the end of a paragraph:  An HTML <tt>&lt;br&nbsp;/&gt;</tt>
tag is inserted anywhere a newline appears, except when it's close to a tag that prohibits
newlines near it.  While this is very convenient for the user, this is very much un-HTML-like,
as HTML is a fully free-formatted language:  Newlines mean nothing special in HTML.</p>

<p>NBBC can be told to treat newlines as plain whitespace, just like HTML does.  To do
this, you use the <a href="api_core.html#ref_parser_SetIgnoreNewlines">SetIgnoreNewlines</a>
function:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>$htmlparser = new BBCode;
$htmlparser->SetIgnoreNewlines(true);
...
$output = $htmlparser->Parse($input);</xmp>

<p>When "ignore-newlines" is <tt>true</tt>, NBBC will treat newlines almost exactly the same
as it treats whitespace, and will <i>not</i> generate <tt>&lt;br&nbsp;/&gt;</tt> tags in
the output.  (In fact, the only difference between newlines and other whitespace is that
newlines are regularized to Un*x format:  Whether they're "\r\n" or "\n" or "\r" in the
input, they'll always be "\n" in the output.)</p>

<p>By default, "ignore-newlines" is <tt>false</tt>, and you can determine the current state
by calling <a href="api_core.html#ref_parser_GetIgnoreNewlines">GetIgnoreNewlines</a>.</p>

<a name="entities"></a>
<h4>Allowing HTML Entities</h4>

<p>Normally, NBBC takes all input characters and makes them safe for HTML output:  For example,
a &lt; symbol in the input will be turned into a <tt>&amp;lt;</tt> entity in the output.
Usually, this is desirable; however, when you have set the tag marker to '<tt>&lt;</tt>', you
probably want HTML behavior, and want to be able to type <tt>&amp;lt;</tt> in the input to
get a &lt; symbol in the output.</p>

<p>To allow entities, you need to allow the ampersand character ('&amp;') to be passed through
unchanged to the output.  Normally, NBBC, upon seeing a &amp; symbol in the input, will
turn it into <tt>&amp;amp;</tt> in the output, which means that if you type <tt>&amp;lt;</tt>
in the input, you'll see <tt>&amp;lt;</tt> in the output (which is actually
<tt>&amp;amp;lt;</tt> if you look at the HTML source).  But this isn't what you want
when you're trying to process HTML:  You want a &amp; in the input to be an &amp; in
the output.</p>

<p>NBBC includes a convenient pair of functions to control how the ampersand character is
processed, whether it's translated to safe HTML or whether it's passed through unchanged.
You can allow &amp; to be passed through unchanged like this:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>$htmlparser = new BBCode;
$htmlparser->SetAllowAmpersand(true);
...
$output = $htmlparser->Parse($input);</xmp>

<p>When "allow-ampersand" is <tt>true</tt>, the ampersand will be passed to the output
entirely unchanged, which is exactly what you want when processing HTML.  The default is
<tt>false</tt>, and you can determine the current state
by calling <a href="api_core.html#ref_parser_GetAllowAmpersand">GetAllowAmpersand</a>.</p>

<a name="together"></a>
<h4>Putting It All Together</h4>

<p>So now let's assemble all these pieces into a single short script that can parse HTML.
Our HTML tags will match the BBCode tags, but if you need HTML-specific tags, you can always
add support for them with <a href="api_rule.html#ref_parser_AddRule">AddRule</a>.  (That said,
in a future version of NBBC, there may be a Standard HTML Library added if enough people want
it.)  So this code is generally what you'll want if you're implementing HTML:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>$htmlparser = new BBCode;
$htmlparser->SetTagMarker('<');         // HTML uses <angle brackets>.
$htmlparser->SetIgnoreNewlines(true);   // HTML is free-formatted.
$htmlparser->SetAllowAmpersand(true);   // HTML uses & for escaping entities.
$htmlparser->DisableSmileys();          // HTML doesn't have built-in smileys.
...
$htmlparser->ClearRules();              // No BBCode rules.
$htmlparser->AddRule("p", ...);         // Allow the <p> element.
$htmlparser->AddRule("b", ...);         // Allow the <b> element.
$htmlparser->AddRule("i", ...);         // Allow the <i> element.
$htmlparser->AddRule("a", ...);         // Allow the <a> element.
$htmlparser->AddRule("pre", ...);       // Allow the <pre> element.
...
...more rules...
...
$output = $htmlparser->Parse($input);</xmp>

<p>First, we switch to &lt;HTML&gt; tag markers, and we treat newlines as plain whitespace,
and we allow amperands to be used to provide entities.  Then we remove all the BBCode rules,
and add rules specific to HTML.  And that's all it takes.</p>
