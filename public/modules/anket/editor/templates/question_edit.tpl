<div class="help">
Вопрос для анкеты &laquo;<a href="?m=anket&c=form">{$current.anket_form.title}</a>&raquo;
</div><br/>
                                          
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

 
    <div class="form-block" class="hidden">
        <label>Вопрос</label>
        <textarea cols="60" rows="6" name="text">{$return.form.text}</textarea>
    </div>      
    
    <div class="form-block">
        <label>Баллы</label>
        <div>
            <input type="text" name="value" size="10" value="{if !$return.form.id}1{else}{$return.form.value}{/if}" validate="{ number:1 }"/>
            <div class="help">за правильный ответ</div>
        </div>  
    </div>       
     
    <input type="hidden" name="pid" size="10" value="{if $return.form.id}{$return.form.pid}{else}{$req.pid}{/if}"/>
    
    <br clear="all"/>      
              
    <div class="form-bottom">
    <input class="main" name="item_submit" type="submit" value="{$lang.save}"/>
    </div>
    
  
</div>
                
<input type="hidden" name="c" value="{$req.c}"/>
<input type="hidden" name="m" value="{$req.m}"/>
<input type="hidden" name="id" value="{$return.form.id}"/>

</form> 

 
