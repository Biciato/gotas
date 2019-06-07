/**
 * webroot\js\scripts\cupons\relatorio_caixa.js
 *
 * Arquivo jquery para element src\Template\Cupons\relatorio_caixa.ctp
 *
 * @file webroot/js/scripts/cupons/relatorio_caixa.js
 * @author Gustavo Souza Gonçalves
 * @since 2019-01-06
 *
 */

$(document).ready(function () {
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
