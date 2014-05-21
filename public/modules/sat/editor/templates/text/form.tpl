{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes"}
    {$model = $return.form.data}
{/block}

{block "form-top"}
{/block}

{block 'form'}

<div class="form form100">

    <div class="form-block">
        <label>{$lang.title}*</label>
        <div>
        <input type="text"
               class="form-control"
               name="title" size="40" value="{$model.title}"
               data-rule-required="true" />
        </div>
    </div>     

    <div class="form-block">
        <label>{$lang.name}*</label>
        <div>
        <input type="text"
               class="form-control"
               name="name" size="40" value="{$model.name}"
               data-rule-required="true" />
        </div>
    </div> 
    
    <div class="form-block">
        <label>{$lang.text}</label>
        <div>
        <textarea class="form-control wysiwyg"
                  cols="100" rows="16"
                  name="text"
                  >{$model.text}</textarea>
        </div>
    </div> 

  
</div>


{/block}

{block 'form-bottom'}
<input type="hidden" name="pid" value="{if $model.id}{$model.site_id}{else}{$req.pid}{/if}"/>
{/block}