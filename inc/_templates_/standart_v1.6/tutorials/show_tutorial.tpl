<div style="margin-top:20px;"></div>
{$notification_page}
<div class="col-md-12" style="padding-top: 5px;">
  <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative card-cell">
    <div class="col-auto d-none d-lg-block newsImage">
      <img src="{$pic}" alt="" class="newsImage" />
    </div>
    <div class="col p-4 d-flex flex-column position-static">
      <strong class="d-inline-block mb-2 news">{$head}</strong>
      <p class="card-text mb-auto">{$tutorial}</p>
    </div>
    <div class="col-md-12 card-header bg-news-sec">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="left" style="padding-left:12px;padding-bottom: 3px;">
            <div style="text-align: left;margin-top: 13px;">
              {$autor} - {$datum}{lang msgID="uhr"}
            </div>
          </td>
          <td align="right" style="padding-right:12px;padding-bottom: 3px;">
            <div class="row">
              <div class="col">
                <div id="tut_rating" class="tut-rating" style="text-align: right;"></div>
              </div>
            </div>
            <script>
              $("#tut_rating").starRating({
                totalStars: 4,
                initialRating: {$level},
                starSize: 20,
                readOnly: true,
              });
            </script>
          </td>
        </tr>
      </table>
    </div>
  </div>
</div>
<div class="col-md-12" style="padding-top: 5px;">
  {notification index="tut_tr"}
  {$comments}
</div>