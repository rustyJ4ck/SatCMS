{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes, grid: yes, notify: '#`$controller.grid_name`', enctype: 'multipart/form-data'"}
    {$dialog = true}
    {$model = $return.form.data}
{/block}

{block 'form'}


    <div class="form">

        <div class="form-block">
            <label>{$lang.title}</label>
            <div>
            <input class="form-control" type="text" name="title" size="60" value="{$return.form.title}"/>
            </div>
        </div>


        <div class="form-block">
            <label>{$lang.file}</label>
            <div>
                {control type='file' name="file" value=$model.file}
                {*
                <input class="form-control" type="file" name="file" size="40" value=""/>
                    {if $return.form.file.url}
                    <a target="_blank_img" href="{$return.form.file.url}">Показать</a>, удалить <input type="checkbox" class="remove-image" value="remove" name="file"/>
                    {else}{/if}
                 *}
            </div>
        </div>

        <div class="form-block">
            <label>{$lang.text}</label>
            <div>
            <textarea class="form-control"
                      cols="60" rows="5" name="comment">{$return.form.comment}</textarea>
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

 
