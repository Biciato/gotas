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

    var dataPesquisaTemp = $(".data-pesquisa-envio").val().length != 0 ? $(".data-pesquisa-envio").val() : undefined;
    console.log(dataPesquisaTemp);

    var dataPesquisa;

    if (dataPesquisaTemp !== undefined) {
        dataPesquisa = dataPesquisaTemp;
    }

    initializeDatePicker("data_pesquisa", "data_pesquisa_envio", dataPesquisa, null, new Date());

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
