
<table
       id="table-nodes"
       class="table table-normal table-sortable"
       data-base="{$config.base_url}&pid={$req.pid}">

    <thead>
    <tr class="nodrag">

        <!--<a href="index.php?c={$g_c}&op=new"><img src="templates/images/b_new.gif" alt="" border="0"/></a>
        -->

        <td class="fit">ID</td>

        <td>Заголовок</td>

        <td class="fit"  data-popover="true" data-content="Добавить подраздел" data-container="tr" data-placement="top">SUB</td>
        <td class="fit">Ссылка</td>
        <td class="fit" data-popover="true" data-content="Active flag" data-container="tr" data-placement="top">A</td>
        <td class="fit" data-popover="true" data-content="Featured flag" data-container="tr" data-placement="top">F</td>
        <td class="fit" data-popover="true" data-content="System flag" data-container="tr" data-placement="top">S</td>
        <td class="fit">Изменен</td>
        <td class="fit">Подразделы</td>

        <td class="fit"><span class="glyphicon glyphicon-sort"></span></td>
        <td class="fit">{$lang.ops}</td>
    </tr>

    </thead>
    
    <TBODY id="{$req.c}_table">

    {foreach $return.list.collection.data as $i => $item}

        <tr data-id="{$item.id}"
            data-position="{$item.position}"
            {if !$item.active}class="inactive"{/if}   >

            <td data-popover="true" data-content="ID: {$item.id}" data-container="tr" data-placement="left"
                    >
                <input type="checkbox" value="{$item.id}" name="nodes_id">
            </td>

            {*
            <td>
                {if $item.b_draft}
                    черновик
                {elseif NOT $item.active}
                    неактивен
                {else}
                    {$item.id}
                {/if}
                &nbsp;
            </td>
            *}

            <td>
                {*
                <input class="quick_edit noborder" value="{$item.title|escape}" rel="{$item.id}" name="title" size="40" _value="{$item.title}"/>
                *}
                <a href="#" class="editable"
                   data-type="text"
                   data-url="{$config.base_url}"
                   data-pk="{$item.id}"
                   data-params="{ id: {$item.id}, op:'change_field', field: 'title' }"
                >{$item.title}</a>
            </td>

            <td>
                <a dialog="true"
                   class="btn-info btn btn-xs"
                   href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&pid={$item.id}&op=new&embed=yes"
                   data-title="Добавить подраздел"
                        >
                    <span class="glyphicon glyphicon-plus"></span>
                </a>
            </td>

            <td>
                <a  class="btn btn-xs btn-dlg-link btn-warning"
                        target="_site"
                        href="{$item.urls.full}">ссылка</a>
            </td>

            <td>
                <input class="editable" type="checkbox" value="1"
                       {if $item.active}checked="checked"{/if}
                       href="{$config.editor_url}?sef=false&m={$req.m}&c={$req.c}&do={$req.do}&op=active&id={$item.id}&pid={$item.pid}"
                       data-callback="$(this).parents('tr').toggleClass('inactive');"
                        />
            </td>


            <td>
                <input class="editable" type="checkbox" value="1"
                       {if $item.b_featured}checked="checked"{/if}
                       href="{$config.editor_url}?sef=false&m={$req.m}&c={$req.c}&do={$req.do}&op=toggle_featured&id={$item.id}&pid={$item.pid}" />

            </td>

            <td>
                <input class="editable" type="checkbox" value="1"
                       {if $item.b_system}checked="checked"{/if}
                       href="{$config.editor_url}?sef=false&m={$req.m}&c={$req.c}&do={$req.do}&op=toggle_system&id={$item.id}&pid={$item.pid}" />

            </td>


            <td class="nowrap">
                {$item.updated_at}
            </td>

            <td>
                {if $item.c_children}
                <a class="btn btn-xs btn-info"
                   href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&pid={$item.id}">
                    <span class="glyphicon glyphicon-folder-open"></span><sup>&nbsp;{$item.c_children}</sup>&nbsp; подразделы
                </a>
                {/if}
            </td>

            <td class="drag-handle">
                            <span type="button" class="btn btn-warning btn-xs">
                            <i class="glyphicon glyphicon-sort"></i>
                            </span>
            </td>

            <td class="btn-group-xs" >

                <a type="button" class="btn btn-default btn-sm glyphicon glyphicon-pencil"
                   href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}&pid={$item.pid}{$page}"
                   d1ialog="{ldelim}width:540,height:380{rdelim}"
                        >
                </a>

                <a type="button" class="btn btn-default btn-sm a-delete glyphicon glyphicon-trash"
                   data-href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=drop&id={$item.id}&pid={$item.pid}">
                </a>

                {*
                 <a class="dialog" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&pid={$item.id}&op=new&embed=yes"
                        title="Добавить новый"
                        dialog="{ldelim}width:960,height:360{rdelim}"
                    >
                    <img  src="{$config.site_url}editor/templates/images/b_add_page.gif" alt="" />
                    </a>
                  &nbsp;

                  <a href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}&pid={$item.pid}"
                        title="Изменение элемента"
                  >
                 <img src="{$config.site_url}editor/templates/images/b_edit.png" alt="" border="0"/>
                 </a>

                &nbsp;
            {if NOT $item.b_deleted}
                <a class="a_delete" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=drop&id={$item.id}&pid={$item.pid}"><img src="{$config.site_url}editor/templates/images/b_drop.png" alt="" border="0"/></a>
            {else}
                <a class="a_delete"
                href="index.php?m={$req.m}&c={$req.c}&do=purge&op=drop&id={$item.id}&pid={$item.pid}"><img src="{$config.site_url}editor/templates/images/b_drop.png" alt="" border="0"/></a>
            {/if}

                 *}
            </td>

        </tr>


        {foreachelse}

        <tr class="nodrag empty"><td colspan="10" style="padding:15px;text-align:center">

                {$lang.empty}

            </td></tr>

    {/foreach}

    </TBODY>

</table>


<div class="table-footer">

    {* pagination *}
    {include "partials/pagination.tpl" pagination=$return.list.pagination}

</div>
