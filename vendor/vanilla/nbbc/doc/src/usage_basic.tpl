
<p>NBBC is designed to be very easy to use, even for the most meager of PHP programmers.  Let's
try creating your first program that parses BBCode.  Create a new file in your project directory,
named "<tt>hello.php</tt>", and use your favorite text editor to put this very simple PHP
program in it:

<div class='code_header'>Code:</div>
<xmp class='code'><?php
    require_once("nbbc.php");
    $bbcode = new BBCode;
    print $bbcode->Parse("[i]Hello, World![/i]  This is the magic of [b]BBCode[/b]!");
?></xmp>

<div class='output_header'>Output:</div>
<div class='output'>
<i>Hello, World!</i>  This is the magic of <b>BBCode</b>!
</div>

<p>Let's look at what this does.  First, the <tt>require_once</tt> directive adds NBBC to your
program.  Next, <tt>new BBCode</tt> creates a new BBCode parser object and sets it up to be
ready for performing parsing.  The new <a href="api.html"><tt>BBCode</tt></a> object is then stored in the variable
<tt>$bbcode</tt>.  Finally, the big one...  we ask the <tt>BBCode</tt> object to convert
some BBCode text to HTML, and we print the result.</p>
