/**
 * @file webroot/js/scripts/pages/dashboard_funcionario.js
 * @author Gustavo Souza Gonçalves
 * @date 02/08/2017
 * @
 * 
 */

$(document).ready(function () {

    // remove focus inicial quando tela carrega
    $("input").blur();

    /**
     * Reseta o layout de todos os componentes quando muda de aba
     */
    $(".atalhos").on('click', function () {
        $(".display-content").hide();

        resetUserTab();

        resetRedeemTab();
    });

    $("#cadastro-rapido-btn").on('click', function () {
        $("#cadastro-rapido").show('500');
    });

    $("#atribuicao-gotas-btn").on('click', function () {
        $("#atribuicao-gotas").show('500');
        if (!$(".video-capture-gotas-user-select-container").is(":visible")) {
            $(".video-capture-gotas-user-select-container").css('visibility', 'visible');

        }
    });

    var clearAllFields = function () {

        $("#cpf").val("");
        $("#email").val("");
        $("#nome").val("");
        $("#sexo").val(null);
        $("#data_nasc").val("");
        $("#senha").val("");
        $("#confirm_senha").val("");
        $("#telefone").val("");
        $("#endereco").val("");
        $("#endereco_numero").val("");
        $("#endereco_complemento").val("");
        $("#bairro").val("");
        $("#municipio").val("");
        $("#estado").val("");
        $("#pais").val("");
        $("#cep").val("");
    };

    clearAllFields();

});


/**
 * Reseta a aba de usuário
 */
var resetUserTab = function () {
    // exibe região de busca do usuário
    $(".user-query-region").show();

    // limpa os campos de busca do usuário
    $(".opcoes").val('nome');
    $(".opcoes").change();
    $("#parametro").val(null);

    $("#new-user-search").click();
    // limpa os campos armazenados de busca

    $("#usuarios_id").val(null);
    $("#usuariosNome").val(null);
    $("#usuariosDataNasc").val(null);
    $("#usuariosPontuacoes").val(null);

    // reseta o layout de usuário
    $(".video-capture-gotas-user-select-container").show();

    // reseta o layout de todos os formulários de inserção via QR Code
    $(".group-video-capture-gotas").show();
    $(".gotas-instascan-manual-insert").hide();
    $(".video-gotas-scanning-container").hide();

    $(".video-receipt-capture-container").hide();
    $(".video-receipt-captured-region").hide();

    $(".video-gotas-captured-region").hide();

    // reseta o layout de todos os formulários de inserção manual
    $(".gotas-camera-manual-insert").hide();
    $(".video-gotas-capture-container").hide();

};

/**
 * Reseta a aba de resgate de brindes
 */
var resetRedeemTab = function () {
    $(".resgate-cupom-main").show();
    $(".resgate-cupom-result").hide();

    popularDadosCupomResgate(null);

    $(".pdf-417-code").val(null);
}
