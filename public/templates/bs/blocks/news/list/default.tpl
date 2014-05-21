{extends 'blocks/block.tpl'}

{block 'title'}
    {$block.params.title|default:$block.title}
{/block}

{block 'content'}

    <ul class="nav nav-stacked">
        {foreach $block.data as $item}
            <li>
                <a href="{$item.urls.self}"><span class="badge pull-right">{$item.updated_at}</span>{$item.title}</a>
            </li>
        {/foreach}
    </ul>

{/block}