/**
* @author Gustavo Souza Gonçalves
* @file webroot\js\scripts\brindes\brindes_filtro_ajax.js
* @date 13/06/2018
*
*/
$(document).ready(function () {

    $(".validation-message").hide();

    $(".list-gifts").on('change', function () {
        if (this.value !== undefined && this.value.length > 0 && this.value > 0) {
            var arr = arrayBrindes.get();
            var id = parseInt(this.value);

            var brindePesquisa = $.grep(arr, function (value, index) {
                if (value.id === id) {
                    return value;
                }
            });

            var brindeSelecionado = brindePesquisa[0];

            $(".gift-image").attr('src', brindeSelecionado.brinde.nome_img);
            $("#brindes_id").val(brindeSelecionado.brindes_id);
            if (brindeSelecionado.brinde_habilitado_preco_atual == null) {
                callModalError("Nâo há preço configurado para brinde " + brindeSelecionado.brinde.nome);
                $(".print-gift-shower").attr('disabled', true);
            } else {
                $("#preco_banho").val(brindeSelecionado.brinde_habilitado_preco_atual.preco);
                $(".print-gift-shower").attr('disabled', false);
            }
        } else {
            $("#brindes_id").val(null);
            $("#preco_banho").val(null);
            $(".gift-image").attr('src', null);
        }

    });

    var arrayBrindes = {
        array: [],
        get: function () {
            return this.array;
        },
        set: function (array) {
            this.array = array;
        }
    };

    /**
     *
     * @deprecated
     * @param {object} data
     */
    // var setBrindesInfo = function (data) {
    //     if (data !== undefined) {
    //         $("#brindes_id").val(data.id);
    //         $("#brindes_nome").val(data.brinde.nome);
    //         $("#tempo_uso_brinde").val(data.brinde.tempo_uso_brinde);
    //         $("#preco_banho").val(data.brinde_habilitado_preco_atual.preco);

    //         $(".gifts-result").show();
    //         $(".gifts-result-table").hide();
    //         $(".gifts-query-region").hide();

    //     } else {
    //         $("#brindes_id").val(null);
    //         $("#brindes_nome").val(null);
    //         $("#tempo_uso_brinde").val(null);
    //         $("#preco_banho").val(null);
    //     }

    // };

    var searchBrinde = function () {

        callLoaderAnimation();
        $.ajax({
            url: '/Brindes/findBrindes',
            type: 'POST',
            data: JSON.stringify({
                parametro_brinde: $("#parametro_brinde").val(),
                clientes_id: $("#clientes_id").val(),
                tipo_pagamento: $("#tipo_pagamento").val()
            }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            },
            error: function (e) {
                console.log(e);
                callModalError(e.responseJSON.mensagem.message, e.responseJSON.mensagem.errors);

            },
            success: function (e) {
                console.log(e.user);
            },
            complete: function (result) {

            }
        }).done(function (result) {

            closeLoaderAnimation();

            if (result.brindes !== null && result.brindes.length > 0) {

                arrayBrindes.set(result.brindes);

                var brindeSemPreco = false;

                var isVendaAvulsa = $(".venda_avulsa").val();

                $.each(result.brindes, function (index, value) {

                    if (value.brinde_habilitado_preco_atual == null) {
                        brindeSemPreco = true;
                        $(".list-gifts").append($('<option>', {
                            value: value.id,
                            text: value.brinde.nome_brinde_detalhado + " - Preço: <NÃO CONFIGURADO>"
                        }));
                    }
                    else {

                        var valorAvulso = (value.brinde_habilitado_preco_atual.valor_moeda_venda_formatado != null) ? value.brinde_habilitado_preco_atual.valor_moeda_venda_formatado : 0;
                        var valorGotas = (value.brinde_habilitado_preco_atual.preco != null) ? parseFloat(value.brinde_habilitado_preco_atual.preco) : 0;

                        $(".list-gifts").append($('<option>', {
                            value: value.id,
                            text: "Tipo de Venda: " + value.brinde.tipo_venda + " / Brinde: " +  value.brinde.nome_brinde_detalhado + " - Preço: " + ((isVendaAvulsa) ? valorAvulso : valorGotas)
                        }));
                    }

                });

                if (brindeSemPreco) {
                    callModalError("Há brindes sem configuração de preço! Avise seu gerente!");
                }
            } else {
                callModalError("Não há brindes cadastrados para este Ponto de Atendimento! Não será possível emitir Brindes pelo sistema!");
            }

        }).fail(function (e) {
            console.log("error");

        }).always(function (e) {
            console.log("complete");
        });
    };

    searchBrinde();

});
