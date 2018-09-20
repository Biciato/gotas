/**
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 20/09/2018
 */
angular
    .module('GotasApp')
    .service('transportadorasService', function transportadorasService($http, $q) {

        $self = {
            obterDadosTransportadorasUsuario: obterDadosTransportadorasUsuario
        }

        /**
         * transportadorasService::obterDadosTransportadorasUsuario
         *
         * Obtem dados de transportadoras do usuário
         *
         * @param {int} id
         * @param {string} cnpj
         * @param {string} nomeFantasia
         * @param {string} razaoSocial
         * @param {int} usuariosId
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 20/09/2018
         *
         * @return Array data
         */
        function obterDadosTransportadorasUsuario(
            cnpj = undefined,
            nomeFantasia = undefined,
            razaoSocial = undefined,
            usuariosId = undefined) {

            var url = "/api/transportadoras/get_transportadoras_usuario";

            var data = {
                cnpj: cnpj,
                nomeFantasia: nomeFantasia,
                razaoSocial: razaoSocial,
                usuariosId: usuariosId
            };

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
