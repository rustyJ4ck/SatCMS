{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: no"}
    {$model = $return.form.data}
{/block}

{block 'form'}

    <div class="form">

        <div class="form-block">
            <label>{$lang.title}*</label>
            <div><input type="text" name="title" size="40"
                        class="form-control"
                        value="{$model.title}"
                        data-rule-required="true"
                        /></div>
        </div>

        <div class="form-block">
            <label>{$lang.name}</label>
            <div><input type="text"
                        class="form-control"
                        name="name" size="40" value="{$model.name}"/></div>
        </div>

        {include "./form.acls.tpl"}

    </div>

{/block}