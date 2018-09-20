/**
 * Controller para Relatório de Usuários Ativo
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 17/09/2018
 */
// var GotasApp = angular.module("GotasApp");
angular.module('GotasApp').controller("relUsuariosAssiduosController",
    function ($scope, FileSaver, Blob, toastr, clientesService,
        downloadService,
        relUsuariosAssiduosService) {

        $scope.inputData = {
            clientesSelectedItem: undefined,
            clientesId: undefined,
            nome: undefined,
            clientesList: [],
            statusList: [
                { codigo: 0, nome: "Ativado" },
                { codigo: 1, nome: "Desativado" }
            ],
            assiduidadeList: [
                { codigo: 0, nome: "Sim" },
                { codigo: 1, nome: "Não" }
            ],
            veiculo: undefined,
            documentoEstrangeiro: undefined,
            usuarioContaAtivadaSelectedItem: undefined,
            assiduidadeSelectedItem: undefined,
            dataInicial: undefined,
            dataFinal: undefined
        };

        /**
         * Função que valida vazio
         * @param object value Objeto à ser validado
         *
         * @returns bool
         */
        $scope.empty = function (value) {
            if (value !== undefined) {
                return false;
            }
            return true;
        }

        // ------------------------ Configurações de tabela ------------------------

        $scope.paginaAtual = 1;
        $scope.limitePagina = 50;
        $scope.tamanhoDaPagina = 50;

        $scope.cabecalhos = [
            "Usuário",
            "CPF",
            "Documento Estrangeiro",
            "Saldo Gotas",
            "Gotas Consumidas",
            "Moeda Adquirida ",
            "Data Cadastro na Rede",
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
        $scope.validarFiltro = function (inputData) {

            var dataInicial = $scope.empty(inputData.dataInicial) ? undefined : moment(inputData.dataInicial);
            var dataFinal = $scope.empty(inputData.dataFinal) ? undefined : moment(inputData.dataFinal);

            // validação de data só irá ocorrer se as duas datas estiverem preenchidas
            if (!$scope.empty(dataInicial) && !$scope.empty(dataFinal)) {
                if (dataInicial > dataFinal) {
                    toastr.error("A data final deve ser maior que a data inicial!", "Erro!");
                    return false;
                }
                return true;
            }

            return true;
        }

        // ------------------------ Pesquisas ------------------------

        /**
         * relUsuariosAssiduosController::$scope.obterListaClientes
         *
         * Obtem a lista de clientes
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 13/09/2018
         */
        $scope.obterListaClientes = function () {
            clientesService.obterListaClientes().then(function (success) {
                $scope.clientesList = success;

                if (success.length == 1) {
                    $scope.clientesSelectedItem = $scope.clientesList[0];
                }

            }, function (error) {
                if (!$scope.empty(error)) {
                    toastr.error(error.description, error.title);
                    console.log(error);
                }
            })
        }

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
        $scope.pesquisarUsuarios = function (inputData) {

            if ($scope.validarFiltro(inputData)) {

                var dataInicial = undefined;
                var dataFinal = undefined;

                if (!$scope.empty(inputData.dataInicial)) {
                    dataInicial = moment(inputData.dataInicial).format("YYYY-MM-DD");
                }

                if (!$scope.empty(inputData.dataFinal)) {
                    dataFinal = moment(inputData.dataFinal).format("YYYY-MM-DD");
                }

                var clientesIds = [];

                if (!$scope.empty(inputData.clientesSelectedItem) && inputData.clientesSelectedItem.id > 0) {
                    clientesIds = inputData.clientesSelectedItem.id;
                } else {
                    angular.forEach($scope.clientesList, function (value, key) {
                        clientesIds.push(value.id);
                    });
                }

                relUsuariosAssiduosService.pesquisarUsuarios(
                    clientesIds,
                    inputData.nome,
                    inputData.cpf,
                    inputData.documentoEstrangeiro,
                    inputData.placa,
                    inputData.status,
                    dataInicial,
                    dataFinal
                ).then(
                    function (success) {
                        $scope.dadosUsuarios = success;
                    },
                    function (error) {
                        console.log(error);
                        toastr.error(error.description, error.title);
                    }
                );

            }
        }

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
        $scope.gerarExcel = function (inputData) {

            var dataInicial = undefined;
            var dataFinal = undefined;

            if (!$scope.empty(inputData.dataInicial)) {
                dataInicial = moment(inputData.dataInicial).format("YYYY-MM-DD");
            }

            if (!$scope.empty(inputData.dataFinal)) {
                dataFinal = moment(inputData.dataFinal).format("YYYY-MM-DD");
            }

            var clientesIds = [];
            if (!$scope.empty(inputData.clientesSelectedItem) && inputData.clientesSelectedItem.id > 0) {
                clientesIds = inputData.clientesSelectedItem.id;
            } else {
                angular.forEach($scope.clientesList, function (value, key) {
                    clientesIds.push(value.id);
                });
            }

            relUsuariosAssiduosService.gerarExcel(
                clientesIds,
                inputData.nome,
                inputData.cpf,
                inputData.documentoEstrangeiro,
                inputData.placa,
                inputData.status,
                dataInicial,
                dataFinal
            ).then(function (success) {
                downloadService.downloadExcel(success, "relUsuariosAssiduos");
            }, function (error) {
                toastr.error(error.description, error.title);
                console.log(error);
            });
        }

        /**
         * relUsuariosAssiduosController::limparDados
         *
         * Limpa todos os campos da tela e aplica reset inicial aos filtros
         *
         * @author Gustavo Souza Gonçalves
         * @since 14/09/2018
         *
         */
        $scope.limparDados = function () {
            var date = new Date();
            var year = date.getFullYear();
            var month = date.getMonth();
            $scope.inputData = {
                clientesSelectedItem: undefined,
                clientesId: undefined,
                nome: undefined,
                clientesList: [],
                statusList: [
                    { codigo: 0, nome: "Ativado" },
                    { codigo: 1, nome: "Desativado" }
                ],
                assiduidadeList: [
                    { codigo: 0, nome: "Sim" },
                    { codigo: 1, nome: "Não" }
                ],
                veiculo: undefined,
                documentoEstrangeiro: undefined,
                usuarioContaAtivadaSelectedItem: undefined,
                assiduidadeSelectedItem: undefined,
                dataInicial: new Date(year, month, 1),
                dataFinal: new Date(year, month + 1, 0)
            };
        };

        /**
         * Inicializa a tela
         */
        $scope.init = function () {
            $scope.limparDados();
            $scope.obterListaClientes();
        };
    }
);
