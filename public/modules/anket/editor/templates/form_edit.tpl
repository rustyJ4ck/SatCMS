        
<form action="{$config.editor_url}" method="post" name="site_mod" id="frm_" enctype="multipart/form-data"
    class="_validable"
>


<div class="form">

    <div class="form-block">
        <label>{$lang.title}*</label>
        <div>
            <input type="text" name="title" size="60" value="{$return.form.title}"
            validate="{ required:1 }"
            />
        </div>  
    </div>    

    {if $return.form.id}

    <div class="form-block">
        <label>{$lang.name}</label>
        <input type="text" name="name" size="60" value="{$return.form.name}"/>
    </div> 
    
    {/if}
    
  <div class="form-block">
        <label>{$lang.description}</label>
        <textarea cols="60" rows="6" name="text" class="wysiwyg">{$return.form.description}</textarea>
    </div> 
    
    <div class="form-block hidden">
        <label>Баллы</label>
        <input type="text" name="value" size="10" value="{$return.form.value}"/>
    </div>           
    
    <div class="form-block">
        <label>Уведомить по почте (email)</label>
        <div>
            <input type="text" name="notify_email" size="40" value="{$return.form.notify_email}"
             validate="{ email:1 }"
            />
        </div>  
    </div>                  
    
    <br clear="all"/>      
              
    <div class="form-bottom">
    <input class="main" name="item_submit" type="submit" value="{$lang.save}"/>
    </div>
    
  
</div>
                
<input type="hidden" name="c" value="{$req.c}"/>
<input type="hidden" name="m" value="{$req.m}"/>
<input type="hidden" name="id" value="{$req.id}"/>

<input type="hidden" name="site_id" value="{if $return.form.id}{$return.form.site_id}{else}{$current_site.id}{/if}"/>

</form> 

 