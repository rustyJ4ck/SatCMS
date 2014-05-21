define([
    'app',
    'plugins/i18n',
    'plugins/editor',
],

    function(app, i18n, ed) {

    "use strict";

    function loadGithubComments() {
        $('#github-commits').githubInfoWidget(
            { user: 'angular', repo: 'angular.js',
                branch: 'master', last: 15,
                limitMessageTo: 255, avatarSize: 48
            }, function(elm) {
                elm.find('a').attr('target', '_blank');
                $('.github-commits-list .github-user > a').addClass('label label-info');
            }
        );
    }

    app.ngApp().controller('indexController',

        function ($scope) {

            console.log('+!!+indexController');

            ed.bindUI(app.getViewportElement());

            require(['/vendor/github.commits.widget/js/github.commits.widget.js'], function(){
                loadGithubComments();
            });

            $scope.$emit('menuAction', {action:{title: i18n.T('Dashboard') }});

        }

    );
});
