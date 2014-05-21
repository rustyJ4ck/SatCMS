
{if NOT EMPTY($model.acls.objects)}

    {*$model.acls|debug_print_var*}

    <div class="form-block">
        <label>Права доступа</label>
        <div>

            <table class="table table-normal table-bordered">




                {foreach $model.acls.objects as $object}

                    <thead>
                    <tr>
                        <td>{$object.title} ({$object.name})</td>{*<td>ID</td>*}
                        {foreach $model.acls.actions as $ac}
                            <td>{$ac.title}</td>
                        {/foreach}
                        <td></td>
                    </tr>
                    </thead>


                    <tbody>
                    {*
                    <tr>
                        <td colspan="9" class="norborder" align="center" style="padding:10px">{$object.title} ({$object.name})</td>
                    </tr>
                    *}

                    {foreach $object.items as $acs}
                    <tr>
                            {*<td>{$acs.id}</td>*}<td align="right">{$acs.title}</td>
                            {foreach $model.acls.actions as $ac}

                                <td align="center" >

                                    <input type="checkbox"
                                           class="perms"
                                           {*col="{$smarty.foreach.col.index}" row="{$row}"*}
                                           name="acls[{$object.name}][{$acs.id}][{$ac.id}]"
                                           {if $acs.actions[$ac.id].allow}checked="checked"{/if}
                                           value="1"
                                     />

                                    &nbsp;

                                </td>
                            {/foreach}

                            <td class="opacity50 nowrap">

                               <div class="btn-group-xs">

                                   <a class="btn btn-default" onclick="jq_toggle_check_row({$row}, 3); return false;">
                                        <span class="glyphicon glyphicon-ok"></span> </a>

                                    <a class="btn btn-default" onclick="jq_toggle_check_row({$row}, 2); return false;">
                                        <span class="glyphicon glyphicon-remove"></span></a>

                                    <a class="btn btn-default" onclick="jq_toggle_check_row({$row}); return false;">
                                        <span class="glyphicon glyphicon-resize-small"></span></a>

                               </div>

                            </td>
                        </tr>
                    {/foreach}

                    </tbody>

                {/foreach}

                <tfoot>

                <tr>
                    <td ></td>
                    {foreach $model.acls.actions as $ac}
                        <td class="opacity50" align="center">

                            <div class="btn-group-xs">
                            <a class="btn btn-default" onclick="jq_toggle_check_col({$row}, 3); return false;">
                                <span class="glyphicon glyphicon-ok"></span> </a>

                            <a class="btn btn-default" onclick="jq_toggle_check_col({$row}, 2); return false;">
                                <span class="glyphicon glyphicon-remove"></span></a>

                            <a class="btn btn-default" onclick="jq_toggle_check_col({$row}); return false;">
                                <span class="glyphicon glyphicon-resize-small"></span></a>

                            </div>

                            {*
                            <a href="" onclick="jq_toggle_check_col({$smarty.foreach.col.index}, 3); return false;">
                                <img src="/templates/sestat/_assets/images/tick.png" border="0"/></a>
                            <a href="" onclick="jq_toggle_check_col({$smarty.foreach.col.index}, 2); return false;">
                                <img src="/templates/sestat/_assets/images/cross.png" border="0"/></a>
                            <a href="" onclick="jq_toggle_check_col({$smarty.foreach.col.index}); return false;">
                                <img src="/templates/sestat/_assets/images/contrast.png" border="0"/></a>
                        *}
                        </td>
                    {/foreach}
                    <td>

                        <div class="btn-group-xs">
                            <a class="btn btn-default" onclick="jq_toggle_check_col({$row}, 3); return false;">
                                <span class="glyphicon glyphicon-ok"></span> </a>

                            <a class="btn btn-default" onclick="jq_toggle_check_col({$row}, 2); return false;">
                                <span class="glyphicon glyphicon-remove"></span></a>

                            <a class="btn btn-default" onclick="jq_toggle_check_col({$row}); return false;">
                                <span class="glyphicon glyphicon-resize-small"></span></a>

                        </div>

                    </td>
                </tr>

                </tfoot>

            </table>
        </div>
    </div>


{/if}
