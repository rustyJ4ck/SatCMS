define(['jquery', 'app'],

    function ($, app) {

        'use strict';

        /** @var editor passed with initialize() */
        var ed;

        var widgetsLoaded = [];



        /**
         * If compile, apply scope as this
         * @param id
         * @param url
         * @param $scope - if passed, use ng compile
         */
        function loadWidget(id, url, $scope) {

            if (widgetsLoaded.indexOf(id) >= 0) {
                // do not load twice
                return;
            }

            widgetsLoaded.push(id);

            var $target = $(id);

            if (!$target.size()) {
                app.message('loadWidget failed ' + id, 1);
                return;
            }

            // app.blockUI(1);

            $target.load(url, function(response, status, xhr) {

                if ( status == "error" ) {
                    app.message('Bad response' + xhr.status + " " + xhr.statusText, 1);
                }
                else {

                    // compile ng
                    if (typeof $scope != 'undefined' && $scope) {
                        ed.ngCompile($target, $scope);
                    }

                    ed.bindUI(this);

                }

                //app.blockUI(0);
            });

        }

        /**
         * public API
         */
        return {

            initialize: function(_ed) {
                ed = _ed;
            },

            loadWidget: loadWidget

        }
    }
);