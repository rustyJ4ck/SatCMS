{extends 'root.base.tpl'}

{block 'header'}<br/>{/block}

{block 'sidebar'}

    {satblock action="sat.widgets" name="sidebar"}

{/block}

{block 'content'}

    <div class="panel panel-default">

    <div class="panel-heading">
        @HOWITWORKS|LAYOUT@
    </div>

    <div class="panel-body">
        {$smarty.block.parent}
    </div>
    </div>

    {*$current.node.text*}

{/block}

{block 'node-files'}
<div class="label-danger" style="width:100px;height:100px;">@FILES</div>
{/block}

{block 'footer'}{/block}