<!-- start postausgang.tpl -->
<tr>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" width="3%">{$readed}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" width="37%">{$titel}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" width="25%">{$empfaenger}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" width="30%">{$datum}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center"><input class="checkbox" type="checkbox" id="postausgang_{$id}" name="postausgang_{$id}" value="{$id}" /></td>
</tr>
<!-- end postausgang.tpl -->