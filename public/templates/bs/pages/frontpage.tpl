{extends "root.base.tpl"}

{block 'content' prepend}

    <div class="padded-bottom">
        <span class="label-success label">
            FRONTPAGE OVERRIDE
            [site]
            frontpage[template] = pages/frontpage
        </span>
    </div>

{/block}