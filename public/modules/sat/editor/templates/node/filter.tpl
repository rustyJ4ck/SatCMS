{extends "widgets/model/filter.tpl"}

{*include "widgets/model/filter.tpl"
params="new: yes, sortable: yes"
btnNew="title: 'Добавить страницу', dialog: yes, dialogTitle: 'Добавить страницу'"
btnEdit="title: 'Правка страницы', dialog: no"
model=$return.list.collection
gridName='grid-nodes'
filter="controller: nodeListFilterController"
*}

{block 'filter-params'}

    {$params="new: yes, sortable: yes"}
    {$btnNew="title: 'Добавить страницу', dialog: yes, dialogTitle: 'Добавить страницу'"}
    {$btnEdit="title: 'Правка страницы', dialog: no"}
    {$model=$return.list.collection}
    {$gridName='grid-nodes'}
    {$filter="controller: nodeListFilterController"}
    {$actionUrlExtra = "&pid=`$req.pid`"}
    {$page = "&start=`$req.start`"}

{/block}


{block 'filter-controls' append}

    <a type="button" class="btn btn-info btn-sm"
       ng-disabled="isLoading"
       ng-click="showVisual({$req.pid})"
            >
        <span class="glyphicon glyphicon-share"></span> Visual
    </a>

{/block}

{block 'filter-filters-todo'}

    {* {$config.base_url|debug_print_var}  "?m=sat&c=news" *}

    {if $config.debug}
        <code>
            {$return.list.sql}
        </code>
    {/if}

    <div class="form-horizontal">


        <div class="form-group col-xs-3">
            <label class="control-label col-xs-3">Заголовок</label>
            <div class="col-sm-9">
                <input type="text" value="" class="form-control filter filter-persist" name="title" placeholder="Начинается с ...">
            </div>
        </div>


        <div class="form-group col-xs-3">

        </div>

        <div class="form-group col-xs-4">
            <label class="control-label col-xs-2">Дата</label>
            <div class="col-sm-5">
                {control type="date" name="created_at" attrs='data-index-name="from"' class="filter filter-persist" placeholder="с"}
            </div>
            <div class="col-sm-5">
                {control type="date" name="created_at" attrs='data-index-name="to"' class="filter filter-persist" placeholder="по"}
            </div>
        </div>

        <div class="form-group col-xs-2">
            <label class="control-label col-xs-6">Кол-во</label>
            <div class="col-xs-6">
                <select name="limit" n1g-bind="filters.limit" class="filter filter-persist" data-no-search>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

    </div>

{/block}

