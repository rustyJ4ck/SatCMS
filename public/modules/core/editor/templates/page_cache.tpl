
<!--
шаблон
-->

<table cellpadding="5" cellspacing="0" border="0">

<tr>

<!--<a href="index.php?c={$g_c}&op=new"><img src="templates/images/b_new.gif" alt="" border="0"/></a>   
-->
<th>#</th> 
<th>URL</th>
<th>ACCESS</th> 
<th>COUNT</th> 
<th>PREV_ACCESS</th> 
<th>SUM COUNT</th> 
<th>CACHED</th> 
<!--th nowrap="nowrap" width="50">{$lang.ops}</th-->
</tr>

<TBODY id="{$req.c}_table">

<!-- {if $smarty.foreach.out.index is odd}class="row1"{/if} -->

                                       
{assign var="index_" value="0"}
{foreach key=i name=out item=item from=$tpl_page_cache}   

    <tr id="{$req.c}_item_{$item.id}" {if $item.b_is_global}class="row_red"{else}{if $index_ is odd}class="row1"{/if}{/if}   >

    <td class="first">
    {$item.id}
    </td>    
    
    <td class="first">
    {$item.url}
    </td>     

    <td class="first">
    {$item.access_time}&nbsp;
    </td>    
    
    <td class="first">
    {$item.counter} &nbsp;
    </td>
    
    <td class="first">
    {$item.prev_access_time}&nbsp;
    </td>   
    
    <td class="first">
    {$item.sum_counter} &nbsp;
    </td>   
    
    <td class="first">
    {if $item._is_cached}
    {$item.expire_time}
    {else}
    -
    {/if}
    </td>       

    <!--td class="last" >

    <a href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}"><img src="{$config.site_url}editor/templates/images/b_edit.png" alt="" border="0"/></a>
    &nbsp;

    <a href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=drop&id={$item.id}"><img src="{$config.site_url}editor/templates/images/b_drop.png" alt="" border="0"/></a>
    <b onclick="$('tr#{$req.c}_item_{$item.id}').remove();">x</b>
   &nbsp;
     
    </td-->

    </tr>

{assign var="index_" value=$index_+1}
{foreachelse}

    <tr><td colspan="5" style="padding:10px;text-align:center">
   {$lang.empty}
    </td></tr>   

{/foreach}

</TBODY>

</table>

<!--
<div id="meee" style="width:100px; height:100px; background-color:red;">99</div>

<script>
$("#meee").corner("10px");
</script>
-->

{* pagination *}
{include file="pagination.tpl"}