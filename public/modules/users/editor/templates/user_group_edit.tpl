        
<form action="{$config.editor_url}" method="post" name="site_mod" id="frm_" class="_validable"
>


<div class="form">

    <div class="form-block">
        <label>{$lang.title}*</label>
        <input type="text" name="title" size="40" value="{$return.form.title}" validate="{ldelim}required:1{rdelim}"/>
    </div> 

    <div class="form-block">
        <label>{$lang.name}</label>
        <input type="text" name="name" size="40" value="{$return.form.name}"/>
    </div> 
    
{if $req.op != 'new'}
{include file="./user_group_edit_full.tpl"}
{/if}   
    
    <!-- extra data -->
    
    <br clear="all"/>      
              
    <div class="form-bottom">
    <input class="main" name="item_submit" type="submit" value="Сохранить"/>
    </div>
    
  
</div>
                
<input type="hidden" name="c" value="{$req.c}"/>
<input type="hidden" name="m" value="{$req.m}"/>

<input type="hidden" name="id" value="{$req.id}"/>

</form> 



 
<script type="text/javascript">
{literal} 

    function jq_toggle_check_col(c, f) {
        $('.perms[col='+c+']').each(function(k,v){
            jq_toggle_check($(v).next('img'), f);
        });
    }

    function jq_toggle_check_row(c, f) {
        $('.perms[row='+c+']').each(function(k,v){
            jq_toggle_check($(v).next('img'), f);
        });
    }    
    
    function jq_toggle_check(i, force) {
        var ch = $(i).prev('input');
        var val = (force) ? (force==1 ? ch.attr('checked'):force-2): (ch.attr('checked')?0:1);
        ch.attr('checked', val);
        $(i).attr('src', '/templates/sestat/_assets/images/' + (val ? 'tick' : 'cross') + '.png');
            
            if (!force || force>1)
            $(i).parent()
            .addClass('backred', 200, function() {$(this).removeClass('backred', 200);});

        
    }               

    $(function(){
        $('.perms').each(function(i,v){
            $(v).parent().append('<img class="ch_img"/>');
            jq_toggle_check($(v).next('img'), 1);
        });
        
        $('.ch_img').click(function(){
            jq_toggle_check(this);    
        });
    });     
    
{/literal}     
</script>

