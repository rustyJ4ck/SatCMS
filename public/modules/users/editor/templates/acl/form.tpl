{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes"}
    {$model = $return.form.data}
{/block}

{block 'form'}

    <div class="form">

        <div class="form-block">
            <label>{$lang._users.login}</label>
            <div><input type="text" class="form-control"  name="login" size="40" value="{$return.form.login}"/></div>
        </div>

        <div class="form-block_highlight">
            <label>{$lang._users.password}</label>
            <div><input type="text" class="form-control"  name="password" size="40" value="{$return.form.password}"/></div>
        </div>

        <div class="form-block">
            <label>{$lang._users.nick}</label>
            <div><input type="text" class="form-control"  name="nick" size="40" value="{$return.form.nick}"/></div>
        </div>

        <div class="form-block">
            <label>{$lang.email}</label>
            <div><input type="text" class="form-control"  name="email" size="40" value="{$return.form.email}"/></div>
        </div>

        <div class="form-block hidden">
            <label>{$lang._users.active}</label>
            <div><input type="checkbox" name="active" value="1" {if $return.form.active}checked="checked"{/if}/></div>
        </div>

        <!-- level -->

        <div class="form-block">
            <label>{$lang._users.level}</label>
            <div>
                <select name="level">
                    {* sync with users_collection::$_levels *}
                    <option value="1" {if $return.form.level == 1}selected="selected"{/if}>{$lang._users.level_user}</option>
                    <option value="50" {if $return.form.level == 50}selected="selected"{/if}>{$lang._users.level_mod}</option>
                    <option value="100" {if $return.form.level == 100}selected="selected"{/if}>{$lang._users.level_admin}</option>
                </select>
            </div>
        </div>

    </div>

{/block}