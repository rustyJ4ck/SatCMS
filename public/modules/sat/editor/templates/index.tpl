{extends 'partials/content.tpl'}

{*

{block name='top' prepend}    

<!-- disable block if no sites -->

{if empty($sat_sites) AND $req.c != 'sat_site'}
<script> 
$.blockUI({ldelim}message:'Создайте сайт для продолжения работы<br/><br/><a class="inverse_green" href="?m=sat&c=site">Управление сайтами</a>', css:'padding:30px;'{rdelim});
    {if $params.embed != 'yes'}
        setTimeout(function(){
        tf.redirect('?m=sat&c=site');
        }, 3000);
    {/if}   
</script>
{/if}

{if NOT $config.in_ajax AND $params.embed != 'yes' AND NOT empty($sat_sites)}

    <div style="position:fixed;right:6px;width:150px;height:40px;" class="backlight corners">
    <div style="margin:9px;">

        <form id="fchp" method="post" action="{$config.base_url}">
            
        <select id="fchp_sel" size="1" style="width:133px;cursor:pointer;"
        
        {literal}
        onchange="var chosenoption=this.options[this.selectedIndex]; if (chosenoption.value) {  $.cookie('site_id', chosenoption.value, {expires:356, path:'/editor/'});  $('#fchp').submit();       }"
        {/literal}
        
        >
        {foreach item=acc from=$sat_sites}  
        <option value="{$acc.id}" {if $current_site.id == $acc.id}selected="selected"{/if}>{$acc.domain}</option>    
        {/foreach}
        </select>
        
        <input type="hidden" name="c" value="{$req.c}"/>
        <input type="hidden" name="m" value="{$req.m}"/>
        
        </form>

    </div></div>

{/if}

{/block}      

*}