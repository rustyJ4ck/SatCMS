        
<form action="{$config.editor_url}" method="post" name="site_mod" id="frm_" enctype="multipart/form-data">


<div class="form">


    <div class="form-block">
        <label>{$lang.title}</label>
        <input type="text" name="title" size="40" value="{$return.form.title}"/>
    </div> 
    
    <div class="form-block">
        <label>{$lang.title} 2</label>
        <input type="text" name="text" size="40" value="{$return.form.text}"/>
    </div>   

    <div class="form-block">
        <label>{$lang.value}</label>
        <input type="file" name="image" size="40" />
    </div>    

    <br clear="all"/>      
              
    <div class="form-bottom">
    <input class="main" name="item_submit" type="submit" value="{$lang.save}"/>
    </div>
    
  
</div>
                
<input type="hidden" name="c" value="{$req.c}"/>
<input type="hidden" name="m" value="{$req.m}"/>
<input type="hidden" name="id" value="{$req.id}"/>

</form> 

 