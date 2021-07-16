<p>Section IV of the manual completely documents the public methods of the <tt>BBCode</tt> class,
which are listed below in conceptual groupings and in alphabetical order.  All member
variables of the <tt>BBCode</tt> class are private and should not be touched; likewise,
any method of the <tt>BBCode</tt> class with "<tt>Internal_</tt>" in its name should
not be touched either.  The <tt>BBCodeLexer</tt> and <tt>BBCodeLibrary</tt> classes
are used internally by the parser and contain no public interfaces, so they are not
documented here.</p>

<table style='border-collapse:collapse;'><tbody><tr><td style='padding-right:3em;'>
<ul>
<li><b><a href="api_core.html" style="color:#000;">Core Parsing Functions:</a></b>
	<ul>
	<li><a href="api_core.html#ref_parser_BBCode">BBCode::BBCode</a></li>
	<li><a href="api_core.html#ref_parser_Parse">BBCode::Parse</a></li>
	<li><a href="api_core.html#ref_parser_DoTag">BBCode::DoTag</a></li>
	<li><a href="api_core.html#ref_parser_SetDebug">BBCode::SetDebug</a></li>
	<li><a href="api_core.html#ref_parser_GetDebug">BBCode::GetDebug</a></li>
	</ul>
</li>
<li><b><a href="api_behv.html" style="color:#000;">Language-Behavior Functions:</a></b>
	<ul>
	<li><a href="api_behv.html#ref_parser_SetTagMarker">BBCode::SetTagMarker</a></li>
	<li><a href="api_behv.html#ref_parser_GetTagMarker">BBCode::GetTagMarker</a></li>
	<li><a href="api_behv.html#ref_parser_SetAllowAmpersand">BBCode::SetAllowAmpersand</a></li>
	<li><a href="api_behv.html#ref_parser_GetAllowAmpersand">BBCode::GetAllowAmpersand</a></li>
	<li><a href="api_behv.html#ref_parser_SetIgnoreNewlines">BBCode::SetIgnoreNewlines</a></li>
	<li><a href="api_behv.html#ref_parser_GetIgnoreNewlines">BBCode::GetIgnoreNewlines</a></li>
	<li><a href="api_behv.html#ref_parser_SetPlainMode">BBCode::SetPlainMode</a></li>
	<li><a href="api_behv.html#ref_parser_GetPlainMode">BBCode::GetPlainMode</a></li>
	<li><a href="api_behv.html#ref_parser_SetLimit">BBCode::SetLimit</a></li>
	<li><a href="api_behv.html#ref_parser_GetLimit">BBCode::GetLimit</a></li>
	<li><a href="api_behv.html#ref_parser_SetLimitPrecision">BBCode::SetLimitPrecision</a></li>
	<li><a href="api_behv.html#ref_parser_GetLimitPrecision">BBCode::GetLimitPrecision</a></li>
	<li><a href="api_behv.html#ref_parser_SetLimitTail">BBCode::SetLimitTail</a></li>
	<li><a href="api_behv.html#ref_parser_GetLimitTail">BBCode::GetLimitTail</a></li>
	<li><a href="api_behv.html#ref_parser_WasLimited">BBCode::WasLimited</a></li>
	</ul>
</li>
<li><b><a href="api_trim.html" style="color:#000;">Content-Trimming Functions:</a></b>
	<ul>
	<li><a href="api_trim.html#ref_parser_SetPreTrim">BBCode::SetPreTrim</a></li>
	<li><a href="api_trim.html#ref_parser_GetPreTrim">BBCode::GetPreTrim</a></li>
	<li><a href="api_trim.html#ref_parser_SetPostTrim">BBCode::SetPostTrim</a></li>
	<li><a href="api_trim.html#ref_parser_GetPostTrim">BBCode::GetPostTrim</a></li>
	</ul>
</li>
<li><b><a href="api_root.html" style="color:#000;">Root-Class Functions:</a></b>
	<ul>
	<li><a href="api_root.html#ref_parser_SetRootInline">BBCode::SetRootInline</a></li>
	<li><a href="api_root.html#ref_parser_SetRootBlock">BBCode::SetRootBlock</a></li>
	<li><a href="api_root.html#ref_parser_SetRoot">BBCode::SetRoot</a></li>
	<li><a href="api_root.html#ref_parser_GetRoot">BBCode::GetRoot</a></li>
	</ul>
</li>
<li><b><a href="api_rule.html" style="color:#000;">Rule Functions:</a></b>
	<ul>
	<li><a href="api_rule.html#ref_parser_AddRule">BBCode::AddRule</a></li>
	<li><a href="api_rule.html#ref_parser_RemoveRule">BBCode::RemoveRule</a></li>
	<li><a href="api_rule.html#ref_parser_GetRule">BBCode::GetRule</a></li>
	<li><a href="api_rule.html#ref_parser_ClearRules">BBCode::ClearRules</a></li>
	<li><a href="api_rule.html#ref_parser_GetDefaultRule">BBCode::GetDefaultRule</a></li>
	<li><a href="api_rule.html#ref_parser_SetDefaultRule">BBCode::SetDefaultRule</a></li>
	<li><a href="api_rule.html#ref_parser_GetDefaultRules">BBCode::GetDefaultRules</a></li>
	<li><a href="api_rule.html#ref_parser_SetDefaultRules">BBCode::SetDefaultRules</a></li>
	</ul>
</li>
<li><b><a href="api_wiki.html" style="color:#000;">Wiki-Link Functions:</a></b>
	<ul>
	<li><a href="api_wiki.html#ref_parser_SetWikiURL">BBCode::SetWikiURL</a></li>
	<li><a href="api_wiki.html#ref_parser_GetWikiURL">BBCode::GetWikiURL</a></li>
	<li><a href="api_wiki.html#ref_parser_GetDefaultWikiURL">BBCode::GetDefaultWikiURL</a></li>
	<li><a href="api_wiki.html#ref_parser_Wikify">BBCode::Wikify</a></li>
	</ul>
</li>
<li><b><a href="api_smiley.html" style="color:#000;">Smiley Functions:</a></b>
	<ul>
	<li><a href="api_smiley.html#ref_parser_AddSmiley">BBCode::AddSmiley</a></li>
	<li><a href="api_smiley.html#ref_parser_RemoveSmiley">BBCode::RemoveSmiley</a></li>
	<li><a href="api_smiley.html#ref_parser_GetSmiley">BBCode::GetSmiley</a></li>
	<li><a href="api_smiley.html#ref_parser_ClearSmileys">BBCode::ClearSmileys</a></li>
	<li><a href="api_smiley.html#ref_parser_GetDefaultSmiley">BBCode::GetDefaultSmiley</a></li>
	<li><a href="api_smiley.html#ref_parser_SetDefaultSmiley">BBCode::SetDefaultSmiley</a></li>
	<li><a href="api_smiley.html#ref_parser_GetDefaultSmileys">BBCode::GetDefaultSmileys</a></li>
	<li><a href="api_smiley.html#ref_parser_SetDefaultSmileys">BBCode::SetDefaultSmileys</a></li>
	<li><a href="api_smiley.html#ref_parser_SetSmileyDir">BBCode::SetSmileyDir</a></li>
	<li><a href="api_smiley.html#ref_parser_GetSmileyDir">BBCode::GetSmileyDir</a></li>
	<li><a href="api_smiley.html#ref_parser_GetDefaultSmileyDir">BBCode::GetDefaultSmileyDir</a></li>
	<li><a href="api_smiley.html#ref_parser_SetSmileyURL">BBCode::SetSmileyURL</a></li>
	<li><a href="api_smiley.html#ref_parser_GetSmileyURL">BBCode::GetSmileyURL</a></li>
	<li><a href="api_smiley.html#ref_parser_GetDefaultSmileyURL">BBCode::GetDefaultSmileyURL</a></li>
	<li><a href="api_smiley.html#ref_parser_SetEnableSmileys">BBCode::SetEnableSmileys</a></li>
	<li><a href="api_smiley.html#ref_parser_GetEnableSmileys">BBCode::GetEnableSmileys</a></li>
	<li><a href="api_smiley.html#ref_parser_SetMaxSmileys">BBCode::SetMaxSmileys</a></li>
	<li><a href="api_smiley.html#ref_parser_GetMaxSmileys">BBCode::GetMaxSmileys</a></li>
	</ul>
</li>
<li><b><a href="api_repl.html" style="color:#000;">Replaced Item Functions:</a></b>
	<ul>
	<li><a href="api_repl.html#ref_parser_GetRuleHTML">BBCode::GetRuleHTML</a></li>
	<li><a href="api_repl.html#ref_parser_SetRuleHTML">BBCode::SetRuleHTML</a></li>
	<li><a href="api_repl.html#ref_parser_GetDefaultRuleHTML">BBCode::GetDefaultRuleHTML</a></li>
	<li><a href="api_repl.html#ref_parser_GetLocalImgDir">BBCode::GetLocalImgDir</a></li>
	<li><a href="api_repl.html#ref_parser_SetLocalImgDir">BBCode::SetLocalImgDir</a></li>
	<li><a href="api_repl.html#ref_parser_GetDefaultLocalImgDir">BBCode::GetDefaultLocalImgDir</a></li>
	<li><a href="api_repl.html#ref_parser_GetLocalImgURL">BBCode::GetLocalImgURL</a></li>
	<li><a href="api_repl.html#ref_parser_SetLocalImgURL">BBCode::SetLocalImgURL</a></li>
	<li><a href="api_repl.html#ref_parser_GetDefaultLocalImgURL">BBCode::GetDefaultLocalImgURL</a></li>
	</ul>
</li>
<li><b><a href="api_misc.html" style="color:#000;">Miscellaneous Support Functions:</a></b>
	<ul>
	<li><a href="api_misc.html#ref_parser_nl2br">BBCode::nl2br</a></li>
	<li><a href="api_misc.html#ref_parser_IsValidURL">BBCode::IsValidURL</a></li>
	<li><a href="api_misc.html#ref_parser_IsValidEmail">BBCode::IsValidEmail</a></li>
	<li><a href="api_misc.html#ref_parser_HTMLEncode">BBCode::HTMLEncode</a></li>
	<li><a href="api_misc.html#ref_parser_FixupOutput">BBCode::FixupOutput</a></li>
	</ul>
</li>
</ul>
</td><td style='vertical-align:top;'>
<ul>
<li><b>Alphabetical Order:</b>
	<ul>
	<li><a href="api_rule.html#ref_parser_AddRule">BBCode::AddRule</a></li>
	<li><a href="api_smiley.html#ref_parser_AddSmiley">BBCode::AddSmiley</a></li>
	<li><a href="api_core.html#ref_parser_BBCode">BBCode::BBCode</a></li>
	<li><a href="api_rule.html#ref_parser_ClearRules">BBCode::ClearRules</a></li>
	<li><a href="api_smiley.html#ref_parser_ClearSmileys">BBCode::ClearSmileys</a></li>
	<li><a href="api_core.html#ref_parser_DoTag">BBCode::DoTag</a></li>
	<li><a href="api_misc.html#ref_parser_FixupOutput">BBCode::FixupOutput</a><br /><br /></li>

	<li><a href="api_behv.html#ref_parser_GetAllowAmpersand">BBCode::GetAllowAmpersand</a></li>
	<li><a href="api_core.html#ref_parser_GetDebug">BBCode::GetDebug</a></li>
	<li><a href="api_repl.html#ref_parser_GetDefaultLocalImgDir">BBCode::GetDefaultLocalImgDir</a></li>
	<li><a href="api_repl.html#ref_parser_GetDefaultLocalImgURL">BBCode::GetDefaultLocalImgURL</a></li>
	<li><a href="api_rule.html#ref_parser_GetDefaultRule">BBCode::GetDefaultRule</a></li>
	<li><a href="api_repl.html#ref_parser_GetDefaultRuleHTML">BBCode::GetDefaultRuleHTML</a></li>
	<li><a href="api_rule.html#ref_parser_GetDefaultRules">BBCode::GetDefaultRules</a></li>
	<li><a href="api_smiley.html#ref_parser_GetDefaultSmiley">BBCode::GetDefaultSmiley</a></li>
	<li><a href="api_smiley.html#ref_parser_GetDefaultSmileyDir">BBCode::GetDefaultSmileyDir</a></li>
	<li><a href="api_smiley.html#ref_parser_GetDefaultSmileys">BBCode::GetDefaultSmileys</a></li>
	<li><a href="api_smiley.html#ref_parser_GetDefaultSmileyURL">BBCode::GetDefaultSmileyURL</a></li>
	<li><a href="api_wiki.html#ref_parser_GetDefaultWikiURL">BBCode::GetDefaultWikiURL</a></li>
	<li><a href="api_smiley.html#ref_parser_GetEnableSmileys">BBCode::GetEnableSmileys</a></li>
	<li><a href="api_behv.html#ref_parser_GetIgnoreNewlines">BBCode::GetIgnoreNewlines</a></li>
	<li><a href="api_behv.html#ref_parser_GetLimit">BBCode::GetLimit</a></li>
	<li><a href="api_behv.html#ref_parser_GetLimitPrecision">BBCode::GetLimitPrecision</a></li>
	<li><a href="api_behv.html#ref_parser_GetLimitTail">BBCode::GetLimitTail</a></li>
	<li><a href="api_repl.html#ref_parser_GetLocalImgDir">BBCode::GetLocalImgDir</a></li>
	<li><a href="api_repl.html#ref_parser_GetLocalImgURL">BBCode::GetLocalImgURL</a></li>
	<li><a href="api_trim.html#ref_parser_GetPostTrim">BBCode::GetPostTrim</a></li>
	<li><a href="api_behv.html#ref_parser_GetPlainMode">BBCode::GetPlainMode</a></li>
	<li><a href="api_trim.html#ref_parser_GetPreTrim">BBCode::GetPreTrim</a></li>
	<li><a href="api_root.html#ref_parser_GetRoot">BBCode::GetRoot</a></li>
	<li><a href="api_rule.html#ref_parser_GetRule">BBCode::GetRule</a></li>
	<li><a href="api_repl.html#ref_parser_GetRuleHTML">BBCode::GetRuleHTML</a></li>
	<li><a href="api_smiley.html#ref_parser_GetSmiley">BBCode::GetSmiley</a></li>
	<li><a href="api_smiley.html#ref_parser_GetSmileyDir">BBCode::GetSmileyDir</a></li>
	<li><a href="api_smiley.html#ref_parser_GetSmileyURL">BBCode::GetSmileyURL</a></li>
	<li><a href="api_behv.html#ref_parser_GetTagMarker">BBCode::GetTagMarker</a></li>
	<li><a href="api_wiki.html#ref_parser_GetWikiURL">BBCode::GetWikiURL</a><br /><br /></li>


	<li><a href="api_misc.html#ref_parser_HTMLEncode">BBCode::HTMLEncode</a></li>
	<li><a href="api_misc.html#ref_parser_IsValidURL">BBCode::IsValidURL</a></li>
	<li><a href="api_misc.html#ref_parser_IsValidEmail">BBCode::IsValidEmail</a></li>
	<li><a href="api_misc.html#ref_parser_nl2br">BBCode::nl2br</a></li>
	<li><a href="api_core.html#ref_parser_Parse">BBCode::Parse</a></li>
	<li><a href="api_rule.html#ref_parser_RemoveRule">BBCode::RemoveRule</a></li>
	<li><a href="api_smiley.html#ref_parser_RemoveSmiley">BBCode::RemoveSmiley</a><br /><br /></li>
	
	<li><a href="api_behv.html#ref_parser_SetAllowAmpersand">BBCode::SetAllowAmpersand</a></li>
	<li><a href="api_core.html#ref_parser_SetDebug">BBCode::SetDebug</a></li>
	<li><a href="api_rule.html#ref_parser_SetDefaultRules">BBCode::SetDefaultRules</a></li>
	<li><a href="api_smiley.html#ref_parser_SetDefaultSmiley">BBCode::SetDefaultSmiley</a></li>
	<li><a href="api_smiley.html#ref_parser_SetEnableSmileys">BBCode::SetEnableSmileys</a></li>
	<li><a href="api_behv.html#ref_parser_SetIgnoreNewlines">BBCode::SetIgnoreNewlines</a></li>
	<li><a href="api_behv.html#ref_parser_SetLimit">BBCode::SetLimit</a></li>
	<li><a href="api_behv.html#ref_parser_SetLimitPrecision">BBCode::SetLimitPrecision</a></li>
	<li><a href="api_behv.html#ref_parser_SetLimitTail">BBCode::SetLimitTail</a></li>
	<li><a href="api_repl.html#ref_parser_SetLocalImgDir">BBCode::SetLocalImgDir</a></li>
	<li><a href="api_repl.html#ref_parser_SetLocalImgURL">BBCode::SetLocalImgURL</a></li>
	<li><a href="api_behv.html#ref_parser_SetPlainMode">BBCode::SetPlainMode</a></li>
	<li><a href="api_trim.html#ref_parser_SetPostTrim">BBCode::SetPostTrim</a></li>
	<li><a href="api_trim.html#ref_parser_SetPreTrim">BBCode::SetPreTrim</a></li>
	<li><a href="api_root.html#ref_parser_SetRoot">BBCode::SetRoot</a></li>
	<li><a href="api_root.html#ref_parser_SetRootBlock">BBCode::SetRootBlock</a></li>
	<li><a href="api_root.html#ref_parser_SetRootInline">BBCode::SetRootInline</a></li>
	<li><a href="api_repl.html#ref_parser_SetRuleHTML">BBCode::SetRuleHTML</a></li>
	<li><a href="api_smiley.html#ref_parser_SetSmileyDir">BBCode::SetSmileyDir</a></li>
	<li><a href="api_smiley.html#ref_parser_SetSmileyURL">BBCode::SetSmileyURL</a></li>
	<li><a href="api_behv.html#ref_parser_SetTagMarker">BBCode::SetTagMarker</a></li>
	<li><a href="api_wiki.html#ref_parser_SetWikiURL">BBCode::SetWikiURL</a><br /><br /></li>

	<li><a href="api_behv.html#ref_parser_WasLimited">BBCode::WasLimited</a></li>
	<li><a href="api_wiki.html#ref_parser_Wikify">BBCode::Wikify</a></li>
	</ul>
</li>
</ul>
</td></tr></tbody></table>
