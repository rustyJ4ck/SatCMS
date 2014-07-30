{*$user|debug_print_var*}


{*
{assign var="title" value=$lang._users.user_cp}
{if $cp.option}{assign var="title" value="`$title` - `$cp.links[$cp.option].title`"}{/if}
*}

<section id="users-cp-gw">

    <div class="col-xs-9">

    {if $cp.option}
        <div id="user_cp_option">
            {include "./cp/`$cp.option`.tpl"}
        </div>
    {else}


        <!-- default -->

        {if $user.avatar.url}
        <img src="{$user.avatar.url}" align="left" hspace="6"/>
        {/if}
        <h2>{$user.nick}</h2>

        <br/>

        {$lang._users.login} : <b>{$user.email}</b><br/>

    {/if}

    </div>

    <div class="col-xs-3">
          {include './sidebar.tpl'}
    </div>

</section>