/**
 * Controller para Relatório de Usuários Ativo
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 13/09/2018
 */
// var GotasApp = angular.module("GotasApp");
angular.module('GotasApp').controller("relUsuariosAtivosController",
    function ($scope, FileSaver, Blob, toastr, clientesService, relUsuariosAtivosService) {

        $scope.inputData = {
            clientesSelectedItem: undefined,
            clientesId: undefined,
            nome: undefined,
            clientesList: [],
            statusList: [
                { codigo: 0, nome: "Ativo" },
                { codigo: 1, nome: "Inativo" }
            ],
            veiculo: undefined,
            documentoEstrangeiro: undefined,
            statusSelectedItem: undefined,
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

        // ---------------------------------------- Configurações de tabela ----------------------------------------

        $scope.paginaAtual = 1;
        $scope.limitePagina = 50;
        $scope.tamanhoDaPagina = 10;

        $scope.cabecalhos = [
            "Usuário",
            "CPF",
            "Documento Estrangeiro",
            "Saldo Gotas",
            "Gotas Consumidas",
            "Moeda Adquirida ",
            "Data Cadastro na Rede"
        ];

        $scope.dadosUsuarios = [];

        // ---------------------------------------- Configurações de tabela ----------------------------------------

        // ---------------------------------------- Funções ----------------------------------------

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

        // ---------------------------------------- Pesquisas ----------------------------------------

        /**
         * Obtem a lista de clientes
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
         * relUsuariosFidelizadosController::pesquisarUsuarios
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

                relUsuariosAtivosService.pesquisarUsuarios(
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

            relUsuariosAtivosService.gerarExcel(
                clientesIds,
                inputData.nome,
                inputData.cpf,
                inputData.documentoEstrangeiro,
                inputData.placa,
                inputData.status,
                dataInicial,
                dataFinal
            ).then(function (success) {
                // TODO: Criar função excel
                excel = JSON.parse(success);
                var blob = new Blob([excel], {
                    type: 'application/xml;charset=utf-8',
                    encoding: "utf-8"
                });
                FileSaver.saveAs(blob, "Report.xls");
            }, function (error) {
                toastr.error(error.description, error.title);
                console.log(error);
            });
        }

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

                    { codigo: 0, nome: "Ativo" },
                    { codigo: 1, nome: "Inativo" }
                ],
                veiculo: undefined,
                documentoEstrangeiro: undefined,
                statusSelectedItem: undefined,
                dataInicial: new Date(year, month, 1),
                dataFinal: new Date(year, month + 1, 0)
            };


        };



        $scope.init = function () {
            $scope.limparDados();

            $scope.obterListaClientes();
        };
    }
);
