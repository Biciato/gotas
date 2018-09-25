/**
 * Controller para modal de Detalhes de Assiduidade do Usuário
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 24/09/2018
 */
angular.module('GotasApp').controller("modalDetalhesAssiduidadeUsuarioController",
    function ($scope, $uibModalInstance, toastr, relUsuariosAssiduosService, usuariosService, usuario) {

        $scope.inputData = {
            usuario: {},
        }

        $scope.usuario = {};
        $scope.dadosAssiduidade = [];

        // -------------------------- Configurações de tabela --------------------------

        $scope.paginaAtualVeiculos = 1;
        $scope.limitePaginaVeiculos = 50;
        $scope.tamanhoDaPaginaVeiculos = 50;

        $scope.cabecalhosVeiculos = [
            "Placa",
            "Modelo",
            "Fabricante",
            "Ano",
            "Data de Cadastro",
        ];

        // -------------------------- Configurações de tabela --------------------------

        // -------------------------- Funções --------------------------

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

                relUsuariosAssiduosService.pesquisarUsuarios(
                    undefined,
                    id,
                    undefined,
                    undefined,
                    undefined,
                    undefined,
                    undefined,
                    false,
                    undefined,
                    undefined
                ).then(
                    function (success) {
                        $scope.dadosAssiduidade = success;
                    },
                    function (error) {
                        console.log(error);
                        toastr.error(error.description, error.title);
                    }
                );
            }
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
            $scope.usuario = usuario;
            $scope.obterDadosUsuario(usuario.id);

        };

        $scope.init();

        // -------------------------- Funções --------------------------

    }
);
