define(['app', 'angular'], function(app, angular) {

    "use strict";

    console.log('--compileDirective');

    app.ngDirective('compile', ['$compile', function ($compile) {

        return function(scope, element, attrs) {
            var ensureCompileRunsOnce = scope.$watch(
                function(scope) {
                    // watch the 'compile' expression for changes
                    return scope.$eval(attrs.compile);
                },
                function(value) {
                    // when the 'compile' expression changes
                    // assign it into the current DOM
                    element.html(value);

                    // compile the new DOM and link it to the current
                    // scope.
                    // NOTE: we only compile .childNodes so that
                    // we don't get into infinite loop compiling ourselves
                    $compile(element.contents())(scope);

                    // Use un-watch feature to ensure compilation happens only once.
                    ensureCompileRunsOnce();
                }
            );
        };


        }]);


});