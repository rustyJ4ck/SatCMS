{*
    $actionUrlExtra = '&pid=1'

    model = {data: [items], fields: [vf]}

    model => Array (13)
      id => Array (2)
        type => "numeric"
        autoincrement => true
      title => Array (2)
        type => "text"
        size => 127
*}

{*$model|debug_print_var*}

{* extra url postfix *}

{if !$inGrid}
    {if $params}{$params = $params|to_array}{/if}
    {if !empty($btnEdit)}{$btnEdit = $btnEdit|to_array}{/if}
    {if !empty($actionUrlExtra)}{$actionUrlExtra = "&`$actionUrlExtra`"}{/if}
{/if}

{if $list}{$list = $list|to_array}{/if}

{*$buttons|debug_print_var*}

{if $config.debug}
    {$btnNew|debug_print_var}
    {$btnEdit|debug_print_var}
{/if}

{$i18n = $i18n|default:""}
{$fields_count = 2}

{$ignoredFields = ['id', 'position']}

{*ngcontroller*}
{if !empty($list.controller)}
<section {if !$inGrid}class="compilable"{/if}><div ng-controller="{$list.controller}">
{/if}

{$page = "&start=`$req.start`"}

{block 'list-top' hide}
{/block}

<div class="box model-data">

    {*
    <div class="box-header">
        <span class="title">Here be your tasks</span>
        <ul class="box-toolbar">
            <li><span class="label label-green">2 left</span></li>
        </ul>
    </div>
    *}

    <div class="box-content">

        <table class="table table-normal {if $params.sortable}table-sortable{/if} {$class}"
               data-base="{$config.base_url}{$actionUrlExtra}">

            {*class="nice_borders" rel="{$config.base_url}"*}

            <thead>
            <tr class="nodrag">

                <td class="fit">ID</td>

                {foreach $model.fields as $fid => $field}
                {if !$field.hidden && !in_array($fid, $ignoredFields)}{assign var="fields_count" value=$fields_count + 1}
                    <td class="{if $field.class}{$field.class}{/if}{if $field.type!='text' AND $field.type!='virtual'} fit{/if}"
                        {if !empty($field.attrs)}{$field.attrs}{/if}
                        {if !empty($field.description)}data-popover="true" data-placement="top" data-content="{$field.description}" data-container="table" {/if}
                    >{if !empty($field.title)}{$field.title}{else}{$fid|i18n:$i18n}{/if}</td>
                {/if}
                {/foreach}

                {if $params.sortable}
                    <td class="fit"><span class="glyphicon glyphicon-sort"></span></td>
                {/if}

                <td class="fit">{"ops"|i18n}</td>
{*
                <td>ID</td>
                <td>{"name"|i18n}</td>
                <td>{"value"|i18n}</td>
                <td>{"ops"|i18n}</td>
*}
            </tr>
            </thead>

            <TBODY id="{$req.c}_table">

            <!-- {if $smarty.foreach.out.index is odd}class="row1"{/if} -->



            {foreach $model.data as $i => $item}


                <tr data-id="{$item.id}"
                    data-position="{$item.position}"
                    {if $item.b_is_global}class="label-info"{/if}   >

                    <td data-popover="true" data-content="ID: {$item.id}" data-container="tbody" data-placement="left">
                        <input type="checkbox" value="{$item.id}" name="{$gridName}_id">
                    </td>

                    {foreach $model.fields as $key => $field}
                        {if !$field.hidden AND $key != 'id' && !in_array($key, $ignoredFields)}

                            {$value = $item[$key]}

                            <td {if $fied.type == 'numeric' || $field.type == 'unixtime'}class="nowrap"{/if}>


                                {if 'file' == $field.type}

                                    {if !$value.url}
                                        <span class="glyphicon glyphicon-remove"></span>
                                    {else}
                                    <a class="bootbox btn btn-info btn-sm" href="{$value.url}"
                                       data-title="Файл"
                                       data-content="<a href='{$value.url}' target='_blank'>{$current.site.urls.self}{$value.url}</a>"
                                       >файл</a>
                                    <span class="help"> ({math equation="round(x/1024,2)" x=$value.size} KB)</span>
                                    {/if}

                                {elseif 'image' == $field.type}

                                    {if $value.thumbnail.url}

                                        <a href="#" class="bootbox"
                                           data-title="{$field.title}"
                                           data-content="<div class='bootbox-preview'><img src='{$value.url}'/></div>">

                                            <img src="{$value.thumbnail.url}" class="thumbnail"/>

                                           </a>
                                    {else}
                                        <span class="glyphicon {if $value.url}glyphicon-ok text-success{else}glyphicon-remove text-danger{/if}"></span>
                                    {/if}

                                {elseif 'boolean' == $field.type}

                                    {if !$field.editable}
                                        {*<input type="checkbox" disabled="disabled" readonly="readonly" {if $item[$key]}checked="checked"{/if}/>*}
                                        <span class="glyphicon {if $value}glyphicon-ok text-success{else}glyphicon-remove text-danger{/if}"></span>
                                    {else}

                                        <input class="editable" type="checkbox"
                                               data-method="post" data-field="{$key}"
                                               value="1" {if $value}checked="checked"{/if}
                                               href="{$config.editor_url}?sef=false&m={$req.m}&c={$req.c}&op=change_field&id={$item.id}{$actionUrlExtra}"
                                               {*href="{$config.base_url}&op=change_field&id={$item.id}&pid={$item.pid}&sef=false"*}
                                                />
                                    {/if}


                                {else}


                                    {*textfield*}
                                    {if $field.editable}

                                        <a href="#"
                                           class="editable"
                                           data-type="text"
                                           data-url="{$config.base_url}{$actionUrlExtra}"
                                           data-pk="{$item.id}"
                                           data-params="{ id: {$item.id}, op:'change_field', field: '{$key}' }"
                                                >{$value}</a>
                                    {else}

                                        {$item[$key]}

                                    {/if}

                                {/if}



                            </td>
                        {/if}
                    {/foreach}

                    {if $params.sortable}
                        <td class="drag-handle">
                            <span type="button" class="btn btn-warning btn-xs">
                            <i class="glyphicon glyphicon-sort"></i>
                            </span>
                        </td>
                    {/if}

                    <td class="btn-group-xs">

                        {if $params.edit !== 0}
                        <a type="button" class="btn btn-default btn-xs glyphicon glyphicon-pencil"
                           data-title="{$btnEdit.dialogTitle|default:"Правка элемента"}"
                           href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}&embed=yes{$actionUrlExtra}{$page}"
                           {if $btnEdit.dialog}dialog="{$btnEdit.dialog}"{/if}
                                >
                        </a>
                        {/if}

                        <a type="button" class="btn btn-default btn-xs a-delete glyphicon glyphicon-trash text-danger"
                           data-href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=drop&id={$item.id}{$actionUrlExtra}">
                        </a>

                    </td>


                    {*
                    <td>
                        {if $item.title}
                            {$item.title}
                            <div class="help">({$item.name})</div>
                        {else}
                            {$item.name}
                        {/if}
                    </td>

                    <td>
                        <a href="#" class="editable" data-type="text"
                           data-url="{$config.base_url}"
                           data-pk="{$item.id}"
                           data-params="{ id: {$item.id}, op:'change_field', field: 'value' }"
                                >{$item.value}</a>
                    </td>

                    <td class="btn-group-xs" >

                        <a type="button" class="btn btn-default btn-sm glyphicon glyphicon-pencil"
                           href="index.php?m={$params.m}&c={$params.c}&do={$params.do}&op=edit&id={$item.id}&embed=yes"
                           dialog="{ldelim}width:540,height:380{rdelim}"
                                >
                        </a>

                        <a type="button" class="btn btn-default btn-sm a-delete glyphicon glyphicon-trash"
                           data-href="index.php?m={$params.m}&c={$params.c}&do={$params.do}&op=drop&id={$item.id}">
                        </a>

                    </td>
*}

                </tr>


                {foreachelse}

                <tr class="empty">
                    <td colspan="{$fields_count}">
                        {$lang.empty}
                    </td>
                </tr>

            {/foreach}

            </TBODY>

        </table>

        {* pager *}

        {if !empty($return.list.pagination.count) || $list.footer !== 0}
            <div class="table-footer">
                {include 'partials/pagination.tpl' pagination=$return.list.pagination}
            </div>
        {/if}

    </div>
</div>


{block 'list-bottom' hide}
{/block}


{if !empty($list.controller)}
</div></section>
{/if}
