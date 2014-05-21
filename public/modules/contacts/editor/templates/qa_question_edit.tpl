
                                          
<form action="{$config.editor_url}" method="post" name="site_mod" id="frm_" enctype="multipart/form-data"
    class="_validable"
>

<div class="form">

    <div class="form-block">
        <label>{$lang.title}</label>
        <div>
            <input type="text" name="title" size="60" value="{$return.form.title}"
            />
        </div>  
    </div>    

 
    <div class="form-block" class="hidden">
        <label>Вопрос</label>
        <div>
               <textarea cols="80" rows="8" name="text"
               validate="{ required:1 }"
               >{$return.form.text}</textarea>
        </div>  
    </div>
    
    <div class="form-block">
        <label>Имя</label>
        <input type="text" name="username" size="20" value="{if $return.form.id}{$return.form.username}{else}{$user.nick}{/if}" />
    </div>     
    
    <div class="form-block">
        <label>E-mail</label>
        <input type="text" name="email" size="20" value="{$return.form.email}" />
    </div>           
    
    <div class="form-block">
        <label>Телефон</label>
        <input type="text" name="phone" size="20" value="{$return.form.phone}" />
    </div> 
    
   <div class="form-block">
    <label>Дата изменения</label>
    <div>
        
        {if NOT $return.form.date}
        {assign var='$return.form.date' value='%d.%m.%Y %H:%M'|@strftime:$smarty.now}
        {/if}
                    
        <input type="text" name="date" class="date hidden" value="{$return.form.date}"  />
        <input type="text" name="date_d" class="date date_hm" size="9" value="{$return.form.date|date_format_ex:"d.m.Y"}" />
        ,
        <input class="date_hm" name="date_h" type="text" size="2" validate="{ number:1,maxlen:2,range:[0,24] }" value="{$return.form.date|date_format_ex:"H"}"/>
        <div class="error_inline">
        <label class="error" for="date_h">Неверно</label>
        </div>
        :
        <input class="date_hm" name="date_m" type="text" size="2" validate="{ number:1,maxlen:2,range:[0,60] }" value="{$return.form.date|date_format_ex:"i"}"/>
        <div class="error_inline">
        <label class="error" for="date_m">Неверно</label>
        </div>
        
          <input type="button" value="сейчас" 
                onclick="$('input[name=date_d]').val('{'%d.%m.%Y'|@strftime:$smarty.now}'); $('input[name=date_h]').val('{'%H'|@strftime:$smarty.now}'); $('input[name=date_m]').val('{'%M'|@strftime:$smarty.now}').trigger('change');"
                style="padding:2px 4px;"/>
        
        </div>  
    </div>       
    
       <div class="form-block">
            <label>Уведомлять</label>
            <div>
                <input type="checkbox" name="b_notify" value="1" 
                {if $return.form.b_notify}checked="checked"{/if}/>
            </div>  
        </div>       
        
       <div class="form-block">
            <label>Активный</label>
            <div>
                <input type="checkbox" name="active" value="1" 
                {if $return.form.active || !$return.form.id}checked="checked"{/if}/>
            </div>  
        </div>                
     
    <input type="hidden" name="site_id" size="10" value="{if $return.form.id}{$return.form.site_id}{else}{$current_site.id}{/if}"/>
    <input type="hidden" name="session_id" size="10" value="{if $return.form.id}{$return.form.pid}{else}{$req.pid}{/if}"/>
    
    <br clear="all"/>      
              
    <div class="form-bottom">
    <input class="main" name="item_submit" type="submit" value="{$lang.save}"/>
    </div>
    
  
</div>
                
<input type="hidden" name="c" value="{$req.c}"/>
<input type="hidden" name="m" value="{$req.m}"/>
<input type="hidden" name="id" value="{$return.form.id}"/>

</form> 

 
