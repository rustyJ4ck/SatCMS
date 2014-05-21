
<form id="qa-form" method="POST" action="/contacts/qa/new/">

   
<div style="font-weight:normal;margin-right:3px;" id="qa-result">

   <div class="formfield">
   <h2 style="color:#CCCCCC">Задать вопрос?</h2>
   </div>


   <div class="formfield">
   <label>
   Вопрос *</label>
   <input type="text" name="title"
   validate="{ldelim}required:true{rdelim}" />
   <label class="error" for="title">Вопрос?</label>
   </div>

   <div class="formfield">
   <label>Подробно</label>
   <textarea cols="" rows="5" name="text"></textarea>
   </div>
   
   <div class="formfield">
   <label>
   Ваше имя *</label>
   <input type="text" name="username"
   validate="{ldelim}required:true{rdelim}" 
   value="{if $user.logged_in}{$user.nick}{/if}"
   />
   <label class="error" for="username">Укажите ваше имя.</label>
   </div>
   
   <div class="formfield">
   <label>
   E-mail *</label>
   <input type="text" name="email"
    validate="{ldelim}required:true,email:true{rdelim}" />
   <label class="error" for="name">Email?</label>
   </div>
   
   <div class="formfield">
   <label style="display:inline;" for="b_notify">Уведомить об ответе</label>
   <input type="checkbox" id="b_notify" name="b_notify" class="checkbox"/>
   </div>   
   
   <div class="formfield">
   <label>
   Телефон</label>
   <input type="text" name="phone"/>   
   </div>
   

<div style="text-align:center;padding:20px;font-size:120%">
   <input type="hidden" name="form_submit" value="yes"/>
   <input type="submit" value="Отправить вопрос" />
</div>

</div>   
   
</form>

<script>
$.metadata.setType("attr", "validate");  
$("form#qa-form").validate({
   submitHandler: function(form) {
        $(form).ajaxSubmit({ dataType : 'json', cache: false, success: function(data){
                    //tf.submit_handler($(form), data);
                    if (data.message && data.message.length) $.tf_message(data.message, !data.status);
                    $('#qa-result').html('<div class="formfield"><span class="foregreen">Ваш вопрос успешно добавлен и будет опубликован на сайте после проверки модератором.</span><br/><br/><a href="' + data.data.urls.self + '">Ссылка на вопрос</a></div>');
                }
        });
   }, highlight:false
});
</script>
