<!DOCTYPE html>
<html>
<head>

<title ng-bind-template="[[navAction.section.title]] [[navAction.action&&'/']] [[navAction.action.title]] [[site.domain&&'/']] [[site.domain]]">{$config.title} - Редактор {$config.site_url}</title>

<link href="{$config.static_url}{$config.site_url}vendor/bootstrap/dist/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="{$config.static_url}{$config.site_url}editor/templates/css/main.css" rel="stylesheet" type="text/css" />

<link href="{$config.static_url}{$config.site_url}vendor/select2/select2.css" rel="stylesheet" type="text/css" />
<link href="{$config.static_url}{$config.site_url}vendor/toastr/toastr.css" rel="stylesheet" type="text/css" />
<link href="{$config.static_url}{$config.site_url}vendor/jquery-icheck/skins/flat/blue.css" rel="stylesheet" type="text/css" />
<link href="{$config.static_url}{$config.site_url}vendor/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css" />
<link href="{$config.static_url}{$config.site_url}vendor/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />


{*
<link href="{$config.static_url}{$config.site_url}vendor/uploadify/uploadify.css" rel="stylesheet" type="text/css" />
<link href="{$config.static_url}{$config.site_url}vendor/bootstrap-modal/css/bootstrap-modal-bs3patch.css" rel="stylesheet" type="text/css" />
<link href="{$config.static_url}{$config.site_url}vendor/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet" type="text/css" />
*}

{*<link type="text/css" rel="stylesheet" href="{$config.static_url}{$config.site_url}jscripts/css/ui.css"  />*}

<script type="text/javascript">

    var config = {$config|json_encode};
    var req = {$req|json_encode};

    /** @fixme legacy code */
    /*
    var _site_url = "{$config.static_url}{$config.site_url}";
    var _editor_wysiwyg = '{$config.editor.wysiwyg}';
    */

</script>

</head>

<body ng-class="sidebarActive&&'sidebar-active'||'sidebar-disabled'">

{include 'partials/nav.tpl'}

<div class="main-content">

{*<div>@ [[user.login]] [[user.level]] @</div>*}

    {block name="page-top"}

        {*header*}
        <div ng-hide1="!user.logged">
            <div class="r1ow">

                <div class="area-top clearfix">
                    <div class="pull-left header">
                        <h3 class="title">

                            <a href="" ng1-click="toggleSidebar()" class="glyphicon  glyphicon-th-large"></a>
                            {*<span ng-bind="state.current.module">@module</span> <span ng-bind="state.current.section">@section</span>*}

                            <span ng-if="loading" class="label label-warning">Loading...</span>
                            <span ng-bind="navAction.section.title"></span>
                            <span ng-if="navAction.section" ng-cloak="">/</span>
                            <span ng-bind="navAction.action.title"></span>
                        </h3>
                    </div>

                    {*
                    <ul class="inline pull-right sparkline-box">

                        <li class="sparkline-row">
                            <h4 class="blue"><span>Orders</span> 847</h4>
                            <div class="sparkline big" data-color="blue"><!--15,3,16,19,23,21,14,19,15,18,12,13--></div>
                        </li>

                        <li class="sparkline-row">
                            <h4 class="green"><span>Reviews</span> 223</h4>
                            <div class="sparkline big" data-color="green"><!--23,25,16,14,8,7,14,7,4,25,16,17--></div>
                        </li>

                        <li class="sparkline-row">
                            <h4 class="red"><span>New visits</span> 7930</h4>
                            <div class="sparkline big"><!--21,22,25,14,19,14,10,11,26,11,15,3--></div>
                        </li>

                    </ul>
                    *}
                </div>
            </div>
        </div>

    {/block}

    <div id="content" ui-view="" class="container">

        {block name="content"}

        {if $req.m && empty($exception)}

            {$view = "`$modtpl_prefix`index.tpl"}
            {include $view}

        {else}

            <!-- back end index -->
            {*include "partials/index.tpl"*}

            {*angular loaded content*}

            {*
            [[section.url]]
            <test123>@test</test123>
            <i test123>@i-test</i>
            *}

            {*
            <div class="progress progress-striped active" style="width:50%;margin-left:25%;margin-top:20%;">
                <div class="progress-bar"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
                    <span class="sr-only">45% Complete</span>
                </div>
            </div>
            *}


        {/if}

        {/block}


    </div>
</div>


{if $config.lib_editor.optimized && !$config.debug}
<script data-main="/assets/editor/main" src="/vendor/requirejs/require.js"></script>
{else}
<script data-main="/editor/templates/js/main" src="/vendor/requirejs/require.js"></script>
{/if}

{block 'page-bottom'}
{/block}

</body>
</html>


