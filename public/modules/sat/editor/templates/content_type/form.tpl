{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes"}
    {$model = $return.form.data}
{/block}

{block 'form'}

<div class="form">

    <div class="form-block">
        <label>{$lang.title}*</label>
        <div><input class="form-control" type="text"
                    name="title" size="60"
                    value="{$model.title}"
                    data-rule-required="true"
                    /></div>
    </div>

    <div class="form-block">       
        <label>
            {$lang.slug}
        </label>
        <div>
            <input class="form-control" type="text"
                   name="slug" size="40"
                   value="{$model.slug}"/>
        </div>  
    </div>


    <div class="form-block">
        <label>
            ExtraIDs
        </label>
        <div>
            {*test-ids*}

            <div class="control-group">

            <input class="form-control"
                   type="checkbox"
                   name="extra_fields[2]" value="2"
                   {if !empty($model.extra_fields[2])}checked="checked"{/if}
            />
                <label for="extra_fields[2]" class="control-label">@2</label>
            </div>

            <div class="control-group">

            <input class="form-control"
                   type="checkbox"
                   name="extra_fields[1]" value="1"
                   {if !empty($model.extra_fields[1])}checked="checked"{/if}
            />

                <label for="extra_fields[1]" class="control-label">@1</label>

            </div>

        </div>
    </div>

</div>

{/block}

{block 'form-bottom'}
    <input type="hidden" name="site_id" value="{if $model.id}{$model.site_id}{else}{$current.site.id}{/if}"/>
{/block}