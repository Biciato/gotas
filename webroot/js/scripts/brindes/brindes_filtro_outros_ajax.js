$(document).ready(function () {

    $(".validation-message").hide();

    $("#new-gift-search").click(function () {
        $(".gifts-query-region").show();
    });

    $("parametro_brinde").on('keydown', function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    $("parametro_brinde").on('keyup', function (event) {
        if (event.keyCode == 13) {
            searchBrinde();
        }
    });

    var initializeSelectClicks = function () {
        $(".select-button").on('click', function (data) {

            var a = arrayBrindes.get();

            var id = parseInt($(this).attr('value'));

            var result = $.grep(a, function (value, index) {

                if (value.id === id)
                    return value;

            });

            setBrindesInfo(result[0]);

        });
    }

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
     * Atribui informações de brindes
     */
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

    }

    /**
     * Procura Brinde
     */
    var searchBrinde = function () {
        $(".gifts-result").hide();

        $.ajax({
            url: '/Brindes/findBrindes',
            type: 'POST',
            dataType: 'json',
            data: JSON.stringify({
                parametro_brinde: $("#parametro_brinde").val(),
                clientes_id: $("#clientes_id").val()
            }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            },
            success: function (e) {
                console.log(e.user);
            }
        }).done(function (result) {
            if (result.brindes !== null && result.brindes.length > 0) {
                arrayBrindes.set(result.brindes);

                $("#gifts-result-table >tbody").html('');

                $.each(result.brindes, function (index, value) {

                    if (value.brinde_habilitado_preco_atual == null) {
                        callModalError("Nâo há preço configurado para brinde " + value.brinde.nome);
                        $("#gifts-result-table").append($('<option>', {
                            value: value.id,
                            text: value.brinde.nome + " - Preço: <NÃO CONFIGURADO>"
                        }));

                        $(".print-gift-comum").attr('disabled', true);

                    }
                    else {

                        var html = "<tr><td>" + value.brinde.nome +
                            "</td><td>" + value.brinde.tempo_rti_shower +
                            "</td><td>" + value.brinde_habilitado_preco_atual.preco +
                            "</td><td>" + "<div class='btn btn-primary btn-xs select-button' value='" + value.id + "'><i class='fa fa-check-circle-o'></i> Selecionar</div>" + "</td></tr>";

                        $("#gifts-result-table ").append(html);
                    }
                });

                $(".gifts-result-table").show();

                initializeSelectClicks();

            }

        }).fail(function (e) {
            console.log("error");
        }).always(function (e) {
            console.log("complete");
        });
    }
    $("#searchBrinde").on('click', function () {
        searchBrinde();
    });
});
