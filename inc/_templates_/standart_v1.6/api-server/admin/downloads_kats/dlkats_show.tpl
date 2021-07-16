<tr>
  <th scope="row">{$id}</th>
  <td>{$name}</td>
  <td>{if $addons == -1}Sichtbar auf allen Seiten{/if}
    {if $addons == 0}Nur sichtbar auf "dzcp.de"{/if}
    {if $addons == 1}Nur sichtbar auf "addons.dzcp.de"{/if}</td>
  <td>{$subkats}</td>
  <td>{$edit}</td>
  <td>{$delete}</td>
</tr>
