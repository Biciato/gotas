/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\genero_brindes_clientes\form_genero_brindes_clientes.js
 * @date 2018/06/06
 *
 */

$(document).ready(function () {
    // ------------------------------------------------------------------
    // Métodos de Validação de dados
    // ------------------------------------------------------------------


    /**
     * Verifica o tipo de brinde e configura conforme o item inserido
     */
    $("#genero_brindes_id").on('change', function (obj) {

        // eu sei que obj.target.value <= 4 é Smart Shower, então o tipo principal deve ser igual o valor
        // e o tipo secundário deve ser 00

        if (obj.target.value <= 4) {
            $("#tipo_principal_codigo_brinde").val(obj.target.value);
            $("#tipo_secundario_codigo_brinde").val("00");
            $("#tipo_principal_codigo_brinde").attr("disabled", true);
            $("#tipo_secundario_codigo_brinde").attr("disabled", true);
        } else {
            $("#tipo_principal_codigo_brinde").attr("disabled", false);
            $("#tipo_secundario_codigo_brinde").attr("disabled", false);
        }
    });

    /**
     * Valida o valor inserido para tipo principal de brinde
     */
    $("#tipo_principal_codigo_brinde").on("change", function (obj) {
        if (obj.target.value <= 4 && $("#genero_brindes_id").val() > 4) {

            callModalError("Valores de 1 a 4 estão reservados apenas para brindes do tipo SMART Shower! Informe outro valor!");
            this.value = 5;
        }
    })

    $("#genero_brindes_id").change();
});
