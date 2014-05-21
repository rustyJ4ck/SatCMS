{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes"}
    {$entity = $return.form}
    {*$model = $return.form.data*}
{/block}

{block "form-top"}
{/block}

{block 'form'}

    <div class="form form80 form-left-md">

        <div class="form-block">
            <label>{$lang._users.login}*</label>
            <div><input class="form-control"name="login" value="{$model.login}" validate="{ldelim}required:1{rdelim}"/></div>
        </div>

        <div class="form-block">
            <label>{$lang._users.password}</label>
            <div><input class="form-control"name="password" value="{$model.password}"/></div>
        </div>

        <div class="form-block">
            <label>{$lang._users.nick}*</label>
            <div><input class="form-control"name="nick" value="{$model.nick}" validate="{ldelim}required:1{rdelim}"/></div>
        </div>


        <div class="form-block">
            <label>Группа</label>
            <div>

                {control type="select"
                    value=$model.gid
                    name="gid"
                    src=$controller.ug
                }

                {*
                <select name="gid">
                    <option value="">Не указан</option>
                    {foreach item=list from=$tpl_user_group}
                        <option value="{$list.id}" {if $list.id == $model.gid}selected="selected"{/if}>{$list.title}</option>
                    {/foreach}
                </select>
                *}
            </div>
        </div>

        <div class="form-block">
            <label>{$lang.email}</label>
            <div><input class="form-control"name="email" value="{$model.email}"/></div>
        </div>

        <div class="form-block">
            <label>{$lang._users.active}</label>
            <div><input type="checkbox" name="active" value="1" {if $model.active}checked="checked"{/if}/></div>
        </div>

        <!-- level -->

        <div class="form-block">
            <label>{$lang._users.level}</label>
            <div>
                <select name="level">
                    {* sync with users_collection::$_levels *}
                    <option value="1" {if $model.level == 1}selected="selected"{/if}>{$lang._users.level_user}</option>
                    <option value="50" {if $model.level == 50}selected="selected"{/if}>{$lang._users.level_mod}</option>
                    <option value="100" {if $model.level == 100}selected="selected"{/if}>{$lang._users.level_admin}</option>
                </select>
            </div>
        </div>


        <div class="form-block">
            <label>Аватар</label>
            <div>

                {control type='image' name="avatar" value=$model.avatar field=$entity.fields.avatar}

            </div>
        </div>


    </div>

{/block}
