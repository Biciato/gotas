/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\usuarios\filtro_usuarios_ajax.js
 * @date 11/08/2017
 *
 */

var contaAvulsa = $("#usuarios_id").val() == "conta_avulsa";

$(document).ready(function () {
    $("#cpf").mask("###.###.###-##");

});
