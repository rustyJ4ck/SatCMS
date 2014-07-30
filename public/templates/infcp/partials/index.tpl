{* frontpage partial *}

<div {if $user.level >= 50} class="editable-wysiwyg" data-ctype="sat.site" data-field="text" data-id="{$current.site.id}"{/if}>
{$current.site.text}
</div>

<div class="jumbotron">
{satblock action="sat.node" name="main-page"}
</div>

<div class="">
{satblock action="sat.news" count="3" title="Последние новости"}
</div>
