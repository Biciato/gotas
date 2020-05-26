(function () {
    'use strict';

    angular
        .module('GotasApp')
        .directive('ifLoading', ifLoading);

    ifLoading.$inject = ['$http'];
    function ifLoading($http) {
        var directive = {
            link: link,
            restrict: 'A',
            scope: {
            }
        };
        return directive;

        function link(scope, element, attrs) {
            scope.isLoading = isLoading;
            scope.$watch(scope.isLoading, toggleElement);

            function toggleElement(loading) {
                if (loading) {
                    element[0].style.display = "block";
                } else {
                    element[0].style.display = "none";
                }
            }

            function isLoading() {
                return $http.pendingRequests.length > 0;
            }
        }
    }

})();
