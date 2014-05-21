{extends 'widgets/model/form.tpl'}

{block 'params'}
    {$title = "Правка новости"}
    {$form = "dialog: no"}
    {$entity = $return.form}
    {*$model = $return.form.data*}
{/block}


{block 'sidebar'}

{*
    <code>
    $model.files|debug_print_var}
    @attach#{$controller.attach_sid}@
    {$entity.ctype|debug_print_var}

        @attach#{$controller.attach_sid}@
    </code>
*}


    <div class="widget"
         data-url="?m=sat&c=file&pid={$model.id}&sid={$controller.attach_sid}&ctype_id={$entity.ctype.id}"
         data-compilable="true"
         data-isolated="true"
         >
    </div>

    {* include '../sat_file/list.tpl' model='****'*}

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

{block "form-top"}
{/block}

{block 'form'}

    <section class="compilable">


    <div class="form">


        <ul class="nav nav-tabs" ng-controller="nodeFormTabsController">

            <li class="active"><a href="#tab-main" data-toggle="tab"><span>Контент</span></a></li>
            <li><a href="#tab-properties" data-toggle="tab"><span>Свойства</span></a></li>


            <li><a href="#tab-node-images"
                   data-toggle="tab"
                   ng-click="openTab('#tab-node-images', '{$config.editor_url}index.php?m={$req.m}&c=node_image&pid={$model.id}&sid={$controller.attach_sid}&ctype_id={$entity.ctype.id}&embed=yes');"
                   ><span>Изображения</span></a></li>


        </ul>


        <div class="tab-content">

            {include "./form.tab.main.tpl"}
            {include "./form.tab.properties.tpl"}

            <div id="tab-node-images" class="tab-pane">...</div>

         </div>



    </div>

    </section>

                
{/block}

{block 'form-bottom' append}
    <input type="hidden" name="attach_sid" value="{$controller.attach_sid}"/>
    <input type="hidden" name="site_id" value="{if $model.id}{$model.site_id}{else}{$current.site.id}{/if}"/>
{/block}


{*
{include file="`$modtpl_prefix`_attach_uploader.tpl"}
{include file="`$modtpl_prefix`_attach_images.tpl"}
*}