/**
 * js/main.js
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: main.js,v 1.3.2.8.2.14 2013/10/16 08:27:55 Vova Exp $
 */

require.config({

    //baseUrl: "../../../",

    paths: {
        angular: '../../../vendor/angular/angular',
        jquery: "../../../vendor/jquery/dist/jquery",
        bootstrap: "../../../vendor/bootstrap/dist/js/bootstrap",

        //angularText: loaded ondemand
        text: "../../../vendor/requirejs-text/text",

        angularSelect2: "../../../vendor/angular-ui-select2/src/select2",
        angularSanitize: "../../../vendor/angular-sanitize/angular-sanitize",
        angularRouter: "../../../vendor/angular-ui-router/release/angular-ui-router",
        angularAnimate: "../../../vendor/angular-animate/angular-animate",

        angularStorage: "../../../vendor/ngstorage/ngStorage",

        /*
        jqueryMigrate:"../../../vendor/jquery-migrate/jquery-migrate",
        jqueryMigrateFacade:"../../../jscripts/jquery/jquery-migrate",
         jqueryPlugins: "../../../jscripts/jquery/plugins",
        */

        momentjs: "../../../../vendor/moment/moment",
        bootstrapDateTime: "../../../vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min",

        jqueryValidate: "../../../../vendor/jquery.validation/dist/jquery.validate",
        jqueryBlockUI: "../../../vendor/blockui/jquery.blockUI",
        jqueryCookie: "../../../vendor/jquery-cookie/jquery.cookie",
        jqueryTableDND: "../../../vendor/TableDND/jquery.table-multi-dnd",

        select2: "../../../vendor/select2/select2",
        tinyMCE: "../../../vendor/tinymce/tinymce.jquery",
        notify:"../../../vendor/toastr/toastr",
        bootbox: "../../../vendor/bootbox/bootbox",

        uploadify: "../../../vendor/uploadify/jquery.uploadify",

        'bootstrap-modal': "../../../vendor/bootstrap-modal/js/bootstrap-modal",
        'bootstrap-modal-manager': "../../../vendor/bootstrap-modal/js/bootstrap-modalmanager",

        'sugar': "../../../vendor/sugar/release/sugar-full.development",

        'vis': "../../../vendor/vis/dist/vis"

    },

    waitSeconds: 0,

    // + (new Date()).getTime(),
    //urlArgs: "v=2",
    urlArgs: window.config.debug ? "v=" + (new Date()).getTime() : '',

    shim: {

        angular : {exports : 'angular', deps : ["jquery"]},

        jquery: {exports: 'jQuery'},

    //    angularSelect2: ['angular', 'jquery', 'select2'],
        select2: ['jquery'],
    //    jqueryMigrate: ['jquery'],
    //    jqueryPlugins: ['jqueryMigrate'],

        jqueryCookie: ['jquery'],

        angularRouter: ['angular'],
        angularSanitize: ['angular'],
        angularAnimate: ['angular'],
        angularStorage: ['angular'],

        bootstrapDateTime: ['momentjs'],

        bootstrap : {
            deps : ["jquery"]
        },

        'app': ['sugar'],

        'controllers/navigation': ['app'],
        'directives/basic': ['app'],

//        'tf': {'exports': 'tf'}
    },

    priority: ['angular', 'jquery', 'sugar'] //, 'app'],

});

// window.name = "NG_DEFER_BOOTSTRAP!";

require([

    'angular',

    'app',

    // base

    'angularRouter',
    'angularStorage',

    'angularSanitize',
    //'angularSelect2',
    'angularAnimate',

    'controllers/navigation',
    'controllers/index',

    'bootstrap',
  //  'jqueryMigrateFacade',

  // obsolete
  //  'jqueryPlugins',
    'jqueryCookie',

    'sugar',

    'directives/basic',

], function(angular, app) {
    'use strict';

    /*
     var $html = angular.element(document.getElementsByTagName('html')[0]);

     angular.element().ready(function() {
     angular.resumeBootstrap([app['name']]);
     });
     */

    // Expose jQuery to the global object
    //window.jQuery = window.$ = $;

    angular.element(document).ready(function () {
        console.log('dom-ready:', typeof app);
        app.bootstrap(document);
    });

    console.log('require-done:');

});
