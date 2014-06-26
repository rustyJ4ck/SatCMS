{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes, controller: extrafsFieldsFormController"}
    {$dialog = true}
    {$model = $return.form.data}
{/block}

{block 'form'}

    {*prepare saved data*}
    {if !empty($model.value)}
        {foreach $model.value as $ik => $iv}
        <section ng-init="value.{$ik} = '{$iv|htmlspecialchars|escape:javascript}'"></section>
        {/foreach}
    {/if}

    {*
    <span>@[[value]]</span>
    <a class="btn btn-default" href="" ng-click="test()">&*******************</a>
    *}

    <div class="form">

    <div class="form-block">
        <label>Группа</label>
        <div>
            <span class="label label-info">
               {$controller.group.title}
            </span>
        </div>
    </div>

    <div class="form-block">
        <label>{$lang.title}* [[fieldTest]]</label>
        <div><input class="form-control"
                    type="text" name="title" size="50" value="{$model.title}"
                    data-rule-required="true"
                    /></div>
    </div>    


    <div class="form-block">
        <label>{$lang.name}*</label>
        <div><input class="form-control"
                    type="text" name="name" size="50" value="{$model.name}"
                    data-rule-required="true"
                    /></div>
    </div> 
    
  <div class="form-block">
        <label>{$lang.description}</label>
        <div><textarea class="form-control"
                       cols="50" rows="2"
                       name="description"
                    >{$model.description}</textarea></div>
    </div>     
    
    <div class="form-block">
        <label>Тип поля</label>
        <div>
            <select name="type">
            {foreach from=$controller.field_types item=ct key=cid}
            <option value="{$cid}" data-type="{$ct}"
            {if $model.type == $cid}selected="selected"{/if}
            >{$ct}</option>
            {/foreach}
            </select>
        </div>  
    </div>  
    
    <div class="form-block">
        <label>CSS класс </label>
        <div><input class="form-control"
                    type="text" name="class" size="50" value="{$model.class}"/></div>
    </div>     
    
    {*
    <div class="form-block" class="hidden">
        <label>Значение</label>
        <textarea cols="60" rows="6" name="value">{$model.value}</textarea>
    </div>       *}
    
    <div class="extrafs-container">
    </div>

    {*
    <div class="form-block">
        <label>{$lang.description}</label>
        <textarea cols="80" rows="6" name="text" class="wysiwyg">{$model.description}</textarea>
    </div> 
    *}


</div>
                
    <input type="hidden" name="gid" value="{if $model.id}{$model.gid}{else}{$req.gid}{/if}"/>

{/block}
 
{* type-defined templates

<script>

{literal}

var _extra_fs_$model = {};   

{/literal}

// prepare edit data
{if NOT EMPTY($model.value)}
    {foreach from=$model.value item=iv key=ik}
    _extra_fs_$model.{$ik} = '{$iv|htmlspecialchars|escape:javascript}';
    {/foreach}
{/if}      

$('select[name=type]').trigger('change');
  
</script>

*}