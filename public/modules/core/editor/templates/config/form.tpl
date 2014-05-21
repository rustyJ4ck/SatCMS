{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes"}
{/block}


{block 'form'}


    <div class="form">

        <div class="form-block">
            <label>{$lang.title}</label>
            <div><input class="form-control" type="text" name="title" size="40" value="{$model.title}"/>
            </div>
        </div>

        <div class="form-block">
            <label>{$lang.name}</label>
            <div><input class="form-control" type="text" name="name" size="40" value="{$model.name}"

                        data-rule-required="true"
                        data-msg-required="{'Required Field'|i18n:'core.validator'}"
                        data-msg-minlength="{'Too short'|i18n:'core.validator'}"
                        data-rule-minlength="3"

                        /></div>
        </div>

        <div class="form-block">
            <label>{$lang.value}</label>
            <div><textarea class="form-control" name="value" cols="80" rows="5">{$model.value}</textarea></div>
        </div>

        <div class="form-block">
            <label>Системный</label>
            <div>
                <input type="hidden" name="b_system" value="false" /> {* fix empty checkbox *}
                <input type="checkbox" name="b_system" value="1" {if $model.b_system || NOT $model.id}checked="checked"{/if}/>
            </div>
        </div>


    </div>

{/block}