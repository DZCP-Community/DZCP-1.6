
	<div class='tipbox'>
	<div class='tipbox_header'>
		<div class='tipbox_star'>*</div><div><b>Tech Tip</b></div>
	</div>
	<div class='tipbox_content'><div class='tipbox_content2'>
		<p>This section is intended primarily for experienced software engineers and
		computer scientists.  It's heavy on theory, light on practice, and if you're
		uncomfortable around concepts like computational orders, finite-state machines,
		and push-down automata, you're going to be lost.  If you need an introduction to
		these concepts, we recommend the classic <a href="http://www.amazon.com/Compilers-Principles-Techniques-Tools-2nd/dp/0321486811/ref=pd_bbs_sr_1?ie=UTF8&s=books&qid=1216066409&sr=8-1">Dragon Book</a>
		and Hopcroft and Ullman's <a href="http://www.amazon.com/Introduction-Automata-Theory-Languages-Computation/dp/0321462254/ref=sr_1_3?ie=UTF8&s=books&qid=1216066500&sr=1-3">Little Book of Evil</a>,
		but be forewarned that compilers are a dense subject, and each one of those books
		could readily be the subject of a year or two of study at a decent university.</p>
		
		<p>For what it's worth, you don't need to know any of the stuff in this section
		to be able to use NBBC; this section is primarily of academic interest.</p>
	</div></div>
	</div>

<p>NBBC is a compiler, and is broken down into two main layers:  There's the lexical analyzer,
which is responsible for breaking characters sequences into tokens, and is implemented in
<tt>nbbc_lex.php</tt>; and there's the parser, which is responsible for analyzing the token
sequences and generating proper output.  This appendix is <i>not</i> intended to be a complete
analysis of the structure of NBBC:  Rather, it's intended to be a broad overview that will
help you to understand the rationale embodied by the source code, which is well-commented,
so should be relatively easy to follow once you've read this short discussion.</p>

<h4>The Lexer</h4>

<p>Most of the discussion and most of the complexity is focussed in the parser, so let's
quickly get the lexical analyzer (lexer) out of the way.  Like most lexers, NBBC's is
built on regular expressions.  The general principle used by the lexer is to
use <tt>preg_split</tt> to initially divide the input into an array of strings that
alternate between tags/whitespace/newlines in the odd array indices and non-whitespace
text strings in the even array indices.  A simple flag is used to track whether the
next item in the array is a tag/whitespace/newline or a text string.</p>

<p>For this discussion, we'll use the following input as our primary example:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>Before.
[center]Hello, [i]World[/i]![/center]
After.</xmp>

<p>The lexer breaks this input down into the following stream of tokens (wrapped here for
clarity's sake; note that the empty array elements are omitted in this discussion for
clarity):</p>

<div class='code_header'>Code:</div>
<xmp class='code'>"Before." WS NL [center] "Hello," WS [i] "World" [/i] "!" [/center] NL "After."</xmp>

<p>Each token is of type <tt>BBCODE_TEXT</tt> (like "<tt>Before.</tt>"), <tt>BBCODE_NL</tt>
(which is a newline matching either Un*x, Windows, or Mac formats), <tt>BBCODE_WS</tt> (any
sequence of non-newline whitespace characters), <tt>BBCODE_TAG</tt> (a start tag), or
<tt>BBCODE_ENDTAG</tt> (an end tag).  These are retrieved by the parser one at a time
by calling <tt>BBCodeLexer::NextToken()</tt>.</p>

<h4>Overview of the Parser</h4>

<p>The parser is a push-down automaton that recognizes an LL(1) grammar.
Its design is inspired by that of simple push-down mathematical expression parsers, where
the state of the stack and a single token of lookahead is used to determine what operation
to perform next.  This means that the parser is implicitly constructing a document tree
based on the tokens and the tags' class contraints, but because it's an implict instead
of explicit construction, it's much faster than a genuine tree-builder:  We don't need to
allocate, store, and free tree nodes for this to work.</p>

<p>When provided with a string, the parser gives it to the lexer, which breaks it down
into tokens; then the parser reads each successive token, one at a time, and processes
the token according to its current state and the contents of the stack.  This is an
approximate description of what happens for each incoming token, ignoring the many edge
cases, error-detection and error-correction, and whitespace cleanups:</p>

<ul>
<li><b><tt>BBCODE_TEXT</tt></b>:  Plain text is filtered, and then pushed onto the top
	of the stack to await further processing.</li>
<li><b><tt>BBCODE_WS</tt></b>:  Non-newline whitespace is pushed just like text.</li>
<li><b><tt>BBCODE_NL</tt></b>:  Newlines are pushed onto the stack mostly like text.</li>
<li><b><tt>BBCODE_TAG</tt></b>:  One of several things can happen, depending on what kind of tag this is:
	<ul>
	<li><b>If the tag is a verbatim tag,</b> run a BBCODE_CHECK on it, then collect successive
		tokens until we reach its matching end tag.  Pack the intervening tokens together,
		and pass the result to the tag's processing function.  The output of the processing
		function is then pushed back onto the stack as text.</li>
	<li><b>If the tag is an isolated tag</b> (it has no end tag), run a BBCODE_CHECK on it,
		and then immediately pass the tag to the tag's processing function.  The output of
		the processing function is then pushed back onto the stack as text.</li>
	<li><b>For all other start tags,</b> including those with both required and optional end
		tags, we simply push the tag onto the stack, since we won't know how to fully
		process it until its body has been processed.</li>
	</ul>
	</li>
<li><b><tt>BBCODE_ENDTAG</tt></b>:  Walk backwards up the stack, searching for a matching
	start tag.  If an ending-optional tag is found in between, a suitable end tag is generated
	for it on the spot and processed, until there is nothing between this end tag and its
	start tag except for processed HTML text.  The processed HTML text is collected as a string,
	and passed to the tag's processing function; the output of
	the processing function is then pushed back onto the stack as text.</li>
</ul>

<p>At the end of processing the input, the stack will contain the output as multiple stack
entries; these are collected to a single string, which is returned as the output.</p>

<h4>Performance analysis</h4>

<p>This algorithm runs in O(n) time except when we encounter an end tag and have to go back
up the stack; then it's O(n) to walk back to the start tag, and O(n) to collect tokens between
the start tag and end tag and collapse them into a string.  So this is effectively O(n+n*m),
where "n" is the number of input tokens and "m" is the number of end tags; however, that's
a worst-case analysis, assuming that each end tag contains an entire document's worth of tokens
between it and its start tag.  In practice, the number of tokens between a start and an end tag is
fairly small, approaching some constant k (which is around 20 or 30 tokens, in practice), so in
typical, average cases, this algorithm runs in O(n+m) time.</p>

<p>(As a future optimization, a constant-time speedup could be achieved by using
hash tables to track start tag locations, instead of searching for them, but the
intervening tokens must be collected anyway when the start tag is found, so the resulting
search performance would still be O(n).)</p>

<h4>Error-correction</h4>

<p>While <tt>BBCODE_TEXT</tt>, <tt>BBCODE_WS</tt>, and <tt>BBCODE_NL</tt> tokens are always
legal, <tt>BBCODE_TAG</tt> and <tt>BBCODE_ENDTAG</tt> tokens may be illegal in some cases,
and the parser needs to cope with this.  There are several possibilities:</p>

<ul>
<li><b><tt>BBCODE_TAG</tt> with no matching <tt>BBCODE_ENDTAG</tt></b>:  When text is being collected
	at the end, the initial tag will have an invisible <tt>BBCODE_ENDTAG</tt> added to the end of the input.</li>
<li><b><tt>BBCODE_ENDTAG</tt> with no matching <tt>BBCODE_TAG</tt></b>:  This is caught during the
	processing of <tt>BBCODE_ENDTAG</tt>:  If the search up through the stack finds no
	matching <tt>BBCODE_TAG</tt>, this end tag is simply converted to plain text and pushed
	as a new <tt>BBCODE_TEXT</tt> entry.</li>
<li><b>Tag inside a class that doesn't allow it</b>:  If, for example, a user places a
	<tt>[center]</tt> inside an <tt>[i]</tt> tag, that's illegal, and NBBC has a special
	routine to handle this case.  When a <tt>BBCODE_TAG</tt> is encountered, it is checked
	against the current containing class, which can be determined from the top of the stack;
	if the containing class is disallowed, this <tt>BBCODE_TAG</tt> will be converted to
	plain text and pushed as a <tt>BBCODE_TEXT</tt> token.</li>
<li><b>Mis-ordered <tt>[i][b]</tt>tag nesting<tt>[/i][/b]</tt></b>:  This is detected during
	<tt>BBCODE_ENDTAG</tt> processing:  If the walk back up the stack crosses over any
	non-matching start tags, NBBC generates a matching end tag for each of those start tags
	just before the current end tag, and sets a flag to indicate that any single following
	unmatched end tag with the same name should be silently removed from the input.  We can
	be sure that any start tags that are crossed are not start tags of a higher class,
	because they would have been turned into plain text (by the previous error-handling rule)
	if they were.  So in the example of <tt>[i][b]tag&nbsp;nesting[/i][/b]</tt>, upon reaching the <tt>[/i]</tt>, the
	parser would search for the <tt>[i]</tt>, find the <tt>[b]</tt> first, and generate an
	additional <tt>[/b]</tt> before the <tt>[/i]</tt>; then, to help "fix" the output, NBBC
	will silently remove any one unmatched <tt>[/b]</tt> it finds later in the input stream.
	So the output would be <tt>&lt;i&gt;&lt;b&gt;tag&nbsp;nesting&lt;/b&gt;&lt;/i&gt;</tt>,
	which is about what the user might expect to get.  Note that for some really ugly mis-nestings,
	this rule will produce weird results, but they'll still at least be valid XHTML 1.0 Strict.</li>
</ul>

<h4>Example processing</h4>

<p>Let's look at how this would then process our example input.  First, this is again our
incoming token stream:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>"Before." WS NL [center] "Hello," WS [i] "World" [/i] "!" [/center] NL "After."</xmp>

<p>This is what happens during the parse of this stream:</p>

<ol>
<li>Initial class is "block".</li>
<li><b><tt>"Before."</tt></b> read, and pushed onto the stack.</li>
<li><b><tt>WS</tt></b> read, and pushed onto the stack.</li>
<li><b><tt>NL</tt></b> read.  <tt>WS</tt> on the stack is popped (NL consumes nearby WS), and the NL is pushed onto the stack.</li>
<li><b><tt>[center]</tt></b> read.  Class is checked, and <tt>[center]</tt> is allowed within "block".  Pop the NL
	from the stack, since <tt>[center]</tt> consumes newlines.  <tt>[center]</tt> is
	then pushed onto the stack, and class is changed to "block".</li>
<li><b><tt>"Hello,"</tt></b> read, and pushed onto the stack.</li>
<li><b><tt>WS</tt></b> read, and pushed onto the stack.</li>
<li><b><tt>[i]</tt></b> read.  Class is checked, and <tt>[i]</tt> is allowed within "block".  <tt>[i]</tt>
	is pushed onto the stack, and class is changed to "inline".</li>
<li><b><tt>"World"</tt></b> read, and pushed onto the stack.</li>
<li><b><tt>[/i]</tt></b> read.  Search down the stack for a <tt>[i]</tt>.  Remove all tokens in between and
	convert them to a string.  Pass string to <tt>[i]</tt>'s processing function, and pop <tt>[i]</tt> from stack.
	Push the resulting HTML, "<tt>&lt;i&gt;World&lt;/i&gt;</tt>", onto the stack.</li>
<li><b><tt>"!"</tt></b> read, and pushed onto the stack.</li>
<li><b><tt>[/center]</tt></b> read.  Search down the stack for a <tt>[center]</tt>.  Remove all tokens in between and
	convert them to a string.  Pass string to <tt>[center]</tt>'s processing function, and pop <tt>[center]</tt> from stack.
	Push the resulting HTML, "<tt>&lt;div style="text-align:center;"&gt;Hello, &lt;i&gt;World&lt;/i&gt;!&lt;/div&gt;</tt>", onto the stack.
	Remove any trailing NL or WS tokens, since <tt>[/center]</tt> consumes newlines.</li>
<li>(The next <tt>NL</tt> was skipped by <tt>[/center]</tt>, so it's not seen by the main parsing loop.)</li>
<li><b><tt>"After."</tt></b> read, and pushed onto the stack.</li>
<li><b>End-of-input.</b>  Main parsing loop ends.  Everything still on the stack is collected as a string,
	"<tt>Before.&lt;div style="text-align:center;"&gt;Hello, &lt;i&gt;World&lt;/i&gt;!&lt;/div&gt;After.</tt>",
	which is then returned to the caller.</li>
</ol>

<p>For what it's worth, you can test this yourself by enabling <a href="#ref_parser_SetDebug">debug mode</a>:
In debug mode, the parser prints to the browser each action it performs, separating each token with horizontal
rules.  In addition to being useful for debugging new tags, this mode can also be useful for divining the
basic workings of the parser itself.</p>
