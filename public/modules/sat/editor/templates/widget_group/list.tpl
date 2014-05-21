{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: yes"}
    {*$model     = $tpl_sat_widget_group*}
    {$btnNew    = "dialog: yes"}
    {$btnEdit   = "dialog: yes"}
    {$actionUrlExtra = "pid=`$req.pid`"}

{/block}

{block 'filter-controls' prepend}

    {if $current.menu}
        <a type="button" class="btn btn-info btn-sm"
           href="{$current.menu.urls.self}">
            <span class="glyphicon glyphicon-share-alt"></span> {$current.menu.title}
        </a>
    {/if}

{/block}


{block "grid-bottom" append}
<filter name="site_id">{$current.site.id}</filter>
{/block}



{*
{if $current.menu}
Элементы меню &laquo;<a href="?m=sat&c=menu&pid={$current.menu.pid}">{if $current.menu.title}{$current.menu.title}{else}{$current.menu.name}*{/if}</a>&raquo;
<br/><br/>
{/if}     

        
<table class="nice_borders" 
id="dnd_table1" rel="{$config.base_url}">

<tr class="nodrag">

<!--<a href="index.php?c={$g_c}&op=new"><img src="templates/images/b_new.gif" alt="" border="0"/></a>   
-->


<th>

        <a class="dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=new&embed=yes&pid={$req.pid}"
            title="Добавить новый" 
            dialog="{ldelim}width:840,height:470{rdelim}"
        >        
        <img src="{$config.site_url}editor/templates/images/b_add_page.gif" alt="" border="0"/>
</a> 
</th> 

<th>{$lang.title}</th>
<th>{$lang.name}</th>

<th>Виджеты</th>  

<th nowrap="nowrap" width="50">{$lang.ops}</th>
</tr>

<TBODY id="{$req.c}_table">

<!-- {if $smarty.foreach.out.index is odd}class="row1"{/if} -->

                                       
{assign var="index_" value="0"}
{foreach key=i name=out item=item from=$tpl_sat_widget_group}   

    <tr rel="{$item.position}" rel_id="{$item.id}" id="{$req.c}_item_{$item.id}"
    {if $item.b_is_global}class="row_red"{else}{if $index_ is odd}class="row1"{/if}{/if}   >

    <td class="first">
    {$item.id}
    </td>    
    
    <td class="first">
    {$item.title|escape}&nbsp;
    </td>          

    <td class="first">
    {$item.name|escape}&nbsp;
    </td>      
    
    <td class="first">
    <a href="?m=sat&c=widget&pid={$item.id}">Виджеты</a> &nbsp; 
    </td> 
    
    <td class="last" >

      <a class="dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}&embed=yes&pid={$req.pid}"
            title="Изменение элемента" 
            dialog="{ldelim}width:840,height:470{rdelim}"
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