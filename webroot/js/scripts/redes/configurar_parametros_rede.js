/**
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @file webroot\js\scripts\redes\configurar_parametros_rede.js
 * @since 2019-07-22
 *
 */

$(document).ready(function () {
    $("#custo_referencia_gotas").maskMoney();

    fixMoneyValue($("#custo_referencia_gotas"));
});
