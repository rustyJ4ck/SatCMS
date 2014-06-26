{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes"}
    {$model = $return.form.data}
{/block}

{block 'form'}

<div class="form">

    <div class="form-block">
        <label>
            Категория
        </label><div>

            {if $model.id}{$pid = $model.type_id}{else}{$pid = $req.type_id}{/if}

            {control type="select"
                value=$pid
                name="type_id"
                src=$controller.types
            }

        </div>
    </div>

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
            {$lang.url}
        </label>
        <div>
            <input class="form-control" type="text"
                   name="slug" size="40"
                   value="{$model.slug}"/>
        </div>  
    </div> 
  
  
</div>

{/block}

{block 'form-bottom'}
    <input type="hidden" name="site_id" value="{if $model.id}{$model.site_id}{else}{$current.site.id}{/if}"/>
{/block}