angular.module('starterApp', ['ngMaterial', 'ngRoute', 'controllers', 'services'])
    .config(['$routeProvider', '$locationProvider', '$mdThemingProvider', '$mdIconProvider', function($routeProvider, $locationProvider, $mdThemingProvider, $mdIconProvider) {
        var basePath = 'app/src/';

        $mdIconProvider
            .defaultIconSet("./app/assets/svg/avatars.svg", 128)
            .icon("menu", "./app/assets/svg/menu.svg", 24)
            .icon("toggle-arrow", "./app/assets/svg/toggle-arrow.svg", 24)
            .icon("share", "./app/assets/svg/share.svg", 24)
            .icon("google_plus", "./app/assets/svg/google_plus.svg", 512)
            .icon("hangouts", "./app/assets/svg/hangouts.svg", 512)
            .icon("twitter", "./app/assets/svg/twitter.svg", 512)
            .icon("phone", "./app/assets/svg/phone.svg", 512);

        $mdThemingProvider.theme('default')
            .primaryPalette('brown')
            .accentPalette('red');

        $routeProvider
            .when('/', {
                controller: 'MainCtrl'
            })
            .when('/detail', {
                templateUrl: basePath + 'views/detail.html',
                controller: 'DetailCtrl',
                controllerAs : 'detail'
            })
            .otherwise({
                redirectTo: '/'
            });
    }]);