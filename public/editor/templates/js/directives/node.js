define(['app', 'angular', 'bootbox'], function(app, angular, bootbox) {

    "use strict";

    console.log('--nodeDirective', typeof app);

    // test
    app.ngDirective('test', function() {

        console.info('testDirective init');

        return {
            restrict: 'AE',
            transclude: true,
            replace: true,
            template: '<div class="btn btn-danger"><span ng-transclude></span></div>',

            /*
            link:
                function(scope, $element, attrs) {
                    console.log('testDirective fired');
                    $element.addClass('');
                    $element.html('hello###########');
                }
            */


        }});




});