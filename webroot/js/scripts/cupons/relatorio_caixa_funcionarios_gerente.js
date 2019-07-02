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
    console.log(dataInicioTemp);
    console.log(dataFimTemp);

    var dataInicio;
    var dataFim;

    if (dataInicioTemp !== undefined) {
        dataInicio = dataInicioTemp;
    }

    if (dataFimTemp !== undefined) {
        dataFim = dataFimTemp;
    }

    initializeDatePicker("data_inicio", "data_inicio_envio", dataInicio, null, new Date());
    initializeDatePicker("data_fim", "data_fim_envio", dataFim, null, new Date());

    $(".print-button-thermal").on("click", function () {

        setTimeout($(".print-area-thermal").printThis({
            importCss: true
        }), 100);

    })
    $(".print-button-common").on("click", function () {

        setTimeout($(".print-area-common").printThis({
            importCss: true
        }), 100);
    })

});
