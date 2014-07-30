<style>
#form-contacts-ctx-killer {
    width:971px;
    height:572px;
    position:absolute;
    background:white;
    z-index:1;
    display:none;
    opacity:0.01;
}

.formfield {
    position:relative;
}

.formfield label.error {
    position:absolute;
    m1argin-top:-26px;
    m1argin-left:460px;
    left:460px;
    width:100px;
    
}
</style>

<div id="form-contacts-ctx-killer"></div>

<form id="form-contacts" action="/contacts/mailer/" method="POST"
 style="width:50%;display:block;font-weight:normal"
>

   <div>
   <h2>Отзыв студента</h2>
   </div>

   <div class="formfield">
   <label>Имя *</label>                                  
   <label class="error" for="name">Укажите имя</label>
   <input type="text" name="name" validate="{ required:1,minlen:3 }" value="" />
   
   </div>         
   
   <div class="formfield">
   <label>E-mail</label>
   <label class="error" for="email">Укажите почту</label>   
   <input type="text" name="email" validate="{ email:1,required:1 }" />   
   </div>
   
   <div class="formfield">
   <label>Контактный телефон</label>
   <label class="error" for="phone">Укажите телефон</label>   
   <input type="text" name="phone" validate="{ phone:1 }" />    
   </div>  
   
   <div class="formfield">
   <label>Текст сообщения</label>
   <label class="error" for="text">Введите текст сообщения</label>   
   <textarea cols="10" rows="8" name="text"  validate="{ required:1,minlen:10 }"></textarea>   
   </div>   
   

   <div class="formfield">
   <input type="submit" value="Отправить отзыв"/>
    <input type="hidden" name="template" value="contacts"/>
   </div>

</form>



<script>
$(function() {
    
   
    
       $.metadata.setType("attr", "validate");  
       $("#form-contacts").validate({
                   submitHandler: function(form) {
                        $(form).ajaxSubmit( { dataType : 'json', success: function(data) {
                            $('#form-contacts-ctx-killer').show();            
                                    alert(data.status
                                        ? 'Сообщение успешно отправлено'
                                        : 'Ошибка отправки сообщения'
                                        );
                            }
                        } );
                   }
                   , highlight: false
               } );
} );
</script>