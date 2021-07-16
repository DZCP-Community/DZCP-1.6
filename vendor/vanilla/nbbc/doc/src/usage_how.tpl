
<p>While the example in the <a href="usage_basic.html">previous section</a>
is simple, it's not very useful.  Generally, most programs using NBBC
will be structured like this:</p>

<div class='code_header'>Code:</div>
<xmp class='code'><?php
    ...other includes...
    require_once("nbbc.php");
	
    ...
	
    $bbcode = new BBCode;
    ...possible additional optional setup for the $bbcode object...
	
    ...
	
    $string = [some BBCode from somewhere, usually either a file or a database]
    $output = $bbcode->Parse($string);
	
    ...
	
    print $output;
?></xmp>

<p>The setup phase, where you create the new <tt>BBCode</tt> object and maybe alter its
functionality a little (say, to add more smileys and BBCode rules, or remove existing ones),
only needs to be done once in your script:  The <tt>BBCode</tt> object, once it's been
set up, can be reused again and again to convert different pieces of BBCode text in the
same script.</p>
