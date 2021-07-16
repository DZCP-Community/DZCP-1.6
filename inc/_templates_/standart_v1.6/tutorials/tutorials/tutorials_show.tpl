<div class="col-md-12" style="padding-top: 5px;">
    <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative card-cell">
        <div class="col-md-12">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="contentHead" colspan="2" style="padding-left:12px;padding-bottom: 3px;">
                        <span class="fontBold">{$bezeichnung}</span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-auto d-none d-lg-block newsImage">
            <a href="?action=tutorial&amp;id={$id}">
                <img src="{$pic}" alt="" class="newsImage" />
            </a>
        </div>
        <div class="col p-4 d-flex flex-column position-static">
            <p class="card-text mb-auto">{$beschreibung}</p>
        </div>
        <div class="col-auto d-none d-lg-block newsImage card-header" style="margin-right:10px;padding:15px;margin-bottom:12px;height:100px;">
            <div id="tut_rating_{$id}" class="tut-rating"></div>
            <div class="tut-rating_text">{$level_text}</div>
            <script>
                $("#tut_rating_{$id}").starRating({
                    totalStars: 4,
                    initialRating: {$level},
                    starSize: 20,
                    readOnly: true,
                });
            </script>
        </div>
        <div class="col-md-12 card-header bg-news-sec">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="left" style="padding-left:16px;padding-bottom:6px;padding-top:6px;">{$autor} - {$datum}{lang msgID="uhr"}</td>
                    <td align="right" style="padding-right:16px;padding-bottom:2px;padding-top:4px;">
                        <button type="button" class="btn btn-dark btn-sm" onclick="DZCP.goTo('index.php?action=tutorial&amp;id={$id}')">{lang msgID="tutorials_read"}</button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>