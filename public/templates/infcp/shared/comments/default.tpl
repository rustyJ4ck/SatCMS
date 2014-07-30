{* $comments =   *}

<section class="cloak">

    <div class="panel panel-default comments-widget">

        <div class="panel-heading">
                {if NOT $commentbox_title}Обсудить публикацию{else}{$commentbox_title}{/if}
        </div>

        <div class="panel-body">

            {if !$parent.id}
                <label class="label label-danger">Comments: Parent not assigned</label>
            {else}
                {include file="./list.tpl"}
                {include file="./form.tpl"}
            {/if}
        </div>
    </div>

</section>