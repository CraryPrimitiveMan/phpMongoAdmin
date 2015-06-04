angular
    .module('controllers', ['services'])
    .controller('MainCtrl', ['$mdSidenav', '$rootScope', '$location', 'restService', '$log', MainCtrl])
    .controller('DetailCtrl', ['$location', 'restService', DetailCtrl]);

/**
 * Main Controller for the Angular Material Starter App
 * @param $mdSidenav
 * @param $rootScope
 * @param $location
 * @param restService
 * @param $log
 * @constructor
 */
function MainCtrl($mdSidenav, $rootScope, $location, restService, $log ) {
    var self = this;

    self.selected           = null;
    self.databases          = [];
    self.selected           = {};
    self.selectDB           = selectDB;
    self.selectCollection   = selectCollection;
    self.toggleList         = toggleList;
    self.isOpen             = isOpen;

    restService
        .get('server/index')
        .success(function(data){
            $rootScope.servers = data;
            self.selected.server = data[0];
            $location.search('server', data[0]);
            restService
                .get('database/index')
                .success(function(data) {
                    self.databases = data;
                });
        });

    /**
    * Hide or Show the 'left' sideNav area
    */
    function toggleList() {
        $mdSidenav('left').toggle();
    }

    function isOpen(database) {
        return !!self.selected.db && self.selected.db === database;
    }

    /**
     * Select the database
     * @param database
     */
    function selectDB(database) {
        $location.search('db', database);
        restService
            .get('collection/index')
            .success(function(data) {
                self.collections = data;
            });
        self.selected.db = database;
    }

    /**
     * Select the collection
     * @param collection
     */
    function selectCollection(collection) {
        self.selected.collection = collection;
        $location.search('collection', collection).path('/detail');
    }

}

function DetailCtrl($location, restService) {
    var self = this;
    restService
        .get('doc/index')
        .success(function(data) {
            self.doc = data;
        });
}
