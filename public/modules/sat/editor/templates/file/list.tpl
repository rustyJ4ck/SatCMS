{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: yes, sortable: no"}
    {$filter    = "captions: no"}
    {$list      = "footer: no"}

    {*$model     = $tpl_sat_file*}

    {$btnNew    = "dialog: yes"}
    {$btnEdit   = "dialog: yes"}

    {$actionUrlExtra = "pid=`$req.pid`&sid=`$req.sid`&ctype_id=`$req.ctype_id`"}

{/block}

{*block 'list-top'}
    <code>
        {$attach_sid}
        @{$req|debug_print_var}@
    </code>
{/block*}


{block "grid-bottom" append}
    <filter name="sid">{$req.sid}</filter>
    <filter name="ctype_id">{$req.ctype_id}</filter>
{/block}