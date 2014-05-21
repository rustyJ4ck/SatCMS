{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: no, sortable: no"}
    {*$model     = $tpl_sat_comment*}
    {$btnNew    = "dialog: yes"}
    {$btnEdit   = "dialog: yes"}

{/block}

