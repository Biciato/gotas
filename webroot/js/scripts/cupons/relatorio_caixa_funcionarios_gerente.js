/**
 * webroot\js\scripts\cupons\relatorio_caixa_funcionarios_gerente.js
 *
 * Arquivo jquery para element src\Template\Cupons\relatorio_caixa_funcionarios_gerente.ctp
 *
 * @file webroot/js/scripts/cupons/relatorio_caixa_funcionarios_gerente.js
 * @author Gustavo Souza Gonçalves
 * @since 2019-06-09
 *
 */
'use strict';
$(document).ready(function () {

    var dataInicioTemp = $(".data-inicio-envio").val().length != 0 ? $(".data-inicio-envio").val() : undefined;
    var dataFimTemp = $(".data-fim-envio").val().length != 0 ? $(".data-fim-envio").val() : undefined;
    console.log(dataInicio);
    console.log(dataFim);

    var dataInicio = undefined;
    var dataFim = undefined;

    if (dataInicioTemp !== undefined) {
        // dataInicio = moment(dataInicioTemp, "YYYY-MM-DD HH:mm").format("DD/MM/YYYY HH:mm");
        dataInicio = dataInicioTemp;
        // moment(new Date(), "YYYY-MM-DD HH:mm A").format("DD/MM/YYYY HH:mm")
    }

    if (dataFimTemp !== undefined) {
        // dataFim = moment(dataFimTemp).format("YYYY-MM-DD HH:mm");
        dataFim = dataFimTemp;
    }

    initializeDateTimePicker("data_inicio", "data_inicio_envio", dataInicio, null, new Date());
    initializeDateTimePicker("data_fim", "data_fim_envio", dataFim, null, new Date());

    $(".print-button-thermal").on("click", function () {

        setTimeout($(".print-area-thermal").printThis({
            importCss: false
        }), 100);

    })
    $(".print-button-common").on("click", function () {

        setTimeout($(".print-area-common").printThis({
            importCss: false
        }), 100);
    })

});
