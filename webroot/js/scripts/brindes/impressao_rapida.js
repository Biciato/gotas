/**
 * @file webroot/js/scripts/brindes/impressao_rapida.js
 * @author Gustavo Souza Gonçalves
 * @date 04/02/2018
 * @
 *
 */


$(document).ready(function () {

    // Definição de regra: parâmetro deve ser required
    $("label[for=parametro").text("Parãmetro*");
    $("#parametro").prop("required", true);

    $("#print_gift").addClass("botao-confirmar");

    $("#impressao-rapida-escolha-btn").on('click', function () {
        $("#impressao-rapida-escolha").show('500');
    });

    $(".impressao-rapida-escolha-rti-shower-btn").on('click', function () {
        $(".display-content").hide();
        $(".impressao-rapida-escolha-rti-shower").show(500);
    });

    $(".impressao-rapida-escolha-brinde-comum-btn").on('click', function () {
        $(".display-content").hide();
        $(".impressao-rapida-escolha-brinde-comum").show(500);
    });

    $("#resgate-brindes-btn").on('click', function () {
        $(".resgate-brindes").show(500);
        $(".resgate-cupom-main").show();

        $(".pdf417_code").val(null);

        $(".resgate-cupom-result").hide();
    });

});
