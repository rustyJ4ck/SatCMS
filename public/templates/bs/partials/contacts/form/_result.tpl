<style>
* {ldelim} font-family: arial; font-size: 12px; {rdelim}
</style>

   <div>
   <h2>{$form.title}</h2>
   {$form.text}
   </div>
   
   <br/>Вопросы:<br/><br/>
   
   <table class="nice_borders">
   
   {foreach from=$form.questions item=v}
   <tr><td style="padding: 10px;">
   <div style="font-weight: bold;margin-bottom:4px;">{$v.title}</div>
   <div class="help" style="margin-bottom:10px;">{$v.text}</div>
     
    <div style="margin:10px;">
        {foreach name=va from=$v.answers item=va} 
        <div style="margin-bottom:3px;">
        {$smarty.foreach.va.iteration}. <label class="anket_label" style="{if $va.b_valid}color:darkgreen;{else}color:darkred;{/if}{if $va.id == $v.user_answer.id}font-style:italic;font-weight:bold"{/if}">{$va.text}</label>
        </div>
        {/foreach}
    </div>
   </td></tr>
   {/foreach}
   
   </table>    
   
   <div class="formfield">
   <label>Комментарий</label>
   {$form.user_data.comment}
   </div>
   
   <div class="formfield">
   <label>
   Ваше имя</label>
   {$form.user_data.name}
   </div>
   
   <div class="formfield">
   <label>
   E-mail</label>
   {$form.user_data.email}
   </div>
   
   <div class="formfield">
   <label>
   Контактный телефон</label>
   {$form.user_data.phone} 
   </div>

<br/>
   
Отправлено {$form.user_data.date}

