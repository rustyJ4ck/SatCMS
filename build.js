({

    paths: {

	text: "../../../vendor/requirejs-text/text", // relative to baseUrl

        angular: "../../../vendor/angular/angular",
        jquery: "../../../vendor/jquery/dist/jquery",
        bootstrap: "../../../vendor/bootstrap/dist/js/bootstrap",

        angularSelect2: "../../../vendor/angular-ui-select2/src/select2",
        angularSanitize: "../../../vendor/angular-sanitize/angular-sanitize",
        angularRouter: "../../../vendor/angular-ui-router/release/angular-ui-router",
        angularAnimate: "../../../vendor/angular-animate/angular-animate",

        angularStorage: "../../../vendor/ngstorage/ngStorage",

        momentjs: "../../../vendor/moment/moment",
        bootstrapDateTime: "../../../vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min",

        jqueryValidate: "../../../vendor/jquery.validation/dist/jquery.validate",
        jqueryBlockUI: "../../../vendor/blockui/jquery.blockUI",
        jqueryCookie: "../../../vendor/jquery-cookie/jquery.cookie",
        jqueryTableDND: "../../../vendor/TableDND/jquery.table-multi-dnd",

        select2: "../../../vendor/select2/select2",
        tinyMCE: "../../../vendor/tinymce/tinymce.jquery.min",
        notify:"../../../vendor/toastr/toastr",
        bootbox: "../../../vendor/bootbox/bootbox",
        
        "bootstrap-modal": "../../../vendor/bootstrap-modal/js/bootstrap-modal",

        "sugar": "../../../vendor/sugar/release/sugar-full.development",

        "vis": "../../../vendor/vis/dist/vis"

    },

    deprecated_entries: {
        uploadify: "../../../vendor/uploadify/jquery.uploadify",
        "bootstrap-modal-manager": "../../../vendor/bootstrap-modal/js/bootstrap-modalmanager"
    },

    waitSeconds: 0,

    shim: {

        angular : {exports : "angular", deps : ["jquery"]},

        jquery: {exports: "jquery"},

        select2: ["jquery"],
        jqueryCookie: ["jquery"],

        angularRouter: ["angular"],
        angularSanitize: ["angular"],
        angularAnimate: ["angular"],
        angularStorage: ["angular"],

        bootstrapDateTime: ["momentjs"],

        bootstrap : {
            deps : ["jquery"]
        },

        "app": ["sugar", "jquery", "bootstrap", "angular"],

        "controllers/navigation": ["app"],
        "directives/basic": ["app"],

    },

    priority: ["angular", "jquery", "sugar", "bootstrap"],

    baseUrl: "public/editor/templates/js",

    preserveLicenseComments: false,

    removeCombined: true,
    findNestedDependencies: true,

    useStrict: true,

//  wrap: true,
//  wrapShim: true,
    inlineText: false,

    // stubModules: ['tinyMCE'], //'text'

    d1ir: "dist",
    n1ame: "path/to/almond",

    include: ["main"],
    out: "public/assets/editor/main.js",

    optimize: "uglify2",

    uglify2: {
        output: {
            beautify: false
        },
        compress: {
            sequences: true,
            dead_code: true,
            join_vars: true,
            drop_console: true,

            global_defs: {
                DEBUG: false
            }
        },
        warnings: true,

        // @fixme angular don't like this (cost 300+Kb)
        mangle: false,
        //{
        //    except: ['angular']
        //}
    }

})