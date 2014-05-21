
require.config({

    //baseUrl: "../../../",

    paths: {
        angular: '../../../vendor/angular/angular',
        jquery: "../../../vendor/jquery/dist/jquery",
        bootstrap: "../../../vendor/bootstrap/dist/js/bootstrap",
        jqueryMigrate:"../../../vendor/jquery-migrate/jquery-migrate",
        jqueryMigrateFacade:"../../../jscripts/jquery/jquery-migrate",
        jqueryPlugins: "../../../jscripts/jquery/plugins",

        select2: "../../../vendor/select2/select2",
        angularSelect2: "../../../vendor/angular-ui-select2/src/select2",

        angularSanitize: "../../../vendor/angular-sanitize/angular-sanitize",
        angularRouter: "../../../vendor/angular-ui-router/release/angular-ui-router"
    },

    waitSeconds: 0,

    // + (new Date()).getTime(),
    //urlArgs: "v=2",
    urlArgs: "v=" + (new Date()).getTime(),

    shim: {

        angular : {exports : 'angular'},

        jquery: {exports: 'jquery'},

        angularSelect2: ['angular', 'jquery', 'select2'],
        select2: ['jquery'],
        jqueryMigrate: ['jquery'],
        jqueryPlugins: ['jqueryMigrate'],
        angularRouter: ['angular'],
        angularSanitize: ['angular'],

        bootstrap : {
            deps : ["jquery"]
        }

//        'tf': {'exports': 'tf'}
    },

    priority: ['angular', 'jquery']

});

// window.name = "NG_DEFER_BOOTSTRAP!";

require([

    'angular',

    'angularRouter',

    'angularSanitize',
    'angularSelect2',

    'bootstrap',

    'jqueryPlugins',

], function(angular) {
    'use strict';

    /*
     var $html = angular.element(document.getElementsByTagName('html')[0]);

     angular.element().ready(function() {
     angular.resumeBootstrap([app['name']]);
     });
     */

    var app =
    angular
        .module('applicationModule', [
            'ui.router',
        ])

            .config(['$stateProvider',
            function($stateProvider) {

                var changeActiveLink = function() {
                    var pageKey = this.pageKey;
                    $(".pagekey").toggleClass("is-active", false);
                    $(".pagekey_" + pageKey).toggleClass("is-active", true);
                }

                $stateProvider.state('home', {
                    controller : function(){console.warn('HomeCtrl');},
                    url : "/home",
                    pageKey : 'HOME',
                    template : '@home.html',
                    onEnter : changeActiveLink

                });

                $stateProvider.state('contact', {

                    url : "/contact",
                //    controller : 'ContactCtrl',
                    pageKey : 'CONTACT',
                    template : '@contact.html',
                    onEnter : changeActiveLink
                });


            }]).run(['$state',
            function($state) {
                $state.transitionTo('home');
            }]);


    app.controller('navigationController',

        ['$scope', '$http', '$log', function ($scope, $http, $log) {
            $scope.menu = [];
        }]);

    // Expose jQuery to the global object
    //window.jQuery = window.$ = $;

    angular.element(document).ready(function () {
        console.log('dom-ready:', typeof app);
        angular.bootstrap(document, ['applicationModule']);
    });

    console.log('require-done:');

});
