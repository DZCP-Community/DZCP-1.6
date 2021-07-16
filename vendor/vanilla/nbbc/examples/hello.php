<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Hello Example</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php

	require_once("../nbbc.php");
	
	$input = "[b][i]Hello, World![/i][/b]";
	
	$bbcode = new BBCode;
	$output = $bbcode->Parse($input);

	print "<div class='bbcode'>$output</div>";

?>
</body>
</html>
