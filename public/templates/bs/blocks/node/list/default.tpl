<ul class="nav nav-stacked">
{foreach item=node from=$block.data}    
<li><a class="link" href="{$node.urls.self}">{$node.title}</a></li>
{/foreach}
</ul>