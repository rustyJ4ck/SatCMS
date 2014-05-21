{extends 'widgets/model/grid.tpl'}

{*
    controller.groups
    controller.group
*}

{block 'params' append}

    {$params    = "new: yes, sortable: yes"}
    {*$model     = $tpl_extrafs_field*}
    {$btnNew    = "dialog: yes"}
    {$btnEdit   = "dialog: yes"}
    {$actionUrlExtra = "gid=`$req.gid`"}

{/block}


{block 'filter-controls' prepend}

        <a type="button"
           class="btn btn-info btn-sm"
           href="?m=extrafs&c=group">
           <span class="glyphicon glyphicon-share-alt"></span>&nbsp;Группа: {$controller.group.title}
        </a>

{/block}
 
 
{* type-defined templates *}
{*
<script type="text/javascript" src="/modules/extrafs/editor/templates/fields.js"></script>
*}
