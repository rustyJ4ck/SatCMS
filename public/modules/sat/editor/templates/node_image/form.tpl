{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes, grid: yes, notify: '#`$controller.grid_name`', enctype: 'multipart/form-data'"}
    {$dialog = true}
    {$model = $return.form.data}
{/block}

{block 'form'}

{*
<form action="{$config.editor_url}" method="post" name="site_mod" id="frm_" enctype="multipart/form-data">
*}

<div class="form">

    <div class="form-block">
        <label>{$lang.title}</label>
        <div>
        <input class="form-control"
               type="text"
               name="title" size="60"
               value="{$model.title}"/>
        </div>
    </div>    
    
    <div class="form-block">
        <label>Изображение</label>
        <div>

            {control type='image' name="image" value=$model.image}

            {*
            <input type="file" name="file" size="40" value=""/> 
                {if $model.file.url}
                <a target="_blank_img" href="{$model.file.url}">Показать</a>, удалить
                    <input type="checkbox" class="remove-image" value="remove" name="file"/>
                {else}{/if}

                *}
        </div>  
    </div> 

    <div class="form-block">
        <label></label>
        <div>
            <input class="form-control"
                   type="text" name="image_url" size="60" value=""
                   data-rule-url="true"
                   data-popover="true"
                   data-content="Загрузить изображение из интернета"
                   placeholder="URL изображения"
                    />

            <div class="help">Загрузить изображение из интернета</div>
        </div>  
    </div>      
    
    
    <div class="form-block">
        <label>Доп.Изображение</label>
        <div>

            {control type='image' name="alt_image" value=$model.alt_image}

            {*
            <input class="form-control"
                   type="file" name="image" size="40" value=""/>
                {if $model.image.url}
                <a target="_blank_img" href="{$model.image.url}">Показать</a>, удалить
                    <input type="checkbox" class="remove-image" value="remove" name="image"/>
                {else}{/if}

                *}
        </div>  
    </div> 
                    
    <div class="form-block">
        <label></label>
        <div>
            <input type="text" name="alt_image_url" size="60" value=""
                   data-rule-url="true"
                   class="form-control"
                   data-popover="true"
                   data-content="Загрузить изображение из интернета"
                   placeholder="URL изображения"
                    />
            <div class="help">Загрузить изображение из интернета</div>
        </div>  
    </div>                                  

    


    <div class="form-block">
        <label>{$lang.text}</label>
        <div>
        <textarea class="form-control"
                  cols="60" rows="5"
                  name="comment">{$model.comment}</textarea>
        </div>
    </div>     
    
    <br clear="all"/>      


    {*
    <div class="form-bottom">
        <input class="btn btn-primary"
               name="form-submit"
               type="submit" value="{$lang.save}"/>
    </div>
    *}

    
  
</div>

{/block}

{block 'form-bottom'}

    <input type="hidden" name="pid" value="{if $model.id}{$model.pid}{else}{$req.pid}{/if}"/>
    <input type="hidden" name="sid" value="{if $model.id}{$model.sid}{else}{$req.sid}{/if}"/>
    <input type="hidden" name="ctype_id" value="{if $model.id}{$model.ctype_id}{else}{$req.ctype_id}{/if}"/>

{/block}

