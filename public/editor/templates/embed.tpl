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


{*fix embed*}
{if $config.in_ajax && $main_template == 'embed.tpl'}
    {$main_template = false}
{/if}


{if $req.m}
    {*@todo page template*}
    {$view = "`$modtpl_prefix`index.tpl"}
    {include $view}
{else}

    @loading

{/if}
