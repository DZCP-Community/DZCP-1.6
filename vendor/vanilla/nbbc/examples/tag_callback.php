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

	function MyBorderFunction($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK) {
			if (isset($params['color'])
				&& !preg_match('/^#[0-9a-fA-F]+|[a-zA-Z]+$/', $params['color']))
				return false;
			if (isset($params['size'])
				&& !preg_match('/^[1-9][0-9]*$/', $params['size']))
				return false;
			return true;
		}

		$color = isset($params['color']) ? $params['color'] : "blue";
		$size = isset($params['size']) ? $params['size'] : 1;
		return "<div style=\"border: {$size}px solid $color\">$content</div>";
	}

	$bbcode->AddRule('border', Array(
		'mode' => BBCODE_MODE_CALLBACK,
		'method' => 'MyBorderFunction',
		'class' => 'block',
		'allow_in' => Array('listitem', 'block', 'columns'),
	));

	$output = $bbcode->Parse($input);

	print "<div class='bbcode'>$output</div>";

?>
</body>
</html>
