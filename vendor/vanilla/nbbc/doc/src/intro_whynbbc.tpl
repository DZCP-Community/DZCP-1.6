
<p>So if you're convinced that BBCode is right for your application, you still may
be wondering why you should use NBBC instead of another BBCode library, or instead
of just rolling your own solution.  We believe NBBC is better:</p>

<ul>
<li><b>NBBC is correct and compliant.</b>  Unlike many other libraries (and definitely
	unlike many roll-your-own solutions), NBBC's output is compatible with the
	restrictions of XHTML 1.0 Strict:  The output HTML is correctly-structured no
	matter how badly-mangled the user's input is, so even the ugliest damaged input
	can still pass an XHTML-validation test.</li>
<li><b>NBBC is fast.</b>  Built on a solid foundation of compiler technologies,
	NBBC can compete with the best BBCode-parsing solutions that you can implement,
	and can sometimes even outperform a simple <tt>str_replace()</tt> or
	<tt>preg_replace()</tt>-based solution!</li>
<li><b>NBBC is lightweight.</b>  You can easily implement NBBC in any environment by including
	a single PHP source file.  Drop it in and away you go!</li>
<li><b>NBBC is easy to use.</b>  Adding basic support for BBCode in your application
	can be done in only three lines of code.  Why limit your users to plain text
	or implement complicated HTML validation when you can add sophisticated formatting
	in three lines of code?</li>
<li><b>NBBC implements most common BBCode tags.</b>  Right out of the box, you have
	support for everything from common tags like <tt>[b]</tt> and <tt>[i]</tt> to
	sophisticated tags like <tt>[code]</tt> and <tt>[quote]</tt> and even <tt>[columns]</tt>.
	For most needs, you won't need to add or change a thing.</li>
<li><b>NBBC supports smileys!</b>  Not all BBCode parsers support smileys (emoticons)
	directly, and fewer still include 30 of the most commonly-used ones right out of the
	box!  You can always add your own custom smileys, but for a lot of purposes, NBBC's
	built-in smileys will be all you'll ever need.</li>
<li><b>NBBC is extensible.</b>  If NBBC doesn't have the tags you want or need for
	your environment, they're incredibly easy to add with its sophisticated
	API.  Many tags can be added in only a few lines of code!</li>
<li><b>NBBC is well-documented.</b>  Not only is the source-code well-commented
	and designed to be easy to read, there are over 50 pages of documentation
	describing how to use NBBC and its features.</li>
<li><b>NBBC is tested.</b>  NBBC has a large regression-test suite that is designed
	to ensure its correctness:  While most other BBCode solutions are content to
	just swap a tag for a tag, NBBC is designed with predictability, security, and
	stability in mind, so you can ensure that its output is safe and correct no
	matter what input your users provide.</li>
</ul>

<p>Fast, powerful, clean, flexible, and lightweight:  NBBC is designed to be the
perfect BBCode-processing library.</p>
