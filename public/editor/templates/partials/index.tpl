
<div>

    <div class="box">
        {*
        <div class="box-header"><span class="title">Welcome to SATCMS REV.{$config.version}</span></div>
        *}

        <div class="box-content padded">

            <div class="pull-right" style="margin:0;">
                <div class="btn btn-default disabled">
                <span class="glyphicon glyphicon-user">
                </span>&nbsp;{$user.login} / {$user.email}
                    </div>
            </div>

            {*<li><a ui-sref="login">Login</a></li>*}
            <div class="btn-group">
               <a href="/editor/sat/site/" class="btn btn-primary">Сайты <span class="badge">x{$current.sites|count}</span></a>

               <a class="btn btn-default" ng-repeat="action in modulesToolbar" href="[[action.url]]">
                   [[action.title]]
               </a>

            </div>

        </div>
    </div>

    {*Commits*}
    <div class="box" ng-cloak>
        <div class="box-header"><span class="title">Latest commits</span>
        <div class="pull-right title">
            <a class="label label-info" href="https://github.com/rustyJ4ck/satcms">github repo</a></div>
        </div>
        <div class="box-content" id="github-commits">

        </div>
    </div>



    <div class="box">
        <div class="box-header"><span class="title">Contact us!</span></div>
        <div class="box-content padded">

{*
            github link
            Оставить отзыв / пожелание:
*}


        {include "./contact-form.tpl"}

        </div>
        </div>
    </div>


</div>

