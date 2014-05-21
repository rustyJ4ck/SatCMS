
        
<table class="nice_borders" 
 rel="{$config.base_url}">

<tr class="nodrag">

<!--<a href="index.php?c={$g_c}&op=new"><img src="templates/images/b_new.gif" alt="" border="0"/></a>   
-->



<th>

        <a class="dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=new&embed=yes&pid={$req.pid}"
            title="Добавить новый" 
            dialog="{ldelim}width:940,height:530{rdelim}"
        >        
        <img src="{$config.site_url}editor/templates/images/b_add_page.gif" alt="" border="0"/>
</a> 
</th> 

<th>Вопрос</th>
<th>Дата</th> 
<th>Пользователь</th> 
<th>Ответы</th>  

<th nowrap="nowrap" width="50">{$lang.ops}</th>
</tr>

<TBODY id="{$req.c}_table">

<!-- {if $smarty.foreach.out.index is odd}class="row1"{/if} -->

                                       
{assign var="index_" value="0"}
{foreach key=i name=out item=item from=$tpl_contacts_qa_question}   

{* {$req.c}_item_ *}
    <tr rel="{$item.position}" rel_id="{$item.id}" id="{$req.c}_item_{$item.id}"
    {if $item.b_is_global}class="row_red"{else}{if $index_ is odd}class="row1"{/if}{/if}   >
      
    
    <td class="first" align="center">
        <input id="check_{$item.id}" type="checkbox" value="1" 
        {if $item.active}checked="checked"{/if} 
        onclick="$.get('{$config.editor_url}?sef=false&m={$req.m}&c={$req.c}&do={$req.do}&op=active&id={$item.id}&to=' + $('#check_{$item.id}').is(':checked'));$.tf_message('Статус изменен');" />
    </td>    
    
    <td class="first">
    {$item.title|escape} &nbsp;
    </td>    
    
    <td align="center" class="help">
    {$item.date}&nbsp;
    </td>
    
    <td class="first">
    {$item.username|escape} &nbsp;
    </td>       
    

    <td nowrap="nowrap">
    
    <a href="?m=contacts&c=qa_answer&pid={$item.id}">Ответы{if $item.c_count} ({$item.c_count}){/if}</a>
    
    </td>  
     

    <td class="last" >

      <a class="dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}&pid={$req.pid}&embed=yes"
            title="Изменение элемента" 
            dialog="{ldelim}width:940,height:530{rdelim}"
      >        
     <img src="{$config.site_url}editor/templates/images/b_edit.png" alt="" border="0"/>
     </a>    
       
    &nbsp;

    <a class="a_delete" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=drop&id={$item.id}&pid={$req.pid}"><img src="{$config.site_url}editor/templates/images/b_drop.png" alt="" border="0"/></a>
     
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

 