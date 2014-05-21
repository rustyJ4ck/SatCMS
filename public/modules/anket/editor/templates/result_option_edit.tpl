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
        <label>Текст</label>
        <textarea cols="60" rows="4" name="text">{$return.form.text}</textarea>
    </div>  
    
    <div class="form-block">
        <label>{$lang.name} (tag)</label>
        <div>
            <input type="text" name="name" size="40" value="{$return.form.name}"
            />
        </div>  
    </div>    
    
    <div class="form-block">
        <label>ОТ (баллы)</label>
        <input type="text" name="score_low" size="10" value="{$return.form.score_low}" validate="{ number:1 }"/>
    </div>  
    
    <div class="form-block">
        <label>ДО (баллы)</label>
        <input type="text" name="score_high" size="10" value="{$return.form.score_high}"  validate="{ number:1 }"/>
    </div>                 

    <div class="form-block">
        <label>Тест пройден</label>
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

 
