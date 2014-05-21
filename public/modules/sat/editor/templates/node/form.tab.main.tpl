<!-- TAB -->
<div id="t-main" class="tab-pane">

    <div class="form-block">
        <label>Идентификатор (URL)</label>
        <div>
        <input class="form-control"
               type="text" name="url"
               size="80"
               value="{$model.url}"
               data-rule-required="true"
               />
        </div>
    </div>

    {if $req.op == "new"}
        <div class="form-block">
            <label>Раздел</label>
            <div>

                <select class="select2" name="pid" {*style="width:590px;"*}>
                    <option value="0">Корень</option>
                    {foreach item=list from=$tree}
                        <option value="{$list.id}" style="padding-left:{math equation="x * 10" x = $list.level}px"
                                {if ($list.id && $list.id == $model.pid) || ($req.op == 'new' && $req.pid == $list.id)}selected="selected"{/if}>{$list.title|truncate:60} [{$list.id}]</option>
                    {/foreach}
                </select>

            </div>
        </div>
    {else}
        <input type="hidden" name="pid" value="{$model.pid}"/>
    {/if}

    <div class="form-block">
        <label>Черновик</label>
        <div>
            <input type="checkbox" name="b_draft" value="1"
                   {if $model.b_draft}checked="checked"{/if}/>
        </div>
    </div>

    <!--
    </div>
    -->


    <div class="form-block">
        <label>Системная страница</label>
        <div>
            <input type="checkbox" name="b_system" value="1"
                   {if $model.b_system}checked="checked"{/if}
                   title="Используется для внутренних нужд" class="tip"/>
        </div>
    </div>

    <div class="form-block">
        <label>Популярный</label>
        <div data-popover="true" data-content="Используется для особенных страниц">
            <input type="checkbox" name="b_featured" value="1"
                   {if $model.b_featured}checked="checked"{/if}
            />
        </div>
    </div>



    <div class="form-block">
        <label>Шаблон страницы</label>
        <div>

            <select class="select2" name="template" style="width:150px;">
                <option value="0">По-умолчанию</option>
                {foreach item=list key=list_k from=$subtemplates}
                    <option value="{$list_k}"
                            {if $list_k == $model.template}selected="selected"{/if}>{if $list.title}{$list.title}{else}{$list}{/if}</option>
                {/foreach}
            </select>

        </div>
    </div>

    {* <!-- depricated -->
     <div class="form-block">
        <label>Шаблон для подразделов</label>
        <div>

            <select name="child_template" style="width:150px;">
            <option value="0">По-умолчанию</option>
            {foreach item=list key=list_k from=$subtemplates}
                <option value="{$list_k}"
                {if $list_k == $model.child_template}selected="selected"{/if}>{if $list.title}{$list.title}{else}{$list}{/if}</option>
            {/foreach}
            </select>

        </div>
    </div>
    *}


    <div class="form-block">
        <label>Кол-во на странице</label>
        <div>
            <input class="form-control" type="text"
                   name="pagination"
                   size="6"
                   style="width:50px;"
                   value="{$model.pagination}"
                   data-content="Дочерних элементов на странице<br/>0 - неограничено"
                   data-popover
                   />
        </div>
    </div>

</div>     <!-- /close first tab -->

