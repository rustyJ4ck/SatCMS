{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: yes, sortable: yes"}
    {*$model     = $return.list.collection*}
    {$btnNew    = "dialog: yes"}
    {$btnEdit   = "dialog: yes"}
    {$actionUrlExtra = "pid=`$req.pid`&sid=`$req.sid`&ctype_id=`$req.ctype_id`"}

{/block}

{block "grid-bottom" append}
    <filter name="sid">{$req.sid}</filter>
    <filter name="ctype_id">{$req.ctype_id}</filter>
{/block}


