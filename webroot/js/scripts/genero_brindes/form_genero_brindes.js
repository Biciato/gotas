/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\genero_brindes\form_genero_brindes.js
 * @date 13/06/2018
 *
 */

$(document).ready(function () {
    // ------------------------------------------------------------------
    // Métodos de Validação de dados
    // ------------------------------------------------------------------

    /**
     * Configura atribuição automática
     */
    $(".atribuir-automatico").on('change', function (obj) {

        if ($(".atribuir-automatico").is(":checked")) {
            $(".tipo-principal-codigo-brinde-default").attr('readonly', false);
            $(".tipo-secundario-codigo-brinde-default").attr('readonly', false);
            $(".tipo-principal-codigo-brinde-default").attr('required', true);
            $(".tipo-secundario-codigo-brinde-default").attr('required', true);
        } else {
            $(".tipo-principal-codigo-brinde-default").attr('readonly', true);
            $(".tipo-secundario-codigo-brinde-default").attr('readonly', true);
            $(".tipo-principal-codigo-brinde-default").attr('required', false);
            $(".tipo-secundario-codigo-brinde-default").attr('required', false);

        }
    });

    $(".atribuir-automatico").change();


});
