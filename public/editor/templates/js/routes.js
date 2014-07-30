/**
 * js/routes.js
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */


define(['bootbox'], function (bootbox) {

    'use strict';

    console.log('routes.js');

    /** @todo unhardcode active modules */
    var validModules = [
        'fm', 'core', 'users' // 'sat', 'contacts', 'anket', 'catalog', 'users', 'extrafs', 'test'
    ];

    var stringValidModules = validModules.join('|');

    return [


        // Dashboard
        {
            name: 'index',
            title: 'Index',
            url: "index",
            templateUrl: "partials/index.html",

            controller: function () {
                console.info('indexState');
            }
        },


        {
            name: 'error',
            title: 'Error',
            url: "error",
            //templateUrl: "partials/login.html",
            template: "@error",
            controller: function () {
                console.info('errorState');
            }
        },


        {
            name: 'config',
            title: 'config',
            url: "core/config",
            //templateUrl: "partials/login.html",
            template: "@config",
            controller: function () {
                console.info('configState');
            }
        },

        {
            name: 'login',
            title: 'Login',
            url: "users/login",
            templateUrl: "partials/login.html",
            controller: function () {
                console.info('loginState');
                // bootbox.alert('Not authorized!');
            },
            options: {
                userLevel:0
            }
        },


        {
            name: 'action-base',
            title: 'controller',
            //url: "(" + stringValidModules + ")/.*",
            url: "{module:(?:" + stringValidModules + ")}/{path:.*}",
            //templateUrl: "partials/login.html",
            //template:"<div ng-bind-html='response'></div>",
            template:"",
            controller: 'actionController',
        },


        {
            name: 'nodeVisual',
            title: 'nodeVisual',
            url: 'node/visual/:id/:pid',
            templateUrl: "/modules/sat/editor/templates/node/list.vis.html?4",
            //template:"<div ng-bind-html='response'></div>",
            //template:"nodeVisual",
            controller: 'nodeVisualController'
        },







    ];

});