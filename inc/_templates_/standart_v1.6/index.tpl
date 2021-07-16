<!DOCTYPE html>
<html lang="en">
<head>
	<title>{$title}</title>
	<meta http-equiv="title" content="{$title}" />
	<meta http-equiv="pragma" content="No-Cache" />
	<meta http-equiv="classification" content="General" />
	<meta http-equiv="pragma" content="No-Cache" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="google-site-verification" content="WTqpgPL3hwQfBjGCM39lRrtHmtCMDCwn0TfbO3AQk0E" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta id="identifier" name="identifier-url" content="https://www.dzcp.de" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap-grid.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap-reboot.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.11.2/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
	<link rel="stylesheet" type="text/css" href="{dir}/_css/lightslider.css"/>
	<link rel="stylesheet" type="text/css" href="{dir}/_css/test.css"/>
	<link rel="stylesheet" type="text/css" href="{dir}/_css/star-rating-svg.css">
	<link rel="stylesheet" type="text/css" href="../inc/ajax.php?i=less{$regen}" />
	<link rel="alternate" type="application/rss+xml" href="../rss.xml" title="{$clanname} RSS-Feed" />
	<link rel="icon" href="{dir}/favicon.ico" />
	<link rel="home" href="/" title="Home" />
	<link rel="top" href="#toplink" title="TOP" />
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/js/bootstrap-select.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jquery-ui-dist@1.12.1/jquery-ui.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-autohidingnavbar@4.0.0/dist/jquery.bootstrap-autohidingnavbar.min.js"></script>
	{$java_vars}
	<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-colorpicker@3.1.2/dist/js/bootstrap-colorpicker.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/magnific-popup@1.1.0/dist/jquery.magnific-popup.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jquery-bar-rating@1.2.2/jquery.barrating.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/twbs-pagination@1.4.1/jquery.twbsPagination.min.js"></script>
	<script src="https://static.dzcp.de/js/lightslider.min.js"></script>
	<script src="https://static.dzcp.de/js/jquery.star-rating-svg.min.js"></script>
	<script src="{dir}/_js/jquery.dzcp.js"></script>
</head>
<body>
{dsgvo}
{$check_msg}
<a style="visibility:hidden;" href="../inc/bot.php"><img src="{idir}/1px.gif" width="1" height="1" border="0" alt="" /></a>

<!-- Top Navbar Start -->
<nav class="navbar fixed-top bg-dzcp navbar-left">
	<div class="navbar">
		<div class="navbar-left">
			<nav>
				<img alt="Brand" style="padding-right: 10px;padding-left: 10px;" src="{dir}/favicon.png">
				<a class="navbar-brand" href="#">
					{$clanname}
				</a>
			</nav>
		</div>
		<div>
			<nav class="animenu" role="navigation" aria-label="Menu">
				<ul class="animenu__nav">
					{navi kat="main"}
					<li><a style="cursor: pointer;" onclick="DZCP.dsgvo()">{lang msgID="txt_datenschutz"}</a></li>
				</ul>
			</nav>
		</div>
		<div class="navbar-version">
			<div style="margin-left: 10px;margin-right: 10px;color: #1F811F;" id="version">Aktuelle Stable: <a id="version" href="https://github.com/DZCP-Community/DZCP-1.6/commits/final" title="Github">{version type='live'}</a></div>
			<div style="margin-left: 10px;margin-right: 10px;color: #bf2d28;" id="version">Aktuelle Beta:&nbsp;&nbsp;&nbsp; <a id="version" href="https://github.com/DZCP-Community/DZCP-1.6/commits/final" title="Github">{version type='beta'}</a></div>
		</div>
	</div>
</nav>
<!-- Top Navbar Ende -->
<a name="toplink"></a>
<!-- Template Start -->
<!-- Wrapper bereich Start -->
<div id="wrapper">
	<!-- Header Start -->
	<div id="header">
		<img src="{dir}/images/clanlogo.png" alt="Clanlogo"/>
	</div>
	<!-- Header Ende -->
	<div id="content" class="content">
		<!-- mittlere Spalte Start -->
		<div id="middleContent">
			<div class="index corner" style="margin-bottom:5px; padding:7px">
				<div class="leftFloat">{welcome}</div>
				<div class="rightFloat">{languages}</div>
				<div class="clearFix"></div>
			</div>
			<div class="index corner">
				<!-- Wo bin ich & Suche Start -->
				<div id="brackets">
					{$where}
					<div id="search">{search}</div>
				</div>
				<!-- Wo bin ich $ Suche Ende -->
				<!-- Main Content Frame Start -->
				<div id="index">
					{$notification}
					{$index}
				</div>
				<!-- Main Content Frame Ende -->
			</div>
		</div>
		<!-- mittlere Spalte Ende -->
		<!-- rechte Spalte Start -->
		<div class="sideContent">
			<!-- Userbereich Start -->
			{if $lock}
				<div class="box corner">
					<h2 class="headline">{lang msgID="txt_userarea"}</h2>
					{login}
					<div class="leftFloat">{avatar}</div>
					<div class="leftFloat">{navi kat="user"}{navi kat="member"}{navi kat="admin"}</div>
					<br style="clear:both" />
				</div>
			{/if}
			<!-- Userbereich Ende -->
			<!-- Ftopics Box Start -->
			<div class="box corner">
				<h2 class="headline">{lang msgID="txt_ftopics"}</h2>
				{ftopics}
			</div>
			<!-- Ftopics Box Ende -->
			<!-- News Start -->
			<div class="box corner">
				<h2 class="headline">{lang msgID="txt_l_news"}</h2>
				{l_news}
			</div>
			<!-- News Ende -->
			<!-- Top Downloads Box Start -->
			<div class="box corner">
				<h2 class="headline">{lang msgID="txt_top_dl"}</h2>
				{top_dl}
			</div>
			<!-- Top Downloads Box Ende -->
			<!-- Vote Box Start -->
			<div class="box corner">
				<h2 class="headline">{lang msgID="txt_vote"}</h2>
				{vote}
			</div>
			<!-- Vote Box Ende -->
			<!-- Switchbox Sponsors/Partners Start -->
			<div class="box corner">
				<h2 class="headline tabs">
					<span class="tabright">{lang msgID="txt_sponsors"}</span>
					<span class="tableft">{lang msgID="txt_partners"}</span>
				</h2>
				<div class="switchs">{sponsors}</div>
				<div class="switchs">{partners}</div>
			</div>
			{if $lock && $templateswitch}
				<!-- Templateswitch Box Start -->
				<div class="box corner">
					<h2 class="headline">{lang msgID="txt_template_switch"}</h2>
					{templateswitch}
				</div>
				<!-- Templateswitch Box Ende -->
			{/if}
		</div>
		<!-- rechte Spalte Ende -->
		<!-- Clearfix Hack Start -->
		<div class="clearFix"></div>
		<!-- ClearFix Hack Ende -->
	</div>
</div>
<!-- Wrapper bereich Ende -->
<!-- Template Ende -->
</body>
