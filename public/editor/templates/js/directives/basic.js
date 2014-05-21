define(['app', 'angular', 'bootbox'], function(app, angular, bootbox) {

    "use strict";

    console.log('--basic directives', typeof app);

    var ngApp = app.ngApp();

    /*
    app.ngDirective('activeGrid', function() {


        return{

        link:
            function(scope, $element, attrs) {
                $element.find('.box-header .title').html('++ativeGridDirective');
            }


    }});

    app.ngDirective('test', function() {


        return{

            restrict: 'E',

            link:
                function(scope, $element, attrs) {
                    app.message('testDirective fired');
                    $element.html('@hello');
                }


        }});

    */


    // test
    ngApp.directive('test123', function() {

        console.info('testDirective init');

        return{
            restrict: 'AE',

            link:
                function(scope, $element, attrs) {
                    console.log('testDirective fired');
                    $element.addClass('btn btn-primary');
                    $element.html('hello');
                }



        }});

});