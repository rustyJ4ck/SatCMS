<!-- TAB -->
<div id="t-seo" class="tab-pane">

    <div class="form-block">
        <label>Заголовок</label>
        <div>
        <input class="form-control"
               type="text"
               name="html_title" size="80" value="{$model.html_title}"/>
        </div>
    </div>

    <div class="form-block">
        <label>Текст</label>
        <div>
        <textarea cols="120" rows="20" name="html_text" class="wysiwyg">{$model.html_text}</textarea>
        </div>
    </div>

{*
    <div class="form-block">
        <div style="padding:15px;">
            Показать <a href="" class="inverse_green" onclick="sat_files_click();return false;">Прикрепленные файлы</a>
        </div>
    </div>
*}

    <div class="form-block">
        <label>MK</label>
        <div>
        <textarea cols="120" rows="2" name="mk" class="form-control wysiwyg1">{$model.mk}</textarea>
        </div>
    </div>

    <div class="form-block">
        <label>MD</label>
        <div>
        <textarea cols="120" rows="2" name="md" class="form-control wysiwyg1">{$model.md}</textarea>
        </div>
    </div>

</div>