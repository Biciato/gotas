/**
 * Controller para Relatório de Usuários Ativo
 */
// var GotasApp = angular.module("GotasApp");
angular.module("GotasApp").controller("relUsuariosFidelizadosController", function ($scope, clientesService, relUsuariosFidelizadosService) {

    console.log('oi');

    var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth();
    $scope.inputData = {
        nome: undefined,
        clientesList: [],
        statusList: [

            { codigo: 0, nome: "Ativo" },
            { codigo: 1, nome: "Inativo" }
        ],
        statusSelectedItem: undefined,

        dataInicial: new Date(year, month, 1),
        dataFinal: new Date(year, month + 1, 0)
    };

    $scope.empty = function(value){
        if (value !== undefined){
            return false;
        }
        return true;
    }

    // ---------------------------------------- Configurações de tabela ----------------------------------------

    $scope.currentPage = 1;
    $scope.pageLimit = 50;
    $scope.pageSize = 10;

    $scope.cabecalhos = [
        "Usuário",
        "CPF",
        "Documento Estrangeiro",
        "Data Cadastro na Rede"
    ];

    $scope.dadosUsuarios = [];
    // ---------------------------------------- Configurações de tabela ----------------------------------------



    // ---------------------------------------- Funções ----------------------------------------

    $scope.validarFiltro = function(inputData) {

    }

    // ---------------------------------------- Pesquisas ----------------------------------------


    $scope.pesquisarUsuarios = function (inputData) {



        var dataInicio = undefined;
        var dataFim = undefined;

        if (!$scope.empty(inputData.dataInicial)){
            dataInicio = moment(inputData.dataInicial).format("YYYY-MM-DD");
        }

        if (!$scope.empty(inputData.dataFinal)){
            dataFim = moment(inputData.dataFinal).format("YYYY-MM-DD");
        }

        console.log(dataInicio);

        relUsuariosFidelizadosService.pesquisarUsuarios(
            inputData.clientesId,
            inputData.nome,
            inputData.cpf,
            inputData.documentoEstrangeiro,
            inputData.placa,
            inputData.status,
            dataInicio,
            dataFim
        ).then(function (success) {
            console.log(success);

            $scope.dadosUsuarios = success;
        }).then(function (error) {

            console.log(error);
        });
    }

    $scope.limparDados = function () {
        var date = new Date();
        var year = date.getFullYear();
        var month = date.getMonth();
        $scope.inputData = {
            nome: undefined,
            clientesList: [],
            statusList: [

                { codigo: 0, nome: "Ativo" },
                { codigo: 1, nome: "Inativo" }
            ],
            statusSelectedItem: undefined,
            dataInicial: new Date(year, month, 1),
            dataFinal: new Date(year, month + 1, 0)
        };
    };

    $scope.obterListaClientes = function () {
        clientesService.obterListaClientes().then(function (success) {
            $scope.clientesList = success.data.clientes;
        }).then(function (error) {
            console.log(error);
        })
    }

    $scope.init = function () {
        $scope.limparDados();

        $scope.obterListaClientes();
    };
});
