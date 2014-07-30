
{if $config.comment_level > $user.level}

    <b>
    {$lang._content.comment_level_restriction}
    {if NOT $user.logged_in}
     <a href="{$cp.urls.register.url}">{$lang.maybe_register}</a>,     
     <a href="javascript:;" onclick="tf.login_box('{$config.url}')">{$lang.maybe_login}</a>.
    {/if}
    </b>
    

{else}

<!-- comment-box -->

<div class="comment-form-footer">
    <a class="btn btn-default answer" href="#" rel="0">
    Комментировать
    </a>
</div>    

    {* Post comment form *}

    <div id="comment-form-div" class="panel panel-default">

        <div class="panel-heading">
            Добавить сообщение
        </div>

        <div class="panel-body">

            <form action="{$config.url|rtrim:'/'}/comment/modify/"
                  method="post"
                  id="comment-form"
            >

                <input id="comment-pid"  type="hidden" name="pid" size="4" value="{$parent.id}"/>
                <input id="comment-tpid" type="hidden" name="tpid" size="4" value="0"/>
                <input id="comment-type" type="hidden" name="type" value="0"/>

                {*
                <div class="help"><a href="/content/corporate/help/Polzovatelskoje_soglashenie/" target="_blank">Правила добавления материалов на transler.ru</a></div>
                <br/>
                *}

                <textarea id="comment-text"
                        cols="60" rows="5" name="text" class="form-control"
                        placeholder="Комментарий"
                        ></textarea>

                <div class="buttons">

                    <div class="pull-left">
                        {if $user.logged_in}
                            <div class="help">Комментарий от &laquo;{$user.nick}&raquo;</div>
                            <input type="hidden" name="username" value="{$user.nick}"/>
                        {else}

                            <input type="text" name="username" value=""
                                    class="form-control"
                                    placeholder="Ваше имя"
                                    />

                        {/if}
                        <br/></div>
                    <div>


                        <div class="pull-right comment-actions btn-group-sm">

                            <a href=""
                               id="comment-submit-btn"
                               class="btn btn-primary"
                                    >Добавить</a>

                            <a id="comment-close" href=""
                               class="btn btn-danger"
                                    >Отмена</a>

                        </div>

                        {*
                                <input type="button" value="Отправить" onclick="$('#comment_type').val('0'); $('#comment_submit').click();"/>
                                <input type="button" id="comment-close" value="x" title="{$lang._content.comment_cancel}" />
                        *}
                        <input type="submit" id="comment-submit" style="display:none;" />
                    </div>
                </div>



                <input type="hidden" name="action" value="comment_modify"/>

            </form>


        </div>
                                           


    </div>

{/if}

<script type="text/javascript">
//    site.styles.push('{$config.template_url}shared/comments/comments');
//    site.scripts.push("{$config.template_url}shared/comments/comments");
    head.load('{$config.template_url}shared/comments/comments.css');
    site.scripts.push("{$config.template_url}shared/comments/comments");
    console.log('--push');
</script>


