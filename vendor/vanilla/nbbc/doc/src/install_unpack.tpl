
<p>NBBC should have been provided to you as a <tt>.tar.gz</tt> file or as a <tt>.zip</tt>
archive file.  Unpack this archive file.  Inside, you should find this:</p>

<ul>
<li><tt>nbbc/CHANGELOG</tt> &nbsp; &nbsp; &nbsp; (a list of everything new in this version of NBBC)</li>
<li><tt>nbbc/doc/*.*</tt> &nbsp; &nbsp; &nbsp; (various pieces of this user's manual)</li>
<li><tt>nbbc/nbbc.php</tt> &nbsp; &nbsp; &nbsp; (the compressed single-file version of NBBC)</li>
<li><tt>nbbc/readme.html</tt> &nbsp; &nbsp; &nbsp; (the main file for this user's manual)</li>
<li><tt>nbbc/smileys/*.gif</tt> &nbsp; &nbsp; &nbsp; (thirty standard smileys, as GIF image files)</li>
<li><tt>nbbc/src/nbbc_main.php</tt> &nbsp; &nbsp; &nbsp; (the main root include, with comments)</li>
<li><tt>nbbc/src/nbbc_lex.php</tt> &nbsp; &nbsp; &nbsp; (the lexical analyzer, with comments)</li>
<li><tt>nbbc/src/nbbc_lib.php</tt> &nbsp; &nbsp; &nbsp; (the standard BBCode library, with comments)</li>
<li><tt>nbbc/src/nbbc_parse.php</tt> &nbsp; &nbsp; &nbsp; (the core parser, with comments)</li>
<li><tt>nbbc/tools/collect_smileys.php</tt> &nbsp; &nbsp; &nbsp; (used for creating an HTML <a href="app_smiley.html">table of smileys</a>)</li>
<li><tt>nbbc/tools/Makefile</tt> &nbsp; &nbsp; &nbsp; (used for creating <tt>nbbc.php</tt> from the other files)</li>
<li><tt>nbbc/tools/merge.pl</tt> &nbsp; &nbsp; &nbsp; (used for creating <tt>nbbc.php</tt> from the other files)</li>
<li><tt>nbbc/tools/test_nbbc.php</tt> &nbsp; &nbsp; &nbsp; (a test program to ensure NBBC works correctly)</li>
</ul>

<p>To see if NBBC has been installed correctly, open <tt>tools/test_nbbc.php</tt> in your web browser.
This script will test all of the major features of NBBC, and it will perform several
security tests as well.  If it reports that all of the tests succeeded, then NBBC has been
installed correctly.</p>

<p>Including NBBC in your project is fairly easy, and there's two possible ways to do it:
<ul>
<li>You can use the compressed version of NBBC, which loads a little faster and is packed
	into one PHP file.  To do this, copy just "<tt>nbbc.php</tt>" into your project's directory,
	and in your PHP script, you simply <tt>require_once("nbbc.php")</tt>.</li>
<li>You can use the uncompressed version of NBBC, which loads a little slower and is multiple
	PHP files, but is well-commented and supports a debugging mode.  To do this, copy all the
	"<tt>nbbc_*.php</tt>" files from the "<tt>src/</tt>" directory into your project's directory,
	and in your PHP script, you simply <tt>require_once("nbbc_main.php")</tt>.</li>
</ul>

<p>In addition to copying the PHP file(s), you probably also want to copy the "<tt>smileys/</tt>"
directory, which contains thirty smileys supported by the built-in Standard BBCode Library.
You can find a complete list of these smileys in <a href="app_smiley.html">Appendix A</a>.</p>
