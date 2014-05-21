<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>
    {$config.title}
    {if $current.node}{$current.node.title} - {if $current.node.parent}{$current.node.parent.title} - {/if}{/if}{$current.site.html_title}
</title>

<meta name="keywords" content="{$current.site.mk}" />
<meta name="description" content="{$current.site.md}" />

<link rel="icon" href="/favicon.ico" type="image/x-icon" />

<link rel="alternate" type="application/rss+xml" title="News feed" href="/news/rss/" />

    <link href="{$config.site_url}vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{$config.site_url}vendor/bootstrap3-typeahead/style.css" rel="stylesheet" type="text/css" />
    <link href="{$config.template_url}assets/css/main.css" rel="stylesheet" type="text/css" />
{*
    {asset_compile debug_toggle=1}
    {/asset_compile}
*}

{*<link href="{$config.template_url}_assets/css/{$current.site.domain}/app.css" rel="stylesheet" type="text/css" />*}

{strip}

<style>#masthead .well { background: #F5F5F5 url('{$current.site.image.url|default:'http://placehold.it/512x384'}') 50% 50% / cover repeat }</style>

<script type="text/javascript">
var site = {
  id:     {$current.site.id},
  name:   "{$current.site.title}",
  urls: {
    self: "{$config.site_url}"
    , static: "{$config.static_url}"
    , template: "{$config.template_url}"
  },
  domain: "{$current.site.domain}",
  scripts: [],
  styles: [],
  editable: false
};
</script>
{/strip}

<script type="text/javascript" src="{$config.site_url}vendor/headjs/dist/1.0.0/head.min.js"></script>

</head>

<body>

{include file="partials/nav.tpl" c1ache_lifetime="600" c1aching="true"}

{block 'header'}
    <div id="masthead">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <h1>
                        <a href="/">{$current.site.html_title}</a>
                        <p class="lead">{$current.site.description}</p>
                    </h1>
                </div>


                <div class="col-md-5">
                    <div class="well well-lg">
                        <div class="row">
                            <div class="col-sm-6">

                                {if $current.site.image.url}
                                {/if}

                                {*<img src="//placehold.it/180x100" class="img-responsive">*}
                            </div>
                            <div class="col-sm-6 text-white">

                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div><!-- /cont -->
    </div>
{/block}

<!-- Begin Body -->
<div class="container">
    <div class="row">
        <div class="col-md-3" id="leftCol">

            {block 'sidebar'}
            {include "partials/sidebar.tpl"}
            {/block}

        </div>

        <div class="col-md-9">

            <!-- start content -->
         {block 'content'}
             {if $message}{include file="partials/flash.tpl"}{/if}
             {if $main_template}{include file="partials/`$main_template`"}{/if}
         {/block}

            <hr>

            {block 'footer'}
            {include "partials/footer.tpl"}
            {/block}



        </div>
    </div>
</div>

{*inline edit stuff*}
{if $user.level >= 50}
    <script type="text/javascript">
        site.scripts.push('/vendor/jquery-cookie/jquery.cookie');
        site.scripts.push('/vendor/tinymce/js/tinymce/tinymce.jquery.min');
        // site.scripts.push('/vendor/x-editable/dist/bootstrap3-editable/js/bootstrap-editable');
        site.scripts.push('{$config.template_url}assets/js/editor');
    </script>
{/if}

{capture name='jsResources'}
    <script type="text/javascript" src="{$config.site_url}vendor/jquery/jquery.js"></script>
    <script type="text/javascript" src="{$config.site_url}vendor/bootstrap/dist/js/bootstrap.js"></script>
    <script type="text/javascript" src="{$config.site_url}vendor/typeahead.js/dist/typeahead.jquery.js"></script>
    <script type="text/javascript" src="{$config.site_url}vendor/typeahead.js/dist/bloodhound.js"></script>
    <script type="text/javascript" src="{$config.template_url}assets/js/tf.js"></script>
    {block 'scripts' hide}
    {/block}
    <script type="text/javascript" src="{$config.template_url}assets/js/main.js"></script>
{/capture}

{if $config.debug}
    {$smarty.capture.jsResources}
{else}
    {asset_compile}
    {$smarty.capture.jsResources}
    {/asset_compile}
{/if}


</body>
</html>
