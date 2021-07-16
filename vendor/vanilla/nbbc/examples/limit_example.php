<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Length-limiting and plain-text demo</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<h1 style='color:#090;text-align:center;'>Length-limiting and plain-text demo</h1>
<hr />

<?php

	require_once("../nbbc.php");
	
	//-----------------------------------------------------------------------
	//  This is the original BBCode input.

	$input = <<< EOI
From [url=http://www.ushistory.org/Declaration/document/index.htm]ushistory.org[/url]:

[center][b][size=5]In CONGRESS, July 4, 1776[/size]
The unanimous Declaration of the thirteen united States of America[/b][/center]

[b][size=6]W[/size][/b]hen in the Course of human events it becomes necessary for one people to dissolve the political bands which have connected them with another and to assume among the powers of the earth, the separate and equal station to which the Laws of Nature and of Nature's God entitle them, a decent respect to the opinions of mankind requires that they should declare the causes which impel them to the separation.

We hold these truths to be self-evident, that all men are created equal, that they are endowed by their Creator with certain unalienable Rights, that among these are Life, Liberty and the pursuit of Happiness. -- That to secure these rights, Governments are instituted among Men, deriving their just powers from the consent of the governed, -- That whenever any Form of Government becomes destructive of these ends, it is the Right of the People to alter or to abolish it, and to institute new Government, laying its foundation on such principles and organizing its powers in such form, as to them shall seem most likely to effect their Safety and Happiness. Prudence, indeed, will dictate that Governments long established should not be changed for light and transient causes; and accordingly all experience hath shewn that mankind are more disposed to suffer, while evils are sufferable than to right themselves by abolishing the forms to which they are accustomed. But when a long train of abuses and usurpations, pursuing invariably the same Object evinces a design to reduce them under absolute Despotism, it is their right, it is their duty, to throw off such Government, and to provide new Guards for their future security. -- Such has been the patient sufferance of these Colonies; and such is now the necessity which constrains them to alter their former Systems of Government. The history of the present King of Great Britain is a history of repeated injuries and usurpations, all having in direct object the establishment of an absolute Tyranny over these States. To prove this, let Facts be submitted to a candid world.

[right][color=green][i]--- written by Thomas Jefferson[/i][/color][/right]
EOI;

	//-----------------------------------------------------------------------
	//  Display the original input.

	print "<h3 style='color:#090;'>Original BBCode:</h3>\n";

	print "<div style='margin:1em;font:10pt Courier,mono,monospace;'>" . nl2br(htmlspecialchars($input)) . "</div>\n";

	print "<hr />\n";

	//-----------------------------------------------------------------------
	//  Display the normal HTML output that NBBC generates for that.

	print "<h3 style='color:#090;'>Normal HTML output:</h3>\n";

	$bbcode = new BBCode;
	$output = $bbcode->Parse($input);

	print "<div class='bbcode' style='margin:1em;'>$output</div>\n";

	print "<hr />\n";

	//-----------------------------------------------------------------------
	//  Show it truncated to 520 text characters or less, retaining
	//  tags and formatting:

	print "<h3 style='color:#090;'>Limited to 520 characters or less:</h3>\n";

	$bbcode = new BBCode;
	$bbcode->SetLimit(520);
	$output = $bbcode->Parse($input);

	print "<div class='bbcode' style='margin:1em;'>$output</div>\n";

	print "<hr />\n";

	//-----------------------------------------------------------------------
	//  Show it as "plain HTML", that is, with only <b>, <i>, <u>, and <a>.

	print "<h3 style='color:#090;'>As \"plain HTML\" (&lt;b&gt;, &lt;i&gt;, &lt;u&gt;, and &lt;a&gt; tags only):</h3>\n";

	$bbcode = new BBCode;
	$bbcode->SetPlainMode(true);
	$output = $bbcode->Parse($input);
	$output = $bbcode->nl2br($output);

	print "<div class='bbcode' style='margin:1em;'>$output</div>\n";

	print "<hr />\n";

	//-----------------------------------------------------------------------
	//  Now the same thing, with 520 characters or less.

	print "<h3 style='color:#090;'>As length-limited \"plain HTML\" (&lt;b&gt;, &lt;i&gt;, &lt;u&gt;, and &lt;a&gt; tags only):</h3>\n";

	$bbcode = new BBCode;
	$bbcode->SetPlainMode(true);
	$bbcode->SetLimit(520);
	$output = $bbcode->Parse($input);
	$output = $bbcode->nl2br($output);

	print "<div class='bbcode' style='margin:1em;'>$output</div>\n";

	print "<hr />\n";

	//-----------------------------------------------------------------------
	//  Okay, this time, do it as pure plain text:  No HTML at all.  We
	//  use plain-HTML mode and a couple of preg_replace calls to clean
	//  up the result.

	print "<h3 style='color:#090;'>As plain text:</h3>\n";

	$bbcode = new BBCode;
	$bbcode->SetPlainMode(true);
	$output = $bbcode->Parse($input);
	$output = preg_replace("/<\\/?[buia][^>]*>/", "", $output);
	$output = wordwrap($bbcode->UnHTMLEncode(strip_tags($output)));

	print "<div class='bbcode' style='margin:1em;font:10pt Courier,mono,monospace;white-space:pre;'>$output</div>\n";

	print "<hr />\n";

	//-----------------------------------------------------------------------
	//  Same thing, with 520 characters or less.

	print "<h3 style='color:#090;'>As length-limited plain text:</h3>\n";

	$bbcode = new BBCode;
	$bbcode->SetPlainMode(true);
	$bbcode->SetLimit(520);
	$output = $bbcode->Parse($input);
	$output = preg_replace("/<\\/?[buia][^>]*>/", "", $output);
	$output = wordwrap($bbcode->UnHTMLEncode(strip_tags($output)));

	print "<div class='bbcode' style='margin:1em;font:10pt Courier,mono,monospace;white-space:pre;'>$output</div>\n";

?>

</body>
</html>
