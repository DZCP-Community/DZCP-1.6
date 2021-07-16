<tr>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" colspan="2">{$nick}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center" style="width:1%">{$onoff}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}">{$level}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center">{$hp}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center">{$mf}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center">{$age}</td>
  {$edit}
  {$delete}
  {$full_delete}
</tr>