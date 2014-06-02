{*
, 'phone'      
, 'site'       
, 'city'       
, 'about'      
, 'birthday'   
, 'icq'        
*}
      
<form method="post" id="frm_profile">  

    <div class="form">  
    
    <fieldset>
    <legend>Профиль</legend>
    <div class="container">
    
    

     <div class="left">{$lang._users.phone}</div>
     <div class="right"> <input id="cp_f_phone" type="input" name="phone" size="15" value="{$edit_data.phone}" validate="{ldelim}phone:1{rdelim}"/>    
     <label class="error" for="cp_f_phone">Укажите телефон</label>
     </div>
     
     <div class="left">{$lang._users.site}</div>
     <div class="right"> http:// <input id="cp_f_site" type="input" name="site" size="40" value="{$edit_data.site}" validate="{ldelim}domain:1{rdelim}"/>    
     <label class="error" for="cp_f_site">Укажите URL</label>
     </div>
     
     <div class="left">{$lang._users.city}</div>
     <div class="right"> <input type="input" name="city" size="40" value="{$edit_data.city}"/>    </div>
     
     <div class="left">{$lang._users.about}</div>
     <div class="right"> <textarea type="input" name="about"  cols="40" rows="4" >{$user.about}</textarea>    </div>
     
     <div class="left">{$lang._users.icq}</div>
     <div class="right"> <input type="input" id="f_users_icq" name="icq" size="10" validate="{ldelim}digits:1{rdelim}" value="{$edit_data.icq}"/>
         <label class="error" for="f_users_icq">Укажите номер ICQ</label></div>
     
     <div class="left">{$lang._users.birthday}</div>
     <div class="right"> <input type="input" class="date" name="birthday" size="12" value="{$edit_data.birthday}"/>    </div>
     
     <div class="left">{$lang._users.gender}</div>
     <div class="right">
            <select name="gender">        
            <option value="0">{$lang._users.gender_unknown}</option>
            <option value="1" {if $user.gender_string == 'male'}selected="selected"{/if}>{$lang._users.gender_male}</option>
            <option value="2" {if $user.gender_string == 'female'}selected="selected"{/if}>{$lang._users.gender_female}</option>
            </select>
     </div>
     
     <div class="left">Компания</div>
     <div class="right"> <input type="input" name="company_title" size="40" value="{$edit_data.company_title}"/>    </div>     
     
     <div class="left">{$lang._inforcom.branch_id}</div>
     <div class="right">
        <select name="company_branch_id">
        <option value="">{$lang.undefined}</option>
        {foreach item=list from=$tpl_branch}   
            <option value="{$list.id}" {if $list.id == $edit_data.company_branch_id}selected="selected"{/if}>{$list.title}</option>
        {/foreach}
        </select>   
     </div>        
     
     
    <div class="left">
        Получать сообщения на почту
    </div>
    <div class="right">
        <input type="checkbox" name="b_notify" value="1" {if $edit_data.b_notify}checked="checked"{/if}/> 
    </div>      
     
   
   
    {* some hack *} 
    <input type="hidden" name="job_position" size="40" value="{$edit_data.job_position}"/>   
     
      
   
   
    </div>
    </fieldset>
    
    <fieldset>
    <legend>Регистрационные данные</legend>
    <div class="container">
        
        <div class="left">Имя</div>
        <div class="right"> <input id="f_users_nick" type="input" name="name" size="40" value="{$edit_data.name}" validate="{ldelim}required:1,minlength:3,string:1{rdelim}"/>
        <label class="error" for="f_users_nick">Укажите Ваше имя</label></div>

        <div class="left">Фамилия</div>
        <div class="right"> <input id="f_users_nick1" type="input" name="surname" size="40" value="{$edit_data.surname}" validate="{ldelim}required:1,minlength:3,string:1{rdelim}"/>
        <label class="error" for="f_users_nick1">Укажите Вашу фамилию</label></div>
         
        {*                                                            
        <input type="hidden" name="nick" size="40" value="{$edit_data.nick}" />
        *}
        
        <div class="left">{$lang.email}</div>
        <div class="right"> <input id="f_users_email" type="input" name="email" size="40" value="{$edit_data.email}" validate="{ldelim}required:1,minlength:5,email:1{rdelim}"/>
        <label class="error" for="f_users_email">Нужен правильный email</label>
        </div>
        
        <div class="left">{$lang._users.password} </div>
        <div class="right"> <input type="input" name="password" size="20" value=""/> 
          <br/>
           <span class="help">(Оставьте пустым, если не хотите изменять текущий пароль)</span> 
        </div>
             
    </div>
    </fieldset>
        

          
        <div class="panel">    
        <input type="hidden" name="op" value="update_profile"/>
        <input type="submit" value="{$lang.save}"/>
        </div>

    </div>
    
</form> 

 <script type="text/javascript">

{literal}

$.metadata.setType("attr", "validate");  

jQuery.validator.addMethod("string", function( value, element ) {
   var result = /^[a-zA-Zа-яА-Я\s]+$/.test(value);
    if (!result) {
        element.value = element.value.replace(/[^a-zA-Zа-яА-Я\s]+/, '');
        var validator = this;
        setTimeout(function() {
            validator.blockFocusCleanup = true;
            element.focus();
            validator.blockFocusCleanup = false;
        }, 1);
    }
    return result;
}); 
 
$(function() {
    
                      
   $("#frm_profile").validate({
       submitHandler: function(form) {
            $(form).ajaxSubmit({dataType : 'json', success: function(data){
                        tf.user_profile($(form), data);
                    }
            });
       }
       , highlight: false
   });
});

{/literal}

</script>
