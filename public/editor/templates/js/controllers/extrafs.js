define(['app', 'plugins/i18n', '/modules/extrafs/editor/js/fields.js'], function(app, i18n, extrafs) {

    "use strict";

    console.log('--extrafsFieldsController', extrafs);

    app.ngController('extrafsFieldsFormController', function($element, $scope, $timeout){

        // ng-init
        // $scope.value = {};

        /*
        $scope.$watch("value", function(){
            alert(JSON.stringify($scope.value));
        });
        */



        //evil
        $timeout(function(){
            console.log('+extrafsFieldsFormController', $scope.value);
            extrafs.init($element, $scope);
        }, 100);

    });

    app.ngController('extrafsFieldsListController',

        function ($scope) {

            console.log('+extrafsController');

            $scope.fieldTest = '@@###';

            /*
             $scope.user = {
             name   : "Michael Jackson",
             age    : 13,
             gender : "male"
             };

             $scope.save = function (user) {
             testService.run(angular.toJson(user));
             }
             */



        }

    );
});
