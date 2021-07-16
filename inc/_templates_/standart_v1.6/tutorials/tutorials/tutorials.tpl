<div style="margin-top:20px;"></div>
<div class="col-md-12">
  <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative card-cell">
    <div class="col-md-12">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="contentHead" colspan="2" style="padding-left:12px;padding-bottom: 3px;">
            <span class="fontBold">Kategorie: {$kategorie_name}</span>
          </td>
        </tr>
      </table>
    </div>
    <div class="col-auto d-none d-lg-block newsImage">
      <a href="?action=show_tutorials&amp;id={$id}">
        <img src="{$katpic}" alt="" class="newsImage" />
      </a>
    </div>
    <div class="col p-4 d-flex flex-column position-static">
      <p class="card-text mb-auto">{$kat_beschreibung}</p>
    </div>
    <div class="col-md-12">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="contentHead" colspan="2" style="padding-left:12px;padding-bottom: 3px;">
            <span class="fontBold">{lang msgID="tutorials"}</span>
          </td>
        </tr>
      </table>
    </div>
    <div style="margin-top:40px;"></div>
    {$tutorials}
  </div>
</div>
{if $nav}
<div class="col-md-12">
  <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative card-cell">
      <div class="col">
        {$nav}
      </div>
  </div>
</div>
{/if}