{$actionUrlExtra = "&pid=`$req.pid`"}
{$page = "&start=`$req.start`"}

{include './filter.tpl'}

{*$current|debug_print_var*}

{include "./list.nav.tpl" currentID=$req.pid}

<div class="box model-data">

    {*
    <div class="box-header">
    </div>
    *}

    <div class="box-content">

        {include "./list.table.tpl"}

    </div>



</div>
