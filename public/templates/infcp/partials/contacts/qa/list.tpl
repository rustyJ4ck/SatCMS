
<div id="qa-list-ctx">

  <div class="formfield">
   <h2 style="color:#CCCCCC">Вопросы</h2>
   </div>

    {foreach from=$return.posts.data item=item}
        <div class="qa-item">
            <span><a href="{$item.urls.self}" rel="({$item.id})">{$item.title|escape}</a></span>
            <div class="text">
            {$item.text|trim|substr:0:255|nl2br}&nbsp;
            </div>
            <div class="help bar">
            {$item.username} {$item.date} 
            </div>  
        </div>
    {foreachelse}
         <br/>
         <span class="backred" style="padding:4px;">
         Вопросов не получено.
         </span>
    {/foreach}



</div>



<div id="qa-form-ctx">
{include './form.tpl'}
</div>

<div class="clearfix"></div>

{capture 'content_footer'}
{include file="shared/pagination/default.tpl" class="pagination" pagination=$return.posts.pagination}
{/capture}
