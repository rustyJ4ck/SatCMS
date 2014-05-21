<!-- TAB -->
<div id="t-info" class="tab-pane">

    <div class="form-block">
        <label>Дата создания</label>
        <div>{$model.created_at}</div>
    </div>

    <div class="form-block">
        <label>Владелец</label>
        <div class="btn-group-xs">
           {if $model.owner}{$model.owner.nick} ({$model.owner.login}){else}-{/if}
        </div>
    </div>

    <div class="form-block">
        <label>Статичный кэш</label>
        <div class="btn-group-xs">
        <a class="a-ajax btn btn-danger" href="?m={$req.m}&c=node&id={$model.id}&op=clear_static">Удалить для страницы</a>
        </div>
    </div>

    <div class="form-block">
        <label>Ссылка на страницу</label>
        <div class="btn-group-xs">
        <a class="btn btn-default" href="{$model.urls.self}" target="_blank"
           data-popover="true"
           data-content="{$model.urls.full}"
                >Перейти</a>
        </div>
    </div>


</div>