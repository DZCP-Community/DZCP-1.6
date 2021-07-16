<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

<title>Full BBCode Example</title>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<style><!--

/* Basic page-display stuff */
body { font: 10pt Arial,Helv,Helvetica; }
td, th { font: 10pt Arial,Helv,Helvetica; }
th { font-weight: bold; }
div.box { margin:1.5em 4em; border:1px solid #9AC;
	background-color:#E0E8FF; padding:0.5em 1em; }
h1 { font: bold 16pt Arial,Helv,Helvetica; text-align:center; }

/* Add a dashed underline to acronyms. */
span.acronym { border-bottom:1px dashed green; }
span.acronym:hover { color: green; border-bottom:1px dashed lightgreen; }

/* Make spoilers invisible, so that you need to select them with the mouse. */
span.spoiler { background-color: black; color: black; }

/* Align columns to the top, and add some space between them. */
table.bbcode_columns { border-collapse: collapse;
    margin-top: 1em; margin-bottom: 1em; }
table.bbcode_columns td.bbcode_column { padding: 0 1em; vertical-align: top;}
table.bbcode_columns td.bbcode_firstcolumn { border-left: 0; padding-left: 0; }

/* Wrap quotes in a big blue box. */
div.bbcode_quote { border: 1px solid blue; margin: 0.5em 0; }
div.bbcode_quote_head { background-color: blue; color: white;
    font-weight: bold; padding: 0.25em 0.5em; }
div.bbcode_quote_head a:link { color: yellow; }
div.bbcode_quote_head a:visited { color: yellow; }
div.bbcode_quote_head a:hover { color: white; text-decoration: underline; }
div.bbcode_quote_head a:active { color: white; text-decoration: underline; }
div.bbcode_quote_body { background-color: skyblue;
    color: black; padding: 0.5em 1em; }

/* Wrap code in a big blue box. */
div.bbcode_code { border: 1px solid blue; margin: 0.5em 0; }
div.bbcode_code_head { background-color: blue; color: white;
    font-weight: bold; padding: 0.25em 0.5em; }
div.bbcode_code_body { background-color: skyblue; color: black;
    font: 10pt monospace; padding: 0.5em 1em; }

--></style>

</head>

<body>

<?php
	require_once("../nbbc.php");
?>

<h1>Full BBCode Example (NBBC v<?php print BBCODE_VERSION; ?>)</h1>

<div class='box'>

<p style='margin:0.5em 0'>In the box below, type some BBCode to have it converted to HTML:</p>

<form action='full_example.php' method='post'>

<table style='border-collapse:collapse;'><tbody><tr><td style='padding-bottom:0.5em;'>

<textarea name='bbcode_input' rows='10' cols='60' wrap='soft'>
<?php

	if (!isset($_POST['bbcode_input']))
		$input = "Type your message here.";
	else $input = @$_POST['bbcode_input'];
	
	print htmlspecialchars($input);

?>
</textarea><br />

<br />
<input type='submit' value='Show HTML Output' />

</td><td style='padding-left:1em;vertical-align:top;'>

<?php

	function GetCheckboxValue($setting, $default) {
		if (isset($_REQUEST[$setting]))
			return (strtolower($_REQUEST[$setting]) == 'on');
		else return $default;
	}

	$autourl_mode = GetCheckboxValue('autourl', true);
	$enable_smileys = GetCheckboxValue('smileys', true);
	$plain_mode = GetCheckboxValue('plain', false);
	$allow_ampersand = GetCheckboxValue('ampersand', false);
	$tag_marker = GetCheckboxValue('anglebrackets', false);
	$tag_marker = $tag_marker ? '<' : '[';
?>

<input type='checkbox' <?php if ($enable_smileys) print "checked='checked'" ?> name='smileys' /> Enable Smileys<br />
<input type='checkbox' <?php if ($autourl_mode) print "checked='checked'" ?> name='autourl' /> Auto-Detect URLs<br />
<input type='checkbox' <?php if ($plain_mode) print "checked='checked'" ?> name='plain' /> Use Plain-HTML Mode<br />
<input type='checkbox' <?php if ($allow_ampersand) print "checked='checked'" ?> name='ampersand' /> Allow Ampersands<br />
<input type='checkbox' <?php if ($tag_marker == '<') print "checked='checked'" ?> name='anglebrackets' /> Use &lt;&gt; instead of [] for tags<br />

</td></tr></tbody></table>

</form>

</div>

<?php

	if (isset($_POST['bbcode_input'])) {

		$bbcode = new BBCode;

		$bbcode->SetSmileyURL("../smileys");
		$bbcode->SetSmileyDir("../smileys");

		$bbcode->SetTagMarker($tag_marker);
		$bbcode->SetAllowAmpersand($allow_ampersand);
		$bbcode->SetEnableSmileys($enable_smileys);
		$bbcode->SetDetectURLs($autourl_mode);
		$bbcode->SetPlainMode($plain_mode);

		$output = $bbcode->Parse($input);
	
		print "<div class='box'>\n"
			. "<div class='bbcode'>$output</div>\n"
			. "</div>\n";
	}

?>

</body>
</html>
