<ul class="nav nav-stacked " id="sidebar">
    {foreach $current.site.tree as $item}
        {if (!$current.node.pid && $item.pid == 0 || $current.node.pid == $item.pid) && $item.active && !$item.b_system}
            <li {if $current.node.id == $item.id}class="active"{/if}><a href="{$item.url}">{$item.title}</a></li>
        {/if}
    {/foreach}
</ul>

<div class="padded-top">
    <a class="btn btn-success"
            href="{'/editor/'|ngUrl}"
            >Личный кабинет</a>
</div>