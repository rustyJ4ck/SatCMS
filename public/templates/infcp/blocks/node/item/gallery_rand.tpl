
{if count($block.data.node_images)}

    {math equation="-1 + rand(x,y)" x=1 y=$block.data.node_images|@count assign="ni_count"}
    
    <a href="{$block.data.urls.self}">
    <img src="{$block.data.node_images[$ni_count].file.thumbnail.url}" border="0">
    </a>

    <div class="more_link">
    <a href="{$block.data.urls.self}" class="link">[+] Смотреть</a>
    </div>

{/if}

