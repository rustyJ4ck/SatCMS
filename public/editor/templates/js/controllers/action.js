/**
 * Load cms page and render it in viewport
 */

define(

    ['app', 'angular', 'bootbox', 'directives/grid']

    , function(app, angular, bootbox) {

    "use strict";

    console.log('--actionController', typeof app);

    /* @todo no-dom-manipulation-in-the-controller-mantra */

    app.ngController('actionController',

        ['$timeout', '$sce', '$scope', '$http', '$log', '$state', '$stateParams', '$compile', 'app',

            function ($timeout, $sce, $scope, $http, $log, $state, $stateParams, $compile, app) {

            /*
            // Your $stateParams object would be
            { id:'123', type:'default', repeat:'0', from:'there', to:'here' }
            */
            $log.log('++actionController', $state, $stateParams);


            // $stateParams.path = 'core/config';
            // $scope.response = '@action:/' + $stateParams.path;

            var routeUrl = $stateParams.module + '/' + $stateParams.path;

            // make embed layout default for actions
            var actionUrl = app.urls.toBase(routeUrl
                + ((routeUrl.indexOf('?') === -1) ? '?' : '&')
                + 'embed=yes');

            $scope.test = "@Compiled";

            $scope.blockUI = true;

            $scope.reload = function() {
                console.log('reload');
                // $state.transitionTo($state.current, $stateParams, { reload: true, inherit: true, notify: true });
                app.ngReload();
            }


            function runAction() {

                // Not so angular way to do this, sorry!
                var element = angular.element('#content');

                // cleanup dom!
                // element.find('select[class!="system"]').select2('destroy');

                $http.get(actionUrl)
                    .success(function(data, status){

                        // error||something
                        if (data.status !== undefined && !data.status) {
                            element.empty();
                            app.message(data.message, true);
                            return;
                        }

                        // $scope.response = data ? $sce.trustAsHtml(data) : 'Bad response';

                        element.html(data);

                        // Compile chunks (.compilable)

                        if (element.find('.compilable').size()) {
                            element.find('.compilable').each(function(k, v){
                                $compile(angular.element(v).contents())($scope);
                            });
                        }

                        //$compile(element.contents())($scope);

                        $timeout(function(){
                            app.plugins.editor.bindUI('#content');
                        }, 10);

                    })
                    .error(function(data, status){
                        $state.go('error');
                        bootbox.alert('Error fetching content : ' + status + ' ' + angular.toJson(data));
                    })

            }


            // resolve deps

            var action = app.getCurrentAction() && app.getCurrentAction().action || false;

            if (action && action.require !== undefined && action.require.js !== undefined) {

                console.log('-Require-deps', action.require.js);

                var actions = action.require.js.slice(); //copy

                // @todo check for absolute path
                // fix for r.js
                for (var i in actions) {
                    actions[i] = app.urls.toBase('templates/js/' + actions[i] + '.js');
                }

                // load
                require(actions, function(){
                    console.log('+Require-deps');
                    runAction();
                    // $apply for directives?
                });

            } else {

                runAction();

            }

        }]
    );

});
