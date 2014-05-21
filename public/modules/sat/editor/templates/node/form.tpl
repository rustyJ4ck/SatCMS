{* node *}

{$entity = $return.form}
{$model = $return.form.data}

{if $req.op == 'new'}


    {include "./form.new.tpl"}


{else}

    {include "./form.full.tpl"}

{/if}




