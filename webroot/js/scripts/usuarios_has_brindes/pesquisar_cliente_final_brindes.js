/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\usuarios_has_brindes\pesquisar_cliente_final_brindes.js
 * @date 21/02/2018
 * 
 */

$(document).ready(function () {

    // ------------------------------------------------------------------
    // Inicialização

    /**
     * Configura comportamento de tela ao selecionar um usuário
     */

    setInterval(() => {

        if ($("#usuarios_id").val().length !== 0) {
            $(".open-cliente-final-brindes").removeClass('disabled');
            $(".open-cliente-final-brindes").on('click', function (e) {
                location.href = "/UsuariosHasBrindes/exibir_cliente_final_brindes/" + $("#usuarios_id").val();

            });
        } else {

            $(".open-cliente-final-brindes").addClass('disabled');
            $(".open-cliente-final-brindes").on('click', function (e) {
                e.preventDefault();
                return false;
            });
        }
    }, 200);

});
