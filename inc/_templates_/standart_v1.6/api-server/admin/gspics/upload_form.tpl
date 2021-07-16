<tr>
  <td class="contentHead" align="center" colspan="2"><span class="fontBold">{$head}</span></td>
</tr>
<tr>
  <td class="contentMainTop" colspan="4">
    <div style="margin-right: 10px;margin-left: 10px;">
      <form name="dlkats" action="?admin=api_server&module=gspics&id={$id}&action=upload" method="post" enctype="multipart/form-data">
        <table class="hperc" cellspacing="1">
          <tr>
            <td class="contentMainTop" width="100"><span class="fontBold">Protocol:</span></td>
            <td class="contentMainFirst" align="center">{$protocol}</td>
          </tr>
          <tr>
            <td class="contentMainTop" width="100"><span class="fontBold">Game:</span></td>
            <td class="contentMainFirst" align="center">{$type}</td>
          </tr>
          <tr>
            <td class="contentMainTop" width="100"><span class="fontBold">Mod:</span></td>
            <td class="contentMainFirst" align="center">{$mod}</td>
          </tr>
          <tr>
            <td class="contentMainTop" width="100"><span class="fontBold">Map:</span></td>
            <td class="contentMainFirst" align="center">{$mapname}</td>
          </tr>
          <tr>
            <td class="contentMainTop" width="100"><span class="fontBold">Bild:</span></td>
            <td class="contentMainFirst" align="center"><input name="map" type="file" size="50" accept="image/*"></td>
          </tr>
          <tr>
            <td class="contentBottom" colspan="2"><input id="contentSubmit" type="submit" class="submit btn" value="{$what}" /></td>
          </tr>
        </table>
      </form>
    </div>
  </td>
</tr>