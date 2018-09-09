/**
 * Controller para Relatório de Usuários Ativos
 */
angular.module("GotasApp").controller("relUsuariosAtivos", function ($scope) {

    console.log('oi');

    $scope.inputData = {
        nome: undefined
    };

    $scope.limparDados = function () {
        $scope.inputData = {
            nome: undefined,
            statusList: {
                1: "Ativos",
                0: "Inativos"
            },
            statusSelectedItem: undefined
        };
    };

    $scope.init = function () {
        $scope.limparDados();
    };
});
