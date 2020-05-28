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
         * relUsuariosAssiduosService::pesquisarUsuarios
         *
         * Busca dados de assiduidade de usuários da rede, conforme filtros
         *
         * @param int clientesIds Clientes Ids
         * @param string nome Nome
         * @param int cpf Cpf
         * @param int documentoEstrangeiro Documento Estrangeiro
         * @param int placa Placa
         * @param int status Status
         * @param int dataInicio Data Inicio
         * @param int dataFim Data Fim
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2018-09-10
         *
         * @return promise
         */
        function pesquisarUsuarios(clientesIds = undefined, usuariosId = undefined, nome = undefined, cpf = undefined, documentoEstrangeiro = undefined, placa = undefined, status = undefined, assiduidade = undefined, agrupamento = undefined, dataInicio = undefined, dataFim = undefined) {

            var url = "/api/usuarios/get_usuarios_assiduos";
            var data = {
                clientesIds: clientesIds,
                usuariosId: usuariosId,
                nome: nome,
                cpf: cpf,
                documentoEstrangeiro: documentoEstrangeiro,
                placa: placa,
                status: status,
                assiduidade: assiduidade,
                agrupamento: agrupamento,
                dataInicio: dataInicio,
                dataFim: dataFim,
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
         * relUsuariosAssiduosService::gerarExcel
         *
         * Busca dados de assiduidade de usuários da rede, conforme filtros, e gera excel
         *
         * @param int clientesIds Clientes Ids
         * @param string nome Nome
         * @param int cpf Cpf
         * @param int documentoEstrangeiro Documento Estrangeiro
         * @param int placa Placa
         * @param int status Status
         * @param int dataInicio Data Inicio
         * @param int dataFim Data Fim
         * @param bool filtrarPorUsuario Filtrar Por Usuario
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2018-09-10
         *
         * @return promise
         */
        function gerarExcel(redesId = undefined, clientesIds = undefined, usuariosId = undefined, nome = undefined, cpf = undefined, documentoEstrangeiro = undefined, placa = undefined, status = undefined, assiduidade = undefined, agrupamento = undefined, dataInicio = undefined, dataFim = undefined, filtrarPorUsuario = false) {

            var url = "/api/usuarios/generate_excel_usuarios_assiduos";
            var data = {
                redesId: redesId,
                clientesIds: clientesIds,
                usuariosId: usuariosId,
                nome: nome,
                cpf: cpf,
                documentoEstrangeiro: documentoEstrangeiro,
                placa: placa,
                status: status,
                assiduidade: assiduidade,
                agrupamento: agrupamento,
                dataInicio: dataInicio,
                dataFim: dataFim,
                filtrarPorUsuario: filtrarPorUsuario
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
