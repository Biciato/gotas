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

            var brinde = $.grep(arr, function (value, index) {
                if (value.id === id) {
                    return value;
                }
            });

            console.log(brinde[0].brinde.nome_img);
            $(".gift-image").attr('src', brinde[0].brinde.nome_img);
            $("#brindes_id").val(brinde[0].id);
            if (brinde[0].brinde_habilitado_preco_atual == null) {
                callModalError("Nâo há preço configurado para brinde " + brinde[0].brinde.nome);
                $(".print-gift-shower").attr('disabled', true);
            } else {
                $("#preco_banho").val(brinde[0].brinde_habilitado_preco_atual.preco);
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

    var setBrindesInfo = function (data) {
        if (data !== undefined) {
            $("#brindes_id").val(data.id);
            $("#brindes_nome").val(data.brinde.nome);
            $("#tempo_rti_shower").val(data.brinde.tempo_rti_shower);
            $("#preco_banho").val(data.brinde_habilitado_preco_atual.preco);

            $(".gifts-result").show();
            $(".gifts-result-table").hide();
            $(".gifts-query-region").hide();

        } else {
            $("#brindes_id").val(null);
            $("#brindes_nome").val(null);
            $("#tempo_rti_shower").val(null);
            $("#preco_banho").val(null);
        }

    };

    var searchBrinde = function () {

        callLoaderAnimation();
        $.ajax({
            url: '/Brindes/findBrindes',
            type: 'POST',
            data: JSON.stringify({
                parametro_brinde: $("#parametro_brinde").val(),
                clientes_id: $("#clientes_id").val()
            }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            },
            error: function (e) {
                console.log(e);
                closeLoaderAnimation();

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
                $(".list-gifts").append($('<option>'));

                var brindeSemPreco = false;
                $.each(result.brindes, function (index, value) {

                    if (value.brinde_habilitado_preco_atual == null) {
                        brindeSemPreco = true;
                        $(".list-gifts").append($('<option>', {
                            value: value.id,
                            text: value.brinde.nome + " - Preço: <NÃO CONFIGURADO>"
                        }));
                    }
                    else {

                        $(".list-gifts").append($('<option>', {
                            value: value.id,
                            text: value.brinde.nome + " - Preço: " + value.brinde_habilitado_preco_atual.preco
                        }));
                    }

                });

                if (brindeSemPreco) {
                    callModalError("Há brindes sem configuração de preço! Avise seu gerente!");
                }
            }

        }).fail(function (e) {
            console.log("error");
        }).always(function (e) {
            console.log("complete");
        });
    };

    searchBrinde();

});
