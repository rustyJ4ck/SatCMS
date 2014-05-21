{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$form = "dialog: yes"}
    {$model = $return.form.data}
{/block}

{block "form-top"}
{/block}

{block 'form'}

{*<form action="{$config.editor_url}" method="post" name="site_mod" id="frm_" class="_validable">*}


<div class="form">

    {if $current.menu.title}
        <div class="form-block">
            <label>Родитель</label>
            <div>
                <span class="label label-info">{$current.menu.title}</span>
            </div>
        </div>
    {/if}

    <div class="form-block">
        <label>{$lang.title}*</label>
        <div>
            <input class="form-control"
                   type="text" name="title" size="70" value="{$model.title}"
                   data-rule-required="true"
                    />
        </div>
    </div>    
    
    <div class="form-block">
        <label>{$lang.name}</label>
        <div>
            <input class="form-control"
                   type="text" name="name" size="70" value="{$model.name}"/>
        </div>
    </div>      

    <div class="form-block">
        <label>{$lang.url}</label>
        <div>
            <input class="form-control"
                   type="text" name="url" size="70" value="{$model.url}" id="s-menu-url"
            />
        </div>  
    </div>


    <div class="form-block">
        <label></label>
        <div>
            <select id="sat_choose_url_handler"
                    name="pid"
                    onchange="$('#s-menu-url').val($(this).val())"
                    >

                <option value="">Выбрать ссылку на раздел</option>

                <option value="/">Корень</option>

                {* so hardcode *}
                <option value="/news/">Новости</option>

                {foreach $current.tree as $list}
                    <option value="{$list.url}" {if $list.id == $model.pid}checked="checked"{/if}>
                        {'-'|str_repeat:(2*$list.level)} {$list.title|truncate:70}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="form-block">
        <label>{$lang.active}</label>
        <div>
            <input class="form-control"
                   type="checkbox" name="active" value="1" {if $model.active || NOT $model.id}checked="checked"{/if}/>
        </div>
    </div>     
    
    <div class="form-block">
    <label>Описание</label>
        <div>
        <textarea class="form-control"
                  cols="70" rows="6" name="description"
                  class="wysiwyg">{$model.description}</textarea>
        </div>
    </div>      
    
    <br clear="all"/>      


</div>
                
{/block}

{*block 'form-controls'}
   <a class="btn btn-default" onclick="alert('2222');">@hello</a>
{/block*}

{block 'form-bottom'}

    <input type="hidden" name="pid" value="{if $model.id}{$model.pid}{else}{$req.pid}{/if}"/>

    {if $model.id}
        <input type="hidden" name="site_id" value="{$model.site_id}"/>
    {else}
        <input type="hidden" name="site_id" value="{$current.site.id}"/>
    {/if}

{/block}

