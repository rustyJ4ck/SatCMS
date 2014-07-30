{extends 'blocks/block.tpl'}

{block 'title'}
    {$block.title}
{/block}

{block 'content'}
    <ul class="nav newcat-{$block.data.name}">

        <li>
            <a href="/news/">Все новости</a>
        </li>


        {foreach $block.data as $v}
            <li>
                <a href="{$v.urls.self}">{$v.title}</a>
            </li>
        {/foreach}
    </ul>
{/block}
