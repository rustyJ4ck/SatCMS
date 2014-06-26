{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: yes, reset: yes"}
    {*$model     = $tpl_sat_news*}
    {$model     = $return.list.collection}
    {$btnNew    = "dialog: no"}
    {$btnEdit   = "dialog: no"}
    {$actionUrlExtra = "type_id=`$req.type_id`"}

{/block}

{block 'filter-toolbar'}


    <li class="btn-group-sm">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Content type: <b>{$controller.type.title}</b>&nbsp; <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right">
            {foreach $controller.types as $type}
                <li><a href="{$config.editor_url}{$req.m}/{$req.c}/type_id/{$type.id}/">{$type.title}</a></li>
            {/foreach}
        </ul>
    </li>

{/block}


{block 'filter-filters'}

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
            <label class="control-label col-xs-3">Категория</label>
            <div class="col-xs-9">

                {control type="select"
                    class="filter filter-persist"
                    value=$req.pid
                    name="pid"
                    src=$controller.categories
                }

                {*
                <select name="pid" value="" n1g-bind="filters.pid" class="filter filter-persist" style="width:200px">
                    <option value="0">{'undefined'|i18n}</option>
                    {foreach $controller.categories as $cat}
                        <option value="{$cat.id}" {if $req.pid == $cat.id}selected="selected"{/if}>{$cat.title}</option>
                    {/foreach}
                </select>
                *}
            </div>
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


    {if $req.type_id}
        <filter name="type_id">{$req.type_id}</filter>
    {/if}

{/block}


{block 'filter-controls' prepend}

        <a type="button" class="btn btn-info btn-sm"
           href="?m={$req.m}&c=content_category">
            <span class="glyphicon glyphicon-share-alt"></span> Категории
        </a>

    {*
        <input type="text" name="pid" value="" ng-model="filters.pid" style="width:50px" class="form-control filter inline-block"/>
    *}
{/block}


{block "grid-bottom" append}
    <filter name="site_id">{$current.site.id}</filter>
{/block}

