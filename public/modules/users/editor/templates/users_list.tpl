
<!--
шаблон
-->

<table class="nice_borders" cellpadding="5" cellspacing="0" border="0">

<tr>

<!--<a href="index.php?c={$g_c}&op=new"><img src="templates/images/b_new.gif" alt="" border="0"/></a>   
-->
<th>Активен</th>
<th>
<a href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=new">
<img src="{$config.site_url}editor/templates/images/b_add_page.gif" alt="" border="0"/>
</a> 
</th>           
<th>{$lang._users.login}</th>
<th>{$lang._users.nick}</th> 
<th>{$lang.group}</th>  

<th>{$lang.email}</th>


<th nowrap="nowrap" width="50">{$lang.ops}</th>
</tr>

<TBODY id="{$req.c}_table">

<!-- {if $smarty.foreach.out.index is odd}class="row1"{/if} -->

                                       
{assign var="index_" value="0"}
{foreach key=i name=out item=item from=$tpl_users}   

    <tr id="{$req.c}_item_{$item.id}" {if $item.level == 100}class="backred"{elseif $item.level == 50}class="backgreen"{/if}   >
    
    <td class="first" align="center">
        <input id="check_{$item.id}" type="checkbox" value="1" 
        {if $item.active}checked="checked"{/if} 
        onclick="$.get('{$config.editor_url}?sef=false&m={$req.m}&c={$req.c}&do={$req.do}&op=active&id={$item.id}&to=' + $('#check_{$item.id}').is(':checked'));$.tf_message('Статус пользователя изменен');" />
    </td>      

    <td class="first">
    {$item.id}
    </td>    

    <td class="first">
    {$item.login} &nbsp;
    </td>    
    
    <td class="first">
    {$item.nick} &nbsp;
    </td>

    <td class="first">     
    {if $item.gid && $tpl_user_group[$item.gid]}{$tpl_user_group[$item.gid].title}{else}-{/if}&nbsp;   
    </td>    
    
    <td class="first">
    {$item.email} &nbsp;
    </td>    

   
    
    <td class="last" >

    <a href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=edit&id={$item.id}"><img src="{$config.site_url}editor/templates/images/b_edit.png" alt="" border="0"/></a>
    &nbsp;

    <a class="a_delete" href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=drop&id={$item.id}"><img src="{$config.site_url}editor/templates/images/b_drop.png" alt="" border="0"/></a>

   &nbsp;
     
    </td>

    </tr>

{assign var="index_" value=$index_+1}
{foreachelse}

    <tr><td colspan="5" style="padding:10px;text-align:center">
   {$lang.empty}
    </td></tr>   

{/foreach}

</TBODY>

</table>

{* pagination *}
{include file="pagination.tpl"}