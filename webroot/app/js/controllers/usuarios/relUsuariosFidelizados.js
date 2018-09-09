/**
 * Controller para Relatório de Usuários Ativos
 */
angular.module("GotasApp",  ["ngRoute", "ngSanitize", "ui.bootstrap", "ui.mask", "ui.select"]).controller("relUsuariosFidelizados", function ($scope) {

    console.log('oi');

    var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth();
    $scope.inputData = {
        nome: undefined,
        statusList: {
            null: "<Todos>",
            1: "Ativos",
            0: "Inativos"
        },
        statusSelectedItem: "<Todos>",
        dataInicial: new Date(year, month, 1),
        dataFinal: new Date(year, month, 0)
    };


    $scope.limparDados = function () {
        var date = new Date();
        var year = date.getFullYear();
        var month = date.getMonth();
        $scope.inputData = {
            nome: undefined,
            statusList: {
                null: "<Todos>",
                1: "Ativos",
                0: "Inativos"
            },
            statusSelectedItem: "<Todos>",
            dataInicial: new Date(year, month, 1),
            dataFinal: new Date(year, month, 0)
        };
    };

    $scope.init = function () {
        $scope.limparDados();
    };
});
