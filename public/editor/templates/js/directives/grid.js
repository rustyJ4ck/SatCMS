define(['app', 'angular', 'bootbox'],

    function(app, angular, bootbox) {

    "use strict";

    console.log('--gridDirective', typeof app);

    app.ngDirective('gridWidget',  ['$localStorage', function($localStorage) {

        /** gridID */
        var id;

        var storage;

        /** root element */
        var root;

        console.log('Grid-directive');

        /**
         * Filter name must be filter[name]
         * @param name
         * @returns {*}
         * @private
         */
        function _name(name) {

            if (typeof name === undefined || !name) {
                console.warn('filter::_name is empty');
                return false;
            }

            return name;

            /*
                $('[name="filter[title]"]').attr('name')
                name.match(/\[(.*)\]/)[1];
            */
        }


        /** sortable */

        function restoreSortables() {
            return $localStorage['grid-sortables-' + id];
        }

        function getSortables() {

            var data = {};

            root.find('.table-normal thead td[data-sortable]').each(function(k, v){
                var $v = $(v);
                var value = $v.data('sortable');
                if (value == 'ASC' || value == 'DESC') {
                    data[$v.data('name')] = value;
                }
            });

            return data;
        }

        function saveSortables(data) {

            var sortables;

            if (typeof data !== 'undefined') {
                sortables = data;

            } else {
                sortables = getSortables();
            }

            // for client

            $localStorage['grid-sortables-' + id] = sortables;

            // cookie for server-side filters

            var prevValue = $.cookie('grid-sortables');

            var cookieSortables = {};

            if (!prevValue || typeof cookieSortables === 'undefined' /*|| !$(cookieFilters).size()*/) {

            } else {

                try {
                    cookieSortables = JSON.parse(prevValue);
                } catch (e) {

                }
            }

            if (!sortables || $(sortables).size() == 0) {
                delete cookieSortables[id];
            }
            else {
                cookieSortables[id] = sortables;
            }

            var newValue = JSON.stringify(cookieSortables);

            if (newValue !== prevValue) {
                $.cookie('grid-sortables', newValue, { expires: 365, path: app.urls.base });
            }

        }

        function applySortables() {

            var sortables = restoreSortables();

            if (sortables && $(sortables).size()) {
                for (var key in sortables) {
                    var elm = root.find('.table-normal thead td[data-sortable][data-name=' + key + ']');
                    elm.data('sortable', sortables[key]);
                    elm.attr('data-sortable', sortables[key]);
                }
            }
        }

        /**
         * Sortable
         */
        function bindSortable($scope)  {

            root.find('.table-normal thead td[data-sortable]').on('click', function() {

                var $this = $(this);

                var value;
                var currentValue = $this.data('sortable');

                if (currentValue === 'DESC') value = 1;
                else if (currentValue === 'ASC') value = 'DESC';
                else value = 'ASC';

                $this.data('sortable', value);
                $this.attr('data-sortable', value);

                //app.message('Reorder grid');

                saveSortables();

                $scope.grid.reload();
            })

        }


        /** filters */

        function restoreFilters() {
            return $localStorage['grid-filters-' + id];
        }

        function applyFilters() {
            var filters = restoreFilters();

            // app.message(angular.toJson(filters));

            //Object.keys(myArray).length
            if (filters && $(filters).size())
            for (var k in filters) {
                var v = filters[k];
                // console.log(k, v, '[name=' + k +'].filter',  root.find('[name=' + k +'].filter').size());
                root.find('[name="' + k +'"].filter').each(function(){
                    var $this = $(this);

                    if ($this.data('index-name')) {
                        var value = v[$this.data('index-name')];
                        if (typeof value === 'object') value = '';
                        $(this).val(value);
                    } else {
                        $(this).val(v);
                    }

                });
            }
        }

        /**
         * Save filters
         * @param filters
         */
        function saveFilters(filters) {

            // app.message(angular.toJson(filters));

            // for client

            $localStorage['grid-filters-' + id] = filters;

            // cookie for server-side filters

            var prevValue = $.cookie('grid-filters');

            var cookieFilters = {};

            if (!prevValue || typeof cookieFilters === 'undefined' /*|| !$(cookieFilters).size()*/) {

            } else {

                try {
                    cookieFilters = JSON.parse(prevValue);
                } catch (e) {

                }
            }

            if (!filters || $(filters).size() == 0) {
                delete cookieFilters[id];
            }
            else {
                cookieFilters[id] = filters;
            }

            var newValue = JSON.stringify(cookieFilters);

            if (newValue !== prevValue) {
                $.cookie('grid-filters', newValue, { expires: 365, path: app.urls.base });
            }

            // console.log('cookieFilters' , filters, cookieFilters);
        }

        /**
         * Get current filters
         * this = $element
         * @returns {{}}
         */
        function getFilters() {

            var filters = {};
            var persist = {};

            // static filters
            // <filter name="">value</filter>
            this.find('filter').each(function(){
                var $v = $(this);
                if ($v.attr('name')) {
                    filters [($v.attr('name'))]= $v.text();
                }
            });

            // <input class="filter" name="" value=""/>
            this.find('.filter').each(function(){
                var $v = $(this);
                var name = _name($v.attr('name'));

                if (name) {
                    if ($v.data('index-name')) {
                        if (!filters[name] || typeof filters[name] !== 'object') {
                            filters [name] = {};
                        }
                        filters [name][$v.data('index-name')] = $v.val();
                    } else {
                        filters [name]= $v.val();
                    }
                }
            });

            // @todo optimize this
            this.find('.filter.filter-persist').each(function(){
                var $v = $(this);
                var name = _name($v.attr('name'));

                if (name) {
                    if ($v.data('index-name')) {
                        if (!persist[name] || typeof  persist[name] !== 'object') {
                            persist [name] = {};
                        }
                        persist [name][$v.data('index-name')] = $v.val();
                    } else {
                        persist [name]= $v.val();
                    }
                }
                // persist [$v.attr('name')]= $v.val();
            });

            console.log('grid-getFilters', filters, persist);

            return {all: filters, persist: persist};

        }

        /**
         *
         */
        function prepareFilters($scope) {
            var filters = getFilters.call(root);
            $scope.filters = filters.all;
            $scope.filtersPersist = filters.persist;
        }

        /**
         * Listen form for submit
         * this = $scope
         */
        function listenForm($scope, $elm) {

            //app.message('listenForm:' + ($elm.find('[grid]').size()?'yes':'no'));
            //console.log('listenForm:' , ($elm.find('[gridWidget]').size() ?'yes':'no'), $elm);

            $elm/*.find('[grid]')*/
                .off('submitableDone') // prevent multibind
                .on('submitableDone', function(e, data){
                    console.log('submitable.done', data);
                    $scope.grid.reload();
                });
        }

        return {
            restrict: 'A',

            replace: true,
            transclude: true,

            template: '<div><!--GRID--><div ng-transclude></div></div>',

            /*
             scope: {
             source: '@source'
             },
             */

            /** DOM parsed */
            link: function($scope, $element, $attrs) {

                root = $element;

                // init form
                listenForm($scope, $element);

                // restore filters
                applyFilters();

                applySortables();
                bindSortable($scope);


// console.log('events',$._data($('#grid-sat-sat_node_image').get(0), 'events'));

            },


            controller: function($scope, $element, $attrs, $http, $compile, $timeout/*, $animate*/) {

                // runs on action

                id = $element.attr('id');

                /*
                 //scope changed  to defaults on compile (reload)
                 $scope.isLoading = $scope.isLoading||false;
                 $scope.isLoading = false;
                */
                $scope.testValue = Math.ceil(Math.random() * 100000);

                // console.log('Grid-directive-controller', $attr);
                console.log('grid-controller [%s]', id, $scope.$id, $element);

                $scope.grid = {

                    id: id,

                    /**
                     * Reset filters
                     */
                    reset : function() {

                        // app.message('reset');

                        prepareFilters($scope);

                        // $scope.filters = {};

                        // remove persist filters
                        if ($scope.filtersPersist && $($scope.filtersPersist).size()) {
                            for (var i in $scope.filtersPersist) {
                                delete ($scope.filters[i])
                            }
                        }

                        $scope.filtersPersist = {};

                        saveFilters({});

                        saveSortables({});

                        // @todo reset sortables

                        this.fetch();

                    },

                    /**
                     * Reload
                     */
                    reload : function() {

                        app.message('Loading data...');

                        prepareFilters($scope);
                        saveFilters($scope.filtersPersist);

                        this.fetch();
                    },

                    /**
                     * Fetch remote
                     */
                    fetch : function() {

                        app.animateViewportLoading(1, $element);
                        $scope.isLoading = true;
                        var url = $attrs.source;

                        console.log('grid-fetch [%s] scope: %s url %s', id, $scope.$id, url);

                        // update form

                        $.post(url, {filter: $scope.filters, order: getSortables()})

                            .done(function(data, textStatus, jqXHR) {

                                console.log('grid-done: ', textStatus);

                                if (typeof data.status != 'undefined' && !data.status) {
                                    // $element.empty();
                                    $element.html(data.message);
                                    app.message(data.message, 1);
                                    return;
                                } else {

                                    var elm = $(data.trim()).find('[grid-widget]:eq(0)');

                                    // slow?
                                    $element.html(elm.html());

                                    // все еще загружаем
                                    app.animateViewportLoading(1, $element);

                                    // rebind
                                    $compile($element)($scope);

                                    $timeout(function(){
                                        app.plugins.editor.bindUI($element);
                                    }, 20);

                                }

                                app.animateViewportLoading(0, $element);

                                $timeout(function(){
                                    //$animate.removeClass($element, 'loading');
                                    $scope.isLoading = false;
                                    // add second delay (reload button disabled)
                                }, 1000)


                            })

                            .error(function(data, textStatus, jqXHR) {
                                // called asynchronously if an error occurs
                                // or server returns response with an error status.
                                $scope.errors.push(textStatus);
                                console.log('error: ', data, textStatus, jqXHR);
                            });


                    }

                }

                $scope.clickTest = function() {
                    app.message('clickTest' + $scope.isLoading);
                }

                /*
                 var columns = $scope.columns = [];

                 this.addColumn = function(column){
                 columns.push(column);
                 };

                 $scope.sortBy = function(column) {
                 if (!column.sortable) return;

                 if ($scope.sortColumn == column.name) {
                 $scope.reverse = !$scope.reverse;
                 } else {
                 $scope.sortColumn = column.name;
                 $scope.reverse = false;
                 }
                 };

                 $scope.getSortDir = function(column) {
                 if ($scope.sortColumn == column.name) {
                 return $scope.reverse ? 'desc' : 'asc';
                 }
                 };

                 */
            }
        };

    }]);

});