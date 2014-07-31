
<div ng-controller="navigationController" ng-hide1="!user.logged"
     class="area-top clearfix">

    <div id="cp-nav" class="navbar navbar-top">
        <div class="container">

            <div class="cp-nav-header">

                <a id="logo" class="pull-left" ui-sref="default"></a>

                <div id="phone" class="pull-left">
                    Круглосуточная поддержка
                    <p>+7 (495) 785-2444</p>
                </div>
                <div id="cp-box" class="pull-right">
                    <p><span class="user-name">{$user.nick}</span>
                        <span class="user-surname">{$user.email}</span>
                    </p>
                    <p class="padded-top">
                        <a class="btn btn-xs btn-default"
                           ng-click="logout()"
                           data-content="Действительно выйти?">Выйти</a></p>
                </div>

                {*
                <li class="dropdown">

                <a class="a-confirm-ajax" href="{$config.site_url}users/logout/"></a>

                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="btn btn-warning btn-xs">{$user.nick}</span> <b class="caret"></b></a>

                <ul class="dropdown-menu">
                    <li><a href="/" target="_self">Go to site</a></li>
                    <li class="divider"></li>
                    <li><a ng-click1="logout()" href="/users/logout/" target="_self">Logout</a></li>
                </ul>
                </li>
*}

            </div>

            <div class="navbar-header">

                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <a class="navbar-brand" href="#">::</a>

            </div>

            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">

                    <li><a class="active" href="{'/editor/infcp/card/'|ngUrl}">Карты</a></li>
                    <li><a href="{'/editor/infcp/transaction/'|ngUrl}">Транзакции</a></li>

                    <li><a href="{'/editor/infcp/actions/op/docs/'|ngUrl}">Документы</a></li>
                    <li><a href="{'/editor/infcp/actions/op/card/'|ngUrl}">Заказать новую карту</a></li>

                </ul>
            </div><!--/.navbar-collapse -->
        </div>
    </div>



</div>