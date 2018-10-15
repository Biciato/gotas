/**
 * @file webroot/js/scripts/gotas/historico_brindes.js
 * @author Gustavo Souza Gonçalves
 * @date 05/09/2017
 * @
 *
 */

$(document).ready(function () {
    $("#filtrar_unidade").on('change', function () {
        $("#search_button").click();
    });

    $("#valorMinimo").maskMoney();
    $("#valorMaximo").maskMoney();

    // ----------------------------------------------------------------
    // Funções de Inicialização

    /**
     * Função para gerar um date time picker
     *
     * @param {string} target Alvo que irá gerar o bootstrap date time picker
     */
    var generateDatePicker = function (target) {
        console.log(target);
        $("#" + target).datetimepicker({
            minView: 2,
            maxView: 4,
            clearBtn: true,
            format: 'dd/mm/yyyy',
            // format: 'yyyy-mm-dd',
            // altField: "#alt" + target,
            // altFormat:"dd/mm/Y"
        }).on('changeDate', function (ev) {
            $("#" + target).val(ev.target.value);

        });
    }

    // ----------------------------------------------------------------
    // Campos

    generateDatePicker("dataInicio");
    generateDatePicker("dataFim");
});
