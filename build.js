({

    paths: {
        "text" : "../../../vendor/requirejs-text/text",
//        tf: "",
        angular: '../../../vendor/angular/angular',
        jquery: "../../../vendor/jquery/jquery",
        bootstrap: "../../../vendor/bootstrap/dist/js/bootstrap",
        jqueryMigrate:"../../../vendor/jquery-migrate/jquery-migrate",
        jqueryMigrateFacade:"../../../jscripts/jquery/jquery-migrate",
        jqueryPlugins: "../../../jscripts/jquery/plugins",

        select2: "../../../vendor/select2/select2",
        angularSelect2: "../../../vendor/angular-ui-select2/src/select2",

        angularSanitize: "../../../vendor/angular-sanitize/angular-sanitize",
        angularRouter: "../../../vendor/angular-ui-router/release/angular-ui-router",
        angularAnimate: "../../../vendor/angular-animate/angular-animate",

        notify:"../../../vendor/toastr/toastr",
        bootbox: "../../../vendor/bootbox/bootbox",

        'jqueryBlockUI': "../../../vendor/jquery-blockui/jquery.blockUI",

        'bootstrap-modal': "../../../vendor/bootstrap-modal/js/bootstrap-modal",
        'bootstrap-modal-manager': "../../../vendor/bootstrap-modal/js/bootstrap-modalmanager",

        'sugar': "../../../vendor/sugar/release/sugar-full.development"

    },

    waitSeconds: 0,

    shim: {

        angular : {exports : 'angular', deps : ["jquery"]},

        jquery: {exports: 'jquery'},

        angularSelect2: ['angular', 'jquery', 'select2'],
        select2: ['jquery'],
        jqueryMigrate: ['jquery'],
        jqueryPlugins: ['jqueryMigrate'],
        angularRouter: ['angular'],
        angularSanitize: ['angular'],
        angularAnimate: ['angular'],

        bootstrap : {
            deps : ["jquery"]
        }

//        'tf': {'exports': 'tf'}
    },

    priority: ['angular', 'jquery', 'app'],


    m1ainConfigFile : "public/editor/templates/js/main.js",
    baseUrl: "public/editor/templates/js",

    removeCombined: true,

    f1indNestedDependencies: true,

    d1ir: "public/dist",
    n1ame: 'path/to/almond',

    include: ['main'],
    out: 'public/assets/editor/main.js',

    m1odules: [
        {
            name: "main",

            exclude: [
            ],

            excludeOld: [
                "backbone",
                "backbrace",
                "bootstrap",
                "jquery",
                "jqbase64",
                "machina",
                "monologue",
                "monopost",
                "neuquant",
                "omggif",
                "postal",
                "diags",
                "riveter",
                "text",
                "underscore"
            ]

        }
    ]
})