
<p>Often when building applications that use BBCode, it can be useful to generate
partial HTML output or simplified HTML output or even plain-text output from the
BBCode source.  For example, an RSS feed for a blog might require only short excerpts
from each blog posting.  Sadly, simply cutting off the input or output won't do the
job:  How do you know that you haven't broken the text in the middle of a word,
or worse, in the middle of a tag?</p>

<p>NBBC v1.2 and later includes some special functions to handle exactly this
problem:  You can tell it to cut short the text, or produce simplified HTML or
plain-text output, and it will always do it between words, never break or damage
a tag, and will always output proper ending HTML tags no matter where you ask
it to break the input text.  In this section, we'll look at how to use the
features NBBC provides to support these needs, and how to combine these features
to produce useful, practical results.</p>

<p>One side note before we begin:  In the <tt>examples/</tt> directory included
with NBBC, there is a <tt>limit_example.php</tt> program that demonstrates all of
these techniques.  If you're feeling impatient and don't want to read this and
just want to see how a simple working program does it, go study that one.</p>

<a name="limit"></a>
<h4>Limiting Lengths</h4>

<p>Let's say that you have a huge chunk of text.  Really huge.  Monstrously huge.
And you want to trim it down to a svelte, say, 200 characters or less, something
that can be posted as a one-or-two-line blurb somewhere.  NBBC makes this easy
with its <a href="api_behv.html#ref_parser_SetLimit">SetLimit()</a> function.</p>

<p>SetLimit() takes as a parameter the maximum number of text characters you want
NBBC to output.  This is not the same as the number of <i>characters</i> you want
NBBC to output.  What's the difference?  <i>Tags</i>.  In NBBC's way of thinking,
when you cut off text, you don't care about the HTML tags, which usually can't be
seen anyway; you only care about the actual text itself, the words the user sees.
So the following two code blocks have different physical lengths, but the same number
of text characters that the user will see:</p>

<xmp class='code'>The quick brown fox jumps slightly
to the left of the lazy dog.</xmp>

<xmp class='code'>The quick <i>brown</i> fox jumps slightly
to the <b>left</b> of the <a href="http://www.lazydog.com">lazy dog</a>.</xmp>

<p>In both cases, the user will see the same sentence, so NBBC considers these to
be equal from a text-length perspective:  The italics and boldface and link don't
factor into the text length.</p>

<p>So let's look at an example program now and see how this works.  This example
program will produce a nice long copy of the beginning paragraphs of the Declaration
of Independence, neatly formatted, and signed by the author:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>$input = <<< EOI
From [url=http://www.ushistory.org/Declaration/document/index.htm]ushistory.org[/url]:

[center][b][size=5]In CONGRESS, July 4, 1776[/size]
The unanimous Declaration of the thirteen united States of America[/b][/center]

[b][size=6]W[/size][/b]hen in the Course of human events it becomes necessary for one
people to dissolve the political bands which have connected them with another and to
assume among the powers of the earth, the separate and equal station to which the Laws
of Nature and of Nature's God entitle them, a decent respect to the opinions of mankind
requires that they should declare the causes which impel them to the separation.

We hold these truths to be self-evident, that all men are created equal, that they are
endowed by their Creator with certain unalienable Rights, that among these are Life,
Liberty and the pursuit of Happiness. -- That to secure these rights, Governments are
instituted among Men, deriving their just powers from the consent of the governed, --
That whenever any Form of Government becomes destructive of these ends, it is the Right
of the People to alter or to abolish it, and to institute new Government, laying its
foundation on such principles and organizing its powers in such form, as to them shall
seem most likely to effect their Safety and Happiness. Prudence, indeed, will dictate
that Governments long established should not be changed for light and transient causes;
and accordingly all experience hath shewn that mankind are more disposed to suffer,
while evils are sufferable than to right themselves by abolishing the forms to which
they are accustomed. But when a long train of abuses and usurpations, pursuing
invariably the same Object evinces a design to reduce them under absolute Despotism, it
is their right, it is their duty, to throw off such Government, and to provide new
Guards for their future security. -- Such has been the patient sufferance of these
Colonies; and such is now the necessity which constrains them to alter their former
Systems of Government. The history of the present King of Great Britain is a history
of repeated injuries and usurpations, all having in direct object the establishment of
an absolute Tyranny over these States. To prove this, let Facts be submitted to a
candid world.

[right][color=green][i]--- written by Thomas Jefferson[/i][/color][/right]
EOI;

$bbcode = new BBCode;
$output = $bbcode->Parse($input);</xmp>

<div class='output_header'>Output:</div>
<div class='output'>
<div class='bbcode' style='margin:1em;'>From <a href="http://www.ushistory.org/Declaration/document/index.htm" class="bbcode_url">ushistory.org</a>:<br />

<div class="bbcode_center" style="text-align:center">
<b><span style="font-size:1.5em">In CONGRESS, July 4, 1776</span><br />
The unanimous Declaration of the thirteen united States of America</b>
</div>
<br />
<b><span style="font-size:2.0em">W</span></b>hen in the Course of human events it becomes necessary for one people to dissolve the political bands which have connected them with another and to assume among the powers of the earth, the separate and equal station to which the Laws of Nature and of Nature's God entitle them, a decent respect to the opinions of mankind requires that they should declare the causes which impel them to the separation.<br />
<br />
We hold these truths to be self-evident, that all men are created equal, that they are endowed by their Creator with certain unalienable Rights, that among these are Life, Liberty and the pursuit of Happiness. -- That to secure these rights, Governments are instituted among Men, deriving their just powers from the consent of the governed, -- That whenever any Form of Government becomes destructive of these ends, it is the Right of the People to alter or to abolish it, and to institute new Government, laying its foundation on such principles and organizing its powers in such form, as to them shall seem most likely to effect their Safety and Happiness. Prudence, indeed, will dictate that Governments long established should not be changed for light and transient causes; and accordingly all experience hath shewn that mankind are more disposed to suffer, while evils are sufferable than to right themselves by abolishing the forms to which they are accustomed. But when a long train of abuses and usurpations, pursuing invariably the same Object evinces a design to reduce them under absolute Despotism, it is their right, it is their duty, to throw off such Government, and to provide new Guards for their future security. -- Such has been the patient sufferance of these Colonies; and such is now the necessity which constrains them to alter their former Systems of Government. The history of the present King of Great Britain is a history of repeated injuries and usurpations, all having in direct object the establishment of an absolute Tyranny over these States. To prove this, let Facts be submitted to a candid world.<br />

<div class="bbcode_right" style="text-align:right">
<span style="color:green"><i>--- written by Thomas Jefferson</i></span>
</div>
</div>
</div>

<p>(The $<tt>input</tt> declaration was line-wrapped above so you could read it; the
original text doesn't have the line endings, which NBBC would see as good places
to stick in undesirable <tt>&lt;br&nbsp;/&gt;</tt> tags.)</p>

<p>Very nice, right?  But also very long --- way too long for a casual summary!  So
let's change the way we parse this text so that the program outputs something a
little more manageable --- let's say the first 520 text characters.  (And we'll skip
showing the declaration of $<tt>input</tt> again because it's so huge; you can just
assume it comes in full before the code you see here.  And why 520?  Because it's just
enough for the entire first paragraph but not enough for any of the second, making
it a perfect length for demonstration purposes.)  Here's our new length-limiting
program:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>$input = <<< EOI

    (...long text...)

EOI;

$bbcode = new BBCode;
$bbcode->SetLimit(520);
$output = $bbcode->Parse($input);</xmp>

<div class='output_header'>Output:</div>
<div class='output'>
<div class='bbcode' style='margin:1em;'>From <a href="http://www.ushistory.org/Declaration/document/index.htm" class="bbcode_url">ushistory.org</a>:<br />

<div class="bbcode_center" style="text-align:center">
<b><span style="font-size:1.5em">In CONGRESS, July 4, 1776</span><br />

The unanimous Declaration of the thirteen united States of America</b>
</div>
<br />
<b><span style="font-size:2.0em">W</span></b>hen in the Course of human events it becomes necessary for one people to dissolve the political bands which have connected them with another and to assume among the powers of the earth, the separate and equal station to which the Laws of Nature and of Nature's God entitle them, a decent respect to the opinions of mankind requires that they should declare the causes which impel them to the separation....</div>
</div>

<p>That's quite a bit shorter!  Notice that NBBC smartly cut the text after the word <i>separation</i> ---
not in the middle of another word --- and it made sure that all the opened tags got properly closed,
and it even helpfully added an ellipsis (<tt>...</tt>) at the end to show us that some of the text
was removed.</p>

<p>NBBC is smart about its limiting, too:  If the input text is only slightly longer than the limit,
then NBBC will "let it slide" and won't cut off the text.  For example, we're limiting here at 520
characters, and if NBBC saw input that was in the range of 520-600 characters, it would consider that
to not be worth cutting off.</p>

<p>You can control this "fuzziness factor" with the
<a href="api_behv.html#ref_parser_SetLimitPrecision">SetLimitPrecision()</a> function, and you can
control what gets added on the end (like the ellipsis) with the
<a href="api_behv.html#ref_parser_SetLimitTail">SetLimitTail()</a> function.  You can also determine
whether the text was cut off or not in the last parse job with the
<a href="api_behv.html#ref_parser_WasLimited">WasLimited()</a> function.</p>

<a name="plain"></a>
<h4>Plain HTML Output</h4>

<p>Sometimes, just shortening the text isn't good enough; sometimes you need an excerpt that
still looks similar to the original but that won't affect the formatting and layout of the
page it's inserted into.  NBBC understands that, and in v1.2, it offers a <i>Plain HTML</i>
mode to support that need.  In plain HTML mode, NBBC will <i>only</i> output four of the
simplest of simple HTML tags:  <tt>&lt;b&gt;</tt>, <tt>&lt;i&gt;</tt>, <tt>&lt;u&gt;</tt>, and <tt>&lt;a&gt;</tt>.
In plain HTML mode, all other tags will be omitted or converted to one of those four.</p>

<p>(Note:  In plain HTML mode, NBBC
will not output <tt>&lt;div&gt;</tt> or <tt>&lt;br&nbsp;/&gt;</tt> tags either, so if you want
the text to even span multiple lines, you'll have to use <a href="api_misc.html#ref_parser_nl2br">nl2br()</a>
to convert its newlines to <tt>&lt;br&nbsp;/&gt;</tt> tags; otherwise, you'll just get one
long paragraph of HTML output.)</p>

<p>Let's look at how our example paragraph looks in plain-HTML mode.  We'll add a line to
convert newlines to <tt>&lt;br&nbsp;/&gt;</tt> tags so that it looks vaguely similar to the
original:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>$input = <<< EOI

    (...long text...)

EOI;

$bbcode = new BBCode;
$bbcode->SetPlainMode(true);
$output = $bbcode->Parse($input);
$output = $bbcode->nl2br($output);</xmp>

<div class='output_header'>Output:</div>
<div class='output'>
From <a href="http://www.ushistory.org/Declaration/document/index.htm">ushistory.org</a>:<br />
<br />
<b>In CONGRESS, July 4, 1776<br />
The unanimous Declaration of the thirteen united States of America</b><br />
<br />
<b>W</b>hen in the Course of human events it becomes necessary for one people to dissolve the political bands which have connected them with another and to assume among the powers of the earth, the separate and equal station to which the Laws of Nature and of Nature's God entitle them, a decent respect to the opinions of mankind requires that they should declare the causes which impel them to the separation.<br />
<br />
We hold these truths to be self-evident, that all men are created equal, that they are endowed by their Creator with certain unalienable Rights, that among these are Life, Liberty and the pursuit of Happiness. -- That to secure these rights, Governments are instituted among Men, deriving their just powers from the consent of the governed, -- That whenever any Form of Government becomes destructive of these ends, it is the Right of the People to alter or to abolish it, and to institute new Government, laying its foundation on such principles and organizing its powers in such form, as to them shall seem most likely to effect their Safety and Happiness. Prudence, indeed, will dictate that Governments long established should not be changed for light and transient causes; and accordingly all experience hath shewn that mankind are more disposed to suffer, while evils are sufferable than to right themselves by abolishing the forms to which they are accustomed. But when a long train of abuses and usurpations, pursuing invariably the same Object evinces a design to reduce them under absolute Despotism, it is their right, it is their duty, to throw off such Government, and to provide new Guards for their future security. -- Such has been the patient sufferance of these Colonies; and such is now the necessity which constrains them to alter their former Systems of Government. The history of the present King of Great Britain is a history of repeated injuries and usurpations, all having in direct object the establishment of an absolute Tyranny over these States. To prove this, let Facts be submitted to a candid world.<br />
<br />

<i>--- written by Thomas Jefferson</i>
</div>

<a name="text"></a>
<h4>Plain Text Output</h4>

<p>But what if you need more?  For example, in RSS feeds, you're not supposed to include <i>any</i> HTML
tags (although many feeds do, and if you don't care if your do, then plain-HTML mode might work for you);
or maybe you're trying to generate plain text for an e-mail or a text-mode display.
NBBC's plain-HTML mode can be simplified even further:  Since it only ever outputs four well-known classic HTML
tags, they can readily and easily be stripped to produce plain-text output.  Below are two possible ways
to simplify your output even further.</p>

<p>In this example, we just strip the tags and convert entities like <tt>&amp;lt;</tt> to actual
characters like <tt>&lt;</tt>, yielding simple pre-formatted text:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>$input = <<< EOI

    (...long text...)

EOI;

$bbcode = new BBCode;
$bbcode->SetPlainMode(true);
$output = $bbcode->Parse($input);
$output = $bbcode->UnHTMLEncode(strip_tags($output));</xmp>

<div class='output_header'>Output:</div>
<div class='output' style='white-space:pre-wrap;'>From ushistory.org:

In CONGRESS, July 4, 1776
The unanimous Declaration of the thirteen united States of America

When in the Course of human events it becomes necessary for one people to dissolve the political bands which have connected them with another and to assume among the powers of the earth, the separate and equal station to which the Laws of Nature and of Nature's God entitle them, a decent respect to the opinions of mankind requires that they should declare the causes which impel them to the separation.

We hold these truths to be self-evident, that all men are created equal, that they are endowed by their Creator with certain unalienable Rights, that among these are Life, Liberty and the pursuit of Happiness. -- That to secure these rights, Governments are instituted among Men, deriving their just powers from the consent of the governed, -- That whenever any Form of Government becomes destructive of these ends, it is the Right of the People to alter or to abolish it, and to institute new Government, laying its foundation on such principles and organizing its powers in such form, as to them shall seem most likely to effect their Safety and Happiness. Prudence, indeed, will dictate that Governments long established should not be changed for light and transient causes; and accordingly all experience hath shewn that mankind are more disposed to suffer, while evils are sufferable than to right themselves by abolishing the forms to which they are accustomed. But when a long train of abuses and usurpations, pursuing invariably the same Object evinces a design to reduce them under absolute Despotism, it is their right, it is their duty, to throw off such Government, and to provide new Guards for their future security. -- Such has been the patient sufferance of these Colonies; and such is now the necessity which constrains them to alter their former Systems of Government. The history of the present King of Great Britain is a history of repeated injuries and usurpations, all having in direct object the establishment of an absolute Tyranny over these States. To prove this, let Facts be submitted to a candid world.

--- written by Thomas Jefferson</div>

<p>In this example, we not only strip the tags and un-convert entities, but we also perform word-wrap
so that the result looks acceptable on an 80-column display:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>$input = <<< EOI

    (...long text...)

EOI;

$bbcode = new BBCode;
$bbcode->SetPlainMode(true);
$output = $bbcode->Parse($input);
$output = wordwrap($bbcode->UnHTMLEncode(strip_tags($output)));</xmp>

<div class='output_header'>Output:</div>
<div class='output' style='white-space:pre;font:9pt Courier,mono,monospace;'>From ushistory.org:

In CONGRESS, July 4, 1776
The unanimous Declaration of the thirteen united States of America

When in the Course of human events it becomes necessary for one people to
dissolve the political bands which have connected them with another and to
assume among the powers of the earth, the separate and equal station to
which the Laws of Nature and of Nature's God entitle them, a decent respect
to the opinions of mankind requires that they should declare the causes
which impel them to the separation.

We hold these truths to be self-evident, that all men are created equal,
that they are endowed by their Creator with certain unalienable Rights,
that among these are Life, Liberty and the pursuit of Happiness. -- That to
secure these rights, Governments are instituted among Men, deriving their
just powers from the consent of the governed, -- That whenever any Form of
Government becomes destructive of these ends, it is the Right of the People
to alter or to abolish it, and to institute new Government, laying its
foundation on such principles and organizing its powers in such form, as to
them shall seem most likely to effect their Safety and Happiness. Prudence,
indeed, will dictate that Governments long established should not be
changed for light and transient causes; and accordingly all experience hath
shewn that mankind are more disposed to suffer, while evils are sufferable
than to right themselves by abolishing the forms to which they are
accustomed. But when a long train of abuses and usurpations, pursuing
invariably the same Object evinces a design to reduce them under absolute
Despotism, it is their right, it is their duty, to throw off such
Government, and to provide new Guards for their future security. -- Such
has been the patient sufferance of these Colonies; and such is now the
necessity which constrains them to alter their former Systems of
Government. The history of the present King of Great Britain is a history
of repeated injuries and usurpations, all having in direct object the
establishment of an absolute Tyranny over these States. To prove this, let
Facts be submitted to a candid world.

--- written by Thomas Jefferson</div>

<p>All of these different output forms can be generated from the same input source.  It's up to you
to decide what you need, but whatever you need, NBBC can probably do it.</p>

<a name="rss"></a>
<h4>Putting It All Together</h4>

<p>Okay, so let's say you need to generate an RSS feed from your BBCode-based blog.
(We'll generate an RSS 2.0 feed, and leave it up to you to work out how to generate
RSS 0.9 and RSS 1.0 and Atom feeds.)  We'll say your source data starts out as an
array of blog postings, probably inhaled from a database, like this:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>$postings = Array(

        ...older postings...

    372 => Array(
        'id' => 372,
        'title' => "I Saw a Cow Today",
        'date' => "2007-06-05 18:37:43",
        'author' => "Jenny",
        'descr' => "I took the scenic route to work today...",
    ),

    373 => Array(
        'id' => 373,
        'title' => "I Work with Idiots",
        'date' => "2007-06-08 18:03:17",
        'author' => "Jenny",
        'descr' => "I swear this was the day of idiots at work...",
    ),

	374 => Array(
        'id' => 374,
        'title' => "Sunday in the Park with Geoff",
        'date' => "2007-06-10 21:08:06",
        'author' => "Jenny",
        'descr' => "Geoff and I went out to the park in the city today...",
    ),

);</xmp>

<p>Sounds like Jenny has an interesting life.  Let's make an RSS feed out of it.
RSS is an XML data format, and it's structured somewhat like this:</p>

<div class='code_header'>Code:</div>
<xmp class='code'><?xml version="1.0"?>
<rss version="2.0">
    <channel>
        <title>Jenny's Musings</title>
        <link>http://jenny-musings.example.com/</link>
        <description>Thoughts and stuff from Jenny</description>
        <language>en-us</language>

        ...blog postings (items)...

    </channel>
</rss></xmp>

<p>In short, the header part of the feed contains the title of the blog, a link to
the blog, Each item within the RSS feed looks like this:</p>

<div class='code_header'>Code:</div>
<xmp class='code'><item>
    <title>...title of posting...</title>
    <author>...author of posting...</title>
    <link>http://jenny-musings.example.com/...</link>
    <description>...excerpt of blog posting...</description>
    <pubDate>...date of blog posting...</pubDate>
    <guid>...unique identifier (or link) for this posting...</guid>
</item></xmp>

<p>What we need, then, from our blog data is to extract the title, the date, the
author, a link to the posting, and a description (an excerpt of the posting itself).
RSS 2.0 allows HTML in the description, but only if it's been encoded or wrapped
in a CDATA section, and to make our feed friendly to other sites, we'll stick to
plain HTML, excluding tags like <tt>&lt;div&gt;</tt> that might cause problems with
RSS readers.  We also want to generate only the ten most recent postings, with
the newest posting listed first, rather than the whole blog.</p>

<p>So here's how we start.  First, we need a BBCode object to work with, set up
for plain mode and short excerpts, and we need to decide how many blog postings to
output:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>$postings = array_reverse($postings);     // Posts in reverse chronological order
$num_postings = count($postings);
if ($num_postings > 10) $num_postings = 10;  // At most 10 postings to output

$bbcode = new BBCode;
$bbcode->SetPlainMode(true);                 // Use plain mode...
$bbcode->SetLimit(200);                      // ...and limit to 200 characters.
</xmp>

<p>Now we generate each posting in RSS 2.0 format.  This is fairly straightforward, but
we'll leave out some of the details so you can see what's going on:</p>

<div class='code_header'>Code:</div>
<xmp class='code'>// Loop for each of the ten (or fewer) posts we're generating.
for ($i = 0; $i < $num_postings; $i++) {
    $posting = $postings[$i];

    // Generate the basic meta-information about this post.
    print "<item>\n";
    print "<title>" . htmlspecialchars($posting['title']) . "</title>\n";
    print "<author>" . htmlspecialchars($posting['author']) . "</author>\n";
	...convert $posting['date'] to RSS 2.0 format, and then...
    print "<date>" . htmlspecialchars($date) . "</date>\n";
    print "<link>http://jenny-musings.example.com/?post="
        . $posting['id'] . "</link>\n";

    // Now convert the post body from BBCode to a short, plain HTML excerpt.
    $descr = $bbcode->Parse($posting['descr']);   // Convert the descr. to plain HTML
    $descr = nl2br($descr);                       // We allow <br /> tags for breaks.

    // We output it with HTML entities because RSS 2.0 doesn't allow tags.
    print "<description>" . htmlspecialchars($descr) . "</description>\n";

    // End the item.
    print "</item>\n";
}</xmp>

<p>That's it, save for a few surrounding tags.  Let's look at the RSS-feed program
fully-assembled:</p>

<div class='code_header'>Code:</div>
<xmp class='code'><?php print "<?xml version=\"1.0\"?>\n"; ?>
<rss version="2.0">
    <channel>
        <title>Jenny's Musings</title>
        <link>http://jenny-musings.example.com/</link>
        <description>Thoughts and stuff from Jenny</description>
        <language>en-us</language>

<?php

require_once("nbbc.php");

//----------------------------------------------------------------------------------

// Normally, we'd load the postings from a database, but for this example,
// we'll just include them right here.
$postings = Array(

    372 => Array(
        'id' => 372,
        'title' => "I Saw a Cow Today",
        'date' => "2007-06-05 18:37:43",
        'author' => "Jenny",
        'descr' => "I took the scenic route to work today, and wouldn't you know it?
There was an Amish farmer walking along the side of the road with a cow on a leash.
I don't really know where he was going, but I was really surprised to see them, since
the Amish usually don't get this close to the suburbs.  It makes you wonder if times
have gotten hard for them too.  Food for thought, I think.",
    ),

    373 => Array(
        'id' => 373,
        'title' => "I Work with Idiots",
        'date' => "2007-06-08 18:03:17",
        'author' => "Jenny",
        'descr' => "I swear this was the day of idiots at work.  First thing, right
off the bat, Larry comes in talking on a cell phone, and I kid you not, he walks
right smack into the end of the open glass door.  We could hear the [i]*smack*[/i]
all throughout the entire office, and he had a red welt on his face for the rest of
the day.  Then my boss comes in ranting and raving at us about forgetting to e-mail
the Pinsky people, only to have Sam point out that he'd said in the yesterday's
meeting that he'd do it himself.  And then at lunch, Sam was laughing so hard at one
of Bill's jokes that she peed her pants (sorry, Sam!) and had to run out and buy a
new pair.  I don't even [i]wanna[/i] say all the stupid crap that happened in the
afternoon.  Does everybody have a workplace full of numbskulls like I do, or is this
just spring fever or something?  Maybe it's just because it's Friday...",
    ),

	374 => Array(
        'id' => 374,
        'title' => "Sunday in the Park with Geoff",
        'date' => "2007-06-10 21:08:06",
        'author' => "Jenny",
        'descr' => "Geoff and I went out to the park in the city today just for
something to do.  We bought these really awful burgers from a street vendor and some
bottles of Coke, and then sat on the park bench nibbling them (did I mention the
burgers were awful?) while we watched kids skateboarding around the benches.  There
was one kid who was really good.  I watched him do a good three flips in the air just
past the statue of John Adams, off that railing there, but it didn't last.  An old
lady who was trying to feed the pigeons got scared and left, and she came back about
ten minutes later with a police officer.  I really don't understand why the city
kicks the kids out of the park, since they're not hurting anything.  I wonder if I'll
be as cranky as she was when I get old?",
    ),

);

//----------------------------------------------------------------------------------

$postings = array_reverse($postings);        // Posts in reverse chronological order
$num_postings = count($postings);
if ($num_postings > 10) $num_postings = 10;  // At most 10 postings to output

$bbcode = new BBCode;
$bbcode->SetPlainMode(true);                 // Use plain mode...
$bbcode->SetLimit(200);                      // ...and limit to 200 characters.

// Loop for each of the ten (or fewer) posts we're generating.
for ($i = 0; $i < $num_postings; $i++) {
    $posting = $postings[$i];

    // Generate the basic meta-information about this post.
    print "<item>\n";
    print "<title>" . htmlspecialchars($posting['title']) . "</title>\n";
    print "<author>" . htmlspecialchars($posting['author']) . "</author>\n";
    print "<link>http://jenny-musings.example.com/?post="
        . $posting['id'] . "</link>\n";

    // Convert the date from "YYYY-MM-DD HH:MM:SS" format to a timestamp...
    $date = $posting['date'];
    $year = substr($date, 0, 4);
    $month = substr($date, 5, 2);
    $day = substr($date, 8, 2);
    $hour = substr($date, 11, 2);
    $minute = substr($date, 14, 2);
    $second = substr($date, 17, 2);
	$timestamp = mktime($hour, $minute, $second, $month, $day, $year, false);
	
	// ...and to RFC 2822 format, which is required by RSS 2.0.
    $rfc2822 = date("r", $timestamp);
    print "<date>" . htmlspecialchars($rfc2822) . "</date>\n";

    // Now convert the post body from BBCode to a short, plain HTML excerpt.
    $descr = $bbcode->Parse($posting['descr']);   // Convert the descr. to plain HTML
    $descr = nl2br($descr);                       // We allow <br /> tags for breaks.

    // We output it with HTML entities because RSS 2.0 doesn't allow tags.
    print "<description>" . htmlspecialchars($descr) . "</description>\n";

    // End the item.
    print "</item>\n\n";
}

//----------------------------------------------------------------------------------

?>
    </channel>
</rss></xmp>

<p>This program is available as an example in the <tt>examples/</tt> directory,
under the name <tt>rss_example.php</tt>.  This is what it outputs:</p>

<div class='output_header'>Output (raw feed):</div>
<div class='output'><xmp>
<?xml version="1.0"?>
<rss version="2.0">
    <channel>
        <title>Jenny's Musings</title>
        <link>http://jenny-musings.example.com/</link>
        <description>Thoughts and stuff from Jenny</description>
        <language>en-us</language>

<item>
<title>Sunday in the Park with Geoff</title>
<author>Jenny</author>
<date>Sun, 10 Jun 2007 22:08:06 -0500</date>
<description>Geoff and I went out to the park in the city today just for something
to do. We bought these really awful burgers from a street vendor and some bottles of
Coke, and then sat on the park bench...</description>
</item>

<item>
<title>I Work with Idiots</title>
<author>Jenny</author>
<date>Fri, 08 Jun 2007 19:03:17 -0500</date>
<description>I swear this was the day of idiots at work. First thing, right off the
bat, Larry comes in talking on a cell phone, and I kid you not, he walks right smack
into the end of the open glass door. We...</description>
</item>

<item>
<title>I Saw a Cow Today</title>
<author>Jenny</author>
<date>Tue, 05 Jun 2007 19:37:43 -0500</date>
<description>I took the scenic route to work today, and wouldn't you know it? There
was an Amish farmer walking along the side of the road with a cow on a leash. I don't
really know where he was going, but I was...</description>
</item>

    </channel>
</rss>
</xmp></div>

<div class='output_header'>Output (from an RSS reader):</div>
<div class='output'>
<h3 style='border-bottom:1px solid #99C;margin:0;color:#000;text-decoration:none;'><a href="http://jenny-musings.example.com/">Jenny's Musings</a></h3>
<p style='margin:0.5em 0 1em 0;'>Thoughts and stuff from Jenny</p>

<h4 style='color:#000;text-decoration:none;margin:0;'><a href="http://jenny-musings.example.com/post=374">Sunday in the Park with Geoff</a> &nbsp; <span style='font-weight:normal;font-size:10pt;font-style:italic;'>Sunday, June 10, 2007</span></h4>
<p style='margin:0.5em 0 1em 2em;'>Geoff and I went out to the park in the city today just for something
to do. We bought these really awful burgers from a street vendor and some bottles of
Coke, and then sat on the park bench...</p>

<h4 style='color:#000;text-decoration:none;margin:0;'><a href="http://jenny-musings.example.com/post=373">I Work with Idiots</a> &nbsp; <span style='font-weight:normal;font-size:10pt;font-style:italic;'>Friday, June 8, 2007</span></h4>
<p style='margin:0.5em 0 1em 2em;'>I swear this was the day of idiots at work. First thing, right off the
bat, Larry comes in talking on a cell phone, and I kid you not, he walks right smack
into the end of the open glass door. We...</p>

<h4 style='color:#000;text-decoration:none;margin:0;'><a href="http://jenny-musings.example.com/post=372">I Saw a Cow Today</a> &nbsp; <span style='font-weight:normal;font-size:10pt;font-style:italic;'>Tuesday, June 5, 2007</span></h4>
<p style='margin:0.5em 0 1em 2em;'>I took the scenic route to work today, and wouldn't you know it? There
was an Amish farmer walking along the side of the road with a cow on a leash. I don't
really know where he was going, but I was...</p>
</div>

<p>That's a nice, clean, simple RSS feed, just the way nature intended.  There are plenty
of other ways you can format your feeds, or use these same techniques for sampling any
long chunk of BBCode text, but this demonstration covers the basics nicely.</p>
