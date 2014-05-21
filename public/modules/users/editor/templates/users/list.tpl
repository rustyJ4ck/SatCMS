{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: yes, reset: yes"}
    {*$model     = $tpl_sat_news*}
    {$model     = $return.list.collection}
    {$btnNew    = "dialog: yes, title: Добавить пользователя, dialogTitle: Добавить пользователя"}
    {$btnEdit   = "dialog: yes, title: Изменить пользователя"}
    {$actionUrlExtra = "gid=`$req.gid`"}

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
            <label class="control-label col-xs-3">Email</label>
            <div class="col-sm-9">
            <input type="text" value="" class="form-control filter filter-persist" name="email" placeholder="Начинается с ...">
            </div>
        </div>

        <div class="form-group col-xs-3">
            <label class="control-label col-xs-3">Логин</label>
            <div class="col-sm-9">
                <input type="text" value="" class="form-control filter filter-persist" name="login" placeholder="Начинается с ...">
            </div>
        </div>

        <div class="form-group col-xs-5">
            <label class="control-label col-xs-2">Регистрация</label>
            <div class="col-sm-4">
                {control type="date" name="date_reg" attrs='data-index-name="from"' class="filter filter-persist" placeholder="с"}
            </div>
            <div class="col-sm-4">
                {control type="date" name="date_reg" attrs='data-index-name="to"' class="filter filter-persist" placeholder="по"}
            </div>
        </div>

    </div>

{/block}

{block 'filter-controls' prepend}

        <a type="button" class="btn btn-info btn-sm"
           href="?m={$req.m}&c=user_group">
            <span class="glyphicon glyphicon-share-alt"></span> Группы
        </a>

    {*
        <input type="text" name="pid" value="" ng-model="filters.pid" style="width:50px" class="form-control filter inline-block"/>
    *}
{/block}


{block "grid-bottom" append}
    <filter name="site_id">{$current.site.id}</filter>
{/block}

