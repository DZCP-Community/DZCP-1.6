<tr>
    <td class="forumTD" align="center" width="31"><img src="{dir}/images/forum/forum_{$frompic}.gif" alt="" height="27" width="27" class="icon" /></td>
    <td class="forumTD"><a href="?action=showthread&amp;id={$id}{$hl}" title="{$topic_title}">{if $global || $sticky}<span class="fontWichtig" title="{if $global}
[G]{lang msgID="forum_global"}
{/if}
{if $sticky}
[W]{lang msgID="forum_sticky"}
{/if}">[{if $global}G{/if}{if $sticky}W{/if}]</span>{/if} {$topic}</a><p class="forumdesc">{$subtopic}</p></td>
    <td class="forumTD" align="center">{$autor}</td>
    <td class="forumTD" align="center"><p class="forumTopic">{$replys}</p></td>
    <td class="forumTD" align="center"><p class="forumTopic">{$hits}</p></td>
    <td class="forumTD" align="center" nowrap="nowrap">{$lpost}</td>
</tr>