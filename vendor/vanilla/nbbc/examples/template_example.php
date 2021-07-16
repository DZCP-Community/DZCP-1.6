<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Template Example</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php

	require_once("../nbbc.php");
	
	$bbcode = new BBCode;

	// This is our example template.  Variables in {curly braces} will be filled in from
	// the given parameter array.
	$template = "This is {\$testword/h} of how to use {\$progname/h}'s {\$ability/h} functionality.";

	// Generate the output HTML with these parameters.
	$params = Array(
		'testword' => "an example",
		'progname' => "NBBC",
		'ability' => "template",
	);
	$output = $bbcode->FillTemplate($template, $params);

	print $output . "<br />\n<br />\n";

	// Okay, now just for fun, generate a "mad-libs" version.
	$params = Array(
		'testword' => "a test",
		'progname' => "poop",
		'ability' => "stink",
	);
	$output = $bbcode->FillTemplate($template, $params);

	print $output . "<br />\n<br />\n";

?>
</body>
</html>