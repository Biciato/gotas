/**
 * Controller para Relatório de Frequência Média de Usuários
 *
 * @file webroot\app\js\controllers\usuarios\relUsuariosFrequenciaMediaController.js
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-02-11
 *
 */
// var GotasApp = angular.module("GotasApp");
angular
    .module("GotasApp")
    .controller("relUsuariosFrequenciaMediaController", function(
        $scope,
        FileSaver,
        Blob,
        APP_CONFIG,
        toastr,
        $uibModal,
        clientesService,
        downloadService,
        relUsuariosAssiduosService
    ) {
        $scope.inputData = {
            clientesSelectedItem: undefined,
            clientesId: undefined,
            nome: undefined,
            clientesList: [],
            statusList: [
                { id: 1, nome: "Ativado" },
                { id: 0, nome: "Desativado" }
            ],
            assiduidadeList: [{ id: 1, nome: "Sim" }, { id: 0, nome: "Não" }],
            veiculo: undefined,
            documentoEstrangeiro: undefined,
            usuarioContaAtivadaSelectedItem: undefined,
            statusSelectedItem: undefined,
            assiduidadeSelectedItem: undefined,
            dataInicial: undefined,
            dataFinal: undefined
        };
        // @todo Ajustar
        $scope.usuarioLogado = {
            tipoPerfil: 3
        };

        /**
         * Função que valida vazio
         * @param object value Objeto à ser validado
         *
         * @returns bool
         */
        $scope.empty = function(value) {
            if (value !== undefined && value !== null) {
                return false;
            }
            return true;
        };

        // ------------------------ Configurações de tabela ------------------------

        $scope.paginaAtual = 1;
        $scope.limitePagina = 50;
        $scope.tamanhoDaPagina = 50;

        $scope.cabecalhos = [
            "Usuário",
            "CPF",
            "Documento Estrangeiro",
            "Média Assiduidade/Mês",
            "Status Assiduidade",
            "Gotas Adquiridas",
            "Gotas Utilizadas",
            "Gotas Expiradas",
            "Saldo Atual",
            "Total Compras Brinde (R$)",
            // "Data Cadastro na Rede",
            "Ações"
        ];

        $scope.dadosUsuarios = [];

        // ------------------------ Configurações de tabela ------------------------

        // ------------------------ Funções ------------------------

        /**
         * relUsuariosAssiduosController::$scope.validarFiltro
         *
         * @param {Object} inputData Dados de Formulário
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 13/09/2018
         *
         * @return {Boolean} validação
         */
        $scope.validarFiltro = function(inputData) {
            var dataInicial = $scope.empty(inputData.dataInicial)
                ? undefined
                : moment(inputData.dataInicial);
            var dataFinal = $scope.empty(inputData.dataFinal)
                ? undefined
                : moment(inputData.dataFinal);

            // validação de data só irá ocorrer se as duas datas estiverem preenchidas
            if (!$scope.empty(dataInicial) && !$scope.empty(dataFinal)) {
                if (dataInicial > dataFinal) {
                    toastr.error(
                        "A data final deve ser maior que a data inicial!",
                        "Erro!"
                    );
                    return false;
                }
                return true;
            }

            return true;
        };

        // ------------------------ Pesquisas ------------------------

        /**
         * relUsuariosAssiduosController::$scope.obterListaClientes
         *
         * Obtem a lista de clientes
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 13/09/2018
         */
        $scope.obterListaClientes = function() {
            clientesService.obterListaClientes().then(
                function(success) {
                    $scope.clientesList = success;

                    if (success.length == 1) {
                        $scope.clientesSelectedItem = $scope.clientesList[0];
                    }
                },
                function(error) {
                    if (!$scope.empty(error)) {
                        toastr.error(error.description, error.title);
                        console.log(error);
                    }
                }
            );
        };

        /**
         * relUsuariosAssiduosController::$scope.pesquisarUsuarios
         *
         * Realiza pesquisa dos usuários conforme filtro informado
         *
         * @param {Object} inputData
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 13/09/2018
         *
         */
        $scope.pesquisarUsuarios = function(inputData) {
            if ($scope.validarFiltro(inputData)) {
                var dataInicial = undefined;
                var dataFinal = undefined;

                if (!$scope.empty(inputData.dataInicial)) {
                    dataInicial = moment(inputData.dataInicial).format(
                        "YYYY-MM-DD"
                    );
                }

                if (!$scope.empty(inputData.dataFinal)) {
                    dataFinal = moment(inputData.dataFinal).format(
                        "YYYY-MM-DD"
                    );
                }

                var clientesIds = [];

                if (
                    !$scope.empty(inputData.clientesSelectedItem) &&
                    inputData.clientesSelectedItem.id > 0
                ) {
                    clientesIds = inputData.clientesSelectedItem.id;
                } else {
                    angular.forEach($scope.clientesList, function(value, key) {
                        clientesIds.push(value.id);
                    });
                }

                var status = !$scope.empty(inputData.statusSelectedItem)
                    ? inputData.statusSelectedItem.id
                    : null;

                var assiduidade = !$scope.empty(
                    inputData.assiduidadeSelectedItem
                )
                    ? inputData.assiduidadeSelectedItem.id
                    : null;

                relUsuariosAssiduosService
                    .pesquisarUsuarios(
                        clientesIds,
                        undefined,
                        inputData.nome,
                        inputData.cpf,
                        inputData.documentoEstrangeiro,
                        inputData.placa,
                        status,
                        assiduidade,
                        true,
                        dataInicial,
                        dataFinal
                    )
                    .then(
                        function(success) {
                            $scope.dadosUsuarios = [];
                            $scope.dadosUsuarios = success;
                        },
                        function(error) {
                            toastr.error(error.description, error.title);
                            $scope.dadosUsuarios = [];
                        }
                    );
            }
        };

        /**
         * relUsuariosFidelizadosController::$scope.gerarExcel
         *
         * Gera excel
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 15/09/2018
         *
         * @param {Object} inputData Dados de formulário
         */
        $scope.gerarExcel = function(inputData) {
            if ($scope.validarFiltro(inputData)) {
                var dataInicial = undefined;
                var dataFinal = undefined;

                if (!$scope.empty(inputData.dataInicial)) {
                    dataInicial = moment(inputData.dataInicial).format(
                        "YYYY-MM-DD"
                    );
                }

                if (!$scope.empty(inputData.dataFinal)) {
                    dataFinal = moment(inputData.dataFinal).format(
                        "YYYY-MM-DD"
                    );
                }

                var clientesIds = [];

                if (
                    !$scope.empty(inputData.clientesSelectedItem) &&
                    inputData.clientesSelectedItem.id > 0
                ) {
                    clientesIds = inputData.clientesSelectedItem.id;
                } else {
                    angular.forEach($scope.clientesList, function(value, key) {
                        clientesIds.push(value.id);
                    });
                }

                var status = !$scope.empty(inputData.statusSelectedItem)
                    ? inputData.statusSelectedItem.id
                    : null;

                var assiduidade = !$scope.empty(
                    inputData.assiduidadeSelectedItem
                )
                    ? inputData.assiduidadeSelectedItem.id
                    : null;

                relUsuariosAssiduosService
                    .gerarExcel(
                        undefined,
                        clientesIds,
                        undefined,
                        inputData.nome,
                        inputData.cpf,
                        inputData.documentoEstrangeiro,
                        inputData.placa,
                        status,
                        assiduidade,
                        true,
                        dataInicial,
                        dataFinal,
                        true
                    )
                    .then(
                        function(success) {
                            downloadService.downloadExcel(
                                success,
                                "relUsuariosAssiduos"
                            );
                        },
                        function(error) {
                            toastr.error(error.description, error.title);
                            console.log(error);
                        }
                    );
            }
        };

        /**
         * relUsuariosAssiduosController::limparDados
         *
         * Limpa todos os campos da tela e aplica reset inicial aos filtros
         *
         * @author Gustavo Souza Gonçalves
         * @since 14/09/2018
         *
         */
        $scope.limparDados = function() {
            var date = new Date();
            var year = date.getFullYear();
            var month = date.getMonth();
            $scope.inputData = {
                clientesSelectedItem: undefined,
                clientesId: undefined,
                nome: undefined,
                clientesList: [],
                statusList: [
                    { id: 1, nome: "Ativado" },
                    { id: 0, nome: "Desativado" }
                ],
                assiduidadeList: [
                    { id: 1, nome: "Sim" },
                    { id: 0, nome: "Não" }
                ],
                veiculo: undefined,
                documentoEstrangeiro: undefined,
                usuarioContaAtivadaSelectedItem: undefined,
                statusSelectedItem: undefined,
                assiduidadeSelectedItem: undefined,
                dataInicial: new Date(year, month, 1),
                dataFinal: new Date(year, month + 1, 0)
            };
        };

        /**
         * relUsuariosAssiduosController::detalhesUsuario
         *
         * Exibe Modal de detalhes de usuário
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 24/09/2018
         *
         * @param {Object} usuario
         */
        $scope.detalhesUsuario = function(usuario) {
            var modalInstance = $uibModal.open({
                ariaLabelledBy: "modal-title",
                ariaDescribedBy: "modal-body",
                templateUrl:
                    "/webroot/app/pages/relatorios/usuarios/modalDetalhesUsuario.php",
                backdrop: "static",
                controller: "modalDetalhesUsuarioController",
                size: "lg",
                resolve: {
                    usuarioId: function() {
                        return usuario.id;
                    }
                }
            });
        };

        /**
         * relUsuariosAssiduosController::detalhesUsuario
         *
         * Exibe Modal de detalhes de usuário
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 24/09/2018
         *
         * @param {Object} usuario
         */
        $scope.detalhesAssiduidadeUsuario = function(usuario) {
            var modalInstance = $uibModal.open({
                ariaLabelledBy: "modal-title",
                ariaDescribedBy: "modal-body",
                templateUrl:
                    "/webroot/app/pages/relatorios/usuarios/modalDetalhesAssiduidadeUsuario.php",
                backdrop: "static",
                controller: "modalDetalhesAssiduidadeUsuarioController",
                size: "lg",
                resolve: {
                    usuario: function() {
                        return usuario;
                    }
                }
            });
        };

        /**
         * Inicializa a tela
         */
        $scope.init = function() {
            $scope.limparDados();
            $scope.obterListaClientes();
            // $scope.obterDadosSessao();
        };
    });
