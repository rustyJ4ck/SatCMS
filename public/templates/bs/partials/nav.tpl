<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" >


    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">

        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-menu">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>


        <div class="dropdown">
            <a class="navbar-brand" href="#" class="dropdown-toggle" data-toggle="dropdown">

            <i class="glyphicon glyphicon glyphicon-th"></i>

            {*$current.site.html_title} <b class="caret"></b>*}</a>
            
            <ul class="dropdown-menu brand-menu">
                <li><a href="/"><span class="glyphicon glyphicon-home"></span> Главная страница</a></li>
                <li class="divider"></li>
                {foreach $current.site.tree as $item}
                {if $item.pid == 0}
                <li><a href="{$item.url}">{$item.title}</a></li>
                {/if}
                {/foreach}
            </ul>
        </div>

    </div>


    <div id="main-menu" class="collapse navbar-collapse">
        <ul class="nav navbar-nav nav">

            {foreach $current.site.tree as $item}
            {if $item.pid == 0 && $item.b_featured}
                <li class="dropdown">
                    <a href="{$item.url}"
                       class="dropdown-toggle" data-toggle="dropdown"
                            >
                        {$item.title}
                        <b class="caret"></b>
                    </a>

                    <ul class="dropdown-menu">
                        {foreach $current.site.tree as $subItem}
                        {if $item.id == $subItem.pid}
                        <li>
                            <a href="{$subItem.url}">{$subItem.title}</a>
                        </li>
                        {/if}
                        {/foreach}
                    </ul>

                </li>
            {/if}
            {/foreach}

        </ul>

        <div class="col-sm-2 col-md-2 navbar-right">

            <form class="navbar-form" role="search">
                <div class="input-group">


                    <input id="searchBox" type="text"
                           class="form-control"
                           data-provide="typeahead" autocomplete="off"
                           placeholder="Поиск..."
                    >


                    <div class="input-group-btn">
                        <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                    </div>

                </div>
            </form>

        </div>


    </div>
</nav>

{*<header class="navbar navbar-default navbar-fixed-top" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="/" class="navbar-brand">Bootply</a>
        </div>
        <nav class="collapse navbar-collapse" role="navigation">
            <ul class="nav navbar-nav">




            </ul>
        </nav>
    </div>
</header>

{foreach $current.site.tree as $item}
    {if $current.node.pid == $item.pid}
        <li {if $current.node.id == $item.id}class="active"{/if}><a href="{$item.url}">{$item.title}</a></li>
    {/if}
{/foreach}
*}