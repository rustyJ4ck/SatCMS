{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params    = "new: no, sortable: no, readonly: yes, reset: yes, actions: no, ids: no"}
    {*$model     = $tpl_sat_comment*}
    {$btnNew    = "dialog: yes"}
    {$btnEdit   = "dialog: yes"}

{/block}

{block 'filter-filters'}

    {* {$config.base_url|debug_print_var}  "?m=sat&c=news" *}

    {if $config.debug}
        <code>
            {$return.list.sql}
        </code>
    {/if}

    <div class="form-horizontal">


        <div class="form-group col-xs-4">
            <label class="control-label col-xs-4">№ карты</label>
            <div class="col-sm-7">
                <input type="text" value="" class="form-control filter filter-persist"
                       name="cardnumber"
                       data-mask="9999 9999 9999 9999"
                       d1ata-mask-placeholder="0000_0000_0000_0000">
            </div>
        </div>


        {*
        <div class="form-group col-xs-3">
            <label class="control-label col-xs-3">Категория</label>
            <div class="col-xs-9">

                {control type="select"
                class="filter filter-persist"
                value=$req.pid
                name="pid"
                src=$controller.categories
                }

            </div>
        </div>
        *}

        <div class="form-group col-xs-4">
            <label class="control-label col-xs-2">Дата</label>
            <div class="col-sm-5">
                {control type="date"
                    name="edate"
                    attrs='data-index-name="from"'
                    format="DD.MM.YYYY"
                    class="filter filter-persist" placeholder="с"}
            </div>
            <div class="col-sm-5">
                {control type="date"
                    name="edate"
                    attrs='data-index-name="to"'
                    class="filter filter-persist"
                    format="DD.MM.YYYY"
                    placeholder="по"}
            </div>
        </div>

        <div class="form-group col-xs-3">
            <label class="control-label col-xs-6">Кол-во</label>
            <div class="col-xs-4">
                <select name="limit" n1g-bind="filters.limit" class="filter filter-persist" data-no-search>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

    </div>

{/block}