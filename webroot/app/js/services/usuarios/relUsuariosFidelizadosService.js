angular
    .module('GotasApp')
    .service('relUsuariosFidelizadosService', function relUsuariosFidelizadosService($http) {

        $self = {
            exposedFn: exposedFn,
            gerarExcel: gerarExcel,
            pesquisarUsuarios: pesquisarUsuarios
        };

        ////////////////

        function exposedFn() { }

        /**
         *
         * @param int clientesIds
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
        function pesquisarUsuarios(clientesIds = undefined, nome = undefined, cpf = undefined, documentoEstrangeiro = undefined, placa = undefined, status = undefined, dataInicio = undefined, dataFim = undefined) {

            var url = "/api/usuarios/get_usuarios_fidelizados";
            var data = {
                clientesIds: clientesIds,
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
                }
            };

            var dataReturn = undefined;
            return $http.post(url, data, options).then(function (success) {
                return success.data.usuarios;
            }).then(function (error) {
                return error;
            });
        }

        /**
         *
         * @param int clientesIds
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
        function gerarExcel(clientesIds = undefined, nome = undefined, cpf = undefined, documentoEstrangeiro = undefined, placa = undefined, status = undefined, dataInicio = undefined, dataFim = undefined) {

            var url = "/api/usuarios/generate_excel_usuarios_fidelizados";
            var data = {
                clientesIds: clientesIds,
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
