{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes, controls: no"}
{/block}

{block 'form'}

    {$model = $return.form.data}

    <b class="">{$model.title} {if $model.error}{$lang.error}{/if}</b><br/>

    <code>

       {$model|debug_print_var}

    </code>
                
{/block}

