{if $global || $sticky || $closed}
<span class="fontWichtig" title="
{if $global}
[G]{lang msgID="forum_global"}
{/if}
{if $sticky}
[W]{lang msgID="forum_sticky"}
{/if}
{if $closed}
[C]{lang msgID="forum_closed"}
{/if}">
    [{if $global}G{/if}{if $sticky}W{/if}{if $closed}C{/if}]{/if}
    <a href="../forum/?action=showthread&amp;id={$id}">{$topic}</a>