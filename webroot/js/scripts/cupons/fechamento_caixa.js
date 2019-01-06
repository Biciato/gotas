/**
 * webroot\js\scripts\cupons\fechamento_caixa.js
 *
 * Arquivo jquery para element src\Template\Cupons\fechamento_caixa.ctp
 *
 * @file webroot/js/scripts/cupons/fechamento_caixa.js
 * @author Gustavo Souza Gonçalves
 * @since 2019-01-06
 *
 */

$(document).ready(function () {
    $(".print-button").on("click", function(){

        setTimeout($(".print-area").printThis({
            importCss: false
        }), 100);
    })
});
