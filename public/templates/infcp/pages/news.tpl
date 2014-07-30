{extends 'root.base.tpl'}

{block 'sidebar'}

    <div id="sidebar">
        {satblock
            action="sat.news_category"
            name="sidebar"
        }
    </div>

{/block}

{block 'footer'}{/block}