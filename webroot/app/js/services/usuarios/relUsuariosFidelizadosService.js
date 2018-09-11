angular
    .module('GotasApp')
    .service('relUsuariosFidelizadosService', function relUsuariosFidelizadosService($http) {

        $self = {
            exposedFn: exposedFn,
            pesquisarUsuarios: pesquisarUsuarios
        };

        ////////////////

        function exposedFn() { }

        /**
         *
         * @param int clientesId
         * @param string nome
         * @param int cpf
         * @param int documentoEstrangeiro
         * @param int placa
         * @param int status
         * @param int dataInicio
         * @param int dataFim
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2018-09-10
         *
         * @return promise
         */
        function pesquisarUsuarios(clientesId = undefined, nome = undefined, cpf = undefined, documentoEstrangeiro = undefined, placa = undefined, status = undefined, dataInicio = undefined, dataFim = undefined) {

            var url = "/api/usuarios/get_usuarios_fidelizados";
            var data = {
                clientesId: clientesId,
                nome: nome,
                cpf: cpf,
                documentoEstrangeiro: documentoEstrangeiro,
                placa: placa,
                status: status,
                dataInicio: dataInicio,
                dataFim: dataFim
            };

            var options = {
                headers: {

                    "IsMobile": true,
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    // "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MTksInN1YiI6MTksImV4cCI6MTUzNzE1NDQxNH0.MrTTBh_QFF9EQoeSXw2tT6aPuiSJ33liwWG4oQ0W0A4"
                }
            };

            var dataReturn = undefined;
            return $http.post(url, data, options).then(function (success) {
                return success.data.usuarios;
            }).then(function (error) {
                return error;
            });
        }

        return $self;
    }

    );
