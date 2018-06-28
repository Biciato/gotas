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

    var validaTipoCodigoBrindeDefault = function () {
        var tipoPrincipal = $(".tipo-principal-codigo-brinde-default").val();
        var tipoSecundario = $(".tipo-secundario-codigo-brinde-default").val();

        if (tipoPrincipal.length > 0 && tipoSecundario.length > 0) {
            $(".save-button").attr('disabled', false);
        } else {
            $(".save-button").attr('disabled', true);
        }
    }

    $(".tipo-principal-codigo-brinde-default").on('keyup', validaTipoCodigoBrindeDefault);
    $(".tipo-secundario-codigo-brinde-default").on('keyup', validaTipoCodigoBrindeDefault);

    var atribuirAutomaticoOnChange = function (obj) {

        if ($(".atribuir-automatico").is(":checked")) {
            $(".tipo-principal-codigo-brinde-default").attr('readonly', false);
            $(".tipo-secundario-codigo-brinde-default").attr('readonly', false);
            $(".tipo-principal-codigo-brinde-default").attr('required', true);
            $(".tipo-secundario-codigo-brinde-default").attr('required', true);
            $(".save-button").attr('disabled', true);
        } else {
            $(".tipo-principal-codigo-brinde-default").attr('readonly', true);
            $(".tipo-secundario-codigo-brinde-default").attr('readonly', true);
            $(".tipo-principal-codigo-brinde-default").attr('required', false);
            $(".tipo-secundario-codigo-brinde-default").attr('required', false);

            $(".save-button").attr('disabled', false);
        }
    };
    /**
     * Configura atribuição automática
     */
    $(".atribuir-automatico").on('change', atribuirAutomaticoOnChange);

    // $(".save-button").attr('disabled', false);
    $(".atribuir-automatico").change();


});
