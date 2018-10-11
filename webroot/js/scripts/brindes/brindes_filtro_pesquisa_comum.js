/**
 * @description Arquivo de funcionalidades js para filtro de brindes
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\brindes\brindes_filtro.js
 * @date 08/12/2017
 */

$(document).ready(function () {

    $("#preco_padrao").mask("###.###");
    $("#valor_moeda_venda_padrao").mask("#,##0,00", {reverse: true});

    $("#filtrar_unidade").on('change', function(){
        $("#search_button").click();
    });

});
