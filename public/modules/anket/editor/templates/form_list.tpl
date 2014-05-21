
        
<table class="nice_borders" 
id="dnd_table" rel="{$config.base_url}">

<tr class="nodrag">

<!--<a href="index.php?c={$g_c}&op=new"><img src="templates/images/b_new.gif" alt="" border="0"/></a>   
-->



<th>

        <a class="dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=new&embed=yes"
            title="Добавить новый" 
            dialog="{ldelim}width:930,height:430{rdelim}"
        >        
        <img src="{$config.site_url}editor/templates/images/b_add_page.gif" alt="" border="0"/>
</a> 
</th> 

<th>{$lang.title}</th>
<th>Вопросы</th> 
<th>Типы результатов</th>
<th>Результаты</th> 

<th nowrap="nowrap" width="50">{$lang.ops}</th>
</tr>

<TBODY id="{$req.c}_table">

<!-- {if $smarty.foreach.out.index is odd}class="row1"{/if} -->

                                       
{assign var="index_" value="0"}
{foreach key=i name=out item=item from=$tpl_anket_form}   

{* {$req.c}_item_ *}
    <tr rel="{$item.position}" rel_id="{$item.id}" id="{$req.c}_item_{$item.id}"
    {if $item.b_is_global}class="row_red"{else}{if $index_ is odd}class="row1"{/if}{/if}   >
      
    
    <td class="first">
    {$item.id}
    </td>    
    
    <td class="first">
    {$item.title} &nbsp;
    </td>    

    <td>
    <a href="?m=anket&c=question&pid={$item.id}">Вопросы</a>
    </td>  

    <td>
    <a href="?m=anket&c=result_option&pid={$item.id}">Типы результатов</a>
    </td>      
    
    <td class="backlight">
    <a href="?m=anket&c=result&pid={$item.id}">Результаты</a>
    </td>  
    
    <td class="last" >

      <a class="dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}&embed=yes"
            title="Изменение элемента" 
            dialog="{ldelim}width:930,height:430{rdelim}"
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

 