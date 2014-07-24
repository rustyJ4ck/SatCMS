{*
    $actionUrlExtra = '&pid=1'

    model = {data: [items], fields: [vf]}

    model => Array (13)
      id => Array (2)
        type => "numeric"
        autoincrement => true
      title => Array (2)
        type => "text"
        size => 127
*}

{*$model|debug_print_var*}

{* extra url postfix *}

{if !$inGrid}
    {if $params}{$params = $params|to_array}{/if}
    {if !empty($btnEdit)}{$btnEdit = $btnEdit|to_array}{/if}
    {if !empty($actionUrlExtra)}{$actionUrlExtra = "&`$actionUrlExtra`"}{/if}
{/if}

{if $list}{$list = $list|to_array}{/if}

{*$buttons|debug_print_var*}

{if $config.debug}
    {$btnNew|debug_print_var}
    {$btnEdit|debug_print_var}
{/if}

{$i18n = $i18n|default:""}
{$fields_count = 2}

{$ignoredFields = ['id', 'position']}

{*ngcontroller*}
{if !empty($list.controller)}
<section {if !$inGrid}class="compilable"{/if}><div ng-controller="{$list.controller}">
{/if}

{$page = "&start=`$req.start`"}

{block 'list-top' hide}
{/block}

<div class="box model-data">

{*
<div class="box-header">
    <span class="title">Here be your tasks</span>
    <ul class="box-toolbar">
        <li><span class="label label-green">2 left</span></li>
    </ul>
</div>
*}

<div class="box-content">

    <div class="grid-blocks">

    {block 'list-content'}

        {foreach $model.data as $i => $item}

            <div class="col-xs-4" style="height:100px;">
                <div class="padded label-info">
                <a class="pull-left" href="#">
                    {$item.title}
                </a>
                </div>
            </div>


        {/foreach}


    {/block}

    </div>



</div>

{* pager *}

{if !empty($return.list.pagination.count) || $list.footer !== 0}

        {include 'partials/pagination.tpl' pagination=$return.list.pagination}

{/if}

</div>
</div>


{block 'list-bottom' hide}
{/block}


{if !empty($list.controller)}
</div></section>
{/if}
