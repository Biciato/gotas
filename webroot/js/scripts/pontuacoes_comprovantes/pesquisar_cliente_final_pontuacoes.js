/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\pontuacoes_comprovantes\pesquisar_cliente_final_pontuacoes.js
 * @date 20/02/2018
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
            $(".open-cliente-final-pontuacoes").removeClass('disabled');
            $(".open-cliente-final-pontuacoes").on('click', function (e) {
                location.href = "/PontuacoesComprovantes/exibir_cliente_final_pontuacoes/" + $("#usuarios_id").val();

            });
        } else {

            $(".open-cliente-final-pontuacoes").addClass('disabled');
            $(".open-cliente-final-pontuacoes").on('click', function (e) {
                e.preventDefault();
                return false;
            });
        }
    }, 200);

});
