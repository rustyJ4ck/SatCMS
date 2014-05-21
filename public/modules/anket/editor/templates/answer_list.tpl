<h3>
Ответы для анкеты &laquo;<a href="?m=anket&c=form">{$current.anket_form.title}</a>&raquo;,
вопрос &laquo;<a href="?m=anket&c=question&pid={$current.anket_form.id}">{$current.anket_question.title}</a>&raquo;

</h3><br/><br/>
        
<table class="nice_borders" 
id="dnd_table" rel="{$config.base_url}">

<tr class="nodrag">

<!--<a href="index.php?c={$g_c}&op=new"><img src="templates/images/b_new.gif" alt="" border="0"/></a>   
-->



<th>

        <a class="dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=new&embed=yes&pid={$req.pid}"
            title="Добавить новый" 
            dialog="{ldelim}width:950,height:430{rdelim}"
        >        
        <img src="{$config.site_url}editor/templates/images/b_add_page.gif" alt="" border="0"/>
</a> 
</th> 

<th colspan="2">Ответ</th> 
<th>П</th> 

<th nowrap="nowrap" width="50">{$lang.ops}</th>
</tr>

<TBODY id="{$req.c}_table">

<!-- {if $smarty.foreach.out.index is odd}class="row1"{/if} -->

                                       
{assign var="index_" value="0"}
{foreach key=i name=out item=item from=$tpl_anket_answer}   

{* {$req.c}_item_ *}
    <tr rel="{$item.position}" rel_id="{$item.id}" id="{$req.c}_item_{$item.id}"
    {if $item.b_valid}class="backgreen"{else}{if $index_ is odd}class="row1"{/if}{/if}   >
      
    
    <td class="first">
    {$item.id}
    </td> 
    
    <td class="first">
    {$item.title|@escape} &nbsp;
    </td>        
    
    <td class="first">
    {$item.text|@escape} &nbsp;
    </td>  
    
    <td class="first">
        <input id="check_{$item.id}" type="checkbox" value="1" 
        {if $item.b_valid}checked="checked"{/if} 
        onclick="var c = $('#check_{$item.id}').is(':checked'); $.get('{$config.editor_url}?sef=false&m={$req.m}&c={$req.c}&do={$req.do}&op=toggle_valid&pid={$item.pid}&id={$item.id}&to=' + c); $.tf_message('Статус изменен', c);$(this).parents('tr').toggleClass('backgreen');" />
    </td>      

    <td class="last" >

      <a class="dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}&pid={$req.pid}&embed=yes"
            title="Изменение элемента" 
            dialog="{ldelim}width:950,height:430{rdelim}"
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

 