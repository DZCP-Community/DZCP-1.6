<tr>
  <th scope="row">{$id}</th>
  <td>{$titel}</td>
  <td>{$date}</td>
  <td>{if $version == 'all'}Alle{/if}
    {if $version == '1.5'}1.5.x{/if}
    {if $version == '1.6'}1.6.x{/if}
    {if $version == '1.7'}1.7.x{/if}
    {if $version == '1.8'}1.8.x{/if}
    {if $version == '2.0'}2.0.X{/if}</td>
  <td>{if $public == 1}Ja{/if}
    {if $public == 0}Nein{/if}</td>
  <td>{$public_btn}</td>
  <td>{$edit}</td>
  <td>{$delete}</td>
</tr>