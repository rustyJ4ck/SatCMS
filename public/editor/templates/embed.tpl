{*
    @{$params.c}
    @{$exception|debug_print_var}
*}

{if !empty($exception)}

    {if $config.in_ajax}
        Exception: {$exception->getMessage()}
    {else}
        <label class="label label-danger">Exception: {$exception->getMessage()}</label>
    {/if}

{/if}

{if $config.in_ajax}
    {*fix embed*}
    {$main_template = false}
{/if}


{if $req.m}
    {*@todo page template*}
    {$view = "`$modtpl_prefix`index.tpl"}
    {include $view}
{else}

    @loading

{/if}
