{* Content template *}

{*
    @{$main_template}@
    !{$current.controller.mode}
    !{$ident_c}
    !{$tpl_ext}
*}

{if !empty($main_template)}

    {if '/' == $main_template|substr:0:1}
        {include "`$main_template|substr:1`"}
    {else}
        {include "./`$main_template`"}
    {/if}

{else}

    {* defualt model behavior *}
    {* smarty2: $modtpl_prefix *}
    {* smarty3: ./$ident_c *}

    {$action = $controller.mode}

    {if $action}
        {include "./`$req.c`/`$action`.tpl"}
    {else}
        {include "partials/error.tpl" message='action undefined'}
    {/if}



{/if}

