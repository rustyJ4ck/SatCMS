{*$cp|debug_print_var*}

<ul class="ul-left-menu">
{foreach $cp.links as $item}
{if NOT $item.disabled}
<li {if $item.url|is_url}class="selected"{/if}><a href="{$item.url}">{$item.title}</a></li>
{/if}
{/foreach}
 <li><a href="{$cp.urls.logout.url}">{$cp.urls.logout.title}</a></li>
</ul>
