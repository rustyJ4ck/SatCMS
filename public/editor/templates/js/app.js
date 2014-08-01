/**
 * js/app.js
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */

define([
    'jquery',
    'angular',
    'notify',
    'bootbox',
    'jqueryBlockUI',
    'jqueryCookie'
], function($, angular, toastr, bootbox){

    'use strict';

    console.info('app.js');

    // toastr defaults
    toastr.options = {
        positionClass: "toast-top-right",
        timeOut: 3000
    };

    $.cookie.json = true;

    // fix HTML5 locations ie9

    /*
    var buggyAndroid = parseInt((/android (\d+)/.exec(window.navigator.userAgent.toLowerCase()) || [])[1], 10) < 4;

    if (!history.pushState || buggyAndroid) {
        if (window.location.hash) {
            if(window.location.pathname !== '/') window.location.replace('/#!' + window.location.hash.substr(2)); //Hash and a path, just keep the hash (redirect)
        } else {
            window.location.replace('/#!' + window.location.pathname); //No hash, take path
        }
    }
    */

    /**
     * tfApp
     */
    var tfApp = (function(config){

        var startState = 'dashboard';

        var angularApp;
        var angularInjector;
        var angularProviders = {};

        var angularAppDeps = [
            'ui.router',
           // 'ui.select2',
            'ngSanitize',
            'ngAnimate',
            'ngStorage'
        ];

        var angularState;
        var angularStateParams;

        var navigationMenu;
        var navigationAction; // {section, action}

        var loading = false;

        var urls = {

            // @todo unhardcode
            root: '/',
            base: '/editor/',

            /** to site root */
            toRoot: function(url) {
                return urls.root + url;
            },

            /** to editor root */
            toBase: function(url) {
                return urls.base + url;
            },

            /** to current site root */
            toSite: function(url) {
                return getSite().urls.self + (url.substr(0,1) == '/' ? '' : '/') + url;
            }
        }

        var plugins = {
            editor: null
        };

        function getSite() {
            //@todo dirty code
            return angularInjector.get('$rootScope').site;
        }


        function loadLibs(callback) {


            require([
                'plugins/editor',
                'services/auth',
                //      'bootstrap-modal',
                //      'bootstrap-modal-manager',
                'jqueryTableDND',
                'notify',
                'bootbox',
            ]
                , function(ed){

                    plugins.editor = ed;

                    ed.initialize();

                    callback();

            });


            // angular stuff
            /*
            require(angularRequire, function(){
                angularInjector.get('$rootScope').$apply();
            });
            */
        }

        function registerActionState($provider, modules) {

            var stringValidModules = modules.join('|')
            var route = {
                name: 'action',
                title: 'controller',
                url: "{module:(?:" + stringValidModules + ")}/{path:.*}",
                template:"",
                controller: 'actionController'
            };

            route.url = urls.toBase(route.url);
            console.log("..actionState", route.name, route);
            $provider.state(route);
        }

        /**
         * States
         * @param $provider
         */
        function loadStates($provider) {

            console.log("loadStates");


            $provider.state('default', {
                    name: 'default',
                    title: 'Default',
                    url: "/editor/",
                    //templateUrl: "templates/partials/index.html",
                    templateUrl: "/editor/core/dashboard/op/index/",
                    //template: "@index",
                    controller: 'indexController'
            });

            $provider.state('req', {
                controller: function(){alert('ok');}
            });

            $provider.state('redirector',
            {
                title: "Redirecting...",
                url: "/editor/redirect/{path:.*}",
                template:false,
                controller:

                    ['$state', '$stateParams', '$scope', '$location',
                    function($state, $stateParams, $scope, $location){

                    tfApp._redirect = tfApp.urls.toBase($stateParams.path);
                    tfApp.blockUI();

                }]
            });

            // require dependencies,

            require([
                'routes',
                'controllers/index',
                'controllers/action',

            ], function(routes){

                console.log("loadStatesDone");

                angular.forEach(routes, function(route, index){

                    if (route.templateUrl !== undefined && route.templateUrl.indexOf('/') !== 0) {
                        route.templateUrl = urls.toBase('templates/' + route.templateUrl);
                    }

                    route.url = urls.toBase(route.url);
                    console.log("..state: " + route.name, route);
                    $provider.state(route);

                });

                loadStatesDone();
            });




        }

        /**
         * loadStatesDone
         */
        function loadStatesDone() {
        }

        /**
         * Angular service 'auth'
         * @returns {*|Object|array|Object|HttpPromise}
         */
        function ngAuth() {
            return angularInjector.get('auth'); /*angular.injector(['tfApp'])*/
        }

        /**
         *
         */
        function loadUser() {
            console.log('--loadUser');
            ngAuth().getUser().then(function(_user){
                    //checkAuth();
                    angularInjector.get('$rootScope').$broadcast("userChanged", ngAuth().getUser());
                    //scope.$digest();

                    /** redirect do dash */
                    if (!loading) {
                        angularInjector.get('$rootScope').$broadcast("loading");
                        loading = true;
                    }

                });

        }

        /**
         * @returns {_user.logged|*}
         */
        function checkAuth() {
            return loading && ngAuth().user.logged;
            //bootbox.alert('ngAuth' + angular.toJson(ngAuth().user, true));
        }

        /**
         * Instantiate angular app
         * @returns {*|Object}
         * @private
         */
        function _angularAppInstance() {

            return angular

                .module('tfApp', angularAppDeps, ['$controllerProvider', '$compileProvider', '$provide',
                    function ($controllerProvider, $compileProvider, $provide) {
                    angularProviders.controller = $controllerProvider;
                    angularProviders.compiler = $compileProvider;
                    angularProviders.service = $provide;
                    console.log('angular-done');

                    /*
                    $compileProvider
                    .directive('form', function($rootScope) {
                        return {
                            restrict: 'E',
                            terminal: true,
                           // transclude: true,
                            priority: 1000, // give it higher priority than built-in ng-click
                            link:angular.noop,
                            link1: function(scope, element, attr) {
                                //   console.log('form-overridden');

                            }
                        }
                    });
*/
                }])

                .config(['$interpolateProvider', function ($interpolateProvider) {
                        $interpolateProvider.startSymbol('[[');
                        $interpolateProvider.endSymbol(']]');
                }])

                .config(['$stateProvider', '$urlRouterProvider', '$locationProvider',

                        function ($stateProvider, $urlRouterProvider, $locationProvider) {

                            tfApp.ngStateProvider = $stateProvider;

                            $locationProvider.html5Mode(true).hashPrefix('!');

                            console.log('$urlRouterProvider-config');

                            $urlRouterProvider
                                // default index
                                .when('', function(){
                                    //!history.pushState
                                    //fix ie9 redirect to index
                                    tfApp.go('/editor/');
                                    console.log('When-go-dashboard');
                                })
                                // Handle bad URLs
                                .otherwise('/editor/error/')
                           ;


                            loadStates($stateProvider);
                }])

                .config(['$urlRouterProvider', function ($urlRouterProvider) {
                    // Here's an example of how you might allow case insensitive urls
                    $urlRouterProvider.rule(function ($injector, $location) {
                        /*
                        //what this function returns will be set as the $location.url
                        var path = $location.path(), normalized = path.toLowerCase();
                        if (path != normalized) {
                            //instead of returning a new url string, I'll just change the $location.path directly so I don't have to worry about constructing a new url string and so a new state change is not triggered
                            $location.replace().path(normalized);
                        }
                        // because we've returned nothing, no state change occurs
                        */
                        console.log('$location.path: ' + $location.path());
                    });
                }])

                .run(
                    [   '$http', '$rootScope', '$state', '$stateParams', '$injector', '$location', '$window',
                        function ($http, $rootScope,   $state,   $stateParams, $injector, $location, $window) {

                            $rootScope.modulesToolbar = [];
                            $rootScope.loading = true;

                            // @fix me - onchange?
                            angularState = $state;
                            angularStateParams = $stateParams;

                            angularInjector = $injector;

                            $http.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
                            $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
                            $http.defaults.headers.post["SC-CSRF-TOKEN"] =  config.token;

                            console.log($state);

                            $rootScope.$on('userChanged', function(event, user){

                                // assign user to root scope
                                $rootScope.user = user;
                                //$rootScope.$digest();
                                console.log('userChanged', user);

                                    //tfApp.ngReload();
                                    // angularState.go(startState);
                                    // this.go('/editor/dashboard/', 1);

                            });

                            $rootScope.$on('menuLoaded', function(event, menu){

                                navigationMenu = menu;

                                $rootScope.modulesToolbar = [];

                                var modules = [];

                                menu.each(function(mod){

                                    modules.push(mod.id);

                                    var option = mod.actions.find({default: true})
                                    if (angular.isDefined(option))
                                    $rootScope.modulesToolbar.push({title: mod.title, url: option.url});
                                });

                                // @todo modules actions
                                registerActionState(tfApp.ngStateProvider, modules);

                                console.log('menuLoaded-catched', modules);
                            });

                            $rootScope.$on('menuAction', function(event, action){
                                $rootScope.navAction = navigationAction = action;
                                console.log('menuAction-catched');
                            });

                            $rootScope.$on('$stateChangeStart',function(event, toState, toParams, fromState, fromParams){

                                if (!checkAuth() && loading && (toState.options === undefined || toState.options.userLevel)) {
                                    event.preventDefault();
                                    console.log('$stateChangeStart: auth check failed', toState, fromState);
                                    $state.go('login');
                                    //$location.path('/editor/users/login');
                                    return false;
                                }

                                console.log('$stateChangeStart to '+toState.to+'- fired when the transition begins. toState,toParams : \n', toState, toParams);

                                toState.current = {
                                    module:  toParams.module,
                                    section: toParams.path
                                }

                                $rootScope.state = toState;
                                $rootScope.state.params = toParams;

                            });

                            $rootScope.$on('$stateChangeError', function(event, toState, toParams, fromState, fromParams){
                                console.log('$stateChangeError - fired when an error occurs during transition.');
                                console.log(arguments);
                            });

                            $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams){
                                console.log('$stateChangeSuccess to '+toState.name+'- fired once the state transition is complete.');
                            });

                            $rootScope.$on('$viewContentloading', function(event){
                                console.log('$viewContentloading - fired after dom rendered',event);
                            });

                            $rootScope.$on('$stateNotFound', function(event, unfoundState, fromState, fromParams){
                                console.log('$stateNotFound '+unfoundState.to+'  - fired when a state cannot be found by its name.');
                                console.log(unfoundState, fromState, fromParams);
                            });


                            $rootScope.$on('loading', function(event, value){

                                console.log('on-loading', value)

                                value = typeof value === 'undefined' ? false : value;
                                $rootScope.loading = value;

                                // redirector
                                if (!value && tfApp._redirect) {
                                    tfApp.go(tfApp._redirect, 1);
                                    tfApp._redirect = false;
                                    tfApp.blockUI(0);
                                }
                            });

                            $rootScope.$watch("loading", function(from, to){
                               console.log('watch-loading',from, to)
                               if (from != to) {
                               //    tfApp.blockUI(!to);
                               }
                            });

                            $rootScope.sidebarActive = true;
                            $rootScope.toggleSidebar = function() {

                              // console.log(angularInjector('navigationController'));
                               $rootScope.sidebarActive = !$rootScope.sidebarActive;

                            }



                }])

                .value('app', tfApp);
            ;


        }

        function getAngularState() {
            return angularState;
        }

        function getAngularApp() {

            if (!angularApp) {
                angularApp = _angularAppInstance();
            }

            return angularApp;
        }

        /**
         * Message --> pnotify
         * @param message
         * @param isError
         */
        function message (message, isError) {

            if (typeof isError == 'undefined') {
                isError = false;
            }

            if (typeof message == "object" && message !== null) {
                // unimplemented abstract toastr.me(message);
            }
            else
                isError ? toastr.error(message) : toastr.success(message)

        }

        /**
         * @return tfApp
         */
        return {

            _redirect: false,

            plugins: plugins,
            urls: urls,

            config: config,

            ngApp: getAngularApp,
            ngState: getAngularState,
            ngAuth: ngAuth,

            ngController: function(name, controller) {
              //  getAngularApp().controller(name, controller);
              //  angularProviders.controller.register.apply(name, controller);
                console.info('ngController', name);
                angularProviders.controller.register(name, controller);
            },

            ngDirective: function(name, directive) {
                angularProviders.compiler.directive(name, directive);
            },

            ngService: function(name, factory) {
                angularProviders.service.factory(name, factory);
            },

            ngInjector: function() {
                return angularInjector;
            },

            ngLocation: function() {
                return angularInjector.get('$location');
            },

            ngReload: function() {
                angularState.transitionTo(angularState.current, angularStateParams, { reload: true, inherit: true, notify: true });
                this.message('Обновлено');
            },

            ngScope: function() {
                return $('#content').scope();
            },

            reload: function(apply) {
                this.go(this.ngLocation().path(), apply);
            },

            go: function(url, apply) {
                apply = typeof apply === 'undefined' ? 0 : apply;
                //$state.go("lazy.state", {a:1, b:2}, {inherit:false}); .
                //angularState.transitionTo(url, params, { reload: true, inherit: true, notify: true });
                //setTimeout("window.location.href = '" + data.url + "'", 500);
                //this this if you want to change the URL and add it to the history stack

                console.log('app-go: ' + url, apply);

                this.ngLocation().path(url);

                if (apply) {
                    angularInjector.get('$rootScope').$apply();
                }

                //$scope.$apply();
            },

            getSite: getSite,

            bootstrap: function(doc) {

                var app = getAngularApp();

                // @return injector
                angular.bootstrap(doc, ["tfApp"]);

                loadLibs(function(){
                    loadUser();
                });

                //loadMenu();

            },

            // get menu from navigation controller
            getMenu: function() {
                return navigationMenu;
            },

            getCurrentAction: function() {
                return navigationAction;
            },

            /**
             * Message --> pnotify
             * @param message
             * @param isError
             */
            message: message,

            getViewportElement: function() {
                return $('#content');
            },

            /**
             * Animate loading
             * @param state
             */
            animateViewportLoading: function(state, viewportID) {

                var viewport = typeof viewportID == 'undefined' ? this.getViewportElement() : $(viewportID);

                if (state) {
                    viewport.addClass('loading');
                } else {
                    setTimeout(function(){
                        viewport.removeClass('loading');
                    }, 250);
                }
            },

            /**
             * BlockUI with progressbar
             */
            blockUI: function(_option) {
                var option = typeof _option === "undefined" ? 1 : _option;

                if (option) {
                    $.blockUI({message:
                        '<div class="progress progress-striped active">' +
                            '<div class="progress-bar" style="width: 100%;">Loading...</div>' +
                            '</div>', css:{padding:0,margin:0,background:'none',border:0,width:'200px',left:'50%',marginLeft:'-100px'}, class:'hello'
                    });
                }
                else {
                    setTimeout(function(){
                        $.unblockUI();
                    }, 150);
                }
            }

        }

    })(config);

    console.log('tfApp-done', typeof tfApp);

    return tfApp;

});