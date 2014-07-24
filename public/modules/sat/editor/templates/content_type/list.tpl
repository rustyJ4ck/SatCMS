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


{*block 'list-content'}

    <style>
        .list-block > div > a {
            display:block;
            height:100px;
            text-align:center;
            width:100%;
        }
    </style>

    {foreach $model.data as $i => $item}

        <div class="list-block col-xs-2" style="height:100px;">
            <div>
                <a class="btn btn-default" href="#">
                    {$item.title}
                </a>
            </div>
        </div>


    {/foreach}


{/block*}




{block "grid-bottom" append}
    <filter name="site_id">{$current.site.id}</filter>
{/block}

