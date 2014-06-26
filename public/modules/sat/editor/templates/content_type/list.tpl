{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: yes, list: blocks1"}
    {$btnNew    = "dialog: yes"}
    {$btnEdit   = "dialog: yes"}


{/block}


{block 'filter-controls' prepend}

    <a type="button" class="btn btn-info btn-sm"
       href="?m={$req.m}&c=news">
        <span class="glyphicon glyphicon-share-alt"></span> Content Types
    </a>

{/block}

{block "grid-bottom" append}
    <filter name="site_id">{$current.site.id}</filter>
{/block}

