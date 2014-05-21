{*$current.site.id*}


{include "widgets/model/filter.tpl"
    params="new: yes"
    btnNew="title: 'Добавить сайт', dialog: no"
    model=$return.list.collection
}

{*  btnNew="title: 'Hello', dialog: 'modal-xxl' *}

{*if $return.packet_result}
<script>
$.tf_message('Пакетно добавлено {$return.packet_result[0]}, пропущено {$return.packet_result[1]} записей');
</script>
{/if


*}


{include "widgets/model/list.tpl"
    buttons=['foo' => 'bar', 'sub' => [1, 2, 3]]
    params="edit: yes, remove: yes"
    btnEdit="dialogTitle: 'Правка сайта', dialog: no"
    model=$return.list.collection
}


{*

<table class="nice_borders" rel="{$config.base_url}">

<tr class="nodrag">

<th>
ID
</th> 

<th>{$lang.title}</th>
<th>Домен</th> 
<th>Шаблон</th> 
<th>Контент</th>
<th>Акт</th>

<th>{$lang.ops}</th>
</tr>

<TBODY id="{$req.c}_table">

<!-- {if $smarty.foreach.out.index is odd}class="row1"{/if} -->

                                       
{assign var="index_" value="0"}
{foreach key=i name=out item=item from=$tpl_sat_site.data}

    <tr id="{$item.id}" {if $item.b_is_global}class="row_red"{else}{if $index_ is odd}class="row1"{/if}{/if}   >

    <td>
    {$item.id}
    </td>    

    <td>
    {$item.title} &nbsp;
    </td>    

    <td>
    <a href="{$item.urls.self}" target="_blank">{$item.domain}</a> &nbsp; 
    </td>     
    
    <td>
    {if !$item.template}по-умолчанию{else}
    {foreach key=ti item=titem from=$sat_templates}       
    {if $ti == $item.template}{$titem}{/if}
    {/foreach}
    {/if}
    </td>   

    <td>
    <a href="?m=sat&c=node&site_id={$item.id}">Контент</a> &nbsp;
    </td>     

 
    <td>   
    <input name="plchoose" type="radio"  rel="{$item.id}" href="javascript:;" class="plchoose" title="{$item.domain}"/>
    &nbsp;
    </td>   
  

                
    <td class="btn-group-xs" >


        <a type="button" class="btn btn-default btn-sm"
           href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}" rel="&embed=yes"
            dialog="{ldelim}width:540,height:380{rdelim}"
         >                 
            <span class="glyphicon glyphicon-pencil"></span> Правка
        </a>

        <a type="button" class="btn btn-danger btn-sm a_delete tip"
        title="Внимание! Удалится весь контент сайта"
            href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=drop&id={$item.id}">
            <span class="glyphicon glyphicon-trash"></span> Удалить
        </a>
     
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

*}

{*include file="`$modtpl_prefix`_packet`$tpl_ext`"*}

{*

<script>
{literal}

        console.warn('nodes=append ', typeof define, typeof require);

        define(['../../../../.'], function($){

    var c = $.cookie('site_id');
    
    if (c) {
        $('.plchoose[rel='+c+']').click();            
    }              

    $('.plchoose').click(function(){
                  $.cookie('site_id', $(this).attr('rel'), {expires:356, path:'/editor/'});
                  $.tf_message('Выбран сайт &laquo;' + $(this).attr('title') + '&raquo;');
    });

        });



{/literal}
</script>


*}