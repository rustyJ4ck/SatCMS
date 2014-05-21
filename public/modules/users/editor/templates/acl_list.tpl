
<!--
шаблон
-->

<table cellpadding="5" cellspacing="0" border="0">

<tr>

<!--<a href="index.php?c={$g_c}&op=new"><img src="templates/images/b_new.gif" alt="" border="0"/></a>   
-->
<th>
<a href="index.php?m={$req.m}&c={$req.c}&do={$req.do}&op=new">
<img src="{$config.site_url}editor/templates/images/b_add_page.gif" alt="" border="0"/>
</a> 
</th> 
<th>{$lang._users.login}</th>
<th>{$lang._users.nick}</th> 
<!--th>{$lang.m_users.password}</th-->

<th>{$lang._users.last_update}</th>
<th>{$lang.email}</th>
<th>Платный</th>

<th nowrap="nowrap" width="50">{$lang.ops}</th>
</tr>

<TBODY id="{$req.c}_table">

<!-- {if $smarty.foreach.out.index is odd}class="row1"{/if} -->

                                       
{assign var="index_" value="0"}
{foreach key=i name=out item=item from=$tpl_users}   

    <tr id="{$req.c}_item_{$item.id}" {if $item.level == 100}class="backred"{elseif $item.level == 50}class="backgreen"{/if}   >

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
    {$item.last_login} &nbsp;
    </td>    
    
    <td class="first">
    {$item.email} &nbsp;
    </td>    

    <td class="first">
    {if $item.payd_user}
    <b style="color:darkgreen">{$item.payd_till}</b>
    {else}                                              
    <b style="color:darkred">-</b>
    {/if}
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

<!--
<div id="meee" style="width:100px; height:100px; background-color:red;">99</div>

<script>
$("#meee").corner("10px");
</script>
-->
