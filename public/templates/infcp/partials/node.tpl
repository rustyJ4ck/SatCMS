{*deprecated*}


{*if NOT empty($current.node_chain)*}

{if $with_nav !== false}

<ul class="nav nav-pills">
<li><a href="/">Главная</a></li>
{if NOT empty($current.node_chain)} 
{foreach $current.node_chain as $node}
    <li><a href="{$node.urls.self}">{$node.title}</a></li>
{/foreach}
{/if}
</ul>

{/if}

<h1{if $user.level >= 50} class="editable-inline" data-ctype="sat.node" data-field="title" data-id="{$current.node.id}"{/if}>{$current.node.title}</h1>

<div class="row padded-bottom">

    <div
        {if NOT empty($current.node.children)}class="col-xs-9"{/if}
    >

        {if NOT empty($current.node.node_images)}
            {include file="shared/gallery/default.tpl" images=$current.node.node_images}
        {/if}


            <div {if $user.level >= 50} class="editable-wysiwyg" data-ctype="sat.node" data-field="text" data-id="{$current.node.id}"{/if}>
            {if !empty($current.node.text)}
                {$current.node.text}
            {else}
                {loremipsum count=6000}
            {/if}
            </div>

    </div>

    {*if empty($current.node.children)}
    {include file="partials/vk.comments.tpl"}
    {/if*}

    {if NOT empty($current.node.children)}

    <div class="col-xs-3">

        <div class="list-group">
        <span class="list-group-item list-group-item-info">
            Подразделы
        </span>
        {foreach $current.node.children as $node}
        <a class="list-group-item" href="{$node.urls.self}">{$node.title}</a>
        {/foreach}
        </div>

    </div>

    {/if}

</div>






{if NOT empty($current.node.node_files)}   


   <div class="well">

       <b><i class="glyphicon glyphicon-floppy-save"></i> Прикрепленные файлы</b>

       <div class="padded-top"><ol>

       {foreach $current.node.node_files as $file}

           <li>
            {$file.title} &nbsp;

            {if $file.file.url}

            <a class="label label-success"
                    target="_blank"
                    href="{$file.file.url}"
                    >Открыть ({$file.file.type}, {math equation="round(x/1024,2)" x=$file.file.size} KB)</a>
                {else}-{/if}
           </li>

        {/foreach}
        </ol></div>

   </div>

{/if}

{*$current.node.extrafs|debug_print_var*}

{* Comments *}
{if !empty($current.node.extrafs.options.fields.commentable.fvalue)}
    {include "shared/comments/default.tpl" parent=$current.node}
{/if}


{if $user.level >= 50}

<br/>
<br/>

<div class="btn-group input-group btn-group-sm">
<span class="input-group-addon">Операции:</span>
<a class="btn btn-info" href="{$current.node.urls.editor_view}">
    <span class="glyphicon glyphicon-th-list"></span>&nbsp; Список</a>
<a class="btn btn-primary" href="{$current.node.urls.editor_edit}">
    <span class=" glyphicon glyphicon-pencil"></span>&nbsp; Править</a>
</div>
{/if}



