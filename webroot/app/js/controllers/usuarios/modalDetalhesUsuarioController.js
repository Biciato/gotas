/**
 * Controller para modal de Detalhes do Usuário
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 13/09/2018
 */
// var GotasApp = angular.module("GotasApp");
angular.module('GotasApp').controller("modalDetalhesUsuarioController",
    function ($scope, $uibModalInstance, toastr, veiculosService, relUsuariosFidelizadosService, usuarioId) {

        $scope.inputData = {
            usuarioId: undefined,
            dadosVeiculosUsuario: []
        }

        // ---------------------------------------- Configurações de tabela ----------------------------------------

        $scope.paginaAtual = 1;
        $scope.limitePagina = 50;
        $scope.tamanhoDaPagina = 50;

        $scope.cabecalhos = [
            "Placa",
            "Modelo",
            "Fabricante",
            "Ano",
            "Data de Cadastro",
        ];

        $scope.dadosUsuarios = [];

        // ---------------------------------------- Configurações de tabela ----------------------------------------

        /**
         * Função que valida vazio
         * @param object value Objeto à ser validado
         *
         * @returns bool
         */
        $scope.empty = function (value) {
            if (value !== undefined && value !== null) {
                return false;
            }
            return true;
        }

        $scope.obterDadosUsuario = function (id) {

        }

        /**
         *
         * Realiza pesquisa de dados de veículo do Usuário
         *
         * @param int usuariosId Id de Usuários
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 17/09/2018
         *
         * @return Array data
         */
        $scope.obterDadosVeiculosUsuario = function (usuariosId) {

            veiculosService.obterDadosVeiculosUsuario(undefined, undefined, undefined, undefined, usuariosId).then(
                function (success) {
                    $scope.dadosVeiculosUsuario = success;
                },
                function (error) {
                    toastr.error(error.description, error.title);
                    $scope.dadosVeiculosUsuario = [];
                }
            )

        }

        /**
         * Fecha o modal atual
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 17/09/2018
         */
        $scope.fechar = function () {
            return $uibModalInstance.close();
        }


        /**
         * Inicializa a tela
         */
        $scope.init = function () {
            $scope.usuarioId = usuarioId;
            $scope.obterDadosUsuario(usuarioId);
            $scope.obterDadosVeiculosUsuario(usuarioId);

        };

        $scope.init();
    }
);
