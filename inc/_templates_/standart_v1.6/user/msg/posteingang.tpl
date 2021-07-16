<!-- start posteingang.tpl -->
<tr>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" width="3%">{$new}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" width="37%">{$titel}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" width="25%">{$absender}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" width="30%">{$datum}</td>
  <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center"><input class="checkbox" type="checkbox" id="posteingang_{$id}" name="posteingang_{$id}" value="{$id}" /></td>
</tr>
<!-- end posteingang.tpl -->