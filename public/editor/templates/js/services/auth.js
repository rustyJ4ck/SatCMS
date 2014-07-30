define(['app', 'angular'], function(app, angular) {

    "use strict";

    console.log('--authDirective');

    app.ngService('auth', ['$http', function ($http) {

        var url = '/users/api/user/current/';
        var _user = {level:0, 'logged': false, fetched: false};

        function fetchUser() {
            return $http.get(url).success(function(data){
                if (data.id !== undefined) {
                    _user = angular.extend(_user, data);
                }
                _user.fetched = true;
            });
        }

        function _getUser() {
            return _user.fetched ? _user : fetchUser();
        }

        function logout() {
            $.get('/users/api/user/logout/?rnd=' + Math.ceil(10000*Math.random()))
                .success(function(data){
                    location.href = '/editor/in/';
                });
        }

        return {
            getUser: _getUser,
            user: _user,

            logout: logout
        };

    }]);


});