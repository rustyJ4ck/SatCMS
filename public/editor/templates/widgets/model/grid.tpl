{block "params"}
    {$gridName = $gridName|default:$controller.grid_name}
    {$inGrid = true}

    {* if model not set, try global filter result collection *}
    {if !isset($model) && isset($return.list)}
        {$model = $return.list.collection}
    {/if}

{/block}

{if $params}{$params = $params|to_array}{/if}
{if $btnNew}{$btnNew = $btnNew|to_array}{/if}
{if $btnEdit}{$btnEdit = $btnEdit|to_array}{/if}
{if !empty($actionUrlExtra)}{$actionUrlExtra = "&`$actionUrlExtra`"}{/if}

<section class="compilable">

<div grid-widget
     data-source="{$config.base_url}"
     id="{$gridName}"
     {*@todo: make grid unique ID*}
>

    {block "filter"}
        {include "./filter.tpl"}
    {/block}

    {block "list"}
        {if $params.list == 'blocks'}
            {include "./list.blocks.tpl"}
        {else}
            {include "./list.tpl"}
        {/if}
    {/block}

    {block "grid-bottom"}

        {if $config.debug}
        <label class="label label-info">GRID#[[grid.id]]#[[testValue]]</label>
        {/if}

        <filter name="c">{$req.c}</filter>
        <filter name="do">{$req.do}</filter>
        <filter name="m">{$req.m}</filter>
        <filter name="pid">{$req.pid}</filter>
        <filter name="gid">{$req.gid}</filter>
        <filter name="embed">yes</filter>
        <filter name="with_ajax">1</filter>
    {/block}

</div>
</section>
