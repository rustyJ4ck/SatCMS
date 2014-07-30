<h1>{$lang._users.title_login}</h1>
<br/>

<div>  

{if NOT $user.logged_in}
{* anonymous *}
    
    {* login form *}
    <form id="lp_form_body" class="_validable" method="post" action="{$config.site_url}users/login/" 
    >    
    <div class="form" 
    {if !$return.redirect && !$return.sredirect}  
    style="width:300px;"
    {/if}
    >
    
    <div class="clearfix">
    <div class="left">{$lang._users.login}</div>
    <div class="right">        
        <input id="login" name="login" type="login" value="" validate="{ldelim}email:1,required:1{rdelim}"/>
        <label class="error" for="login">Укажите логин</label>
    </div>
    </div>
        
    <div class="clearfix">
    <div class="left">{$lang._users.password}       </div>
    <div class="right">        
                <input name="password" type="password" value="" validate="{ldelim}minlen:4,required:1{rdelim}"/>     
                <label class="error" for="password">Укажите пароль</label>
    </div>
    </div>            

    <p class="panel">
    <input type="submit" value="{$lang._users.login_me}" />
    </p>

    </div>                
    
    {if $return.redirect} <input name="redirect" type="hidden" value="{$return.redirect}"/>{/if}
    {if $return.sredirect}<input name="sredirect" type="hidden" value="{$return.sredirect}"/>{/if}
    </form>
    
    {if $return.redirect || $return.sredirect}
    <script>
    {literal}
    
    $('#lp_form_body').find('input[type=submit]').after('&nbsp;<input type="button" onclick="$(this).hide();$.unblockUI();" value="Закрыть"/>')
    
    $.blockUI({ message: $('#lp_form_body'), css: { width: '400px' } });
    
       // validable just selector dont work                  
       $.metadata.setType("attr", "validate");  
       $("#lp_form_body").validate({
                   submitHandler: function(form) {
                        $(form).ajaxSubmit({dataType : 'json', success: function(data){
                                     tf.user_login(data);
                                }
                        });
                   }
                   , highlight: false
               });
    {/literal}
    </script>
    {/if}
    
{else}

    
    <h2 style="display:inline;margin-right:10px;">{$lang.hello}, {$user.nick}</h2>
    
{/if}


</div>

{literal}
<script type="text/javascript">
user_login = function(data) {

if (data.status)
	// redirect to?
	tf.redirect('/');
else {
	tf.user_login(data);
}	

}
</script>
{/literal}
