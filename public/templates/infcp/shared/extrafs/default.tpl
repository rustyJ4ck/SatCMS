<br/>
<br/>

<h3>{if $title}{$title}{else}Характеристики:{/if}</h3>

{if count($data)} 
<table class="nice_borders">
{foreach from=$data item=v}
<!-- EXTRA_TAB {$v.name} -->
<tr id="t-{$v.name}">
<th colspan="2">{$v.name}</td>
</tr>


    {foreach from=$v.fields item=f}
    
    <tr>
    
        <td>
            {$f.title}            
        </td>
        <td>
            {if $f.type_string == 'file'}
                {if $f.fvalue.url}<a traget="_blank" class="{$f.class}" href="{$f.fvalue.url}" />{$f.value.title}</a>{/if}
            {elseif $f.type_string == 'image'}
                {if $f.fvalue.url}<img class="{$f.class}" src="{$f.fvalue.url}" alt="{$f.value.title}"/>{/if}
            {elseif $f.type_string == 'link'}
                {assign var=f_url_parts value=$f.fvalue|parse_url}
                {if $f.fvalue}<a traget="{$f.value.target}" class="{$f.class}" href="{$f.fvalue}" />{$f_url_parts.host}</a>{/if}
            {elseif $f.type_string == 'select'}
                {if $f.fvalue}{$f.select_options[$f.fvalue]}{else}-{/if}
            {elseif $f.type_string == 'sat_node'}
                {if $f.fvalue}
                    <a href="{$current.site.tree.map[$f.fvalue].url}">{$current.site.tree.map[$f.fvalue].title}</a>
                    {else}-{/if}                
            {else}
                {$f.fvalue}
            {/if}
        </td> 
    
    </tr>
    {/foreach}
{/foreach}
</table>
{/if}


<style>
{literal}
.img100px { width: 100px; }
{/literal}
</style>