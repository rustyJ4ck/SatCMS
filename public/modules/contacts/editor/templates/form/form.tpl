{$model = $return.form.data}

<div class="form">

    <div class="form-block" style="height:40px">
        <label>Дата</label>

        <div>{$model.created_at}</div>
    </div>

    <div class="form-block">
        <label>Тема</label>

        <div>{if $model.title}{$model.title}{else}-{/if}</div>
    </div>

    <div class="form-block">
        <label>Имя</label>

        <div>{$model.name|default:'-'}</div>
    </div>

    <div class="form-block">
        <label>Email</label>

        <div>{if $model.email}{$model.email}{else}-{/if}</div>
    </div>

    <div class="form-block">
        <label>Телефон</label>

        <div>{if $model.phone}{$model.phone}{else}-{/if}</div>
    </div>


    <div class="form-block">
        <label>Сообщение</label>

        <div>{if $model.message}{$model.message}{else}-{/if}
        </div>

    </div>

    <div class="form-block">
        <label>Статус</label>

        <div>
            {if $model.b_confirmed}
                <span class="label label-success">Обработан</span>
            {else}
                <span class="label label-danger">В процессе</span>
            {/if}
        </div>

    </div>

</div>
                

 