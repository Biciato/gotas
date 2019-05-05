/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\cupons\resgate_cupom_form.js
 * @date 30/01/2018
 *
 */

$(document).ready(function () {

    $(".pdf-417-code").mask("AAAAAAAAAAAAAA");
    $(".pdf-417-code").focus();


    $(".pdf-417-code").on('keyup', function (event) {
        if (this.value.length == 14 && event.keyCode == 13) {
            obtemCupomClienteFinal();
        }
    });

    $(".limpar-pdf-417-code").on('click', function () {
        $(".pdf-417-code").val(null);
        $(".pdf-417-code").focus();
    });

    var exibirConfirmacaoImpressaoCanhoto = function () {
        $(".container-emissao-resgate-cupom").hide();
        $(".container-confirmacao-emissao-canhoto").show();
    };

    /**
     * Faz o resgate do cupom
     */
    $(".resgatar-cupom").on('click', function () {
        var data = {
            cupom_emitido: $(".cupom_emitido").val()
        };

        callLoaderAnimation();
        $.ajax({
            type: "POST",
            url: "/Cupons/resgatarCupomAjax",
            data: JSON.stringify(data),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            },
            success: function (response) {
                console.log(response);
            },
            error: function (response) {
                console.log(response);
                closeLoaderAnimation();
            }

        }).done(function (result) {
            closeLoaderAnimation();
            console.log(result);

            if (result.status) {
                // callModalSave();

                exibirConfirmacaoImpressaoCanhoto();

                imprimirCanhotoResgate();
                // resetUserTab();
                // resetRedeemTab();
            } else {
                callModalError(result.error);
            }
        });
    });

    /**
     * Obtem o cupom do cliente para resgate
     */
    var obtemCupomClienteFinal = function () {

        callLoaderAnimation();
        $.ajax({
            type: "POST",
            url: "/Cupons/getCupomPorCodigo",
            data: JSON.stringify({
                cupom_emitido: $(".pdf-417-code").val()
            }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            },
            success: function (response) {
                console.log(response);
            },
            error: function (response) {
                callModalError(response.responseJSON.title);
                // error, arrayContent;
                // console.log(response);
                closeLoaderAnimation();
            }
        }).done(function (result) {
            console.log(result);

            closeLoaderAnimation();
            if (!result.status) {
                callModalError(result.message);
            } else {

                popularDadosCupomResgate(result.data);
                $(".resgate-cupom-result").show(500);
                $(".resgate-cupom-main").hide();
            }
        });
    };
});
