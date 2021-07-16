<a href="../forum/">{$mainkat}</a> &raquo;
<span class="fontBold">{lang msgID="forum_head"}:</span>
<a href="?action=show">{$wherekat}</a>
{if $tid != 0 || $wherepost != ""}
    &raquo; <span class="fontBold">{lang msgID="forum_thread"}:</span>
    <a href="?action=showthread&amp;id={$tid}">{$wherepost}</a>
{/if}