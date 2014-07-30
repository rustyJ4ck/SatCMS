
    <div id="comment-{$comment.id}" 
          class="comment {if $comment.level OR ($config.in_ajax AND $comment.tpid != 0)}comment-nested{/if}{if $config.in_ajax} comment-last{/if}"
          data-level="{$comment.level}" data-children="{$comment.c_children}"
         >

                <a name="comment{$comment.id}" class="sub" href="#{$comment.id}"></a>
  
                <div class="comment-ava">
                {if $comment.user.avatar && $comment.user.avatar.thumbnail.url}<img class="comment-ava-img" src="{$comment.user.avatar.thumbnail.url}"/>{/if}
                </div>
                
                <div class="comment-content">
                
                    <div class="comment-head">
                    
                    <span class="date">{$comment.created_at|date_diff_string}</span> &nbsp;
                    
                    {if $comment.username}
                    {if $comment.user_id}
                        <a href="{*$comment.user.urls.profile*}" onclick="return false;">{$comment.username}</a> 
                    {else}
                       {$comment.username} 
                    {/if}
                    {else}
                        Anonymous
                    {/if}
                    
                    {if $comment.api == 'ios'}
                    <a class="wall_post_source_icon wall_post_source_iphone" 
                    href="#" title="Приложение iOS" onclick="return false;"></a>
                    {/if}
                    
                    
                    </div>
                     

                    <div class="comment-actions btn-group-xs pull-right">

                        {if NOT $config.in_ajax}
                            {*<div class="comment-rate_ctx">{include file=rating_comment.tpl}</div>*}
                            <a class="answer btn btn-warning"
                                  rel="{$comment.id}">{'sat\\Post comment'|i18n}</a>
                        {/if}

                        {if $comment.c_children > 0 AND $comment.level == 0}
                            <a class="btn btn-info branch-expand" rel="{$comment.id}">
                                <i class="glyphicon glyphicon-pushpin"></i>
                                {$comment.c_children}</a>
                        {/if}
                    </div>

                    <p {if !$comment.deleted && $user.level >= 50} class="editable-inline" data-ctype="sat.comment" data-field="text" data-id="{$comment.id}"{/if}
                            >{if $comment.deleted}<span class="text-danger">{'sat\\comment-deleted'|i18n}</span>{else}{$comment.text|nl2br}{/if}</p>

                 
                </div>
   
   </div>

    
   {* open nested comment container *}
   <div id="branch-{$comment.id}" class="comment-next{if $comment.level == 0} comment-next-root{/if}" style="z-index:{$comment.level+1}">
    
   {* close nested comment containers *}
   {if $comment.close_levels > 0}
        {section name=close_levels loop=$comment.close_levels}
        </div>
        {/section}
   {/if}    
