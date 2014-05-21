{extends "widgets/model/form.tpl"}

{block 'params'}
    {$form = "dialog: yes, controls: no, notify: '[ng-controller=nodeFormNewController]', controller: nodeFormNewController"}
    {$title = "Правка страницы &laquo;`{$model.title}`&raquo;"}
{/block}

{block "form"}

<div class="form">

    {*@[[testForm]]@*}

        <div class="form-block">
            <label>Название*</label>
            <div class="col-xs-6">
                <input class="form-control" type="text" name="title" value="{$model.title}"
                       data-rule-required="true"
                        />
            </div>
        </div>

        <div class="form-block">
            <label>URL</label>
            <div class="col-xs-6">
                <input class="form-control" type="text" name="url" value="{$model.url}"
                       data-popover="true"
                       data-content="Slug (путь страницы - генерируется из заголовка, если не указан явно)"
                       data-container=".box-content"
                        />

            </div>
        </div>

        <div class="form-block">
            <label>Раздел</label>
            <div class="col-xs-7">


                <select name="pid">
                    <option value="0">Корень</option>
                    {foreach item=list from=$current.site.tree}
                        <option value="{$list.id}" style="padding-left:{math equation="x * 10" x = $list.level}px"
                                {if ($list.id && $list.id == $model.pid) || ($req.op == 'new' && $req.pid == $list.id)}selected="selected"{/if}>{'&nbsp;'|str_repeat:(2*$list.level)} {$list.title|truncate:60} [{$list.id}]</option>
                    {/foreach}
                </select>


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

        <div class="form-block">
            <label>Активный</label>
            <div>
                <input type="hidden" name="active" value="0"/> {*fixcheckboxemptystate*}
                <input type="checkbox" name="active" value="1" checked="checked" />
            </div>
        </div>

</div>

{/block}

{block 'form-controls'}

    <input  data-popover="true"
            data-placement="top"
            data-content="Сохранить и остаться в текущей категории"
            class="btn btn-primary"
            type="button"
            ng-click="save()"
            value="{$lang.save}"
            />

    <input type="hidden" name="save_continue" value="0"/>
    <input type="hidden" name="form-submit" value="1"/>

    <input data-popover="true"
           data-placement="top"
           data-content="Сохранить и продолжить редактирование полной версии страницы"
           type="button"
           class="btn btn-success"
           value="Продолжить"
           ng-click="saveContinue()"
           />

    <input type="button"
           class="btn btn-danger"
           data-dismiss="modal"
           value="Отмена"/>

{/block}

{block 'form-bottom'}
    <input type="hidden" name="start" value="{$req.start}"/>
{/block}