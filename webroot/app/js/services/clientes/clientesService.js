angular
    .module('GotasApp')
    .service('clientesService', function clientesService($http, $q) {

        $self = {
            obterListaClientes: obterListaClientes
        }

        ////////////////

        function obterListaClientes(redesId = undefined) {

            var url = "/api/clientes/get_clientes_list";
            var data = {};

            if (redesId != undefined) {
                data.redesId = redesId;
            }

            var deferred = $q.defer();
            $http.post(url, data).then(function (success) {
                deferred.resolve(success.data.msg);
            }, function (error) {
                console.log(error);
                deferred.reject(error.data);
            });

            return deferred.promise;
        }

        return $self;
    });
