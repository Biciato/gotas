angular
    .module('GotasApp')
    .service('relUsuariosAssiduosService', function relUsuariosAssiduosService($http, $q) {


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
        function pesquisarUsuarios(
            clientesIds = undefined,
            usuariosId = undefined,
            nome = undefined,
            cpf = undefined,
            documentoEstrangeiro = undefined,
            placa = undefined,
            status = undefined,
            assiduidade = undefined,
            agrupamento = undefined,
            dataInicio = undefined,
            dataFim = undefined) {

            var url = "/api/usuarios/get_usuarios_assiduos";
            var data = {
                clientesIds: clientesIds,
                usuariosId: usuariosId,
                nome: nome,
                cpf: cpf,
                documentoEstrangeiro: documentoEstrangeiro,
                placa: placa,
                status: status,
                agrupamento: agrupamento,
                assiduidade: assiduidade,
                dataInicio: dataInicio,
                dataFim: dataFim
            };

            var deferred = $q.defer();
            $http.post(url, data).then(
                function (success) {
                    deferred.resolve(success.data.msg);
                }
                , function (error) {
                    deferred.reject(error.data);
                });

            return deferred.promise;
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

            var url = "/api/usuarios/generate_excel_usuarios_assiduos";
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

            var deferred = $q.defer();

            $http.post(url, data).then(function (success) {
                deferred.resolve(success.data.msg);
            }, function (error) {
                deferred.reject(error.data);
            });

            return deferred.promise;
        }

        return $self;
    }

    );
