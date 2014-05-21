<ul class="desc_link_list">
{foreach item=node from=$block.data name="desc_link_list"}    
<li><a class="link" href="{$node.urls.self}">{$node.title}</a>
<div class="date">{$node.create_date}</div>
<div class="desc">{$node.description}</div>  

<div class="more_link">
<a href="{$node.urls.self}" class="link">[+] Читать далее</a>
</div>

{if NOT $smarty.foreach.desc_link_list.last AND count($block.data) > 1}<br/>{/if} 
</li>
{/foreach}
</ul>