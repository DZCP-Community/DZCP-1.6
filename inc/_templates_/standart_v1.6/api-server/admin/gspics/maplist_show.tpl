<tr>
  <th scope="row">{$id}</th>
  <td>{$protocol}</td>
  <td>{$type}</td>
  <td>{$mod}</td>
  <td>{$mapname}</td>
  <td style="text-align: center">{$searched}</td>
  <td style="text-align: center"><a href="?admin=api_server&module=gspics&action=public&what=toggle&id={$id}">
    {if $enabled == 0}<i class="fas fa-toggle-off fa-lg"></i>{/if}
    {if $enabled == 1}<i class="fas fa-toggle-on fa-lg"></i>{/if}</a>
  </td>
  <td>{$upload}</td>
  <td>{$delete}</td>
</tr>
