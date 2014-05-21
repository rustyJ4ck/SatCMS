define(['angular', 'app', 'jquery'], function(angular, app, $) {

    "use strict";

    app.ngApp().controller('navigationController',

        ['$scope', '$http', '$log', 'app', '$state', '$stateParams', '$rootScope',

            function ($scope, $http, $log, app, $state, $stateParams, $rootScope) {

                $scope.sites    = [];
                $scope.menu     = [];

                // {section, action}
                $scope.action   = [];

                $scope.siteID   = $.cookie('site_id');

                $rootScope.site = {domain: 'localhost'};

                /**
                 * Load menu
                 */
                function _loadMenu() {
                    $http.get(app.urls.toRoot('core/api/editor/menu/'))
                        .success(function(data, status){
                            $scope.menu = data;
                            $scope.$emit('menuLoaded', data);
                        })
                        .error(function(data, status) {
                            app.message('navigationController fail to load menu', true);

                        });
                }

                /**
                 * Load sites
                 */
                function _loadSites() {
                    $http.get(app.urls.toRoot('sat/api/editor/sites/'))
                        .success(function(data, status){

                            console.log('_loadSites', $scope.siteID);

                            if ($scope.siteID) {
                                $rootScope.site =  data.find({'id': parseInt($scope.siteID)});
                                $rootScope.site.active = true;
                            }

                            $scope.sites = data;
                        })
                        .error(function(data, status) {
                            app.message('navigationController fail to load sites', true);

                        });
                }

                /**
                 * Reload after site change
                 */
                function reload() {
                    if ($state.$current.navigable) {
                        $state.transitionTo($state.current, $stateParams, { reload: true, inherit: true, notify: true });
                    }
                }

                $scope.logout = function() {
                    app.message('Logging out');
                    app.ngAuth().logout();
                }

                /**
                 * @todo refactor
                 * @param id
                 */
                $scope.toggleSite = function(id) {

                    // redundant
                    $.cookie('site_id', id, {expires:356, path:'/editor/'});

                    $scope.sites.each(function(v){
                        if (v.id == id) {
                            v.active = true;
                            $rootScope.site = v;
                        }
                        else delete v.active;;
                    });

                    app.message('Выбран сайт: ' + $scope.sites.find({'id': id}).title);

                    //reload();
                    app.go('/editor/sat/node/site_id/' + id + '/');
                }

                /**
                 * Process state change, set current section/action
                 */
                $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams){

                    //console.log('NAV $stateChangeSuccess to ', $rootScope.state);

                    var params = $rootScope.state.current;

                    if (!params.section || toState.name == 'redirector') return;

                    /*current Object { module="core", section="logs/"} */
                    var regexp = /^([\w_\d]+)/;
                    var section = params.section.match(regexp)[0];

                    var toSection = $scope.menu.find({id: params.module});
                    var toAction = toSection.actions.find({id: section});

                    // console.log('NAV',  toSection.id , params.module , toAction.id , section);

                    // do not trigger twice
                    /*
                    if (toSection.id == params.module && toAction.id == section) {
                        return;
                    }
                    */

                    $scope.action = {section: toSection, action: toAction};

                    console.log('actionClick', $scope.action);
                    $scope.$emit('menuAction', $scope.action);

                    // trigger click
                    //$scope.actionClick(toSection, toAction);

                });

                /**
                 * Menu clicked
                 * @param section (module)
                 * @param action
                 */

                /*

                // Navigation menu click
                $scope.actionClick = function(section, action) {

                    $scope.action = {section: section, action: action};

                    console.log('actionClick', $scope.action);
                    $scope.$emit('menuAction', $scope.action);
                }
                */

                _loadMenu();
                _loadSites();

                $log.log('++navigationController');

                /*
                $scope.save = function (user) {
                    alert(angular.toJson(user));
                }

                $scope.sidebarActive = true;
                $scope.toggleSidebar = function() {
                    $scope.sidebarActive = !$scope.sidebarActive;
                }
                 */
        }]
    );

});
