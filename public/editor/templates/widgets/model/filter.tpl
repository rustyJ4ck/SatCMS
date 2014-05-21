{*

include params="{new:true, title:'title'}" model=$tpl_data

<code>
{$params|@debug_print_var}
{$btnNew|@debug_print_var}
</code>

    !!!<test123>@test</test123>
    <i test>@i-test</i>!!!

*}

{if !$inGrid}
  {if $params}{$params = $params|to_array}{/if}
  {if $btnNew}{$btnNew = $btnNew|to_array}{/if}
  {if !empty($actionUrlExtra)}{$actionUrlExtra = "&`$actionUrlExtra`"}{/if}
{/if}

{if $filter}{$filter = $filter|to_array}{/if}

<section {if !$inGrid || $filter.controller}class="compilable"{/if}>

{if  $filter.controller}
    <div ng-controller="{$filter.controller}">
{/if}

{if $filter.captions !== 0}

<div class="box">
    <div class="box-header">
        <span class="title">{$params.filterTitle|default:"Фильтр"}</span>
        <ul class="box-toolbar">
            {block "filter-toolbar" hide}
                <li></li>
            {/block}
        </ul>
    </div>

{else}
    <div>
{/if}

    {block "filter-filters" hide}
    <div class="grid-filters box-content padded-top btn-group-sm clearfix">
    {$smarty.block.child}
    </div>
    {/block}

    <div class="{if $filter.captions !== 0}box-content padded{else}padded-bottom{/if} btn-group-sm">

        {block "filter-controls"}

{*
            <a type="button" class="btn btn-default btn-sm"
                ng-click="clickTest()">
                <span class="glyphicon glyphicon-refresh"></span> test
            </a>
*}

            <a type="button" class="btn btn-default btn-sm"
               ng-disabled="isLoading"
               ng-click="{if $inGrid}grid.reload(){else}reload(){/if}"
            >
                <span class="glyphicon glyphicon-refresh"></span> {if $filter.captions !== 0}Reload{/if}
            </a>

            {if !empty($params.reset)}
                <a type="button" class="btn btn-default btn-sm"
                   data-popover="true" data-placement="top"
                   data-content="Reset content filter" data-container=".box"
                   ng-click="grid.reset()"
                >
                    <span class="glyphicon glyphicon-retweet"></span>
                </a>
            {/if}


            {if !empty($params.new)}
            <a type="button" class="btn btn-primary btn-sm dialog"
               data-title="{$btnNew.dialogTitle|default:"Добавить новый элемент"}"
               href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&pid={$req.pid}{$actionUrlExtra}&op=new&embed=yes"
               {if $btnNew.dialog}dialog="{$btnNew.dialog}"{/if}
                    >
                <span class="glyphicon glyphicon-plus"></span> {if $filter.captions !== 0}{$btnNew.title|default:"Добавить"}{/if}
            </a>
            {/if}

        {/block}


        {*deprecated*}
        {$filterControls}

        {if count($model.data)}

            <span class="pull-right">

                <a class="btn btn-default btn-sm a-delete-selected"
                   data-href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=drop_selected{$actionUrlExtra}"
                   data-source="[name={$gridName}_id]:checked"
                        ><span class="glyphicon glyphicon-remove"></span> {if $filter.captions !== 0}Удалить отмеченные{/if}
                </a>

                <a class="btn btn-danger btn-sm a-delete"
                   data-title="Удалить все записи?"
                   data-target=".model-data"
                   href=""
                   data-href="?m={$req.m}&c={$req.c}&op=drop_all{$actionUrlExtra}"
                        ><span class="glyphicon glyphicon-trash"></span>
                </a>

            </span>

        {/if}

    </div>

</div>

{if $description}
<div class="padded-hor help">{$description}</div>
{/if}

{if  $filter.controller}
</div>
{/if}

</section>