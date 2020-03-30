/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\usuarios\pesquisar_cliente_alterar_dados.js
 * @date 17/02/2018
 *
 */

$(document).ready(function () {

    // ------------------------------------------------------------------
    // Inicialização

    /**
     * Configura comportamento de tela ao selecionar um usuário
     */

    setInterval(() => {

        if ($("#usuarios-id").val().length !== 0) {
            $(".open-cadastro-usuario").removeClass('disabled');
            $(".open-cadastro-usuario").on('click', function (e) {
                location.href = "/usuarios/editar_cadastro_usuario_final/" + $("#usuarios-id").val();

            });
            $(".open-cadastro-veiculos-usuario").removeClass('disabled');
            $(".open-cadastro-veiculos-usuario").on('click', function (e) {
                location.href = "/veiculos/veiculos_usuario_final/" + $("#usuarios-id").val();

            });
            $(".open-cadastro-transportadoras-usuario").removeClass('disabled');
            $(".open-cadastro-transportadoras-usuario").on('click', function (e) {
                location.href = "/transportadoras/transportadorasUsuario/" + $("#usuarios-id").val();

            });


        } else {
            $(".open-cadastro-usuario").addClass('disabled');

            $(".open-cadastro-usuario").on('click', function (e) {
                e.preventDefault();
                return false;
            });

            $(".open-cadastro-veiculos-usuario").addClass('disabled');

            $(".open-cadastro-veiculos-usuario").on('click', function (e) {
                e.preventDefault();
                return false;
            });

            $(".open-cadastro-transportadoras-usuario").addClass('disabled');

            $(".open-cadastro-transportadoras-usuario").on('click', function (e) {
                e.preventDefault();
                return false;
            });
        }

    }, 200);

});
