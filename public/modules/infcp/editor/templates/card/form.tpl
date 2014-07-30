{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes"}
    {$model = $return.form.data}
{/block}

{block "form-top"}
{/block}

{block 'form'}

{*<form action="{$config.editor_url}" method="post" name="site_mod" id="frm_" class="_validable">*}


<div class="form">

    <div class="form-block">
        <label>
            {$lang.user}
        </label>
        <div>
            <input type="text"
                   class="form-control"
                   name="username" size="40" value="{$model.username}"/>
        </div>
    </div>

    <div class="form-block">
        <label>
            {$lang.text}
        </label>
        <div>
            <textarea
                    class="form-control"
                    cols="100" rows="12" name="text">{$model.text}</textarea>
        </div>
    </div>

</div>

{/block}
