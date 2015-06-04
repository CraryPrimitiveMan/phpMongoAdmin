angular.module('services', [])
    .factory('restService', ['$http', '$location', function($http, $location) {
        var rest = {};
        var methods = ['get', 'post', 'put', 'delete'];

        angular.forEach(methods, function(method) {
            rest[method] = function(router) {
                var params = $location.search();
                if(params.server) {
                    router += '/' + params.server;
                    if (params.db) {
                        router += '/' + params.db;
                        if (params.collection) {
                            router += '/' + params.collection;
                        }
                    }
                }
                var url = 'web/front.php?r=' + router;
                arguments[0] = url;
                return $http[method].apply(this, arguments);
            }
        });

        return rest;
    }]);
    // .factory( 'Resource', [ '$resource', function( $resource ) {
    //   return function( url, params, methods ) {
    //     var defaults = {
    //       update: { method: 'put', isArray: false },
    //       create: { method: 'post' }
    //     };

    //     methods = angular.extend( defaults, methods );

    //     var resource = $resource( url, params, methods );

    //     resource.prototype.$save = function() {
    //       if ( !this.id ) {
    //         return this.$create();
    //       }
    //       else {
    //         return this.$update();
    //       }
    //     };

    //     return resource;
    //   };
    // }]);

//     var module = angular.module( 'services', [ 'my.resource' ] );
//
//module.factory( 'User', [ 'Resource', function( $resource ) {
//  return $resource( 'users/:id', { id: '@id' } );
//}]);
//  var user = new User;
//user.name = 'Kirk Bushell';
//user.$save(); // POST
//
//var user = User.get( { id: 1 });
//user.name = 'John smith';
//user.$save(); // PUT