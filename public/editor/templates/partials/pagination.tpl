{if !empty($pagination)}

    {* <h2>{$lang.pagination}</h2> *}

    <ul class="pagination pagination-sm">

    {if $pagination.prev}
    <li class="previous"><a href="{$pagination.prev.url}">&larr;</a></li>
    {/if}

    {foreach $pagination.pagination as $part}
    <li class="{if $part.current}active{/if} {if NOT $part.url}spacer{/if}"><a href="{if $part.url}{$part.url}{else}#{/if}">{$part.start|default:'&#133;'}</a></li>
    {/foreach}

    {if $pagination.next}
        <li class="next"><a href="{$pagination.next.url}">&rarr;</a></li>
    {/if}

    </ul>

{/if}