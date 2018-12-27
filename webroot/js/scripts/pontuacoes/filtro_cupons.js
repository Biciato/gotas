/**
 * @author Gustavo Souza Gonçalves
 * @date 03/10/2017
 * @
 *
 */

$(document).ready(function () {

    // ----------------------------------------------------------------
    // Funções

    /**
     * Dispara a atualização da tela ao trocar de unidade
     */
    $("#filtrar_unidade").on('change', function () {
        $("#search_button").click();
    })

    initializeDatePicker("data_inicio");
    initializeDatePicker("data_fim");
});
