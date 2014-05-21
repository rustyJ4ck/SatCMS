{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: yes, sortable: yes"}
    {*$model     = $tpl_extrafs_group*}
    {$btnNew    = "dialog: yes"}
    {$btnEdit   = "dialog: yes"}

{/block}


{*block 'filter-content'}
    <a class="btn btn-default">CHILD!!</a>
{/block*}