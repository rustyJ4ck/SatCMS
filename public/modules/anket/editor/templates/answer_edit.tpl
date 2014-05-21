<div class="help">

Ответ для анкеты &laquo;<a href="?m=anket&c=form">{$current.anket_form.title}</a>&raquo;,
вопрос &laquo;<a href="?m=anket&c=question&pid={$current.anket_form.id}">{$current.anket_question.title}</a>&raquo;

</div><br/>
                                          
<form action="{$config.editor_url}" method="post" name="site_mod" id="frm_" enctype="multipart/form-data"
    class="_validable"
>

<div class="form">

    <div class="form-block" class="hidden">
        <label>Ответ</label>
        <div>
        <textarea cols="60" rows="6" name="text">{$return.form.text}</textarea>
        </div>
    </div>       
    
    <div class="form-block" class="hidden">
        <label>Заголовок*</label>
        <div>
             <input type="text" name="title" size="60" value="{$return.form.title}"
               validate="{ required:1 }"
             />
        </div>  
    </div>     
    
    <div class="form-block hidden">
        <label>Баллы</label>
        <div>
            <input type="text" name="value" size="10" value="{$return.form.value}"
             validate="{ number:1 }"
            />
        </div>  
    </div>      
    
    <div class="form-block">
        <label>Правильный ответ</label>
        <div>
            <input type="checkbox" 
                name="b_valid" value="1" {if $return.form.b_valid}checked="checked"{/if}/>
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

 