{*included from module/index ->  partials/content*}
<div class="padded label-danger text-white">ERROR: {$message}
{if $return.message}{$return.message} {$return.code}{/if}
</div>