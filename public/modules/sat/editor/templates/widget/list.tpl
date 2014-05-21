{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: yes"}
    {*$model     = $tpl_sat_widget*}
    {$btnNew    = "dialog: yes"}
    {$btnEdit   = "dialog: yes"}
    {$actionUrlExtra = "pid=`$req.pid`"}

{/block}

{block 'filter-controls' prepend}

    <a type="button"
       class="btn btn-info btn-sm"
       href="?m=sat&c=widget_group">
        <span class="glyphicon glyphicon-share-alt"></span>&nbsp;Группа: {$controller.parent.title}
    </a>

{/block}



{*
{if $current.parent}
Виджеты для группы &laquo;<a href="?m=sat&c=widget_group">{$current.parent.title}</a>&raquo;
<br/><br/>
{/if}     

        
<table class="nice_borders" 
id="dnd_table" rel="{$config.base_url}">

<tr class="nodrag">

<!--<a href="index.php?c={$g_c}&op=new"><img src="templates/images/b_new.gif" alt="" border="0"/></a>   
-->

<th>{$lang.active}</th>

<th>

        <a class="dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=new&embed=yes&pid={$req.pid}"
            title="Добавить новый" 
            dialog="{ldelim}width:840,height:500{rdelim}"
        >        
        <img src="{$config.site_url}editor/templates/images/b_add_page.gif" alt="" border="0"/>
</a> 
</th> 

<th>{$lang.title}</th>
<th>Plain</th>
<th>Raw</th>
 

<th nowrap="nowrap" width="50">{$lang.ops}</th>
</tr>

<TBODY id="{$req.c}_table">

{assign var="index_" value="0"}
{foreach key=i name=out item=item from=$tpl_sat_widget}   

    <tr rel="{$item.position}" rel_id="{$item.id}" id="{$req.c}_item_{$item.id}"
    {if $item.b_is_global}class="row_red"{else}{if $index_ is odd}class="row1"{/if}{/if}   >

    <td class="first" align="center">
        <input id="check_{$item.id}" type="checkbox" value="1" 
        {if $item.active}checked="checked"{/if} 
        onclick="var c = $('#check_{$item.id}').is(':checked'); $.get('{$config.editor_url}?sef=false&m={$req.m}&c={$req.c}&do={$req.do}&op=active&id={$item.id}&to=' + c); $.tf_message('Статус изменен', c);" />
    </td>      
    
    <td class="first">
    {$item.id}
    </td>    
    
    <td class="first">
    {if $item.title}{$item.title}{else}{$item.name}*{/if} &nbsp;
    </td>    
    
    <td align="center">
    {if $item.plain}&bull;{/if}
    </td>      

    <td align="center">
    {if $item.raw}&bull;{/if} 
    </td>      
    
            
    <td class="last" >

      <a class="dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}&embed=yes&pid={$req.pid}"
            title="Изменение элемента" 
            dialog="{ldelim}width:840,height:500{rdelim}"
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

 *}