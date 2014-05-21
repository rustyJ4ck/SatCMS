<div class="box compilable">
    <div class="box-header">
        <span class="title">Инструменты</span>
    </div>

    <div class="box-content padded">

        <a type="button" class="btn btn-default btn-sm" h1ref="{$config.base_url}"
           ng-click="reload();"
                >
            <span class="glyphicon glyphicon-refresh"></span> Обновить
        </a>

        <a type="button" class="btn btn-default btn-sm dialog"
           data-title="Добавить переменную"
           href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&pid={$req.pid}&op=new&embed=yes"
           dialog="{ldelim}width:750,height:380{rdelim}"
                >
            <span class="glyphicon glyphicon-plus"></span> Добавить новый
        </a>

        {if count(tpl_config)}

            <span class="pull-right">

                <a class="btn btn-default btn-sm a-delete"
                   data-title="Удалить все записи?"
                   data-target=".model-data"
                   href=""
                   data-href="?m={$req.m}&c={$req.c}&op=drop_all"
                        >Очистить <span class="glyphicon glyphicon-trash"></span>
                </a>

                <a class="btn btn-default btn-sm a-delete-selected"
                   data-href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=drop_selected"
                   data-source="[name=id]:checked"
                        >Удалить отмеченные <span class="glyphicon glyphicon-trash"></span>
                </a>

            </span>

        {/if}

    </div>

</div>