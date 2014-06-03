{extends "widgets/model/form.tpl"}

{block 'params'}
    {$title = "Правка страницы &laquo;`$model.title`&raquo;"}
    {$sidebarTitle = 'Attachments'}
    {$actionUrlExtra = "&pid=`$req.pid`"}
    {$page = "&start=`$req.start`"}
{/block}


{block 'sidebar'}

    <div class="widget"
         data-url="?m=sat&c=file&pid={$model.id}&sid={$controller.attach_sid}&ctype_id={$entity.ctype.id}"
         data-compilable="true"
         data-isolated="true"
            >
    </div>

    {*
    <section class="compilable">
    <div ng-controller="uploadifyController">

        <form>

            <div id="fileQueue"></div>

            <input id="uploadBtn"
                   class="btn btn-default"
                   name="file_upload"
                   type="file" multiple="true">
        </form>

        <table id="sat_files_list" class="table table-normal">
            <thead>
            <tr><td>Файл</td><td>Операции</td></tr>
            </thead>
            <tbody>
            <tr><td>Файл</td><td>Операции</td></tr>
            </tbody>
        </table>

    </div>
    </section>

    *}


{/block}

{*block 'sidebar-top'}
    <div class="box form-data">
        <div class="box-header">
            <span class="title">2222</span>
        </div>
        <div class="box-content padded">
            {$smarty.block.child}
        </div>
    </div>
{/block*}

{block 'form'}

{* modify node item template *}

{* 
@{$model._template|@debug_print_var}@<br/>
@{$model.extrafs|@debug_print_var}@


*}


    {include "./list.nav.tpl" currentID=$model.id}


<div class="form">

    <section class="compilable">
    <ul class="nav nav-tabs" ng-controller="nodeFormTabsController">

        <li class="active"><a href="#t-extra" data-toggle="tab"><span>Контент</span></a></li>

        <li><a href="#t-main" data-toggle="tab"><span>Свойства</span></a></li>

        {include file="../../../../extrafs/editor/templates/shared/tabs.tpl" data=$model.extrafs}

        {if !$model._template.editor.tabs.seo.disabled}
        <li><a href="#t-seo" data-toggle="tab"><span>SEO</span></a></li>
        {/if}

        {if !$model._template.editor.tabs.images.disabled}
        <li><a href="#tab-node-images"
               data-toggle="tab"
             {*data-url="{$config.editor_url}index.php?m={$req.m}&c=node_image&pid={$model.id}&embed=yes"*}
             {*&pid={$model.id}&sid={$controller.attach_sid}&ctype_id={$entity.ctype.id}&embed=yes*}
               ng-click="openTab('#tab-node-images', '{$config.editor_url}index.php?m={$req.m}&c=node_image&pid={$model.id}&sid={$controller.attach_sid}&ctype_id={$entity.ctype.id}&embed=yes');"
               ><span>Изображения</span></a></li>
        {/if}

        {if !$model._template.editor.tabs.files.disabled}
        <li><a href="#tab-node-files"
               data-toggle="tab"
               ng-click="openTab('#tab-node-files', '{$config.editor_url}index.php?m={$req.m}&c=node_file&pid={$model.id}&sid={$controller.attach_sid}&ctype_id={$entity.ctype.id}&embed=yes');"
               ><span>Файлы</span></a></li>
        {/if}

        {*<li><a href="#t-coms"><span>Комментарии</span></a></li>*}
        <li><a href="#t-info" data-toggle="tab"><span>Информация</span></a></li>
    </ul>
    </section>


    <div class="tab-content">

        {include "./form.tab.main.tpl"}

        {include "./form.tab.extra.tpl"}

        {include "../../../../extrafs/editor/templates/shared/content.tpl" data=$model.extrafs}

        {include "./form.tab.seo.tpl"}

        {include "./form.tab.info.tpl"}

        <div id="tab-node-images" class="tab-pane">...</div>

        <div id="tab-node-files" class="tab-pane">...</div>

    </div>



 </div> <!-- /close tab container div -->

    {*
    <br clear="all"/>      
              
    <div class="form-bottom">
    <input class="main" name="item_submit" type="submit" value="{$lang.save}"/>
        
    </div>
    *}
    
  
</div>


{*
                
<input type="hidden" name="c" value="{$req.c}"/>
<input type="hidden" name="m" value="{$req.m}"/>
<input type="hidden" name="id" value="{$req.id}"/>
<input type="hidden" name="start" value="{$req.start}"/>

</form>

*}






{/block}

{block 'form-bottom' append}
    <input type="hidden" name="pid" value="{if $model.pid}{$model.pid}{else}{$req.pid}{/if}"/>
{/block}