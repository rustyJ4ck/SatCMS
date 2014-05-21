{* 
$Id: content.tpl,v 1.1.2.1 2012/05/17 08:58:22 Vova Exp $
Content tabs include
*}


{if count($data)}

    {foreach $data as $tab}
    <!-- EXTRA_TAB {$tab.name} -->
    <div id="t-{$tab.name}" class="tab-pane">

        {foreach $tab.fields as $f}
        <div class="form-block">

            <label>{$f.title}</label>
            <div {if $f.description}data-popover="true" data-content="{$f.description}"{/if}>
                {$f.control}
            </div>

        </div>
        {/foreach}

    </div>
    {/foreach}

{/if}

