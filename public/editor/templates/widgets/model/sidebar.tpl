@@@

{block name="sidebar" hide}

    <div class="right-sidebar">

        {block name="sidebar-top"}
        {/block}

        <div class="box form-data">
            <div class="box-header">
                <span class="title">{$sidebarTitle|default:'Sidebar'}</span>
            </div>
            <div class="box-content padded">
                {$smarty.block.child}
            </div>
        </div>


        <div class="box form-data">
            <div class="box-header">
                <span class="title">{$sidebarTitle|default:'Sidebar'}</span>
            </div>
            <div class="box-content padded">
                {$smarty.block.child}
            </div>
        </div>

        {block name="sidebar-bottom"}
        {/block}

    </div>

{/block}