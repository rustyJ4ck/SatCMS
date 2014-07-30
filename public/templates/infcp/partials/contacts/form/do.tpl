{*заявка на обучение*}

<script>

function get_user_expirience_level() {
    lang = $('select[name=f_lang] option:selected').attr('rel');
    $("#test-ctx").load("/anket/do/"+lang+"/outfilter/embed/", function() {
        $("#test-ctx").show();
    });
}

$(function(){
       $('select[name=f_lang]').change(function(){
            $("#test-ctx").empty().hide(); 
       });
       
       $('select[name=f_level]').change(function(){           
            if ($(this).val())
            $(this).next('input[type=button]').hide(); 
            else
            $(this).next('input[type=button]').show(); 
       });       
});

</script>

{*$current.data|debug_print_var*}

   <div>
   <h2>Заявка на обучение</h2>
   </div>

<form class="validable" method="POST"

 style="width:50%;font-weight:normal"
>

   <div class="formfield">
   <label>
   Имя *</label>
   <input type="text" name="u_name_1" validate="{ldelim}required:true{rdelim}"/>
   <label class="error" for="u_name_1">Укажите Имя.</label>
   </div>

   <div class="formfield">
   <label>
   Фамилия *</label>
   <input type="text" name="u_name_2" validate="{ldelim}required:true{rdelim}"/>
   <label class="error" for="u_name_2">Укажите Фамилию.</label>
   </div>

   {*
   <div class="formfield">
   <label>
   Отчество</label>
   <input type="text" name="u_name_3" v1alidate="{ldelim}required:true{rdelim}"/>
   <label class="error" for="u_name_3">Укажите Отчество.</label>
   </div>
   *}
   
   
   <div class="formfield">
   <label>
   E-mail</label>
   <input type="text" name="email"  validate="{ldelim}required:true,email:true{rdelim}"/>
   </div>
   
   <div class="formfield">
   <label>
   Контактный телефон *</label>
   <input type="text" name="phone" validate="{ldelim}required:true{rdelim}"/>   
   </div>

   <div class="formfield">
   <label>
   Язык</label>
   <select name="f_lang">
   <option rel="english" value="английский"        {if $current.data.lang == 'en'}selected="selected"{/if}>английский</option>
   <option rel="german" value="немецкий"          {if $current.data.lang == 'de'}selected="selected"{/if}>немецкий</option>
   <option rel="french" value="французский"       {if $current.data.lang == 'fr'}selected="selected"{/if}>французский</option>
   <option rel="spain" value="испанский"         {if $current.data.lang == 'es'}selected="selected"{/if}>испанский</option>
   {*
   <option rel="italian" value="итальянский"       {if $current.data.lang == 'it'}selected="selected"{/if}>итальянский</option>
   <option value="турецкий"          {if $current.data.lang == 'tr'}selected="selected"{/if}>турецкий</option>
   *}
   <option rel="russian" value="русский"           {if $current.data.lang == 'ru'}selected="selected"{/if}>русский</option>
   </select>
   </div>
   
   <div class="formfield">
   <label>
   Уровень знаний</label>          
   
   <select name="f_level">
   <option value="">-- Неопределен</option>
   <option value="A1">A1 - Beginner</option>
   <option value="A2">A2 - Elementary</option>
   <option value="B1">B1 - Pre-intermediate</option>
   <option value="B2">B2 - Intermediate</option>
   <option value="C1">C1 - Upper-intermediate</option>
   <option value="C2">C2 - Advanced</option>
   </select>
   
   <input type="button" value="Пройти тест" style="font-size:100%;height:20px;padding-top:1px;padding-bottom:1px;"
    onclick="get_user_expirience_level();"
   />
   
   {*
   <input type="text" name="f_level"/>
   *}
   </div>   
   
   <div class="formfield backlight hidden" id="test-ctx" style="width:600px;border-radius:6px;">
   </div>
   
   <div class="formfield">
   <label>
   Вид обучения</label>
   <select name="f_edu_type">
   <option value="Индивидуальный" {if $current.data.type == 'indi'}selected="selected"{/if}>Индивидуальный</option>
   <option value="Группа" {if $current.data.type == 'group'}selected="selected"{/if}>Группа</option>
   <option value="Минигруппа" {if $current.data.type == 'mini'}selected="selected"{/if}>Мини группа</option>
   <option value="Корпоративное" {if $current.data.type == 'corp'}selected="selected"{/if}>Корпоративное</option>
   </select>
   </div> 
   
   <div class="formfield">
   <label>Комментарий</label>
   <textarea cols="" rows="10" name="comment"></textarea>
   </div>   
   
   <input type="hidden" name="form_submit" value="yes"/>
   <input type="submit" value="Отправить заявку" style="margin:20px;font-size:120%"/>
   
</form>

<style>
{literal}                    

</style>

<script type="text/javascript">

$('.anket_label').click(function(){
    $(this).prev().click();
});
{/literal}                   
</script>
