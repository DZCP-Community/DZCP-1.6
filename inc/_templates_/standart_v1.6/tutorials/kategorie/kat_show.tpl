<tr>
  <td class="forumTD" style="vertical-align: top; text-align: center; ">
    <div class="col p-4 d-flex flex-column position-static" style="padding: 0.8rem !important;">
      <div class="d-inline-block mb-2 news tut_kat">
        <a href="?action=show_tutorials&amp;id={$id}">
          <img class="tut_pic" src="{$pic}" alt="" />
        </a>
      </div>
    </div>
  </td>
  <td class="forumTD" style="white-space: normal;">
    <div class="tut_text">
      {$beschreibung}
    </div>
    <div style="margin-top: 23px; padding-left: 620px; margin-bottom: 10px;">
      <button type="button" class="btn btn-dark btn-sm" onclick="DZCP.goTo('?action=show&amp;id={$id}')"/>
      {$tuts} {lang msgID="tutorials_show_tutorials"}
      </button>
    </div>
  </td>
</tr>