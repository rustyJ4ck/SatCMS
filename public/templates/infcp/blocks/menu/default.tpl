<ul class="nav nav-pills menu-{$block.data.name}">
{foreach from=$block.data.submenu item=v}
<li>
<a href="{$v.url}">{$v.title}</a>
    {if $v.submenu}
    <ul class="submenu">
    {foreach from=$v.submenu item=vs}
    <li><a href="{$vs.url}">{$vs.title}</a></li>  
    {/foreach}
    </ul>
    {/if}
</li>
{/foreach}
</ul>

