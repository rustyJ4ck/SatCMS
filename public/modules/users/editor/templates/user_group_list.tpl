
        
<table class="nice_borders" cellpadding="5" cellspacing="0" border="0" 
id="dnd_table1" rel="{$config.base_url}">

<tr class="nodrag">

<!--<a href="index.php?c={$g_c}&op=new"><img src="templates/images/b_new.gif" alt="" border="0"/></a>   
-->
<th>

        <a class="dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=new&embed=yes"
            title="Добавить новый" 
            dialog="{ldelim}width:620,height:230{rdelim}"
        >        
        <img src="{$config.site_url}editor/templates/images/b_add_page.gif" alt="" border="0"/>
</a> 
</th> 

<th>{$lang.title}</th>
<th>{$lang.name}</th> 

<th nowrap="nowrap" width="50">{$lang.ops}</th>
</tr>

<TBODY id="{$req.c}_table">

<!-- {if $smarty.foreach.out.index is odd}class="row1"{/if} -->

                                       
{assign var="index_" value="0"}
{foreach key=i name=out item=item from=$tpl_user_group}   

{* {$req.c}_item_ *}
    <tr rel="{$item.position}" rel_id="{$item.id}" id="{$req.c}_item_{$item.id}"
    {if $item.b_is_global}class="row_red"{else}{if $index_ is odd}class="row1"{/if}{/if}   >

    <td class="first">
    {$item.id}
    </td>    
    
    <td class="first">
    {$item.title} &nbsp;
    </td>    

    <td class="first">
    {$item.name} &nbsp; 
    </td>     
    
    <td class="last" >

      <a class="dialog1" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}"
            title="Изменение элемента" 
            dialog="{ldelim}width:520,height:230{rdelim}"
      >        
     <img src="{$config.site_url}editor/templates/images/b_edit.png" alt="" border="0"/>
     </a>    
       
    &nbsp;

    <a class="a_delete" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=drop&id={$item.id}"><img src="{$config.site_url}editor/templates/images/b_drop.png" alt="" border="0"/></a>
     
    </td>

    </tr>

{assign var="index_" value=$index_+1}
{foreachelse}

    <tr class="nodrag"><td colspan="5" style="padding:10px;text-align:center">
   {$lang.empty}
    </td></tr>   

{/foreach}

</TBODY>

</table>

 
 {* pagination *}
{include file="pagination.tpl"}