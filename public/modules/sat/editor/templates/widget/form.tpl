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
                    data-popover="true"
                    data-content="{ldelim}satblock action='sat.widget' name='%name'{rdelim} <i class=text-danger>Не реализовано</i>"
                    value="{$model.name}"
                    data-rule-regex="^[\w_]+$"
                    /></div>
    </div>      

    <div class="form-block">
        <label>Класс</label>
        <div><input class="form-control" type="text" name="class" size="60" value="{$model.class}"
                    data-popover="true"
                    data-content="CSS class"
                    /></div>
    </div>   
    
    <div class="form-block">
        <label>{$lang.active}</label>
        <div><input class="form-control" type="checkbox" name="active" value="1" {if $model.active || NOT $model.id}checked="checked"{/if}/></div>
    </div>     
    
    <div class="form-block">
    <label>Контент</label>
    <div><textarea cols="60" rows="10" name="text"
                   class="wysiwyg">{$model.text}</textarea></div>
    </div> 
    
    <div class="form-block">
        <label>Smarty</label>
        <div>
            <input class="form-control" type="checkbox" name="plain"
                   data-popover="true"
                   data-content="Обработка виджета шаблонизатором smarty (глобальный контекст)"
                   value="1" {if $model.plain || !$model.id}checked="checked"{/if}/>
            Не обрабатывать шаблонизатором
        </div>  
    </div>           
    
    <div class="form-block">
        <label>Wrapped</label>
        <div>
            <input class="form-control"
                   data-popover="true"
                   data-content="Не применять блочное оформление"
                   type="checkbox" name="raw" value="1" {if $model.raw}checked="checked"{/if}/>
            Не использовать обрамление блока
        </div>  
    </div>    

</div>
                


{/block}

{block 'form-bottom'}

    {if $model.id}
        <input type="hidden" name="pid" value="{$model.pid}"/>
    {else}
        <input type="hidden" name="pid" value="{$req.pid}"/>
    {/if}
    
{/block}    