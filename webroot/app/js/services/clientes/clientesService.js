angular
    .module('GotasApp')
    .service('clientesService', function clientesService($http) {

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

            var options = {
                headers: {

                    "IsMobile": true,
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    // "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MTksInN1YiI6MTksImV4cCI6MTUzNzE1NDQxNH0.MrTTBh_QFF9EQoeSXw2tT6aPuiSJ33liwWG4oQ0W0A4"
                }
            };

            var result = undefined;
            return $http.post(url, data, options).then(function (response) {
                console.log(response.data.msg);

                result = response;
                return response;
            }).then(function (error) {
                console.log(error);

                result = error;
                return error;
            })
                ;

        }

        return $self;
    });
