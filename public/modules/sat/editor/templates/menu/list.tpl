{*$config.base_url}"*}

{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: yes, sortable: yes"}
    {*$model     = $return.list.data*}
    {$btnNew    = "dialog: yes"}
    {$btnEdit   = "dialog: yes"}
    {$actionUrlExtra = "pid=`$req.pid`"}

{/block}

{block 'filter-controls' prepend}

    {if $current.menu}
    <a type="button" class="btn btn-info btn-sm"
       href="{$current.menu.urls.self}">
        <span class="glyphicon glyphicon-share-alt"></span> Родитель: {$current.menu.title}
    </a>
    {/if}

{/block}



{*
{include "widgets/model/filter.tpl"
    params="new: yes"
    model=$tpl_sat_menu
    btnNew="dialog:yes"
}

{include "widgets/model/list.tpl"
    params="sortable: yes"
    model=$tpl_sat_menu
    class="table-sortable"
    btnEdit="dialog:yes"
}
*}