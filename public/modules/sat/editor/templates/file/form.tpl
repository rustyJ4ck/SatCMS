{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes, grid: yes, notify: yes"}
    {$model = $return.form.data}
{/block}

{block "form-top"}
{/block}

{block 'form'}

    {*<form action="{$config.editor_url}" method="post" name="site_mod" id="frm_" class="_validable">*}


    <div class="form">


        <div class="form-block">
            <label>{$lang.title}*</label>
            <div>
                <input class="form-control"
                       type="text" name="title" size="70" value="{$model.title}"
                       />
            </div>
        </div>

        <div class="form-block">

            <label>Файл</label>
            <div>

                {control type='file' name="file" value=$model.file}

                {*
                <input type="file" name="file" size="40" value=""/>
                    {if $model.file.url}
                    <a target="_blank_img" href="{$model.file.url}">Показать</a>, удалить
                        <input type="checkbox" class="remove-image" value="remove" name="file"/>
                    {else}{/if}

                    *}
            </div>
        </div>


    </div>

{/block}

{block 'form-controls'}
{/block}

{block 'form-bottom'}

    <input type="hidden" name="pid" value="{if $model.id}{$model.pid}{else}{$req.pid}{/if}"/>
    <input type="hidden" name="sid" value="{if $model.id}{$model.sid}{else}{$req.sid}{/if}"/>
    <input type="hidden" name="ctype_id" value="{if $model.id}{$model.ctype_id}{else}{$req.ctype_id}{/if}"/>

    {*if $model.id}
        <input type="hidden" name="site_id" value="{$model.site_id}"/>
    {else}
        <input type="hidden" name="site_id" value="{$current.site.id}"/>
    {/if*}

{/block}

