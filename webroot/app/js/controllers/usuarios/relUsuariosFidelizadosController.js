/**
 * Controller para Relatório de Usuários Ativos
 */
// var GotasApp = angular.module("GotasApp");
angular.module("GotasApp").controller("relUsuariosFidelizadosController", function ($scope, clientesService) {

    console.log('oi');

    var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth();
    $scope.inputData = {
        nome: undefined,
        clientesList: [],
        statusList: {
            null: "<Todos>",
            1: "Ativos",
            0: "Inativos"
        },
        statusSelectedItem: "<Todos>",
        dataInicial: new Date(year, month, 1),
        dataFinal: new Date(year, month + 1, 0)
    };




    $scope.limparDados = function () {
        var date = new Date();
        var year = date.getFullYear();
        var month = date.getMonth();
        $scope.inputData = {
            nome: undefined,
            clientesList: [],
            statusList: {
                null: "<Todos>",
                1: "Ativos",
                0: "Inativos"
            },
            statusSelectedItem: "<Todos>",
            dataInicial: new Date(year, month, 1),
            dataFinal: new Date(year, month + 1, 0)
        };
    };

    $scope.obterClientes = function () {
        clientesService.obterClientes().then(function (success) {
            $scope.clientesList = success.data.clientes;
        }).then(function (error) {
            console.log(error);
        })
    }

    $scope.init = function () {
        $scope.limparDados();

        $scope.obterClientes();
    };
});
