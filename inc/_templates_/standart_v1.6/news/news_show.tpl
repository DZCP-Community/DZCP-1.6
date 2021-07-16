<!-- start news_show.tpl -->
<div class="col-md-12" style="padding-top: 5px;">
	<div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative card-cell">
		<div class="col-auto d-none d-lg-block newsImage">
			<img src="{$kat}" alt="" class="newsImage" />
		</div>
		<div class="col p-4 d-flex flex-column position-static">
			<strong class="d-inline-block mb-2 news" style="color: {$color} !important;">[ {$kat_name} ] {$sticky} {$intern} {$titel}</strong>
			<p class="card-text mb-auto">{$text}</p>
		</div>
		<div class="col-md-12 card-header bg-news-sec">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="left" style="padding-left:12px;padding-bottom: 3px;">{$autor} - {$datum}{lang msgID="uhr"}</td>
					<td align="right" style="padding-right:12px;padding-bottom: 3px;">
						<a href="?action=show&amp;id={$id}">{if $comments >= 2}{$comments} {lang msgID="news_kommentare"}{else}{$comments} {lang msgID="news_kommentar"}{/if} </a>
						<i class="fas fa-comments fa-lg" style="float:right; margin:-2px 0 0 5px;"></i>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<!-- end news_show.tpl -->