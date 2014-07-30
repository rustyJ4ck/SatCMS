{if $message}{include file="partials/flash.tpl"}{/if}
{if $main_template}{include file="partials/`$main_template`"}{/if}

{$return.content}
