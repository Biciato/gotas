/**
 * @author  Gustavo Souza Gonçalves
 * @file    webroot\js\scripts\cupons\imprimir_brinde_comum.js
 * @date    29/01/2018
 */


$(document).ready(function () {
    $(".print-button").on('click', function () {

        $(".print_area").printThis({
            importCss: true
        });
    });
});
