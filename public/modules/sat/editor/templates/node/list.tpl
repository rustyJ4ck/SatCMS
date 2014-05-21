{capture assign=filterControls}
    <a type="button" class="btn btn-info btn-sm"
       ng-disabled="isLoading"
       ng-click="showVisual({$req.pid})"
    >
        <span class="glyphicon glyphicon-share"></span> Visual
    </a>
{/capture}

{include "widgets/model/filter.tpl"
    params="new: yes, sortable: yes"
    btnNew="title: 'Добавить страницу', dialog: yes, dialogTitle: 'Добавить страницу'"
    btnEdit="title: 'Правка страницы', dialog: no"
    model=$return.list.collection
    actionUrlExtra = "pid=`$req.pid`"
    gridName='grid-nodes'
    filter="controller: nodeListFilterController"
}


{*$current|debug_print_var*}

{include "./list.nav.tpl" currentID=$req.pid}

{*

<div class="panel panel-default">
    <div class="panel-body">

        <a class="btn btn-default dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&pid={$req.pid}&op=new&embed=yes"
            data-title="Добавить материал"
            dialog="{ldelim}width:980,height:360{rdelim}"
        >        
        <span class="glyphicon glyphicon-plus"></span>
        Создать страницу
        </a> 

        

         <a class="btn btn-default dialog a-delete"
            href="index.php?m={$req.m}&c={$req.c}&op=drop_all&pid={$req.pid}"
            onclick="$('.pagination').hide();$('#{$req.c}_table').empty()"
         >
         <span class="glyphicon glyphicon-trash"></span>
         Удалить все
         </a> 

        
        {if NOT $req.pid}

         <a class="btn btn-default" href="?m=sat&c=text&pid={$current_site.id}"
            o1nclick="sat_text_attach_click(); return false;"
         >

         <span class="glyphicon glyphicon-list-alt"></span>
         Тексты для сайта
         </a> 

        {/if}

    </div>
</div>

*}

<div class="box model-data">

    {*
    <div class="box-header">
    </div>
    *}

    <div class="box-content">

        {include "./list.table.tpl"}

    </div>



</div>


{*

<script>

require([], function(){

    // attach images
    var $f;
    var _sat_text_attach_url = '?m=sat&c=text&pid={$current_site.id}&embed=yes';

    function sat_text_attach_click() {
        if (!$f) {
            $f = $('<div><iframe style="width:777px;height:530px" frameborder="0" src="' + _sat_text_attach_url + '"></iframe></div>');
            $f.dialog({ title:'Тексты для сайта', resizable:false,modal:true,width:800,height:600,position:[30,30] });
        }
        $f.dialog('open');
    }

});
</script>

        *}