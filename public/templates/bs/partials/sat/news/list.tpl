{*include file="content/_shared/snets.tpl"  class="news"*}

{$news = $return.filter.collection}

<section class="news-list">

{foreach $news as $item}

    <div class="media">
        <a class="pull-left" href="{$item.urls.self}">

            {if $item.image.thumbnail.url}
                <img class="media-object thumbnail" src="{$item.image.thumbnail.url}" alt="{$item.title}"/>
            {else}
                <img class="thumbnail" src="http://placehold.it/128x96" align="left" alt="{$item.title}"/>
            {/if}

        </a>
        <div class="media-body">
            <h4 class="media-heading">
                <a href="{$item.urls.self}">{$item.title}</a></h4>

            {if $user.level >= 50}<div class="editable-wysiwyg" data-ctype="sat.news" data-field="description" data-id="{$return.news_item.id}">{/if}
                {$item.description}
            {if $user.level >= 50}</div>{/if}


            <div class="label label-info">
                {if $item.category}Категория: <a href="{$item.category.urls.self}" class="text-white">{$item.category.title}</a>,{/if}
                Добавлено: {$item.created_at}
            </div>

        </div>
    </div>

{/foreach}

</section>


{*
<ul class="news_list">
{foreach $news as $item}
    <li>

        <a class="title" href="{$item.urls.self}">{$item.title}</a>

        {if $item.image.thumbnail}
        <img class="thumbnail" src="{$item.image.thumbnail.url}" align="right" hspace="8" vspace="8" alt="{$item.title}"/>
        {/if}

        <div class="desc">
        {$item.description}

        </div>

        <div class="clearfix">
        <div style="float:right;clear:right"><a href="{$item.urls.self}">Читать далее</a></div>
        <div class="help" style="float:left;">Опубликовано: {$item.date_posted}{if $item.author}, Автор фото:

            {if 'www' == $item.author|substr:0:3}
                <a href="http://{$item.author}">Источник</a>
            {elseif 'http' == $item.author|substr:0:4}
                <a href="{$item.author}">Источник</a>
            {else}
                {$item.author}
            {/if}
        {/if}
        <br/>
            {if $item.cat_id}
                Категория: <a href="{$item.category.urls.self}">{$item.category.title}</a>
            {/if}
        </div>
        </div>

    </li>
{/foreach}
</ul>
*}

{include
    "shared/pagination/default.tpl"
    pagination=$return.filter.pagination
}