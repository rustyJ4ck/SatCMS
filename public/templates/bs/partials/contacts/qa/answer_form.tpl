

<form id="qa-form" method="POST" action="/contacts/qa/answer/new/">

   
<div style="font-weight:normal;margin-right:3px;" id="qa-result">

   <div class="formfield">
   <h2 style="color:#CCCCCC">Есть ответ?</h2>
   </div>


   <div class="formfield">
   <label>
   Ответ</label>
   <input type="text" name="title"
    />
   <label class="error" for="title">Ответ?</label>
   </div>

   <div class="formfield">
   <label>Подробно *</label>
   <textarea cols="" rows="5" name="text"
   validate="{ldelim}required:true,minlen:4{rdelim}"
   ></textarea>
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
   E-mail</label>
   <input type="text" name="email"
    validate="{ldelim}email:true{rdelim}" />
   <label class="error" for="name">Email?</label>
   </div>
   
   {*
   <div class="formfield">
   <label style="display:inline;" for="b_notify">Уведомить об ответе</label>
   <input type="checkbox" id="b_notify" name="b_notify" class="checkbox"/>
   </div>   
   
   
   <div class="formfield">
   <label>
   Телефон</label>
   <input type="text" name="phone"/>   
   </div>
   *}
   

<div style="text-align:center;padding:20px;font-size:120%">
   <input type="hidden" name="form_submit" value="yes"/>
   <input type="hidden" name="pid" value="{$return.item.id}"/>
   <input type="submit" value="Ответить" />
</div>

</div>   
   
</form>

<script>
$.metadata.setType("attr", "validate");  
$("form#qa-form").validate({
   submitHandler: function(form) {
        $(form).ajaxSubmit({ dataType : 'json', cache: false, success: function(data){
                    
                    if (data.message && data.message.length) $.tf_message(data.message, !data.status);
                    
                    $('#qa-result').html('<div class="formfield"><span class="foregreen">Ваш ответ успешно добавлен!</div>');
                                        
                    $('#q-answers').append('   \
                        <div class="qa-item">  \
                            <span>' + data.data.title + '</span>   \
                            <div class="text">                     \
                            ' + data.data.text + '                 \
                            </div>                                 \
                            <div class="help bar">                 \
                            ' + data.data.username + ' ' + data.data.date +  '             \
                            </div>                                 \
                        </div>                                     \
                    ');
                    
                }
        });
   }, highlight:false
});



</script>



