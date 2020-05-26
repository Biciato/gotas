/**
 * webroot\js\scripts\cupons\relatorio_caixa_funcionario.js
 *
 * Arquivo jquery para element src\Template\Cupons\relatorio_caixa_funcionario.ctp
 *
 * @file webroot/js/scripts/cupons/relatorio_caixa_funcionario.js
 * @author Gustavo Souza Gonçalves
 * @since 2019-06-20
 *
 */
'use strict';
$(document).ready(function () {

    /**
     * Desabilita os filtros de data e hora
     * 
     * @param {object} e Objeto de verificação 
     */
    var filtroSelecionadoOnChange = function (e) {

        var filtroDatas = false;

        if (e.target.value.toString() === "Turno") {
            filtroDatas = true;
        }
        $(".datetimepicker-input").attr("disabled", filtroDatas);
    }

    $("#tipoFiltro").on("change", filtroSelecionadoOnChange);
    $("#tipoFiltro").change();

    var dataInicioTemp = $(".data-inicio-envio").val().length != 0 ? $(".data-inicio-envio").val() : undefined;
    var dataFimTemp = $(".data-fim-envio").val().length != 0 ? $(".data-fim-envio").val() : undefined;

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
            importCss: true
        }), 100);

    })
    $(".print-button-common").on("click", function () {

        setTimeout($(".print-area-common").printThis({
            importCss: true
        }), 100);
    })

});
