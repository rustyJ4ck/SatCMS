{*
  array(1) {
  [0]=>
  array(3) {
    ["title"]=>
    string(14) "Проекты"
    ["name"]=>
    string(7) "project"
    ["items"]=>
    array(4) {
      [0]=>
      array(3) {
        ["id"]=>
        int(0)
        ["title"]=>
        string(1) "*"
        ["actions"]=>
        array(4) {
          [1]=>
          array(4) {
            ["title"]=>
            string(16) "Создание"
            ["name"]=>
            string(6) "create"
            ["id"]=>
            int(1)
            ["allow"]=>
            bool(false)
          }
          [2]=>
          array(4) {
            ["title"]=>
            string(12) "Чтение"
            ["name"]=>
            string(4) "read"
            ["id"]=>
            int(2)
            ["allow"]=>
            bool(false)
          }
          [3]=>
          array(4) {
            ["title"]=>
            string(18) "Изменение"
            ["name"]=>
            string(6) "update"
            ["id"]=>
            int(3)
            ["allow"]=>
            bool(false)
          }
          [4]=>
          array(4) {
            ["title"]=>
            string(16) "Удаление"
            ["name"]=>
            string(6) "remove"
            ["id"]=>
            int(4)
            ["allow"]=>
            bool(false)
          }
        }
      }

*}

{if NOT EMPTY($return.form.acls.objects)}

    <div class="form-block">
        <label>Права доступа</label>
        <div>
            {*$return.form.acls|@debug_print_var*}
            
            <table class="nice_borders">
            <tr>
            <th>Раздел</th>{*<th>ID</th>*}
                {foreach from=$return.form.acls.actions item=ac}
                <th>{$ac.title}</th>
                {/foreach}
                <th></th>
            </tr>
            
            {foreach from=$return.form.acls.objects name="row" item=object}
            <tr>
            <th colspan="9" class="norborder" align="center" style="padding:10px">{$object.title} ({$object.name})</th> 
            </tr>    
                    {foreach  from=$object.items name="row1" item=acs}
                    {math assign="row" equation="100 * `$smarty.foreach.row.index` + `$smarty.foreach.row1.index`"}
                    <tr>
                    {*<td>{$acs.id}</td>*}<td align="right">{$acs.title}</td>
                        {foreach name="col" from=$return.form.acls.actions item=ac}
                        <td align="center" >
                        
                        <input class="perms hidden" 
                            col="{$smarty.foreach.col.index}" row="{$row}"
                            name="acls[{$object.name}][{$acs.id}][{$ac.id}]" type="checkbox" 
                            {if $acs.actions[$ac.id].allow}checked="checked"{/if} value="1"/>

                        </td>
                        {/foreach}
                        <td class="opacity50">
                        <a href="" onclick="jq_toggle_check_row({$row}, 3); return false;">
                            <img src="/templates/sestat/_assets/images/tick.png" border="0"/></a>
                        <a href="" onclick="jq_toggle_check_row({$row}, 2); return false;">
                            <img src="/templates/sestat/_assets/images/cross.png" border="0"/></a>
                        <a href="" onclick="jq_toggle_check_row({$row}); return false;">
                            <img src="/templates/sestat/_assets/images/contrast.png" border="0"/></a>
                        </td>
                    </tr>
                    {/foreach}  
                  
            {/foreach}
            
            <tr>
            <td ></td>
                {foreach name="col" from=$return.form.acls.actions item=ac}
                <td class="opacity50" align="center">
                    <a href="" onclick="jq_toggle_check_col({$smarty.foreach.col.index}, 3); return false;">
                        <img src="/templates/sestat/_assets/images/tick.png" border="0"/></a>
                    <a href="" onclick="jq_toggle_check_col({$smarty.foreach.col.index}, 2); return false;">
                        <img src="/templates/sestat/_assets/images/cross.png" border="0"/></a>
                    <a href="" onclick="jq_toggle_check_col({$smarty.foreach.col.index}); return false;">
                        <img src="/templates/sestat/_assets/images/contrast.png" border="0"/></a>
                </td>
                {/foreach}
                <td></td>
            </tr>
            
            </table>
        </div>  
    </div> 

    
{/if}    

