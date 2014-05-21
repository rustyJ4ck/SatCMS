{extends "widgets/model/form.tpl"}


{*block "form-actions"}
{/block*}


{block "form-head" prepend}

    {$form = "dialog: no"}

    {if $req.op != 'new'}
        {$title = "Правка сайта `$return.form.data.title`"}
    {else}
        {$title = "Добавление сайта"}
    {/if}

{/block}


{block "form"}

{$model = $return.form.data}


<div class="form form50">

{if $req.op != 'new'}


    <ul class="nav nav-tabs">
        <li  class="active"><a href="#t-main" data-toggle="tab">Свойства</a></li>
        <li><a href="#t-extra" data-toggle="tab">Тексты</a></li>
        <li><a href="#t-ops" data-toggle="tab">Операции</a></li>
    </ul>

<div class="tab-content">

    
{/if}

    <div id="t-main" class="active tab-pane">

        <div class="form-block">
            <label>{$lang.title}*</label>
            <div><input class="form-control" type="text" name="title" size="40"
                        value="{$model.title}"
                        data-rule-required="true"
                        data-msg-required="Название сайта"
                        /></div>
        </div>


        <div class="form-block">
            <label>Домен*</label>
            <div><input class="form-control" type="text" name="domain" size="40"
                        value="{$model.domain}"
                        data-rule-required="true"
                        data-rule-domain="true"
                        data-popover="true"
                        data-content="Основной домен <i>site.com</i>"
                        /></div>
        </div>

        <div class="form-block" style="opacity:0.8">
            <label>Домен (отладка)</label>
            <div>
                <input class="form-control" type="text" name="ddomain" size="40"
                       value="{$model.ddomain}"
                        data-rule-domain="true"
                        data-popover="true"
                        data-content="На этом домене будет автоматически включена отладка"
                        /></div>
        </div>

        <div class="form-block">
            <label>Алиасы</label>
            <div>
                <input class="form-control" type="text" name="aliases" size="40" value="{$model.aliases}"
                       data-popover="true"
                       data-content="Алиасы сайта: www.domain.ru, another.domain"
                        />
            </div>
        </div>

        <div class="form-block">
            <label>Path</label>
            <div><input class="form-control" type="text" name="path" size="40"
                        value="{$model.path}"
                        data-popover="true"
                        data-content="Site root, example: <i>en</i> binds to http://www.site.com/en/"
                        /></div>
        </div>

        <div class="form-block">
            <label>Лого</label>
            <div>

                {control type='image' name="image" value=$model.image}

            </div>
        </div>

        <div class="form-block">
            <label>Шаблон</label>
            <div class="col-xs-4">

                {control type="select"
                    value=$model.template
                    name="template"
                    src=$controller.templates
                    default=[0,'По-умолчанию']
                }

            </div>
        </div>

        <div class="form-block">
            <label>Статика</label>
            <div
                    data-popover=""
                    data-content="Статичная страница генерируется при первом обращении к странице. Сгенерированные файлы расположены в /static/domain/*"

                    >
                <input type="checkbox" name="b_static" value="1"
                        {if $model.b_static}checked="checked"{/if}
                />
            </div>
        </div>

        <div class="form-block">
            <label>Вкл. сайт</label>
            <div
                    data-popover=""
                    data-content="Включить сайт"

                    >
                <input type="checkbox" name="active" value="1"
                       {if $model.active}checked="checked"{/if}
                        />
            </div>
        </div>        

    </div>

{if $req.op != 'new'}
    
    <div id="t-extra"  class="tab-pane">

            <div class="form-block">
            <label>{$lang.title} (html)</label>
            <div><input class="form-control" type="text" name="html_title" size="80" value="{$model.html_title}"/></div>
            </div>

            <div class="form-block">
            <label>Описание</label>
            <div><textarea class="form-control" cols="120" rows="10" name="description">{$model.description}</textarea></div>
            </div>

            <div class="form-block">
            <label>Текст</label>
            <div><textarea class="form-control wysiwyg" cols="120" rows="30" name="text">{$model.text}</textarea></div>
            </div>

            <div class="form-block">
            <label>Meta Keys</label>
            <div><textarea class="form-control" cols="120" rows="2" name="mk">{$model.mk}</textarea></div>
            </div>

            <div class="form-block">
            <label>Meta Desc</label>
            <div><textarea class="form-control" cols="120" rows="2" name="md">{$model.md}</textarea></div>
            </div>

    </div>


    <div id="t-ops"  class="tab-pane">

        <div class="form-block">
            <label>Владелец сайта</label>
            <div>
                 {if $model.owner}{$model.owner.nick} ({$model.owner.login}){else}-{/if}
            </div>
        </div>

        <div class="form-block">
            <label>Статичный кэш</label>
            <div>
            <a class="a-delete btn btn-danger btn-xs" href="?m={$req.m}&c=site&id={$model.id}&op=clear_static">Удалить статику...</a>
            </div>
        </div>

        <div class="form-block">
            <label>Ссылка на сайт</label>
            <div>
            <a class="btn btn-info btn-xs" href="{$model.urls.self}" target="_blank"
               data-popover="true"
               data-content="{$model.urls.self}"
                    >Перейти</a>
            </div>
        </div>

    </div>

</div>

    {/if}
    
    <br clear="all"/>      
              
</div>

    {*
    <div class="form-bottom">
    <input class="main" name="item_submit" type="submit" 
        value="{if $req.op == 'new'}Продолжить{else}{$lang.save}{/if}"/>
    </div>
    *}


<input type="hidden" name="owner_id" value="{if $req.op == 'new'}{$user.id}{else}{$model.owner_id}{/if}"/>
                

<script>
    require(['jquery'], function(){
        console.log('sites');
    })
</script>

{/block}