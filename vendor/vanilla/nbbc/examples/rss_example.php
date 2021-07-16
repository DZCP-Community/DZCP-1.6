<?php print "<?xml version=\"1.0\"?>\n"; ?>
<rss version="2.0">
    <channel>
        <title>Jenny's Musings</title>
        <link>http://jenny-musings.example.com/</link>
        <description>Thoughts and stuff from Jenny</description>
        <language>en-us</language>

<?php

require_once("../nbbc.php");

//----------------------------------------------------------------------------------

// Normally, we'd load the postings from a database, but for this example,
// we'll just include them right here.
$postings = Array(

    372 => Array(
        'id' => 372,
        'title' => "I Saw a Cow Today",
        'date' => "2007-06-05 18:37:43",
        'author' => "Jenny",
        'descr' => "I took the scenic route to work today, and wouldn't you know it?  There was an Amish farmer walking along the side of the road with a cow on a leash.  I don't really know where he was going, but I was really surprised to see them, since the Amish usually don't get this close to the suburbs.  It makes you wonder if times have gotten hard for them too.  Food for thought, I think.",
    ),

    373 => Array(
        'id' => 373,
        'title' => "I Work with Idiots",
        'date' => "2007-06-08 18:03:17",
        'author' => "Jenny",
        'descr' => "I swear this was the day of idiots at work.  First thing, right off the bat, Larry comes in talking on a cell phone, and I kid you not, he walks right smack into the end of the open glass door.  We could hear the [i]*smack*[/i] all throughout the entire office, and he had a red welt on his face for the rest of the day.  Then my boss comes in ranting and raving at us about forgetting to e-mail the Pinsky people, only to have Sam point out that he'd said in the yesterday's meeting that he'd do it himself.  And then at lunch, Sam was laughing so hard at one of Bill's jokes that she peed her pants (sorry, Sam!) and had to run out and buy a new pair.  I don't even [i]wanna[/i] say all the stupid crap that happened in the afternoon.  Does everybody have a workplace full of numbskulls like I do, or is this just spring fever or something?  Maybe it's just because it's Friday...",
    ),

	374 => Array(
        'id' => 374,
        'title' => "Sunday in the Park with Geoff",
        'date' => "2007-06-10 21:08:06",
        'author' => "Jenny",
        'descr' => "Geoff and I went out to the park in the city today just for something to do.  We bought these really awful burgers from a street vendor and some bottles of Coke, and then sat on the park bench nibbling them (did I mention the burgers were awful?) while we watched kids skateboarding around the benches.  There was one kid who was really good.  I watched him do a good three flips in the air just past the statue of John Adams, off that railing there, but it didn't last.  An old lady who was trying to feed the pigeons got scared and left, and she came back about ten minutes later with a police officer.  I really don't understand why the city kicks the kids out of the park, since they're not hurting anything.  I wonder if I'll be as cranky as she was when I get old?",
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
</rss>