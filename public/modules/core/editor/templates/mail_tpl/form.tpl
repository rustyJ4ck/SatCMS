{$model = $return.form.data}

<form action="{$config.editor_url}"
      method="post" name="site_mod" id="frm_"
      class="validable"
      data-success-dismiss="true"
      data-success-reload="true"
      >

<div class="form">

    <div class="form-block">
        <label>{$lang.title}</label>
        <div>
        <input class="form-control"
               type="text" name="title" size="80" value="{$model.title}"/>
        </div>
    </div>     

    <div class="form-block">
        <label>{$lang.name}*</label>

        <div>
        <input class="form-control"
               type="text" name="name" size="80" value="{$model.name}"
               data-rule-required="true"
               />
        </div>
    </div> 
    
    <div class="form-block">
        <label>{$lang.mail.subject}</label>
        <div>
        <input class="form-control"
               type="text" name="t_title" size="80" value="{$model.t_title}"/>
        </div>
    </div>     

    <div class="form-block">
        <label>{$lang.mail.from}</label>
        <div>
        <input class="form-control"
               type="text" name="t_from_email" size="40"
               value="{$model.t_from_email}"
               data-rule-email="true"
               />
        </div>
    </div>     
    
    <div class="form-block">
        <label>{$lang.template}</label>
        <div>
            <textarea class="form-control wysiwyg"
                      cols="100" rows="12" name="t_template"
                    >{$model.t_template}</textarea>
        
        <br/>

        <code>%date% - дата        <br/>
        %host% - хост        <br/>
        %from% - отправитель <br/>
        </code>

        </div> 
        
         
    </div>
    

    <div class="form-bottom">
        <input class="btn btn-primary" name="form-submit" type="submit" value="{$lang.save}"/>
        <input type="button" class="btn btn-danger" data-dismiss="modal" value="Отмена"/>
    </div>
    
  
</div>
                
<input type="hidden" name="c" value="{$req.c}"/>
<input type="hidden" name="m" value="{$req.m}"/>
<input type="hidden" name="id" value="{$req.id}"/>

</form> 

 