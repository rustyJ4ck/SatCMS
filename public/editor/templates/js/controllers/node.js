define(['angular', 'app', 'jquery', 'plugins/editor', 'bootbox'],

    function(angular, app, $, ed, bootbox) {

    "use strict";

    console.log('controllers/node.js');

    /**
     * Node list filter
     */
    app.ngController('nodeListFilterController', function($scope, $element, $state) {

        console.log('nodeListFilterController');

        $scope.showVisual = function(pid) {
            app.message('showVisual');
            pid = parseInt(pid);
            $state.go('nodeVisual', {id: app.getSite().id, pid: pid});
        }

    });

    //

    /**
     * Node list filter
     */
    app.ngController('nodeVisualController', [

        '$scope', '$stateParams', '$http',

        function($scope, $stateParams, $http) {

        var currentTree = [];

        $scope.node = {};

        var locked = false;

        console.log('nodeVisualController');

        function initVis() {

            if (locked) return;

            locked = true;

            require(['vis'], function(vis){

                //http://bs3.satcms.work/sat/api/editor/node/tree/{id}/
                $.get(
                        '/sat/api/editor/node/tree/' + $stateParams.id + '/'
                    )
                    .done(function(data, status) {

                        var edges = [];
                        var nodes = [];

                        currentTree = []; //data

                        var pid = parseInt($stateParams.pid);
                        var pids = [pid];

                        // filter PID
                        if (pid) {
                            data.each(function(v){
                               if (pids.indexOf(v.pid) !== -1 || pid == v.id) {
                                   currentTree.push(v);
                                   //@todo unique only
                                   pids.push(v.id);
                               }
                            });

                        } else {
                            currentTree = data;
                            nodes.push({id:0, label: 'Корень'});
                        }

                        // console.log('pid=', pid, pids);
                        // console.dir(currentTree);

                        // console.profile();

                        currentTree.each(function(v){
                            nodes.push({id:v.id, label: v.title});
                            if (!pid || (pid && pid != v.id)) {
                                edges.push({from: v.id, to: v.pid});
                            }
                        });

                        // console.log('nodes', nodes.count());

                        var gData = {
                            nodes: nodes,
                            edges: edges
                        };

                        // console.log('gData', gData);

                        var options = {
                            nodes: {
                                shape: 'box'
                            },
                            // stabilize: false

                            physics: {
                                barnesHut: {
                                    enabled: false, // faster?
                                }
                            }

                        };

                        // create a graph
                        var container = $('#mygraph').get(0);

                        var graph = new vis.Graph(container, gData, options);

                        // add event listeners
                        graph.on('select', function(params) {

                            var id = parseInt(params.nodes.pop());
                            var node = nodes.find({'id':id});

                            $scope.$apply(function(){
                                $scope.node = node;
                            });

                            // app.message(id);
                            // console.log(params.nodes);
                        });

                        // add event listener
                        //graph.on('select', function(properties) {
                        //    document.getElementById('info').innerHTML += 'selection: ' + JSON.stringify(properties) + '<br>';
                        //});

                        // console.profileEnd();

                        graph.setSelection([pid]);

                        locked = false;

                    });

            });
        }

        // Initialize
        setTimeout(initVis, 50);

        $scope.goHome = function() {
            var url = '/editor/sat/node/pid/' + $stateParams.pid + '/';
            app.go(url);
        }

        $scope.goViewNode = function() {
            var node = currentTree.find({'id':$scope.node.id});
            var url = '/editor/sat/node/pid/' + node.pid + '/';
            app.go(url);
        }

        $scope.goEditNode = function() {
            //http://bs3.satcms.work/editor/sat/sat_node/op/edit/id/30/pid/25/
            var url = '/editor/sat/node/op/edit/id/' + $scope.node.id + '/';
            app.go(url);
        }

        /*
        $scope.showVisual = function() {
            app.message('showVisual');
            app.go('nodeVisual', {id: app.getSite().id});
        }
        */

        return true;

    }]);

    /**
     * New node
     */
    app.ngController('nodeFormNewController',

        ['$scope', '$element',

        function($scope, $element) {

        console.log('nodeFormNewController');

        $scope.testForm = "%%TestForm%%";

        $element
            .off('submitableDone') // prevent multibind
            .on('submitableDone', function(e, data) {

                // console.log('submitable.done', data);
                setTimeout(function(){
                    if (data.status &&  $scope.saveAndContinue) {
                        app.go(data.data.urls.editor_edit, true);
                    } else {
                        app.ngReload();
                    }
                }, 150);

                $('#formModal').modal('hide');

            });

        var $form = $element.find('form');

        $scope.saveAndContinue = 0;

        $scope.save = function() {
            $scope.saveAndContinue = 0;
            $form.find('input[name=save_continue]').val(0);
            $form.submit();
        }

        $scope.saveContinue = function() {
            $scope.saveAndContinue = 1;
            $form.find('input[name=save_continue]').val(1);
            $form.submit();
        }

    }]);

    /**
     * Uploader
     */
    app.ngController('uploadifyController', function($scope) {

        console.log('uploadifyController');

        function bindUploader() {

            var btn = $('#uploadBtn');

            btn.uploadify({

                'swf'      : app.urls.toRoot('vendor/uploadify/uploadify.swf'),
                'uploader' : app.urls.toRoot('vendor/uploadify/uploadify.php'),

                // uploader: app.urls.toRoot('vendor/uploadify/uploadify.swf'),
                // script: app.urls.toBase('/'),

                cancelImg: app.urls.toRoot('vendor/uploadify/uploadify-cancel.png'),

                multi: true,
                buttonClass : 'btn btn-default btn-sm',
                buttonText: 'Add files',
                queueID: 'fileQueue',
                auto: false,

                scriptData: {
                    _ua:   '{$user.useragent}',
                    _sid:  '{$user.session_sid}',
                    pid: '{$model.id}',
                    m: 'sat',
                    c: 'node_files_uploader',
                    fileDataName: 'files'
                },

                onComplete: function(e, qID, response, data) {

                    console.log('onComplete', e, qID, response, data);

                    var _data = eval("_data = (" + data + ')');
                    var $table = $('#sat_files_list');

                    if (typeof(_data.id) == 'undefined' || !parseInt(_data.id)) return;

                    var ops = _upload_ops;
                    ops = ops.replace('%id%', _data.id);

                    var name = _data.file.name;
                    name = name.length > 15 ? (name.substr(0,15) + '&#133') : name;

                    $table.append(
                        '<tr><td><a target="_blank" href="' + _data.file.url + '">' + name + '</a>'
                            +  '</td><td><a href="" onclick="prompt(\'URL файла\',\'' + _data.file.url + '\');return false;">' + _data.file.type + '</a>'
                            +  '</td><td>' + ops
                            + '</td></tr>'
                    );

                    //console.log("%s", data);
                    //console.log("%s", _data.url);
                }
                 //width: '300'

            });

        }

        require(['uploadify'], function(){
            bindUploader();
        });

    });

    /**
     * @depricated use directive
     * nodeFormController
     */
    app.ngController('gridController',

        ['$scope', '$http', '$log', 'app', '$state', '$stateParams', '$element',

            function ($scope, $http, $log, app, $state, $stateParams, $element) {

                // element = gridForm


                $element.on('submitableSuccess', function(e, data){
                    console.log('submit.success', data);
                    app.message('reload grid - success');
                });

                $element.on('submitableError', function(e, data){
                    console.log('submit.error', data);
                    app.message('reload grid - error', 1);
                    return false;
                });

                console.log('nodeFormController++++++++++');

                var grid = {
                    reload : function() {
                        $element.submit();
                        return false;
                    }
                }

                $scope.grid = grid;

            }
     ]);

    /*
     // ajax tabs
     root.find('a[data-toggle=tab][data-url]').on('click', function(){
     var $this = $(this);
     if (!$this.data('_loaded')) {
     var $target = $($this.attr('href'));
     app.blockUI(1);
     $target.load($(this).data('url'), function(){
     _bindUI(this);
     app.blockUI(0);
     });
     $this.data('_loaded', true);

     */


    /**
     * nodeFormTabsController
     */

    app.ngController('nodeFormTabsController',

        ['$scope', '$http', '$log', 'app', '$state', '$stateParams', '$compile',

            function ($scope, $http, $log, app, $state, $stateParams, $compile ) {

                console.log('nodeFormTabsController++++++++++');

                var tabsLoaded = [];

                $scope.openTab = function(id, url) {

                    // alert(url);

                    if (tabsLoaded.indexOf(id) >= 0) return;

                    tabsLoaded.push(id);

                    var $target = $(id);

                    if (!$target.size()) {
                        app.message('openTab failed ' + id, 1);
                        return;
                    }

                    app.blockUI(1);

                    $target.load(url, function(response, status, xhr){

                        if ( status == "error" ) {
                            app.message('Bad response' + xhr.status + " " + xhr.statusText, 1);
                        }
                        else {

                            // compile ng
                            if ($target.find('.compilable').size()) {
                                $target.find('.compilable').each(function(k, v){
                                    $compile(angular.element(v).contents())($scope);
                                });
                            }

                            ed.bindUI(this);

                            $scope.$apply();

                        }

                        app.blockUI(0);
                    })

                }

            }
        ]);


});