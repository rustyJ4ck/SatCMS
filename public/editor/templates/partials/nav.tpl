
<div ng-controller="navigationController" ng-hide1="!user.logged">

<nav id="main-nav" class="navbar navbar-inverse1 navbar-fixed-top" role="navigation" >


    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <a class="navbar-brand" href="" ui-sref="default">SatCMS <span class="badge alert-info">v.{$config.version}</span> </a>
        {*<a class="navbar-brand" href="" ui-sref="default">{$config.editor_title.value} <span class="badge alert-info">v.{$config.version}</span> </a>*}
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div id="main-menu" c1lass="collapse navbar-collapse">

        {*

        <ul class="nav navbar-nav nav">

        <li ng-repeat="item in menu" class="dropdown">
            <a href="[[item.url]]"
               class="dropdown-toggle" data-toggle="dropdown"
               >
                <span ng-bind="item.title"></span>
                <b class="caret"></b>
               </a>

            <ul class="dropdown-menu">
               <li ng-repeat="subitem in item.actions" ng-class="subitem.url&&'menu-item'||'divider'">
                   <a ng-if="subitem.url" href="[[subitem.url]]" ng-bind="subitem.title"></a>
               </li>
            </ul>
        </li>

        </ul>

        *}



        <ul class="nav navbar-nav navbar-right">


            <li class="dropdown">

                {*<a class="a-confirm-ajax" href="{$config.site_url}users/logout/"></a>*}

                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="btn btn-info btn-xs"
                          ng-bind-template="[[site.domain]]  [[site.path&&'/']] [[site.path]]">Сайты...</span> <b class="caret"></b></a>



                {*foreach $current.sites as $site}
                    <option value="{$site.id}">{$site.title}</option>
                {/foreach*}


                <ul class="dropdown-menu">
                    <li ng-repeat="item in sites" ng-class="item.active&&'active'">
                        <a href="#" ng-click="toggleSite(item.id)">
                            [[item.domain]] [[item.path&&'/']] [[item.path]]
                        </a></li>
                </ul>

            </li>

            <li class="dropdown">

                {*<a class="a-confirm-ajax" href="{$config.site_url}users/logout/"></a>*}

                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="btn btn-warning btn-xs">{$user.nick}</span> <b class="caret"></b></a>

                <ul class="dropdown-menu">
                    <li><a href="/" target="_self">Go to site</a></li>
                    <li class="divider"></li>
                    <li><a ng-click="logout()">Logout</a></li>
                </ul>
            </li>
        </ul>


        {*
        <div class="col-sm-2 col-md-2 navbar-right">
            <form class="navbar-form" role="search">
                <div class="input-group">


                    <input
                            class="form-control"
                            ng-model1="launchOptions"
                            ui-select21="{ allowClear:true, placeholder: 'Quick' }"
                            />

                    <div class="input-group-btn">
                        <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
        *}



    </div>
</nav>


<div class="sidebar-background">
    <div class="primary-sidebar-background"></div>
</div>

<div class="primary-sidebar">

    <!-- Main nav -->
    <ul class="nav navbar-collapse">

{*ng-class="(state.params.module == item.id || (!state.params.module && item.id == 'core')) && 'active'">*}
        <li ng-repeat="item in menu" class="dark-nav">

            <span class="glow"></span>
            <a href="#[[item.id]]"
               class="accordion-toggle" data-toggle="collapse"
                    >
                <span ng-bind="item.title"></span>
                <b class="caret"></b>
            </a>

            {*(state.params.module == item.id || (!state.params.module && item.id == 'core'))*}
            <ul id="[[item.id]]" class="collapse" ng-class="item.default&&'in'"> {*ng-class1="state.params.module == item.id && 'in'"*}
                <li ng-repeat="subitem in item.actions" ng-class="subitem.url&&'menu-item'||'divider'" ng-if="!subitem.hidden">
                    <a ng-if="subitem.url"
{*ng-click="actionClick(item, subitem)"*}
                       href="[[subitem.url]]" ng-bind="subitem.title"></a>
                </li>
            </ul>
        </li>

{*
        <li class="">
            <span class="glow"></span>
            <a href="http://beer2code.com/themes/core-admin/pages/dashboard/dashboard.html">
                <i class="icon-dashboard icon-2x"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="dark-nav ">

            <span class="glow"></span>

            <a class="accordion-toggle" data-toggle="collapse" href="#Xcz3n2KpY4">
                <i class="icon-beaker icon-2x"></i>
                    <span>
                      UI Lab
                      <i class="icon-caret-down"></i>
                    </span>

            </a>

            <ul style="height: auto;" id="Xcz3n2KpY4" class="in collapse">

                <li class="">
                    <a href="http://beer2code.com/themes/core-admin/pages/ui_lab/buttons.html">
                        <i class="icon-hand-up"></i> Buttons
                    </a>
                </li>

                <li class="">
                    <a href="http://beer2code.com/themes/core-admin/pages/ui_lab/general.html">
                        <i class="icon-beaker"></i> General elements
                    </a>
                </li>

                <li class="">
                    <a href="http://beer2code.com/themes/core-admin/pages/ui_lab/icons.html">
                        <i class="icon-info-sign"></i> Icons
                    </a>
                </li>

                <li class="">
                    <a href="http://beer2code.com/themes/core-admin/pages/ui_lab/grid.html">
                        <i class="icon-th-large"></i> Grid
                    </a>
                </li>

                <li class="">
                    <a href="http://beer2code.com/themes/core-admin/pages/ui_lab/tables.html">
                        <i class="icon-table"></i> Tables
                    </a>
                </li>

                <li class="">
                    <a href="http://beer2code.com/themes/core-admin/pages/ui_lab/widgets.html">
                        <i class="icon-plus-sign-alt"></i> Widgets
                    </a>
                </li>

            </ul>

        </li>
        <li class="active">
            <span class="glow"></span>
            <a href="http://beer2code.com/themes/core-admin/pages/forms/forms.html">
                <i class="icon-edit icon-2x"></i>
                <span>Forms</span>
            </a>
        </li>
        <li class="">
            <span class="glow"></span>
            <a href="http://beer2code.com/themes/core-admin/pages/charts/charts.html">
                <i class="icon-bar-chart icon-2x"></i>
                <span>Charts</span>
            </a>
        </li>
        <li class="dark-nav ">

            <span class="glow"></span>



            <a class="accordion-toggle collapsed " data-toggle="collapse" href="#tw5EKuXfEr">
                <i class="icon-link icon-2x"></i>
                    <span>
                      Others
                      <i class="icon-caret-down"></i>
                    </span>

            </a>

            <ul id="tw5EKuXfEr" class="collapse ">

                <li class="">
                    <a href="http://beer2code.com/themes/core-admin/pages/other/wizard.html">
                        <i class="icon-magic"></i> Wizard
                    </a>
                </li>

                <li class="">
                    <a href="http://beer2code.com/themes/core-admin/pages/other/login.html">
                        <i class="icon-user"></i> Login Page
                    </a>
                </li>

                <li class="">
                    <a href="http://beer2code.com/themes/core-admin/pages/other/sign_up.html">
                        <i class="icon-user"></i> Sign Up Page
                    </a>
                </li>

                <li class="">
                    <a href="http://beer2code.com/themes/core-admin/pages/other/full_calendar.html">
                        <i class="icon-calendar"></i> Full Calendar
                    </a>
                </li>

                <li class="">
                    <a>
                        <i class="icon-ban-circle"></i> Error 404 page
                    </a>
                </li>

            </ul>

        </li>
*}




    </ul>



</div>

</div>