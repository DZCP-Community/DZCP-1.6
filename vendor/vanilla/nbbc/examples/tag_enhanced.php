<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Enhanced Tag Example</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php

	require_once("../nbbc.php");
	
	$input = "[border color=red size=3]This text is in a medium red border![/border]\n"
		. "[border size=10]This text is in a fat blue border![/border]\n"
		. "[border color=green]This text is in a normal green border![/border]\n";
	
	$bbcode = new BBCode;

	$bbcode->AddRule('border',  Array(
		'mode' => BBCODE_MODE_ENHANCED,
		'template' => '<div style="border: {$size}px solid {$color}">{$_content}</div>',
		'allow' => Array(
			'color' => '/^#[0-9a-fA-F]+|[a-zA-Z]+$/',
			'size' => '/^[1-9][0-9]*$/',
		),
		'default' => Array(
			'color' => 'blue',
			'size' => '1',
		),
		'class' => 'block',
		'allow_in' => Array('listitem', 'block', 'columns'),
	));

	$output = $bbcode->Parse($input);

	print "<div class='bbcode'>$output</div>";

?>
</body>
</html>
