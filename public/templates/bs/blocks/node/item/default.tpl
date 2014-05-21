{$node = $block.data}

<div>

    <div {if NOT empty($node.children)}class="col-xs-9"{/if}>

        {if NOT empty($node.node_images)}
            {include file="shared/gallery/default.tpl" images=$node.node_images}
        {/if}


        <div {if $user.level >= 50} class="editable-wysiwyg" data-ctype="sat.node" data-field="text" data-id="{$node.id}"{/if}>
            {if !empty($node.text)}
                {$node.text}
            {else}
                ...
            {/if}
        </div>

    </div>

    {*if empty($node.children)}
    {include file="partials/vk.comments.tpl"}
    {/if*}

    {if NOT empty($node.children)}

        <div class="col-xs-3">

            <div class="list-group">
        <span class="list-group-item list-group-item-info">
            Подразделы
        </span>
                {foreach $node.children as $childNode}
                    <a class="list-group-item" href="{$childNode.urls.self}">{$childNode.title}</a>
                {/foreach}
            </div>

        </div>

    {/if}

</div>






{if NOT empty($node.node_files)}


    <div class="well">

        <b><i class="glyphicon glyphicon-floppy-save"></i> Прикрепленные файлы</b>

        <div class="padded-top"><ol>

                {foreach $node.node_files as $file}

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

{*$node.extrafs|debug_print_var*}

{* Comments *}
{if !empty($node.extrafs.options.fields.commentable.fvalue)}
    {include "shared/comments/default.tpl" parent=$node}
{/if}


{if $user.level >= 50}

    <div class="btn-group input-group btn-group-sm">
        <span class="input-group-addon">Админ:</span>
        <a class="btn btn-info" href="{$node.urls.editor_view}">
            <span class="glyphicon glyphicon-th-list"></span>&nbsp; Список</a>
        <a class="btn btn-primary" href="{$node.urls.editor_edit}">
            <span class=" glyphicon glyphicon-pencil"></span>&nbsp; Править</a>
    </div>
{/if}



