{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: no, sortable: no"}
    {*$model     = $return.list.data*}

    {$btnEdit="dialog: yes, dialogTitle: Просмотр лога"}


{/block}
