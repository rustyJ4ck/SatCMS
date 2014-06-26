<!-- TAB -->
<div id="tab-properties" class="tab-pane">


    <div class="form-block">
        <label>
            H1 {$lang.title}
        </label><div>
            <input type="text" class="form-control" name="h1_title"
                   size="100" value="{$model.h1_title}"/>
        </div>
    </div>

    <div class="form-block">
        <label>
            H1 {$lang.description}
        </label><div>
            <textarea cols="132" rows="2"
                      class="form-control"
                      name="h1_description"
                    >{$model.h1_description}</textarea>
        </div>
    </div>


    <div class="form-block">
        <label>Изображение</label>
        <div>

            {control type='image' name="image" value=$model.image field=$entity.fields.image}

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


    {*
    <div class="form-block">
        <label>
            {$lang.image} <span class="help">(ios)</span>
        </label>

        <div>
            <input size="10" type="file" name="image_ios" value=""/>

            <div class="help">610x464 &rarr; 250x190
                {if $model.image_ios.url} &nbsp; [
                    <a href="{$model.image_ios.thumbnail.url}" target="_blank">#Small</a>
                    ,
                    <a href="{$model.image_ios.url}" target="_blank">#Big</a>
                    ]{/if}
            </div>
        </div>
    </div>
    *}

    <div class="form-block">
        <label>
            {$lang.active}
        </label>

        <div>
            <input type="checkbox" name="active" value="1" {if $model.active || NOT $model.id}checked="checked"{/if}/>
        </div>
    </div>

    <div class="form-block">
        <label>
            Избранное
        </label>

        <div>
            <input type="checkbox" name="b_featured" value="1" {if $model.b_featured}checked="checked"{/if}/>
        </div>
    </div>

    <div class="form-block">
        <label>
            {'created_at'|i18n}
        </label>

        <div>
            {control type='date' name="created_at" value=$model.created_at}
        </div>
    </div>

    <div class="form-block">
        <label>
            {'updated_at'|i18n}
        </label>

        <div>
            {control type='date' name="updated_at" value=$model.updated_at}
        </div>
    </div>



{*
    <div class="form-block">
        <label>
            Twitter
        </label>

        <div>
            <input type="checkbox" name="twit_me" value="1"/>
        </div>
    </div>

*}

</div>