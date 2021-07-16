<tr>
    <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center" width="1%">{$datum}</td>
    <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center">{$action}</td>
    <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center">{$transid}</td>
    <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center">{$to}</td>
    <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center">{$from}</td>
    <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center">{$balance} {lang msgID="konto_coins"}</td>
    <td class="{if $color % 2}contentMainSecond{else}contentMainFirst{/if}" align="center" width="1%">{$edit}{$delete}</td>
</tr>