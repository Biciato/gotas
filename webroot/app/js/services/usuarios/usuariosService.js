angular
    .module('GotasApp')
    .service('usuariosService', function usuariosService($http, $q) {

        $self = {
            obterDadosUsuario: obterDadosUsuario,
            obterListaClientes: obterListaClientes
        }

        /**
         * usuariosService::obterDadosUsuario
         *
         * Obtem dados de usuário por id
         *
         * @param {int} id Id do usuário
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 13/09/2018
         */
        function obterDadosUsuario(id) {
            var url = "/api/usuarios/get_usuario_by_id";
            var data = {
                id: id
            };

            var deferred = $q.defer();
            $http.post(url, data).then(function (success) {
                deferred.resolve(success.data.msg);
            }, function (error) {
                console.log(error);
                deferred.reject(error.data);
            });

            return deferred.promise;
        }

        /**
         * TODO: não está concluído
         * @param {*} redesId
         */
        function obterListaClientes(redesId = undefined) {

            var url = "/api/usuarios/get_usuarios_list";
            var data = {};

            if (redesId != undefined) {
                data.redesId = redesId;
            }

            var options = {
                headers: {
                    "IsMobile": true,
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                }
            };

            var deferred = $q.defer();
            $http.post(url, data, options).then(function (success) {
                deferred.resolve(success.data.msg);
            }, function (error) {
                console.log(error);
                deferred.reject(error.data);
            });

            return deferred.promise;
        }

        return $self;
    });
