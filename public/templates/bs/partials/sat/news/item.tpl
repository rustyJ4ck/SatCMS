<div class="news-view">

{if $config.debug}
<code>
    {$return.news_item.images|debug_print_var}
</code>
{/if}


<h1 {if $user.level >= 50}class="editable-inline" data-ctype="sat.news" data-field="title" data-id="{$return.news_item.id}"{/if}
        >{$return.news_item.title}</h1>

{include
    file="shared/gallery/default.tpl"
    images=$return.news_item.images
}

{if $return.news_item.image.url}
    <img class="thumbnail" src="{$return.news_item.image.url}" align="left"
        style="margin:0 12px 10px 0" alt="{$return.news_item.title}"/>
{/if}

    {if $user.level >= 50}<div class="editable-wysiwyg" data-ctype="sat.news" data-field="text" data-id="{$return.news_item.id}">{/if}
    {$return.news_item.text}
    {if $user.level >= 50}</div>{/if}

<br clear="all"/>

<div class="label label-info">

    Опубликовано: {$return.news_item.created_at}

    {if $return.news_item.author}
        Автор:
        {if 'www' == $return.news_item.author|substr:0:3}
            <a href="http://{$return.news_item.author}">Источник</a>
        {elseif 'http' == $return.news_item.author|substr:0:4}
            <a href="{$return.news_item.author}">Источник</a>
        {else}
            {$return.news_item.author}
        {/if}
    {/if}

    {if $return.news_item.cat_id}Категория: <a href="{$return.news_item.category.urls.self}">{$return.news_item.category.title}</a>{/if}
    {if $return.news_item.keywords}{if $return.news_item.cat_id}, {/if}
     Теги: {$return.news_item.keywords}{/if}

</div>


<br/>
<br/>

    {if $user.level >= 50}

        <div class="btn-group input-group btn-group-sm padded-vert">
            <span class="input-group-addon">Админ:</span>
            <a class="btn btn-primary" href="{$return.news_item.urls.editor_edit}">
                <span class=" glyphicon glyphicon-pencil"></span>&nbsp; {$lang.edit}</a>
        </div>

    {/if}

{* comments *}
{include
    "shared/comments/default.tpl"
    parent=$return.news_item
}
{* /comments *}

<br/>
<br/>

{if $return.news_item.similar}
Смотрите также:

    <ul>
    {foreach item=p from=$return.news_item.similar}  
    <li><a class="title" href="{$p.urls.self}">{$p.title}</a>
    {/foreach}
    </ul>

{/if}


<a href="/news/">Все новости</a>

</div>