<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Simple Tag Example</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php

	require_once("../nbbc.php");
	
	$input = "This text is [mono]monospaced![/mono]";
	
	$bbcode = new BBCode;

	$new_rule = Array(
		'simple_start' => '<tt>',
		'simple_end' => '</tt>',
		'class' => 'inline',
		'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
	);
	$bbcode->AddRule('mono', $new_rule); 

	$output = $bbcode->Parse($input);

	print "<div class='bbcode'>$output</div>";

?>
</body>
</html>
