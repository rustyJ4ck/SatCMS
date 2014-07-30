<div class="commentbox {if $commentbox_class}commentbox-{$commentbox_class}{/if}">

    {if $parent.comments}
        <div class="comments-list">
         {foreach $parent.comments as $comment}
            {include "./comment.tpl" }
         {/foreach}
        </div>     
    {else}   
        <div class="no-comments">
        <b>{$lang._sat.no_comments}</b>
        </div>
    {/if}
        
    {* 0-level comments placed here *}
    <div id="comment-0"></div>

    <div class="comment-next comment-next-root-empty"></div>
      
</div>    