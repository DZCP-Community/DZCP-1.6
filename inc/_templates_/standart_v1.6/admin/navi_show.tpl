<tr>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}">{$name}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}">{$kat}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}">{$url}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center"><a href="?admin=navi&amp;do=menu&amp;id={$id}&amp;set={$set}">{$shown}</a></td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" style="width:1%">{$edit}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" style="width:1%">{$del}</td>
</tr>