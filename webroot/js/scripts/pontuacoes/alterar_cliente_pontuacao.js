/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\pontuacoes\alterar_cliente_pontuacao.js
 * @date 18/10/2017
 * 
 */

$(document).ready(function () {

    // ----------------------------------------------------------------
    // Funções de Inicialização 

    /**
     * Verifica se o campo possui valor. Se possuir, libera o botão para alterar o usuário
     * Nota: Ele não vai verificar se é o mesmo usuário
     */

    setInterval(function () {

        if ($("#usuarios_id").val().length > 0) {
            $("#button_confirm").prop('disabled', false);
            $("#button_confirm").prop('title', 'Clique para filtrar');
        } else {
            $("#button_confirm").prop('disabled', true);
            $("#button_confirm").prop('title', 'Selecione um usuário para Alterar a pontuação atribuída');
        }
    }, 250);
});
