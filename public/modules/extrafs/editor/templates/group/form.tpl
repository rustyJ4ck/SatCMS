{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes, grid: yes, enctype: 'multipart/form-data'"}
    {*$model = $return.form.data*}
{/block}

{block 'form'}

    <div class="form">

        <div class="form-block">
            <label>{$lang.title}*</label>
            <div>
            <input type="text" name="title" size="60" value="{$model.title}"
                   class="form-control"
                   data-rule-required="true"
                    />
            </div>
        </div>

        <div class="form-block">
            <label>{$lang.name}*</label>
            <div>
            <input type="text"
                   class="form-control"
                   name="name" size="60" value="{$model.name}"
                   data-rule-required="true"
                  />
            </div>
        </div>

        <div class="form-block">
            <label>{$lang.description}</label>
            <div>
                <textarea
                        class="form-control"
                        cols="60" rows="5" name="text"
                        >{$model.description}</textarea>
            </div>
        </div>

    </div>

{/block}

{block 'form-bottom' append}
<input type="hidden" name="site_id" value="{if $model.id}{$model.site_id}{else}{$current.site.id}{/if}"/>
{/block}
 