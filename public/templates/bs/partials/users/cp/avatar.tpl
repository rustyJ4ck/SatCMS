      
{*
#{$user.avatar.url}#{$user.avatar.file}#
*}
      
<form method="POST" enctype="multipart/form-data" id="frm_avatar" onsubmit="$(this).ajaxSubmit({ldelim}dataType : 'json', success: avatar_update{rdelim}); return false;">  

    <div class="form"> 

        <p class="left">{$lang._users.avatar}</p>
        <p class="right"> 
        {if $user.avatar.url}
            <img id="u_avatar" src="{$user.avatar.url}">    
        {else}            
            <img id="u_avatar" />
            <i id="no_avatar">{$lang._users.no_avatar}</i>
        {/if}
        </p>
                                                             
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
        <input type="hidden" name="with_ajax" value="true" >

        <p class="left">Изображение</p>
        <p class="right"> <input type="file" name="avatar" size="0" style="width:70px;" cvalue="Выбрать"/>    
        <br/><span class="help">{$lang._users.change_avatar_help}</span>
        </p>

        
        <p class="panel">    
        <input type="hidden" name="op" value="update_profile"/>        
        <input type="submit" value="{$lang.save}" />
        </p>
        
      
    </div>
    
</form> 

{literal}

<script type="text/javascript"> 
function avatar_update(data) {
    if (data.status) {
        $('#u_avatar').attr('src', data.avatar);   
        $('#no_avatar').hide();
        $.tf_message('Аватар успешно изменен'); 
    }
    else {
        $.tf_message(data.message);
    }
}  
</script>

{/literal}