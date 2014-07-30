

<div id="qa-list-ctx">

{if !$return.item.active}
  <span class="backred" style="padding:4px;">
  Вопрос находится на модерации.
  </span>
{else}

   <div class="formfield qa-title">
   <h2><span  style="color:#CCCCCC">Вопрос</span> {$return.item.title}</h1></h2>
   </div>

   <div class="qa-text">
   {$return.item.text|nl2br}
   </div>
   
   <br/>
   
   <div class="help">
   {$return.item.username}
   {$return.item.date}
   </div>
   
   <br/>
   
   {if empty($return.item.answers)}
         <br/>
         <span class="backred" style="padding:4px;">
         Ответов не получено.
         </span>
   {else}
   <br/>
   Ответы пользователей:
   <br/><br>
   <div id="q-answers">
   {foreach from=$return.item.answers item=answer}
        <div class="qa-item">
            <span>{$answer.title|escape}</span>
            <div class="text">
            {$answer.text|trim|nl2br}&nbsp;
            </div>
            <div class="help bar">
            {$answer.username} {$answer.date} 
            </div>         
        </div>  
    {/foreach}
    </div>
    {/if}
    

{/if}    

</div>



<div id="qa-form-ctx">
    {include './answer_form.tpl'}
</div>

<div style="clear:left;padding-top:60px;text-align:center;">
    <a href="../../">Список вопросов</a>
</div>

<div class="clearfix"></div>




