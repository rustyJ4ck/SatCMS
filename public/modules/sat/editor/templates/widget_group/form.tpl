{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes"}
    {$model = $return.form.data}
{/block}

{block "form-top"}
{/block}

{block 'form'}

<div class="form">

    <div class="form-block">
        <label>{$lang.title}*</label>
        
        <div><input class="form-control" type="text" name="title" size="60" value="{$model.title}"
                    data-rule-required="true"
                    /></div>
    </div>    
    
    <div class="form-block">
        <label>{$lang.name}</label>
        <div><input class="form-control" type="text" name="name" size="60"
                    value="{$model.name}"
                    data-popover="true"
                    data-content="{ldelim}satblock action='sat.widgets' name='%name'{rdelim}"
                    data-rule-regex="^[\w_]+$"
                    /></div>
    </div>   
    
    <div class="form-block">
        <label>класс</label>
        <div><input class="form-control" type="text" name="class" size="60" value="{$model.class}"/></div>
    </div>         
    
    <div class="form-block">
    <label>Описание</label>
    <div><textarea cols="60" rows="6" name="text" class="wysiwyg">{$model.text}</textarea></div>
    </div>
    
    {*
    <div class="form-bottom">
    <input class="form-control" class="main" name="item_submit" type="submit" value="{$lang.save}"/>
    </div>
    *}
    
  
</div>
                
{/block}

{block 'form-bottom'}

    {if $model.id}
        <input type="hidden" name="pid" value="{$model.pid}"/>
        <input type="hidden" name="site_id" value="{$model.site_id}"/>
    {else}
        <input type="hidden" name="pid" value="{$req.pid}"/>
        <input type="hidden" name="site_id" value="{$current.site.id}"/>
    {/if}

{/block}