{block 'params'}
{*
    set params in child

    $title
    $form.dialog - in modals
    $entity = $return.model
    $model = $return.model.data
    $fields = $return.model.fields
*}
{/block}

{if $form}{assign var="form" value=$form|to_array}{/if}
{if $btnNew}{assign var="btnNew" value=$btnNew|to_array}{/if}
{if $btnEdit}{assign var="btnEdit" value=$btnEdit|to_array}{/if}

{* default model *}
{if !isset($entity) && isset($return.form)}
    {$entity = $return.form}
{/if}

{* entity / no model *}
{if !isset($model) && isset($entity)}
    {$model = $entity.data}
{/if}

{* notify grid:yes *}
{if 1 === $form.grid}
    {$form.grid = "#`$controller.grid_name`"}
{/if}

{* notify:yes - use default grid ID *}
{if 1 === $form.notify}
    {$form.notify = $form.grid}
{/if}

{$page = "&start=`$req.start`"}

{*include 'widgets/model/sidebar.tpl'*}

{block "form-top" hide}
{/block}

{block name="sidebar" hide}

    <div class="right-sidebar">

        {block name="sidebar-top"}
        {/block}

        <div class="box form-data">
            <div class="box-header">
                <span class="title">{$sidebarTitle|default:'Sidebar'}</span>
            </div>
            <div class="box-content padded">
                {$smarty.block.child}
            </div>
        </div>

        {block name="sidebar-bottom"}
        {/block}

    </div>

{/block}

{*ngcontroller*}
{if !empty($form.controller)}
<section class="compilable"><div ng-controller="{$form.controller}">
{/if}

{block name="form-head"}

    {$form = $form|to_array}

    <form action="{$form.action|default:$config.editor_url}"
          method="{$form.method|default:'post'}"
          name="{$req.m}-{$req.c}"
          class="validable {$form.class}"
          enctype="{$form.enctype}"

          data-grid="{$form.grid}"

          {if !$form.dialog}
              data-success-url="{$config.base_url}{$actionUrlExtra}{$page}"
          {else}
              data-success-dismiss="true"
              {if !$form.notify} {*grid must register listner*}
              data-success-reload="true"
              {else}
              data-notify="{$form.notify}"
              {/if}
          {/if}

          {$form.attrs}

            {*ng-non-bindable*}

            {*if !empty($form.controller)}
            ng-controller="{$form.controller}"
            {/if*}

          >

{/block}

    {if !$form.dialog}

        <div class="box form-data">

            <div class="box-header">
                <span class="title">{$title}</span>
                <ul class="box-toolbar">
                    <li class="btn-group-sm">

                        {* header buttons, non dialog *}
                        {block "form-buttons"}

                            <input type="submit" class="btn btn-success btn-xs" name="form-submit-save" value="Сохранить"
                                   onclick="$(this).nextAll('[name=form-submit]').val('save');"
                                    />

                            {if $model.id}
                                <input type="submit" class="btn btn-primary" name="form-submit-apply" value="Применить" data-continue="true"
                                       onclick="$(this).nextAll('[name=form-submit]').val('apply');"
                                    />
                            {/if}

                            <input type="hidden" name="form-submit" value=""/>

                            <a class="btn btn-danger" data-dismiss="modal" href="{$config.base_url}{$actionUrlExtra}{$page}">Отмена</a>
                        {/block}

                    </li>

                </ul>
            </div>

            <div class="box-content padded">
    {/if}



    {* Form *}
    {block name="form"}
    {/block}

    {* Form controls / dialog *}
    {block name="form-controls"}

        <div class="form-bottom">
            {if $form.dialog && $form.controls !== 0}
                <input type="submit" class="btn btn-success" name="form-submit" value="Сохранить" />
                <a class="btn btn-danger" data-dismiss="modal" href="{$config.base_url}{$actionUrlExtra}{$page}">Отмена</a>
            {/if}

            {$smarty.block.child}
        </div>

    {/block}


    {if !$form.dialog}

            </div>
        </div>

    {/if}



    {* form params/hiddens *}
    {block name="form-bottom"}
        <input type="hidden" name="op" value="{$formAction}"/>
    {/block}

    <input type="hidden" name="c" value="{$req.c}"/>
    <input type="hidden" name="do" value="{$req.do}"/>
    <input type="hidden" name="m" value="{$req.m}"/>
    <input type="hidden" name="id" value="{$req.id}"/>

    {* pagination back position *}
    <input type="hidden" name="start" value="{$req.start}"/>

</form>

{if !empty($form.controller)}
</div></section>
{/if}