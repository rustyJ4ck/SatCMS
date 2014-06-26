<!-- TAB -->
<div id="tab-main" class="tab-pane active">


    <div class="form-block">
        <label>
            Категория
        </label><div>

            {if $model.id}{$pid = $model.pid}{else}{$pid = $req.pid}{/if}

            {control type="select"
                value=$pid
                name="pid"
                src=$controller.categories
            }

        </div>
    </div>


    <div class="form-block">
        <label>
            {$lang.title}*
        </label><div>
            <input type="text" class="form-control" name="title" size="100"
                   value="{$model.title}"
                   data-rule-required="true"
                   />
        </div>
    </div>

    <div class="form-block hidden">
        <label>
            {$lang.url}
        </label><div>
            <input type="text" class="form-control" name="slug" size="100" value="{$model.slug}"/>
        </div>
    </div>

    <div class="form-block">
        <label>
            {$lang.description}
        </label><div>
            <textarea cols="132" rows="10" name="description" class="wysiwyg">{$model.description}</textarea>
        </div>
    </div>

    {*if $model.id}
        <div class="form-block">


        <div>

            <a href="" class="inverse_green" onclick="sat_files_click();return false;">Прикрепленные файлы</a>

            |

            <a href="" class="inverse_green" onclick="attach_images_click();return false;">Изображения (галерея)</a>

        </div>
        </div>
    {/if*}

    <div class="form-block">
        <label>
            {$lang.text}
        </label><div>
            <textarea cols="140" rows="20" name="text" class="wysiwyg">{$model.text}</textarea>
        </div>
    </div>

    <div class="form-block">
        <label>
            Автор
        </label><div>
            <input type="text" class="form-control" name="author" size="60" value="{$model.author}"/>
        </div>
    </div>


</div>