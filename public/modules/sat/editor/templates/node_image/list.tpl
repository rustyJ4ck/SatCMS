{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: yes, sortable: yes"}
    {*$model     = $tpl_sat_node_image*}
    {$btnNew    = "dialog: yes"}
    {$btnEdit   = "dialog: yes"}
    {$actionUrlExtra = "pid=`$req.pid`&sid=`$req.sid`&ctype_id=`$req.ctype_id`"}

{/block}

{block "grid-bottom" append}
    <filter name="sid">{$req.sid}</filter>
    <filter name="ctype_id">{$req.ctype_id}</filter>
{/block}

{*block 'list' prepend}

    {$params    = "new: 1"}
    {$model     = $tpl_mail_tpl}
    {$class     = "table-sortable"}
    {$active    = 1}
    {$btnEdit   = "dialog: yes"}

{/block*}

{*
{include "widgets/model/filter.tpl"
    params="new: 1, ddd: 2, ddd: 3"
    model=$tpl_mail_tpl
    btnNew="dialog: yes"
}

{include "widgets/model/list.tpl"
    params="new: 1"
    model=$tpl_mail_tpl
    class="table-sortable"
    active=1
    btnEdit="dialog: yes"
}


*}


