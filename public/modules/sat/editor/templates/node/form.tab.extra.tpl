<!-- TAB -->
<div id="t-extra" class="tab-pane active">

    <div class="form-block">
        <label>Название*</label>
        <div>
        <input class="form-control"
               type="text" name="title" size="80"
               value="{$model.title}"
               data-rule-required="true" />
        </div>
    </div>

    <div class="form-block">
        <label>Описание</label>
        <div>
        <textarea class="form-control wysiwyg"
                  cols="120" rows="10"
                  name="description">{$model.description}</textarea>
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
        <label>Текст</label>
        <div>
        <textarea class="form-control wysiwyg"
                  cols="120" rows="20" name="text" class="wysiwyg">{$model.text}</textarea>
        </div>
    </div>


    <div class="form-block">
        <label>Изображение</label>
        <div>
        {control type='image' name="image" value=$model.image}
        </div>
    </div>



    {*
    <div class="form-block">
        <div style="height:10px;border:1px solid white;"></div>
    </div>

   <div class="form-block img-file image-ctx">
       <label>Изображение</label>
       <div>
           <input type="file" name="image" size="40" value=""/>
           <input type="button" value="Загрузить по URL&#133;"
                  onclick="$('.img-url').show(); $('.img-file').hide();"
                  style="padding:2px 4px;"/>
       </div>
   </div>


   <div class="form-block img-url image-ctx hidden">
       <label>Изображение</label>
       <div>
           URL <input type="text" name="image_url" size="40" value=""/>
           <input type="button"
                  onclick="$('.img-url').hide(); $('.img-file').show();"
                  value="Загрузить из файла&#133;" style="padding:2px 4px;"/>
       </div>
   </div>

    <div class="form-block">
        <label>Изображение</label>
        <div>



            {*if $model.image.url}

                {if $model.image.thumbnail.url}

                    <a target="_blank_img" href="{$model.image.url}" style="display:block;float:left;">
                        <img src="{$model.image.thumbnail.url}" border="0" height="60" align="left"/>
                    </a>

                    <div style="float:left;padding-top:20px;padding-left:10px;">
                        удалить <input type="checkbox" value="remove"
                                       class="remove-image"
                                       name="image" style="top:2px;"/>
                    </div>


                {else}
                    <a target="_blank_img" href="{$model.image.url}">Показать</a>
                    &nbsp; удалить <input type="checkbox"
                                          class="remove-image"
                                          value="remove" name="image" style="top:2px;"/>
                {/if}

            {else}{/if}
        </div>
    </div>
    *}

    <div class="form-block">
        <label>
            {'created_at'|i18n}
        </label>

        <div>
            {control type='date' name="created_at" value=$model.created_at attrs="readonly"}
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


</div>

