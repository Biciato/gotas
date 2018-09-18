/**
 * Arquivo de serviço para consultas de veículos
 *
 * @author Gustavo Souza Gonçalves
 * @since 18/09/2018
 * @file /webroot/app/js/services/veiculos/veiculosService.js
 */

angular
    .module('GotasApp')
    .service('veiculosService', function veiculosService($http, $q) {

        $self = {
            obterDadosVeiculosUsuario: obterDadosVeiculosUsuario
        }

        ////////////////

        /**
         * veiculosService::obterDadosVeiculosUsuario
         *
         * Realiza chamada ao serviço de dados de veículos do usuário
         *
         * @param string placa
         * @param string modelo
         * @param string fabricante
         * @param int ano
         * @param int usuariosId
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 18/09/2018
         *
         * @return Array dados
         */
        function obterDadosVeiculosUsuario(placa = undefined, modelo = undefined, fabricante = undefined, ano = undefined, usuariosId = undefined) {

            var url = "/api/veiculos/get_veiculos_usuario";
            var data = {
                placa: placa,
                modelo: modelo,
                fabricante: fabricante,
                ano: ano,
                usuariosId: usuariosId
            };

            // if (usuariosId != undefined) {
            //     data.usuariosId = usuariosId;
            // }

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
