
<!--
шаблон
-->

<table class="nice_borders">

<tr>

<th>
#</th> 
<th>{$lang.id}</th>
<th>{$lang.ip}</th>
<th>key</th>
<th colspan="2">{$lang.m_users.last_update}</th> 
</tr>


<TBODY id="{$req.c}_table">

<!-- {if $smarty.foreach.out.index is odd}class="row1"{/if} -->

                                       
{assign var="index_" value="0"}
{foreach key=i name=out item=item from=$tpl_sessions}   

    <tr id="{$req.c}_item_{$item.id}" {if $item.b_is_global}class="row_red"{else}{if $index_ is odd}class="row1"{/if}{/if}   >

    <td class="first">
    {$item.id}
    </td>    

    <td class="first">
    {$item.uid} &nbsp;
    </td>    
    
    <td class="first">
    {$item.uip} &nbsp;
    </td>
    
    <td class="first">
    {$item.skey} &nbsp;
    </td>    

    <td class="first">
    {$item.last_update} &nbsp;
    </td>    
    
    <td class="first">
    {$item.last_update_} &nbsp;
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

{* pagination *}
{include file="pagination.tpl"}