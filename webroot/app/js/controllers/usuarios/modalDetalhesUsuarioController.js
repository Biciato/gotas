/**
 * Controller para modal de Detalhes do Usuário
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 13/09/2018
 */
// var GotasApp = angular.module("GotasApp");
angular.module('GotasApp').controller("modalDetalhesUsuarioController",
    function ($scope, $uibModalInstance, toastr, transportadorasService, veiculosService, usuariosService, usuarioId) {

        $scope.inputData = {
            usuarioId: undefined,
            usuario: {},
            dadosVeiculosUsuario: [],
            dadosTransportadorasUsuario: []
        }

        // ---------------------------------------- Configurações de tabela ----------------------------------------

        $scope.paginaAtualVeiculos = 1;
        $scope.limitePaginaVeiculos = 50;
        $scope.tamanhoDaPaginaVeiculos = 10;

        $scope.cabecalhosVeiculos = [
            "Placa",
            "Modelo",
            "Fabricante",
            "Ano",
            "Data de Cadastro",
        ];

        // ---------------------------------------- Configurações de tabela ----------------------------------------
        // ---------------------------------------- Configurações de tabela ----------------------------------------

        $scope.paginaAtualTransportadoras = 1;
        $scope.limitePaginaTransportadoras = 50;
        $scope.tamanhoDaPaginaTransportadoras = 10;

        $scope.cabecalhosTransportadoras = [
            "Nome Fantasia",
            "Razao Social",
            "CNPJ",
            "Municipio",
            "Estado",
            "Tel Fixo",
            "Tel Celular",
            "Data de Cadastro",
        ];

        // ---------------------------------------- Configurações de tabela ----------------------------------------

        $scope.usuario = {};


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

        /**
         *
         * Realiza pesquisa de dados de Usuário
         *
         * @param int id Id de Usuários
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 18/09/2018
         *
         * @return Object dados de usuário
         */
        $scope.obterDadosUsuario = function (id) {
            var prosseguir = true;
            if ($scope.empty(id)) {
                prosseguir = false;
            }

            if (prosseguir) {
                usuariosService.obterDadosUsuario(id).then(
                    function (success) {
                        $scope.usuario = success;

                    },
                    function (error) {
                        toastr.error(error.description, error.title);
                        $scope.usuario = {};
                    }
                );
            }
        }

        /**
         * modalDetalhesUsuarioController::obterDadosVeiculosUsuario
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
            veiculosService.obterDadosVeiculosUsuario(undefined, undefined, undefined, undefined, undefined, usuariosId).then(
                function (success) {
                    $scope.dadosVeiculosUsuario = success;
                }
                // ,
                // function (error) {
                //     toastr.error(error.description, error.title);
                //     $scope.dadosVeiculosUsuario = [];
                // }
            )
        }

        /**
         * modalDetalhesUsuarioController::obterDadosTransportadorasUsuario
         *
         * Realiza pesquisa de dados de transportadora do Usuário
         *
         * @param int usuariosId Id de Usuários
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 20/09/2018
         *
         * @return Array data
         */
        $scope.obterDadosTransportadorasUsuario = function (usuariosId) {
            transportadorasService.obterDadosTransportadorasUsuario(
                undefined,
                undefined,
                undefined,
                usuariosId)
                .then(
                    function (success) {
                        $scope.dadosTransportadorasUsuario = success;
                    }
                    // ,
                    // function (error) {
                    //     toastr.error(error.description, error.title);
                    //     $scope.dadosTransportadorasUsuario = [];
                    // }
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
            $scope.obterDadosTransportadorasUsuario(usuarioId);

        };

        $scope.init();
    }
);
