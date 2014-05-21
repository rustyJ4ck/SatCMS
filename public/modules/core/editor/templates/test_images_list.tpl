<table cellpadding="5" cellspacing="0" border="0" rel="{$config.base_url}">

<tr class="nodrag">

<!--<a href="index.php?c={$g_c}&op=new"><img src="templates/images/b_new.gif" alt="" border="0"/></a>   
-->
<th>
<a href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=new">
<img src="{$config.site_url}editor/templates/images/b_add_page.gif" alt="" border="0"/>
</a> 
</th> 
<th>{$lang.title}</th>
<th>{$lang.image}</th>
<th>{$lang.value}</th>
<th nowrap="nowrap" width="50">{$lang.ops}</th>
</tr>

<TBODY id="{$req.c}_table">

<!-- {if $smarty.foreach.out.index is odd}class="row1"{/if} -->

{debug}                
                                       
{assign var="index_" value="0"}
{foreach key=i name=out item=item from=$tpl_test_images}   

{* {$req.c}_item_ *}
    <tr id="{$item.id}" {if $item.b_is_global}class="row_red"{else}{if $index_ is odd}class="row1"{/if}{/if}   >

    <td class="first">
    {$item.id}
    </td>    

    <td class="first">
    1. {$item.title}<br/>
    2. {$item.text}
    </td>      
    
    <td class="first">
    {if $item.image.thumbnail.url}
    <img src="{$item.image.thumbnail.url}"/>
    {/if}
    </td>    
   
    
    <td class="first">
    {$item.image.file} &nbsp; {$item.image.type} / {$item.image.size}
    </td>     
    
                            
    <td class="last" >

    <a href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}"><img src="{$config.site_url}editor/templates/images/b_edit.png" alt="" border="0"/></a>
    &nbsp;

    <a href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=drop&id={$item.id}"><img src="{$config.site_url}editor/templates/images/b_drop.png" alt="" border="0"/></a>
    <b onclick="$('tr#{$req.c}_item_{$item.id}').remove();">x</b>
    <!--
    <a href="javascript:g_js_drop_confirm('ajax_item_drop_ex(\'{$req.c}\',\'{$item.id}\',\'op=drop&m={$req.m}&c={$req.c}&pid={$pid}&id={$item.id}\');');"><img src="{$config.site_url}editor/templates/images/b_drop.png" alt="" border="0"/></a>
   -->
   &nbsp;
     
    </td>

    </tr>

{assign var="index_" value=$index_+1}
{foreachelse}

    <tr><td colspan="5" style="padding:10px;text-align:center">
   {$lang.empty}
    </td></tr>   

{/foreach}

</TBODY>

</table>

 